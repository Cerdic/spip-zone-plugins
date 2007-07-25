<?php

/*
 * gestion_documents
 *
 * interface de gestion des documents
 *
 * Auteur : cedric.morin@yterium.com
 * © 2006 - Distribue sous licence GPL
 *
 */
if (!defined('_DIR_PLUGIN_GESTIONDOCUMENTS')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_GESTIONDOCUMENTS',(_DIR_PLUGINS.end($p)));
}

function generer_query_string($conteneur,$id_type,$nb_aff,$filtre){
  $query = ($conteneur?"conteneur=$conteneur&":"")
		.($id_type?"id_type=$id_type&":"")
		.(($nb_aff)?"nb_aff=$nb_aff&":"")
		.(($filtre)?"filtre=$filtre&":"");

  return $query;
}	

function exec_portfolio_edit(){
	global $updatetable;
	global $connect_statut;
	//global $modif;
	
	include_spip ("inc/presentation");
	include_spip ("inc/documents");
	include_spip('inc/indexation');
	include_spip ("inc/logos");
	include_spip ("inc/session");

	//
	// Recupere les donnees
	//

	debut_page(_T("gestdoc:tous_docs"), "documents", "documents");
	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_T('gestdoc:info_doc'));

	fin_boite_info();


	global $connect_statut;
	if ($connect_statut != '0minirezo') {
		echo "<strong>"._T('avis_acces_interdit')."</strong>";
		fin_page();
		exit;
	}
	
	if ($updatetable == 'oui'){
		$requete = "SELECT * FROM spip_documents";
	 	$result = spip_query($requete);
		while ($row = spip_fetch_array($result)){
			$id_document = $row['id_document'];
			$url_fichier = generer_url_document($id_document);
			$size = @getimagesize($url_fichier);
			$file_size = @filesize($url_fichier);
			$update = "";
			if (($file_size != FALSE)&&($file_size!=$row['taille'])) {
				$update .= ",taille='$file_size'";
			}
			if (($size != FALSE)&&($size[0]!=$row['largeur'])){
				$update .= ",largeur='".$size[0]."'";
			}
			if (($size != FALSE)&&($size[1]!=$row['hauteur'])){
				$update .= ",hauteur='".$size[1]."'";
			}
			if (strlen($update)){
				$update = substr($update,1);
				spip_query("UPDATE spip_documents SET $update WHERE id_document=".spip_abstract_quote($id_document));
			}
	 	}
	}
	if (_request('modif_document')!==NULL){
		$id_document = intval(_request('id_document'));
		$titre=_request('titre_document');
		$jour_doc = intval(_request('jour_doc'));
		$mois_doc = intval(_request('mois_doc'));
		$annee_doc = intval(_request('annee_doc'));
		$descriptif = _request('descriptif_document');
		$date_doc = "$annee_doc-$mois_doc-$jour_doc";
		spip_query("UPDATE spip_documents SET titre=".spip_abstract_quote($titre).",descriptif=".spip_abstract_quote($descriptif).",date='$date_doc' WHERE id_document=".spip_abstract_quote($id_document));
	}

	if (_request('id_type')!==NULL){
		$id_type=intval(_request('id_type'));
		if (!isset($_POST['id_type'])) $_POST['id_type']=_request('id_type');
	}
	if (_request('conteneur')!==NULL){
		$conteneur=addslashes(_request('conteneur'));
		if (!isset($_POST['conteneur'])) $_POST['conteneur']=_request('conteneur');
	}
	else
		$conteneur="";
	if (_request('nb_aff')!==NULL){
		$nb_aff=intval(_request('nb_aff'));
		if (!isset($_POST['nb_aff'])) $_POST['nb_aff']=_request('nb_aff');
	}
	if (_request('t_debut')!==NULL){
		$t_debut=intval(_request('t_debut'));
		if (!isset($_POST['t_debut'])) $_POST['t_debut']=_request('t_debut');
	}
	else
		$t_debut=0;
	if (_request('filtre')!==NULL){
		$filtre=addslashes(_request('filtre'));
		if (!isset($_POST['filtre'])) $_POST['filtre']=_request('filtre');
	}
	$titre_table=_T("gestdoc:tous_docs");
	if (!$icone) $icone = "../"._DIR_PLUGIN_GESTIONDOCUMENTS."/img_pack/stock_broken_image.png";

	$table_type=array();
	$s=spip_query('SELECT * FROM spip_types_documents');
	while ($row=spip_fetch_array($s)){
	  $table_type[$row['id_type']]=$row['titre'];
	}

	$res = spip_query("SELECT COUNT(*) as total FROM spip_documents");
	if ($row = spip_fetch_array($res))
		$nombre_documents = $row['total'];
	else
		$nombre_documents = 0;

	// lister les vignettes
	$res = spip_query("SELECT id_vignette FROM spip_documents WHERE id_vignette>0");
	$vignettes = array();
	while ($row = spip_fetch_array($res))
		$vignettes[] = $row['id_vignette'];
	$in_vignettes = calcul_mysql_in('id_document',join(',',$vignettes),'NOT');
		
	$requete = array('SELECT'=>'docs.*','FROM'=>'spip_documents AS docs','JOIN'=>"",'WHERE'=>$in_vignettes,'ORDER'=>"docs.id_document DESC");

	if ($conteneur){
		$requete['JOIN'] = "spip_documents_$conteneur AS L on L.id_document=docs.id_document";
		$requete['WHERE'] .= " AND L.id_document!=0";
	}
	if ($id_type){
		$requete['WHERE'] .= " AND docs.id_type='$id_type'";
	}
	if ($filtre=='notitle'){
		$requete['WHERE'] .= " AND docs.titre='' AND docs.descriptif=''";
	}
	else if ($filtre=='nofile'){
		$tab_doc_id=array();
		$res = spip_query("SELECT id_document,fichier FROM spip_documents");
		while($row = spip_fetch_array($res)){
			//$url_fichier = generer_url_document($row['id_document']);
			$url_fichier = _DIR_RACINE . $row['fichier'];
			if (!file_exists($url_fichier))
				$tab_doc_id[]=$row['id_document'];
		}
		$in = join(",",$tab_doc_id);
		$requete['WHERE'] .= " AND " . calcul_mysql_in('docs.id_document',$in);
	}
	else if ($filtre=='badsize'){
		$tab_doc_id=array();
		$res = spip_query("SELECT id_document,fichier,taille,largeur,hauteur FROM spip_documents");
		while($row = spip_fetch_array($res)){
			//$url_fichier = generer_url_document($row['id_document']);
			$url_fichier = _DIR_RACINE . $row['fichier'];
			if (file_exists($url_fichier)){
				$size = @getimagesize($url_fichier);
				$file_size = @filesize($url_fichier);
				$ok = true;
				if (($file_size != FALSE)&&($file_size!=$row['taille'])) {
					$ok = false;
				}
				if (($size != FALSE)&&($size[0]!=$row['largeur'])){
					$ok = false;
				}
				if (($size != FALSE)&&($size[1]!=$row['hauteur'])){
					$ok = false;
				}
				if ($ok==false)
					$tab_doc_id[]=$row['id_document'];
			}
		}
		$in = join(",",$tab_doc_id);
		$requete['WHERE'] .= " AND " . calcul_mysql_in('docs.id_document',$in);
	}


	//if (strlen($join)>0)
	//	$requete .= " LEFT JOIN $join";
	//$requete .= " WHERE $where ORDER BY $order";

	if (!$nb_aff)
		$nb_aff = 12;
	
	$select = $requete['SELECT'] ? $requete['SELECT'] : '*';
	$from = $requete['FROM'];
	$join = $requete['JOIN'] ? (' LEFT JOIN ' . $requete['JOIN']) : '';
	$where = $requete['WHERE'] ? (' WHERE ' . $requete['WHERE']) : '';
	$order = $requete['ORDER BY'] ? (' ORDER BY ' . $requete['ORDER BY']) : '';
	$group = $requete['GROUP BY'] ? (' GROUP BY ' . $requete['GROUP BY']) : '';
	$limit = $requete['LIMIT'] ? (' LIMIT ' . $requete['LIMIT']) : '';

	$tmp_var = "t_debut";

	$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM $from$join$where"));
	//if (! ($cpt = $cpt['n'])) return $tous_id ;
	$cpt = $cpt['n'];

	if ($requete['LIMIT']) $cpt = min($requete['LIMIT'], $cpt);

	if (_request('show_docs'))
		$show_docs = intval(_request('show_docs'));
	$deb_aff = intval(_request('t_' .$tmp_var));
	if ($show_docs)
	{
		$pos = 0;
		$res = spip_query("SELECT $select FROM $from$join$where$order$group$limit");
		while (($row = spip_fetch_array($res)) && (intval($row['id_document'])!=$show_docs))
			$pos++;
		$deb_aff = floor($pos/$nb_aff)*$nb_aff;
		$_GET['t_debut'] = $deb_aff; // pour que afficher_tranches_requete le retrouve ...
	}

	$deb_aff = intval(_request($tmp_var));
	if ($cpt > 1.5*$nb_aff) {
		if ($GLOBALS['spip_version_code']<1.92)
			$tranches = afficher_tranches_requete($cpt, 3, $tmp_var, '', $nb_aff);
		else
			$tranches = afficher_tranches_requete($cpt, $tmp_var, '', $nb_aff);
		$limit = ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : "99999");
	}
	else $limit="99999";

	$table_need_update = false;

	if ($cpt) {
	 	$result = spip_query("SELECT $select FROM $from$join$where$order$group LIMIT $limit");
		$num_rows = spip_num_rows($result);

		$ifond = 0;
		$premier = true;

		// d'abord reperer les vignettes
		$tab_vignettes=array();
		$res2 = spip_query("SELECT docs.* FROM spip_documents AS docs WHERE id_vignette<>0");
		while ($row2 = spip_fetch_array($res2)) {
			$tab_vignettes[$row2['id_vignette']] = $row2['id_document'];
		}
		spip_free_result($res2);

		$documents=array();

		$compteur_liste = 0;
		while ($row = spip_fetch_array($result)) {
			$compteur_liste ++;
			
			$doc = $row;

			$fichier = $doc['fichier'];
			$url_fichier = generer_url_document($doc['id_document']);
			$doc['url'] = $url_fichier;
			$doc['script'] = 'portfolio_edit'; # script de retour formulaires
			$doc['id_article'] = 1; # hack 1.9.2 pour avoir le droit de supprimer le doc (pfff)
			$size = @getimagesize($url_fichier);
			$file_size = @filesize($url_fichier);

			// recherche de l'article/rubrique/breve/site lie
			$utile = false;
			$id_search = $doc['id_document'];
			if (isset($tab_vignettes[$id_document])){
				$id_search = $tab_vignettes[$id_document];
			}
			$query2 = "SELECT * FROM spip_documents_articles WHERE id_document=$id_search";
			$res2=spip_query($query2);
			if ($row2 = spip_fetch_array($res2)){
				$url = generer_url_ecrire("articles_edit","id_article=".$row2['id_article']);
				$link_title = _T('ecrire:info_article')." ".$row2['id_article'];
				$utile = true;
		 	}
		 	else {
				$query2 = "SELECT * FROM spip_documents_rubriques WHERE id_document=$id_search";
				$res2=spip_query($query2);
				if ($row2 = spip_fetch_array($res2)){
					$url = generer_url_ecrire("rubriques_edit","id_rubrique=".$row2['id_rubrique']);
					$link_title = _T("gestdoc:info_rubrique")." ".$row2['id_rubrique'];
					$utile = true;
			 	}
			 	else {
					$query2 = "SELECT * FROM spip_documents_breves WHERE id_document=$id_search";
					$res2=spip_query($query2);
					if ($row2 = spip_fetch_array($res2)){
						$url = generer_url_ecrire("breves_edit","id_breve=".$row2['id_breve']);
						$link_title = _T("gestdoc:info_breve")." ".$row2['id_breve'];
						$utile = true;
					}
				 	else {
						$query2 = "SELECT * FROM spip_documents_syndic WHERE id_document=$id_search";
						$res2=spip_query($query2);
						if ($row2 = spip_fetch_array($res2)){
							$url = generer_url_ecrire("sites_edit","id_syndic=".$row2['id_syndic']);
							$link_title = _T("ecrire:info_site")." ".$row2['id_syndic'];
							$utile = true;
						}
					}
				}
			}

			// test de la balise alt
			$montexte = isset($GLOBALS['gestion_doc_img_test'])?str_replace('%d',$id_search,$GLOBALS['gestion_doc_img_test']):"<img$id_search>";
			$montexte = propre($montexte);
			$altgood = false;
			if (NULL !== ($alt = extraire_attribut($montexte,'alt'))) {
				$t=$table_type[$row['id_type']];
				// ca teste quoi tout ca ??
				if ( $alt == $t
					|| preg_match("{\\A\($t\)\\z}",$alt)
					|| preg_match("{\\A$t\s*-\s*[0-9\.]+\s*[ko]+\\z}",$alt)
				)
				  $altgood = false;
				else
				  $altgood = true;
			}
			// balise alt ?
			$doc['info']="";
			if ($utile) {
				$puce = 'puce-verte.gif';
				$doc['info'] .= "<a href='$url' title='$link_title'>";
				$doc['info'] .= "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0' />&nbsp;";
				$doc['info'] .= "</a>";
			}
			else {
				$puce = 'puce-orange.gif';
				$doc['info'] .= "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0' />&nbsp;";
			}

			if ($alt == "")
				$doc['info'] .= _T("gestdoc:attr_alt");
			else {
				if ($altgood == true)
					$doc['info'] .= "<span style='background : #00FF00;'>";
				else{
					$doc['info'] .= "<span style='background : #FF0000;'>";
					$errors++;
				}
				$doc['info'] .= "$alt</span>";
		 	}

			// taille
			$s = "";
			$s .= 'taille : '.$row['taille'];
			if ($file_size != FALSE){
				if ($file_size!=$row['taille']) {
					$table_need_update = true;
					$s .= "(<span style='background : #FF0000;'>";
					$s .= $file_size;
					$s .= "</span>)";
					$doc['info'] .= "<br/>$s";
				}
			}

			// largeur
			$s = "";
			$s .= 'largeur : '.$row['largeur'];
			if ($size != FALSE){
				if ($size[0]!=$row['largeur']) {
					$table_need_update = true;
					$s .= "(<span style='background : #FF0000;'>";
					$s .= $size[0];
					$s .= "</span>)";
					$doc['info'] .= "<br/>$s";
				}
			}

			// hauteur
			$s = "";
			$s .= 'hauteur : '.$row['hauteur'];
			if ($size != FALSE){
				if ($size[1]!=$row['hauteur']) {
					$table_need_update = true;
					$s .= "(<span style='background : #FF0000;'>";
					$s .= $size[1];
					$s .= "</span>)";
					$doc['info'] .= "<br/>$s";
				}
			}


			
			$documents[] = $doc;
		}
		spip_free_result($result);
	}


		debut_raccourcis();

		if ($table_need_update){
			icone_horizontale (_T('gestdoc:mis_jour_tailles'), 
				generer_url_ecrire('portfolio_edit',"updatetable=oui&".generer_query_string($conteneur,$id_type,$nb_aff,$filtre)),
				"administration-24.gif");
		}
		icone_horizontale (_T('gestdoc:reparer_liens'), generer_url_ecrire('reparer_liens_documents'),"../"._DIR_PLUGIN_GESTIONDOCUMENTS."/img_pack/stock_broken_image.png");

		echo "<form action='".generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,"",$nb_aff,$filtre))."' method='post'><div>\n";
		echo _T('gestdoc:type') . "<br /><select name='id_type'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,"",$nb_aff,$filtre).'id_type=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		$s=spip_query('SELECT * FROM spip_types_documents');
		echo "<option value=''>"._T("gestdoc:tous")."</option>";
		while ($row=spip_fetch_array($s)){
			echo "<option value='".$row['id_type']."'";
			if ($row['id_type'] == $id_type)
			  echo " selected='selected'";
			echo ">" . $row['titre'] ."</option>\n";
		}
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";

		echo "<form action='".generer_url_ecrire('portfolio_edit',generer_query_string("",$id_type,$nb_aff,$filtre))."' method='post'><div>\n";
		echo _T('gestdoc:conteneur') . "<br /><select name='conteneur'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio_edit',generer_query_string("",$id_type,$nb_aff,$filtre).'conteneur=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		echo "<option value=''>"._T("gestdoc:tous")."</option>";
		echo "<option value='rubriques'".($conteneur=='rubriques'?(" selected='selected'"):"").">"._T("ecrire:info_rubriques")."</option>";
		echo "<option value='articles'".($conteneur=='articles'?(" selected='selected'"):"").">"._T("ecrire:info_articles_2")."</option>";
		echo "<option value='breves'".($conteneur=='breves'?(" selected='selected'"):"").">"._T("gestdoc:info_breves")."</option>";
		echo "<option value='syndic'".($conteneur=='syndic'?(" selected='selected'"):"").">"._T("gestdoc:info_syndication")."</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";

		echo "<form action='".generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,$id_type,$nb_aff,""))."' method='post'><div>\n";
		echo _T('gestdoc:filtrer') . "<br /><select name='filtre'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,$id_type,$nb_aff,"").'filtre=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		echo "<option value=''>"._T("gestdoc:tous")."</option>";
		echo "<option value='notitle'".($filtre=='notitle'?(" selected='selected'"):"").">"._T("gestdoc:sans_titre_descriptif")."</option>";
		echo "<option value='nofile'".($filtre=='nofile'?(" selected='selected'"):"").">"._T("gestdoc:fichier_introuvable")."</option>";
		echo "<option value='badsize'".($filtre=='badsize'?(" selected='selected'"):"").">"._T("gestdoc:taille_erronee")."</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
	
		echo "<form action='".generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,$id_type,"",$filtre))."' method='post'><div>\n";
		echo _T('gestdoc:affichage') . "<br /><select name='nb_aff'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio_edit',generer_query_string($conteneur,$id_type,"",$filtre).'nb_aff=')."'+this.options[this.selectedIndex].value\"";
		echo " class='forml' >" . "\n";
		echo "<option value='12'>"._T("gestdoc:par",array("numero" => 12))."</option>";
		echo "<option value='24'".($nb_aff=='24'?(" selected='selected'"):"").">"._T("gestdoc:par",array("numero" => 24))."</option>";
		echo "<option value='48'".($nb_aff=='48'?(" selected='selected'"):"").">"._T("gestdoc:par",array("numero" => 48))."</option>";
		echo "<option value='96'".($nb_aff=='96'?(" selected='selected'"):"").">"._T("gestdoc:par",array("numero" => 96))."</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
	
		fin_raccourcis();
	
		debut_droite();

		if (count($documents)) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
			if (isset($tranches))
				echo $tranches;
			$args = "";
			foreach($_GET as $key=>$val)
				if ($key!='exec')
					$args.="$key=".urlencode($val)."&";

			global $couleur_claire;
			$f = 'afficher_portfolio';
			//if (!function_exists($f)) $f='formulaire_documenter';
			if (!function_exists($f)) $f=charger_fonction('documenter', 'inc');
			$ret = $f(
				$documents,	# liste des documents, avec toutes les donnees
				"article",	# article ou rubrique ?
				'portfolio',	# album d'images ou de documents ?
				true,	# a-t-on le droit de modifier ?
				$couleur_claire		# couleur des cases du tableau
			);

			echo "</table>";
			if ($f != 'afficher_portfolio') echo $ret; // spip>=1.9.2

			echo "<a name='bas' />";
			echo "<table width='100%' border='0'>";
			
			$debut_suivant = $t_debut + $nb_aff;
			if ($debut_suivant < $nombre_documents OR $t_debut > 0) {
				echo "<tr style='height:10px;'><td>&nbsp;</td></tr>";
				echo "<tr bgcolor='white'><td style='text-align:left;'>";
				if ($t_debut > 0) {
					$debut_prec = max($t_debut - $nb_aff, 0);
					echo generer_url_post_ecrire("portfolio_edit",generer_query_string($conteneur,$id_type,$nb_aff,$filtre)."t_debut=$debut_prec"),
						"\n<input type='submit' value='&lt;&lt;&lt;' class='fondo' />",
						$visiteurs,
						"\n</form>";
				}
				echo "</td><td style='text-align:right;'>";
				if ($debut_suivant < $nombre_documents) {
					echo generer_url_post_ecrire("portfolio_edit",generer_query_string($conteneur,$id_type,$nb_aff,$filtre)."t_debut=$debut_suivant"),
						"\n<input type='submit' value='&gt;&gt;&gt;' class='fondo' />",
						$visiteurs,
						"\n</form>";
				}
				echo "</td></tr>\n";
			}
			
			echo "</table>\n";
			echo "</div>\n";
		}

	fin_page();
}

?>