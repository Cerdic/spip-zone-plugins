<?php

/***********************************************************************/
/* Definition des choses sur lesquels on peut vouloir mettre des mots clefs*/
/***********************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

require _DIR_RESTREINT . 'inc/afficher_objets.php';
							  
function afficher_liste_articles($choses,$nb_aff=20)
{
  $afficher_objets = charger_fonction('afficher_objets','inc');
  echo $afficher_objets('article', 'Articles', 
		  array(
	'SELECT' => 'id_article, titre, id_rubrique, date, statut, lang, descriptif',
	'FROM' => 'spip_articles as articles',
	'WHERE' => sql_in('articles.id_article', $choses)),
		       'formater_articles_mots');
}

function formater_articles_mots($row, $own='')
{
	static $formater = NULL;
	static $cpt = 0;
	if (!$formater)
		$formater = charger_fonction('formater_article', 'inc');

	$cpt++;
	list ($puce, $lien, $auteurs, $date, $num) = $formater($row, $own);
	$id_article=$row['id_article'];
	$in = "<input type='checkbox' name='choses[]' value='$id_article' id='id_chose$cpt' />";
	return array($puce, $lien, $auteurs, $date, $in);
}


function afficher_liste_documents($choses,$nb_aff=20) {

  $afficher_objets = charger_fonction('afficher_objets','inc');
  echo $afficher_objets('document', 'Documents', 
			array('SELECT' => '*',
			      'FROM' => 'spip_documents AS D',
			      'WHERE' => sql_in('id_document', $choses)));
}

// cette fonction n'existe pas en standard,
// il risque d'y avoir des conflits.

function afficher_documents_boucle($document,$own='') {
  global $spip_lang_right;

  $id_document = $document['id_document'];
  $id_vignette = $document['id_vignette'];
  $id_type = $document['id_type'];
  $titre = $document['titre'];
  $descriptif = $document['descriptif'];
  $url = generer_url_entite($id_document, 'document');
  $fichier = $document['fichier'];
  $largeur = $document['largeur'];
  $hauteur = $document['hauteur'];
  $taille = $document['taille'];
  
  // Pourquoi y aurait-il un label pour lui et pas les autres ?
  $in= # "<label for='doc'>"._T('motspartout:voir').'</label>' .
  "<input type='checkbox' name='choses[]' id='doc$case' value='$id_document' />";
  
  // Signaler les documents distants par une icone de trombone
  if ($document['distant'] == 'oui') {
	$puce = "<img src='"._DIR_IMG_PACK.'attachment.gif'."' style='float: $spip_lang_right;' alt=\"".entites_html($document['fichier'])."\" title=\"" .
	  entites_html($document['fichier'])."\" />\n";
  }
  
  // bloc vignette + rotation
  // 'extension', a ajouter dans la base quand on supprimera spip_types_documents
  switch ($id_type) {
	case 1:
	  $document['extension'] = "jpg";
	  break;
	case 2:
	  $document['extension'] = "png";
	  break;
	case 3:
	  $document['extension'] = "gif";
	  break;
  }
  
  //
  // Recuperer la vignette et afficher le doc
  //

  $vignette = "<div style='text-align:center;'>"
    . document_et_vignette($document, $url, true)
    . "</div>"; // fin du bloc vignette + rotation
  
  // bloc titre et descriptif
  if (strlen($titre) > 0) {
	$nom = '<div class=\'verdana2\' style=\'text-align:center;\'><b>'.typo($titre).'</b></div>';
  } else {
	$nom_fichier = basename($fichier);
	
	if (strlen($nom_fichier) > 20) {
	  $nom_fichier = substr($nom_fichier, 0, 10)."...".substr($nom_fichier, strlen($nom_fichier)-10, strlen($nom_fichier));
	}
	$nom = "<div class='verdana1' style='text-align:center;'>$triangle$nom_fichier</div>";
  }
  
  if (strlen($descriptif) > 0) {
	$nom .= "<div class='verdana1'>".propre($descriptif)."</div>";
  }
  
  // Taille de l'image ou poids du document
  $dim=  "<div class='verdana1' style='text-align: center;'>";
  if ($largeur * $hauteur)
	$dim .= _T('info_largeur_vignette',
			array('largeur_vignette' => $largeur,
				  'hauteur_vignette' => $hauteur));
  else
	$dim .= taille_en_octets($taille);
  $dim .= "</div>";
  return array($puce, $vignette, $nom, $dim, $in);
 
}

function afficher_liste_messages($choses,$nb_aff=20) {
	$afficher_objets = charger_fonction('afficher_objets','inc');
	echo $afficher_objets('message', 'Messages', 
			array('SELECT' => '*',
			      'FROM' => 'spip_messages',
			      'WHERE' => sql_in('id_message', $choses)
			      ));
}

// cette fonction n'existe pas en standard,
// il risque d'y avoir des conflits.

function afficher_messages_boucle($row,$own='') {
  
	static $i = 0;

	$i++;
	$id_message = $row['id_message'];

	$titre = "<div>"
	.'<a href="'.generer_url_ecrire('message',"id_message=$id_message").'" style="display:block;">'
	.typo($row['titre'])
	."</a>"
	."</div>";

	$date = affdate_jourcourt($row['date_heure']);
	
	$num = "<b>"._T('info_numero_abbreviation')."$id_message</b>";
	
	$in = "<input type='checkbox' name='choses[]' value='$id_message' id='id_chose$i'/>";
	
	return array($titre, $date, $num, $in);
}


function afficher_liste_auteurs($choses,$nb_aff=20) {
  
  $afficher_objets = charger_fonction('afficher_objets','inc');
  echo $afficher_objets('auteur', 'Auteurs', 
			array('SELECT' => 'id_auteur, nom, login, email, extra, statut',
			      'FROM' => 'spip_auteurs as auteurs',
			      'WHERE' => sql_in('auteurs.id_auteur', $choses)),
			'formater_auteur_mots');
}

function formater_auteur_mots($row, $own='')
{
	static $formater = NULL;
	static $cpt = 0;
	if (!$formater)
		$formater = charger_fonction('formater_auteur', 'inc');

	$cpt++;
	$id_auteur = $row['id_auteur'];
	list($s, $mail, $nom, $w, $p) = $formater($id_auteur);
	$in = "<input type='checkbox' name='choses[]' value='$id_auteur' id='id_chose$cpt' />";
	return array($s, $mail, $nom, $w, $in);
}


function afficher_liste_groupes_mots($choses, $nb_aff=20)
{  
  $afficher_objets = charger_fonction('afficher_objets','inc');
  echo $afficher_objets('groupes_mot', 'mots', 
			array('SELECT' => 'id_groupe, titre, descriptif',
			      'FROM' => 'spip_groupes_mots',
			      'WHERE' => sql_in('id_groupe', $choses),
			      'ORDER BY' => 'titre'));
}

function afficher_groupes_mots_boucle($row,$own='')
{
	static $i = 0;
	$i++;
	$id_groupe = $row['id_groupe'];
	return array($id_groupe, $row['titre'], $row['descriptif'],
		      "<input type='checkbox' name='choses[]' value='$id_groupe' id='id_chose$i'/>");		
}

//=============================MOTS=========================================
/*
on ne peut pas vraiment mettre de mots sur les mots comme c'est fait maintenant :(

function md_afficher_groupe_mots($id_groupe,$nb_aff=20) {
  global $connect_id_auteur, $connect_statut, $connect_toutes_rubriques;
  global $spip_lang_right, $couleur_claire;
  
  include_ecrire("inc_mots");
  $query = "SELECT id_mot, titre, ".creer_objet_multi ("titre", "$spip_lang")." FROM spip_mots WHERE id_groupe = '$id_groupe' ORDER BY multi";
  
  $tranches = afficher_tranches_requete($query, 3, 'debut', false);
  
  $occurrences = calculer_liens_mots();
  
  $table = '';
  
  if (strlen($tranches)) {
 	
	if (!$GLOBALS["t_$tmp_var"]) echo "<div id='$tmp_var' style='position: relative;'>";
 	
	echo "<div class='liste'>";
	echo "<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">";
 	
	echo $tranches;
 	
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
	  
	  $vals = '';
	  
	  $id_mot = $row['id_mot'];
	  $titre_mot = $row['titre'];
	  
	  if ($connect_statut == "0minirezo")
		$aff_articles="prepa,prop,publie,refuse";
	  else
		$aff_articles="prop,publie";
	  
	  if ($id_mot!=$conf_mot) {
		$couleur = $ifond ? "#FFFFFF" : $couleur_claire;
		$ifond = $ifond ^ 1;

		$vals[] = "<input type='checkbox' name='choses[]' value='$id_auteur' id='id_chose$i'/>";
		
		if ($connect_statut == "0minirezo" OR $occurrences['articles'][$id_mot] > 0)
		  $s = "<a href='" .
			generer_url_ecrire('mots_edit', "id_mot=$id_mot&redirect=" . urlencode(generer_url_ecrire('mots_partout'))) .
			"' class='liste-mot'>".typo($titre_mot)."</a>";
		else
		  $s = typo($titre_mot);
		
		$vals[] = $s;

		$texte_lie = array();
		
		if ($occurrences['articles'][$id_mot] == 1)
		  $texte_lie[] = _T('info_1_article');
		else if ($occurrences['articles'][$id_mot] > 1)
		  $texte_lie[] = $occurrences['articles'][$id_mot]." "._T('info_articles_02');
		
		if ($occurrences['breves'][$id_mot] == 1)
		  $texte_lie[] = _T('info_1_breve');
		else if ($occurrences['breves'][$id_mot] > 1)
		  $texte_lie[] = $occurrences['breves'][$id_mot]." "._T('info_breves_03');
		
		if ($occurrences['sites'][$id_mot] == 1)
		  $texte_lie[] = _T('info_1_site');
		else if ($occurrences['sites'][$id_mot] > 1)
		  $texte_lie[] = $occurrences['sites'][$id_mot]." "._T('info_sites');
		
		if ($occurrences['rubriques'][$id_mot] == 1)
		  $texte_lie[] = _T('info_une_rubrique_02');
		else if ($occurrences['rubriques'][$id_mot] > 1)
		  $texte_lie[] = $occurrences['rubriques'][$id_mot]." "._T('info_rubriques_02');
		
		$texte_lie = join($texte_lie,", ");
		
		$vals[] = $texte_lie;
		
	        $table[] = $vals;           
	  }
	}
  $largeurs = array('', 100);
	  $styles = array('arial11','arial11', 'arial1');

	afficher_liste($largeurs, $table, $styles);
 	
	echo "</table>";
 	//        fin_cadre_relief();
	echo "</div>";
	
	if (!$GLOBALS["t_$tmp_var"]) echo "</div>";
  }
}

function afficher_liste_mots($choses,$nb_aff=20) {
  
  $query = 'SELECT DISTINCT id_groupe, '.creer_objet_multi ("type", "$spip_lang").' FROM spip_mots as mots WHERE mots.id_mot'.((count($choses))?(' IN('.calcul_in($choses).')'):'')."ORDER BY multi";
  
  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);

  if($tranches) {
	echo "<div style='height: 12px;'></div>";
	$result = spip_query($query);
	$i = 0;
	while ($row = spip_fetch_array($result)) {
          $id_groupe = $row['id_groupe'];
	  $query_groupes = "SELECT *, ".creer_objet_multi ("titre", "$spip_lang")." FROM spip_groupes_mots WHERE id_groupe=$id_groupe ORDER BY multi";
 	$result_groupes = spip_query($query_groupes);
 	
 	if ($row_groupes = spip_fetch_array($result_groupes)) {
 	    $id_groupe = $row_groupes['id_groupe'];
 	    $titre_groupe = typo($row_groupes['titre']);
 	  
 	    // Afficher le titre du groupe
 	    debut_cadre_enfonce("groupe-mot-24.gif", false, '', $titre_groupe);
 	  
 	    //
 	    // Afficher les mots-cles du groupe
 	    //
 	    md_afficher_groupe_mots($id_groupe,$nb_aff);
 	 	
 	    fin_cadre_enfonce();
 	
 	}	
	}
  }
}
*/

?>
