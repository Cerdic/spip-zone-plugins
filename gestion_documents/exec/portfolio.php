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

define('_DIR_PLUGIN_GESTION_DOCUMENTS',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function generer_query_string($conteneur,$id_type,$nb_aff,$filtre){
  $query = ($conteneur?"conteneur=$conteneur&":"")
		.($id_type?"id_type=$id_type&":"")
		.(($nb_aff)?"nb_aff=$nb_aff&":"")
		.(($filtre)?"filtre=$filtre&":"");

  return $query;
}	

function exec_portfolio(){
	global $updatetable;
	global $connect_statut;
	//global $modif;
	
	include_ecrire ("inc_presentation");
	include_ecrire ("inc_documents");
	include_ecrire ("inc_index");
	include_ecrire ("inc_logos");
	include_ecrire ("inc_session");

	//
	// Recupere les donnees
	//

	debut_page(_L("Tous les Documents"), "documents", "documents");
	debut_gauche();


	//////////////////////////////////////////////////////
	// Boite "voir en ligne"
	//

	debut_boite_info();

	echo propre(_L('Cette page r&eacute;capitule la liste de tous vos documents. Pour modifier les informations de chaque document, suivez le lien vers la page de sa rubrique.'));

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
				spip_query("UPDATE spip_documents SET $update WHERE id_document=$id_document");
			}
	 	}
	}
	if (isset($_POST['modif_document'])){
		$id_document = intval($_POST['id_document']);
		$titre=addslashes($_POST['titre_document']);
		$jour_doc = intval($_POST['jour_doc']);
		$mois_doc = intval($_POST['mois_doc']);
		$annee_doc = intval($_POST['annee_doc']);
		$descriptif = addslashes($_POST['descriptif_document']);
		$date_doc = "$annee_doc-$mois_doc-$jour_doc";
		spip_query("UPDATE spip_documents SET titre='$titre',descriptif='$descriptif',date='$date_doc' WHERE id_document='$id_document'");
	}

	if (isset($_REQUEST['id_type'])){
		$id_type=intval($_REQUEST['id_type']);
		if (!isset($_POST['id_type'])) $_POST['id_type']=$_REQUEST['id_type'];
	}
	if (isset($_REQUEST['conteneur'])){
		$conteneur=addslashes($_REQUEST['conteneur']);
		if (!isset($_POST['conteneur'])) $_POST['conteneur']=$_REQUEST['conteneur'];
	}
	else
		$conteneur="";
	if (isset($_REQUEST['nb_aff'])){
		$nb_aff=intval($_REQUEST['nb_aff']);
		if (!isset($_POST['nb_aff'])) $_POST['nb_aff']=$_REQUEST['nb_aff'];
	}
	if (isset($_REQUEST['t_debut'])){
		$t_debut=intval($_REQUEST['t_debut']);
		if (!isset($_POST['t_debut'])) $_POST['t_debut']=$_REQUEST['t_debut'];
	}
	else
		$t_debut=0;
	if (isset($_REQUEST['filtre'])){
		$filtre=addslashes($_REQUEST['filtre']);
		if (!isset($_POST['filtre'])) $_POST['filtre']=$_REQUEST['filtre'];
	}
	$titre_table=_L("Tous les Documents");
	if (!$icone) $icone = "../"._DIR_PLUGIN_GESTION_DOCUMENTS."/stock_broken_image.png";

	$table_type=array();
	$s=spip_query('SELECT * FROM spip_types_documents');
	while ($row=spip_fetch_array($s)){
	  $table_type[$row['id_type']]=$row['titre'];
	}

	$res = spip_query("SELECT COUNT(*) FROM spip_documents");
	if ($row = spip_fetch_array($res))
		$nombre_documents = $row[0];
	else
		$nombre_documents = 0;


	$join = "";
	$where = "1=1";
	$order = "docs.id_document DESC";

	if ($conteneur){
		$join = "spip_documents_$conteneur AS L on L.id_document=docs.id_document";
		$where .= " AND L.id_document!=0";
	}
	if ($id_type){
		$where .= " AND docs.id_type='$id_type'";
	}
	if ($filtre=='notitle'){
		$where .= " AND docs.titre='' AND docs.descriptif=''";
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
		$where .= " AND " . calcul_mysql_in('id_document',$in);
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
		$where .= " AND " . calcul_mysql_in('id_document',$in);
	}

	$requete = "SELECT docs.* FROM spip_documents AS docs";
	if (strlen($join)>0)
		$requete .= " LEFT JOIN $join";
	$requete .= " WHERE $where ORDER BY $order";

	if (!$nb_aff)
		$nb_aff = 12;
	
	global $t_debut;
	if (isset($_GET['show_docs']))
		$show_docs = intval($_GET['show_docs']);
	if (isset($_POST['show_docs']))
		$show_docs = intval($_POST['show_docs']);
	if ($show_docs)
	{
		$pos = 0;
		$res = spip_query($requete);
		while (($row = spip_fetch_array($res)) && (intval($row['id_document'])!=$show_docs))
			$pos++;
		$t_debut = floor($pos/$nb_aff)*$nb_aff;
	}

	//$url = generer_url_ecrire('portfolio',generer_query_string($conteneur,$id_type,$nb_aff,$filtre)."::deb::");
	$tranches = afficher_tranches_requete($requete, 3,'debut',false,$nb_aff);

	$table_need_update = false;
	if ($tranches) {
	 	$result = spip_query($requete);
		$num_rows = spip_num_rows($result);

		$ifond = 0;
		$premier = true;

		// d'abord reperer les vignettes
		$tab_vignettes=array();
		$query2 = "SELECT docs.* FROM spip_documents AS docs WHERE id_vignette<>0";
		$res2 = spip_query($query2);
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
			$doc['script'] = 'portfolio'; # script de retour formulaires
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
					$link_title = _L("Rubrique ")." ".$row2['id_rubrique'];
					$utile = true;
			 	}
			 	else {
					$query2 = "SELECT * FROM spip_documents_breves WHERE id_document=$id_search";
					$res2=spip_query($query2);
					if ($row2 = spip_fetch_array($res2)){
						$url = generer_url_ecrire("breves_edit","id_breve=".$row2['id_breve']);
						$link_title = _L("Breve ")." ".$row2['id_breve'];
						$utile = true;
					}
				 	else {
						$query2 = "SELECT * FROM spip_documents_syndic WHERE id_document=$id_search";
						$res2=spip_query($query2);
						if ($row2 = spip_fetch_array($res2)){
							$url = generer_url_ecrire("sites_edit","id_syndic=".$row2['id_syndic']);
							$link_title = _L("Site ")." ".$row2['id_syndic'];
							$utile = true;
						}
					}
				}
			}

			// test de la balise alt
			$montexte = "<img$id_search>";
			$montexte = propre($montexte);
			$alt = "";
			$altgood = false;
			if (preg_match("{alt=[\"]([^\"]*)[\"]}",$montexte)){
				global $alt;
				$t=$table_type[$row['id_type']];
				$alt = preg_replace("{\\A(.*)alt=['\"]([^\"]*)['\"].*\\z}is","\\2",$montexte);
				if ( (preg_match("{\\A\($t\)\\z}",$alt))
					|| (preg_match("{\\A$t\s*-\s*[0-9\.]+\s*[ko]+\\z}",$alt)) )
				  $altgood = false;
				else
				  $altgood = true;
			}
			// balise alt ?
			$doc['info']="";
			if ($utile) {
				$puce = 'puce-verte.gif';
				$doc['info'] .= "<a href='$url' title='$link_title'>";
				$doc['info'] .= "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;";
				$doc['info'] .= "</a>";
			}
			else {
				$puce = 'puce-orange.gif';
				$doc['info'] .= "<img src='"._DIR_IMG_PACK."$puce' width='7' height='7' border='0'>&nbsp;";
			}

			if ($alt == "")
				$doc['info'] .= _L("Pas de balise alt ?? ").htmlentities($montexte);
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
			icone_horizontale (_L('Mettre les tailles a jour'), 
				generer_url_ecrire('portfolio',"updatetable=oui&".generer_query_string($conteneur,$id_type,$nb_aff,$filtre)),
				"administration-24.gif");
		}

		echo "<form action='".generer_url_ecrire('portfolio',generer_query_string($conteneur,"",$nb_aff,$filtre))."' method='post'><div>\n";
		echo _L('Type :') . "<br /><select name='id_type'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio',generer_query_string($conteneur,"",$nb_aff,$filtre).'id_type=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		$s=spip_query('SELECT * FROM spip_types_documents');
		echo "<option value=''>Tous</option>";
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

		echo "<form action='".generer_url_ecrire('portfolio',generer_query_string("",$id_type,$nb_aff,$filtre))."' method='post'><div>\n";
		echo _L('Conteneur :') . "<br /><select name='conteneur'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio',generer_query_string("",$id_type,$nb_aff,$filtre).'conteneur=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		echo "<option value=''>Tous</option>";
		echo "<option value='rubriques'".($conteneur=='rubriques'?(" selected='selected'"):"").">Rubriques</option>";
		echo "<option value='articles'".($conteneur=='articles'?(" selected='selected'"):"").">Articles</option>";
		echo "<option value='breves'".($conteneur=='breves'?(" selected='selected'"):"").">Breves</option>";
		echo "<option value='syndic'".($conteneur=='syndic'?(" selected='selected'"):"").">Syndication</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";

		echo "<form action='".generer_url_ecrire('portfolio',generer_query_string($conteneur,$id_type,$nb_aff,""))."' method='post'><div>\n";
		echo _L('Filtrer :') . "<br /><select name='filtre'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio',generer_query_string($conteneur,$id_type,$nb_aff,"").'filtre=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		echo "<option value=''>Tous</option>";
		echo "<option value='notitle'".($filtre=='notitle'?(" selected='selected'"):"").">Sans titre ni descriptif</option>";
		echo "<option value='nofile'".($filtre=='nofile'?(" selected='selected'"):"").">Fichier introuvable</option>";
		echo "<option value='badsize'".($filtre=='badsize'?(" selected='selected'"):"").">Taille erron&eacute;e</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
	
		echo "<form action='".generer_url_ecrire('portfolio',generer_query_string($conteneur,$id_type,"",$filtre))."' method='post'><div>\n";
		echo _L('Affichage :') . "<br /><select name='nb_aff'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('portfolio',generer_query_string($conteneur,$id_type,"",$filtre).'nb_aff=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		echo "<option value='12'>Par 12</option>";
		echo "<option value='24'".($nb_aff=='24'?(" selected='selected'"):"").">Par 24</option>";
		echo "<option value='48'".($nb_aff=='48'?(" selected='selected'"):"").">Par 48</option>";
		echo "<option value='96'".($nb_aff=='96'?(" selected='selected'"):"").">Par 96</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
	
		fin_raccourcis();
	
		debut_droite();

		if ($tranches) {
			if ($titre_table) echo "<div style='height: 12px;'></div>";
			echo "<div class='liste'>";
			bandeau_titre_boite2($titre_table, $icone, $couleur_claire, "black");
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
			echo $tranches;
			$args = "";
			foreach($_GET as $key=>$val)
				if ($key!='exec')
					$args.="$key=".urlencode($val)."&";

			global $couleur_claire;
			afficher_portfolio(
				$documents,	# liste des documents, avec toutes les donnees
				"article",	# article ou rubrique ?
				'portfolio',	# album d'images ou de documents ?
				true,	# a-t-on le droit de modifier ?
				$couleur_claire		# couleur des cases du tableau
			);

			/*echo "<form action='".generer_url_ecrire("portfolio",$args)."' method='post'>";
			echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
			echo $tranches;

			$largeurs = array('','','','','','','','','','');
			$styles = array('arial11', 'arial1', 'arial1','arial1','arial1','arial1','arial1','arial1','arial1','arial1');
			afficher_liste($largeurs, $table, $styles);
			echo "<input type='submit' name='modif' value='"._T('bouton_valider')."' class='fondo' />";
			echo "</form>";*/
			echo "</table>";

			echo "<a name='bas'>";
			echo "<table width='100%' border='0'>";
			
			$debut_suivant = $t_debut + $nb_aff;
			if ($debut_suivant < $nombre_documents OR $t_debut > 0) {
				echo "<tr height='10'></tr>";
				echo "<tr bgcolor='white'><td align='left'>";
				if ($t_debut > 0) {
					$debut_prec = max($t_debut - $nb_aff, 0);
					echo generer_url_post_ecrire("portfolio",generer_query_string($conteneur,$id_type,$nb_aff,$filtre)."t_debut=$debut_prec"),
						"\n<input type='submit' value='&lt;&lt;&lt;' class='fondo' />",
						$visiteurs,
						"\n</form>";
				}
				echo "</td><td style='text-align:right;'>";
				if ($debut_suivant < $nombre_documents) {
					echo generer_url_post_ecrire("portfolio",generer_query_string($conteneur,$id_type,$nb_aff,$filtre)."t_debut=$debut_suivant"),
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
