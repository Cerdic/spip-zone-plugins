<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2005                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


function documents_tous(){
	global $updatetable;
	global $connect_statut;
	global $modif;
	
	include_ecrire("inc_presentation");
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

	echo propre(_L('Cette page récapitule la liste de tous vos documents. Pour modifier les informations de chaque document, suivez le lien vers la page de sa rubrique.'));

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
	if ($modif){
	  foreach($_POST['titre'] as $id=>$titre){
	    $id=intval($id);
	    $titre=addslashes($titre);
	  	spip_query("UPDATE spip_documents SET titre='$titre' WHERE id_document='$id'");
		}
	  foreach($_POST['descriptif'] as $id=>$descriptif){
	    $id=intval($id);
	    $descriptif=addslashes($descriptif);
	  	spip_query("UPDATE spip_documents SET descriptif='$descriptif' WHERE id_document='$id'");
		}
	}

	if (isset($_REQUEST['id_type']))
		$id_type=intval($_REQUEST['id_type']);
	if (isset($_REQUEST['contenant']))
		$contenant=addslashes($_REQUEST['contenant']);
	else
		$contenant="";
	if (isset($_REQUEST['nb_aff']))
		$nb_aff=intval($_REQUEST['nb_aff']);

	$titre_table=_L("Tous les Documents");
	if (!$icone) $icone = "doc-24.gif";

		if ($id_type)
			if ($contenant)
				$requete = "SELECT docs.* FROM spip_documents AS docs LEFT JOIN spip_documents_$contenant AS L on L.id_document=docs.id_document WHERE L.id_document!=0 AND docs.id_type='$id_type' ORDER BY docs.id_document DESC";
			else
				$requete = "SELECT docs.* FROM spip_documents AS docs WHERE docs.id_type='$id_type' ORDER BY docs.id_document DESC";
		else
			if ($contenant)
				$requete = "SELECT docs.* FROM spip_documents AS docs LEFT JOIN spip_documents_$contenant AS L on L.id_document=docs.id_document WHERE L.id_document!=0 ORDER BY docs.id_document DESC";
			else
				$requete = "SELECT docs.* FROM spip_documents AS docs ORDER BY docs.id_document DESC";

		if ($nb_aff)
			$tranches = afficher_tranches_requete($requete, 9,false,false,$nb_aff);
		else
			$tranches = afficher_tranches_requete($requete, 9);

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

			$vals = '';
			$vals[] = _L('id');
			$vals[] = _L('fichier');
			$vals[] = _L('titre et descriptif');
			$vals[] = _L('date');
			$vals[] = _L('vignette');
			$vals[] = _L('alt');
			$vals[] = _L('taille');
			$vals[] = _L('largeur');
			$vals[] = _L('hauteur');
			$table[] = $vals;

			$compteur_liste = 0;
			while ($row = spip_fetch_array($result)) {
				$compteur_liste ++;
				$vals = '';

				$titre = $row['titre'];
				$descriptif = $row['descriptif'];
				$date = $row['date'];
				$id_document = $row['id_document'];
				$fichier = $row['fichier'];
				$url_fichier = generer_url_document($id_document);
				$size = @getimagesize($url_fichier);
				$file_size = @filesize($url_fichier);

				// recherche de l'article/rubrique/breve/site lie
				$utile = false;
				$id_search = $id_document;
				if (isset($tab_vignettes[$id_document])){
					$id_search = $tab_vignettes[$id_document];
				}
				$query2 = "SELECT * FROM spip_documents_articles WHERE id_document=$id_search";
				$res2=spip_query($query2);
				if ($row2 = spip_fetch_array($res2)){
					$url = generer_url_ecrire("articles_edit","id_article=".$row2['id_article']);
					$utile = true;
			 	}
			 	else {
					$query2 = "SELECT * FROM spip_documents_rubriques WHERE id_document=$id_search";
					$res2=spip_query($query2);
					if ($row2 = spip_fetch_array($res2)){
						$url = generer_url_ecrire("rubriques_edit","id_rubrique=".$row2['id_rubrique']);
						$utile = true;
				 	}
				 	else {
						$query2 = "SELECT * FROM spip_documents_breves WHERE id_document=$id_search";
						$res2=spip_query($query2);
						if ($row2 = spip_fetch_array($res2)){
							$url = generer_url_ecrire("breves_edit","id_breve=".$row2['id_breve']);
							$utile = true;
						}
					 	else {
							$query2 = "SELECT * FROM spip_documents_syndic WHERE id_document=$id_search";
							$res2=spip_query($query2);
							if ($row2 = spip_fetch_array($res2)){
								$url = generer_url_ecrire("sites_edit","id_syndic=".$row2['id_syndic']);
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
					$alt = preg_replace("{\\A(.*)alt=['\"]([^\"]*)['\"].*\\z}is","\\2",$montexte);
					if (preg_match("{\\A\(.{1,4}\)\\z}i",$alt))
					  $altgood = false;
					else
					  $altgood = true;
				}
				// le tableau
				// id
				$s = "$id_document&nbsp;&nbsp;";
				$vals[] = $s;

				// puce et titre
				$s = "";
				if ($utile) {
					$puce = 'puce-verte-breve.gif';
					$s = "<a href='$url'>";
					$s .= "<img src='img_pack/$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
					if ($titre=="") $s .= $fichier;
					else						$s .= typo($titre);
					$s .= "</a>";
				}
				else {
					$puce = 'puce-orange-breve.gif';
					$s .= "<img src='img_pack/$puce' width='7' height='7' border='0'>&nbsp;&nbsp;";
					if ($titre=="") $s .= $fichier;
					else						$s .= typo($titre);
				}
				if (isset($tab_vignettes[$id_document])){
					$s .= "<br /><span style='font-style: italic;'>";
					$s .= _L('vignette du document n°') . $tab_vignettes[$id_document] . "</span>";
				}
				if (!$utile) {
					/*$link = new Link ($image_url);
					$link->addVar('redirect', $redirect_url);
					$link->addVar('hash', calculer_action_auteur("supp_doc ".$id_document));
					$link->addVar('hash_id_auteur', $connect_id_auteur);
					$link->addVar('doc_supp', $id_document);*/
					ob_start();
					$redirect_url = generer_url_ecrire('documents_tous', "id_document=$id_document");
					icone_horizontale (_T('icone_supprimer_document'), ""/*generer_action_auteur('supprimer', $id_document, $redirect_url)*/, "image-24.gif", "supprimer.gif");
					//icone_horizontale (_T('icone_supprimer_document'), $link->getUrl(), "image-24.gif", "supprimer.gif");
					$s .= "<br/>" . ob_get_contents();
					ob_end_clean();
				}

				$s .= " &nbsp;&nbsp;";
				$vals[] = $s;

				// titre et descriptif
				$s = "";
				if (!isset($tab_vignettes[$id_document])){
					//$s .= "<input type='hidden' name='id_document' value='$id_document' />\n";
					$s .= "<input type='text' name='titre[$id_document]' value='".htmlentities($titre,ENT_QUOTES)."' class='forml' style='width:150px' /><br/>\n";
					$s .= "<input type='text' name='descriptif[$id_document]' value='".htmlentities($descriptif,ENT_QUOTES)."' class='forml' style='width:150px' /><br/>\n";
				}
				$s .= " &nbsp;&nbsp;";
				$vals[] = $s;
				

				// date
				$s = affdate_jourcourt($date)."&nbsp;&nbsp;";
				$vals[] = $s;

				// vignette
				$s = document_et_vignette($row, $url_fichier);
				$s .= " &nbsp;&nbsp;";

				// fichier OK ?
				$fichier_present = @file_exists($url_fichier);
				if (!$fichier_present) $s.='(fichier absent)';

				$vals[] = $s;

				// balise alt ?
				$s = "";
				if ($alt == "")
					$s .= "Pas de balise alt ?? ".htmlentities($montexte);
				else {
					if ($altgood == true)
						$s .= "<span style='background : #00FF00;'>";
					else{
						$s .= "<span style='background : #FF0000;'>";
						$errors++;
					}
					$s .= "$alt</span>";
			 	}
				$s .= " &nbsp;&nbsp;";
				$vals[] = $s;

				// taille
				$s = "";
				$s .= $row['taille'];
				if ($file_size != FALSE){
					if ($file_size!=$row['taille']) {
						$table_need_update = true;
						$s .= "<br/><span style='background : #FF0000;'>";
						$s .= $file_size;
						$s .= "</span>";
					}
				}
				$vals[] = $s;

				// largeur
				$s = "";
				$s .= $row['largeur'];
				if ($size != FALSE){
					if ($size[0]!=$row['largeur']) {
						$table_need_update = true;
						$s .= "<br/><span style='background : #FF0000;'>";
						$s .= $size[0];
						$s .= "</span>";
					}
				}
				$vals[] = $s;

				// hauteur
				$s = "";
				$s .= $row['hauteur'];
				if ($size != FALSE){
					if ($size[1]!=$row['hauteur']) {
						$table_need_update = true;
						$s .= "<br/><span style='background : #FF0000;'>";
						$s .= $size[1];
						$s .= "</span>";
					}
				}
				$vals[] = $s;

				$table[] = $vals;
			}
			spip_free_result($result);
		}


		debut_raccourcis();

		if ($table_need_update){
			icone_horizontale (_L('Mettre les tailles a jour'), generer_url_ecrire('documents_tous',"updatetable=oui"), "administration-24.gif");
		}

		// recupere les types
		/*$res = spip_query("SELECT * FROM spip_types_documents");
		while ($row = spip_fetch_array($res))
			$types[$row['id_type']] = $row;*/
		
		echo "<form action='".generer_url_ecrire('documents_tous',(($contenant)?"contenant=$contenant&":"")).(($nb_aff)?"nb_aff=$nb_aff":"")."' method='post'><div>\n";
		echo _L('Type :') . "<br /><select name='id_type'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('documents_tous',($contenant?"contenant=$contenant&":"").(($nb_aff)?"nb_aff=$nb_aff&":"").'id_type=')."'+this.options[this.selectedIndex].value\"";
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
	
		echo "<form action='".generer_url_ecrire('documents_tous',($id_type?"id_type=$id_type&":"")).(($nb_aff)?"nb_aff=$nb_aff":"")."' method='post'><div>\n";
		echo _L('Contenant :') . "<br /><select name='contenant'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('documents_tous',($id_type?"id_type=$id_type&":"").(($nb_aff)?"nb_aff=$nb_aff&":"").'contenant=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		echo "<option value=''>Tous</option>";
		echo "<option value='rubriques'".($contenant=='rubriques'?(" selected='selected'"):"").">Rubriques</option>";
		echo "<option value='articles'".($contenant=='articles'?(" selected='selected'"):"").">Articles</option>";
		echo "<option value='breves'".($contenant=='breves'?(" selected='selected'"):"").">Breves</option>";
		echo "<option value='syndic'".($contenant=='syndic'?(" selected='selected'"):"").">Syndication</option>";
		echo "</select>";
		echo "<noscript><div>";
		echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' />";
		echo "</div></noscript></div>\n";
		echo "</form>\n";
	
		echo "<form action='".generer_url_ecrire('documents_tous',($id_type?"id_type=$id_type&":"").(($contenant)?"contenant=$contenant":""))."' method='post'><div>\n";
		echo _L('Affichage :') . "<br /><select name='nb_aff'";
		echo "onchange=\"document.location.href='";
		echo generer_url_ecrire('documents_tous',($id_type?"id_type=$id_type&":"").(($contenant)?"contenant=$contenant&":"").'nb_aff=')."'+this.options[this.selectedIndex].value\"";
		echo ">" . "\n";
		echo "<option value='10'>Par 10</option>";
		echo "<option value='20'".($nb_aff=='20'?(" selected='selected'"):"").">Par 20</option>";
		echo "<option value='50'".($nb_aff=='50'?(" selected='selected'"):"").">Par 50</option>";
		echo "<option value='100'".($nb_aff=='100'?(" selected='selected'"):"").">Par 100</option>";
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
			$args = "";
			foreach($_GET as $key=>$val)
				if ($key!='exec')
					$args.="$key=".urlencode($val)."&";
			echo "<form action='".generer_url_ecrire("documents_tous",$args)."' method='post'>";
			echo "<table width='100%' cellpadding='5' cellspacing='0' border='0'>";
			echo $tranches;

			$largeurs = array('','','','','','','','','','');
			$styles = array('arial11', 'arial1', 'arial1','arial1','arial1','arial1','arial1','arial1','arial1','arial1');
			afficher_liste($largeurs, $table, $styles);
			echo "</table>";
			echo "<input type='submit' name='modif' value='"._T('bouton_valider')."' class='fondo' />";
			echo "</form>";
			echo "</div>\n";
		}

	fin_page();
}

?>
