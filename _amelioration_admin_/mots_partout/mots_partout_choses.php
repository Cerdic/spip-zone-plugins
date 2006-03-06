<?php

/***********************************************************************/
/* Définition des choses sur lesquels on peut vouloir mettre des mots clefs*/
/***********************************************************************/

//==========================ARTICLES============================================


$choses_possibles['articles'] = array(
									  'titre_chose' => 'public:articles',
									  'id_chose' => 'id_article',
									  'table_principale' => 'spip_articles',
									  'table_auth' => 'spip_auteurs_articles',
									  'tables_limite' => array(
															   'articles' => array(
																				   'table' => 'spip_articles',
																				   'nom_id' => 'id_article'),
															   'rubriques' => array(
																					'table' => 'spip_articles',
																					'nom_id' =>  'id_rubrique'),
															   'documents' => array(
																					'table' => 'spip_documents_articles',
																					'nom_id' =>  'id_document'),
															   'auteurs' => array(
																				  'table' => 'spip_auteurs_articles',
																				  'nom_id' => 'id_auteur')
															   )
									  );
									  
									  
function afficher_liste_articles($choses,$nb_aff=20) {
  
  $query = 'SELECT id_article, titre, id_rubrique, date, statut, lang, descriptif FROM spip_articles as articles WHERE articles.id_article'
	.((count($choses))?(' IN('.calcul_in($choses).')'):'');
  
  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);
  if($tranches) {

	$results = spip_query($query);
	
	echo "<div class='liste'>";
	bandeau_titre_boite2('Articles', "article-24.gif");
	
	echo afficher_liste_debut_tableau();
	echo $tranches;
	
	$i = 0;
	while ($row = spip_fetch_array($results)) {
	  $i++;
	  $vals = '';
	  
	  $id_article = $row['id_article'];
	  $tous_id[] = $id_article;
	  $titre = $row['titre'];
	  $id_rubrique = $row['id_rubrique'];
	  $date = $row['date'];
	  $statut = $row['statut'];
	  if ($lang = $row['lang']) changer_typo($lang);
	  $descriptif = $row['descriptif'];
	  if ($descriptif) $descriptif = ' title="'.attribut_html(typo($descriptif)).'"';
	  
	  $vals[] = "<input type='checkbox' name='choses[]' value='$id_article' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  
	  $s .= '<a href="'.generer_url_ecrire('articles',"id_article=$id_article").'"$descriptif$dir_lang style="display:block;">';
	  
	  if ($spip_display != 1 AND $spip_display != 4 AND lire_meta('image_process') != "non") {
		include_ecrire("inc_logos"._EXTENSION_PHP);
		$s .= decrire_logo('art','on',$id_article,24,24);
	  }
	  
	  $s .= typo($titre);
	  if ($afficher_langue AND $lang != $langue_defaut)
		$s .= " <font size='1' color='#666666'$dir_lang>(".traduire_nom_langue($lang).")</font>";
	  $s .= "</a>";
	  $s .= "</div>";
	  
	  $vals[] = $s;
	  
	  // La date
	  $s = affdate_jourcourt($date);
	  $vals[] = $s;
	  
	  // Le numero (moche)
	  if ($options == "avancees") {
		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_article</b>";
	  }
	  
	  
	  $table[] = $vals;
	}
	spip_free_result($results);
	
	if ($options == "avancees") { // Afficher le numero (JMB)
	  if ($afficher_auteurs) {
		$largeurs = array(11, '', 80, 100, 35);
		$styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	  } else {
		$largeurs = array(11, '', 100, 35);
		$styles = array('', 'arial2', 'arial1', 'arial1');
	  }
	} else {
	  if ($afficher_auteurs) {
		$largeurs = array(11, '', 100, 100);
		$styles = array('', 'arial2', 'arial1', 'arial1');
	  } else {
		$largeurs = array(11, '', 100);
		$styles = array('', 'arial2', 'arial1');
	  }
	}
	afficher_liste($largeurs, $table, $styles);
	
	echo afficher_liste_fin_tableau();
	echo '</div>';
  }
}


//==========================DOCUMENTS============================================

$choses_possibles['documents'] = array(
									   'titre_chose' => 'info_documents',
									   'id_chose' => 'id_document',
									   'table_principale' => 'spip_documents',
									   'tables_limite' => array(
																'articles' => array(
																					'table' => 'spip_documents_articles',
																					'nom_id' => 'id_article'),
																'rubriques' => array(
																					 'table' => 'spip_documents_rubriques',
																					 'nom_id' =>  'id_rubrique'),
																'documents' => array(
																					 'table' => 'spip_documents',
																					 'nom_id' =>  'id_document')
																)
									   );

function afficher_horizontal_document_assoc($document,$with_check, $case) {
  global $connect_id_auteur, $connect_statut;
  global $spip_lang_left, $spip_lang_right;

  $bord_droit = 2;

  $id_document = $document['id_document'];
  $id_vignette = $document['id_vignette'];
  $id_type = $document['id_type'];
  $titre = $document['titre'];
  $descriptif = $document['descriptif'];
  $url = generer_url_document($id_document);
  $fichier = $document['fichier'];
  $largeur = $document['largeur'];
  $hauteur = $document['hauteur'];
  $taille = $document['taille'];
  $date = $document['date'];
  $mode = $document['mode'];
  
  if ($case == 0) {
	echo "<tr style='border-top: 1px solid black;'>";
  }
  
  $style = "border-$spip_lang_left: 1px solid $couleur; border-bottom: 1px solid $couleur;";
  if ($case == $bord_droit) $style .= " border-$spip_lang_right: 1px solid $couleur;";
  echo "<td width='33%' style='text-align: $spip_lang_left; $style' valign='top'>";
  
  echo "<label for='doc$case'>"._T('motspartout:voir').'</label>';
  echo "<input type='checkbox' name='choses[]' id='doc$case' value='$id_document' />";
  
  // Signaler les documents distants par une icone de trombone
  if ($document['distant'] == 'oui') {
	echo "<img src='"._DIR_IMG_PACK.'attachment.gif'."' style='float: $spip_lang_right;' alt=\"".entites_html($document['fichier'])."\" title=\"" .
	  entites_html($document['fichier'])."\" />\n";
  }
  
  // bloc vignette + rotation
  echo "<div style='text-align:center;'>";
  
  
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
  echo document_et_vignette($document, $url, true); 
  
  echo "</div>"; // fin du bloc vignette + rotation
  
  
  // bloc titre et descriptif
  if (strlen($titre) > 0) {
	echo '<div class=\'verdana2\' style=\'text-align:center;\'><b>'.typo($titre).'</b></div>';
  } else {
	$nom_fichier = basename($fichier);
	
	if (strlen($nom_fichier) > 20) {
	  $nom_fichier = substr($nom_fichier, 0, 10)."...".substr($nom_fichier, strlen($nom_fichier)-10, strlen($nom_fichier));
	}
	echo "<div class='verdana1' style='text-align:center;'>$triangle$nom_fichier";
	echo '</div>';
  }
  
  if (strlen($descriptif) > 0) {
	echo "<div class='verdana1'>".propre($descriptif)."</div>";
  }
  
  // Taille de l'image ou poids du document
  echo "<div class='verdana1' style='text-align: center;'>";
  if ($largeur * $hauteur)
	echo _T('info_largeur_vignette',
			array('largeur_vignette' => $largeur,
				  'hauteur_vignette' => $hauteur));
  else
	echo taille_en_octets($taille);
  echo "</div>";
  
  
  echo "</td>\n";
}

function afficher_liste_documents($choses,$nb_aff=20) {
  global $spip_lang_left;
  echo "<table width='100%' cellspacing='0' cellpadding='3' style=\"border-top:1px solid black\">\n";
  $i=0;
  
  $query = "SELECT * FROM spip_documents WHERE id_document".((count($choses))?(' IN('.calcul_in($choses).')'):'');

  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);
  if($tranches) {
	echo $tranches;
	
	$results = spip_query($query);
	
	while($document = spip_fetch_array($results)) {
	  afficher_horizontal_document_assoc($document,true,$i);
	  $i++;
	  if ($i > 2) {
		$i = 0;
		echo "</tr>\n";
	  }
	}
	// fermer la derniere ligne
	if ($i > 0) {
	  echo "<td style='border-$spip_lang_left: 1px solid $couleur;'>&nbsp;</td>";
	  echo "</tr>";
	}
  }
  echo '</table>';
}


//============================MESSAGES==========================================


$choses_possibles['messages'] = array(
									  'titre_chose' => 'Messages',
									  'id_chose' => 'id_message',
									  'table_principale' => 'spip_messages',
									  'table_auth' => 'spip_auteurs_messages',
									  'tables_limite' => array(
															   'messages' => array(
																				   'table' => 'spip_messages',
																				   'nom_id' => 'id_message'),
															   'auteurs' => array(
																				  'table' => 'spip_auteurs_messages',
																				  'nom_id' => 'id_auteur')
															   )
									  );


function afficher_liste_messages($choses,$nb_aff=20) {

  $query = 'SELECT id_message,titre,type,date_heure,statut FROM spip_messages as messages WHERE messages.id_message'.((count($choses))?(' IN('.calcul_in($choses).')'):'');
  
  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);
  
  if($tranches) {

	echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, "stock_mail.gif");
	
	echo afficher_liste_debut_tableau();

	echo $tranches;
	
	$result = spip_query($query);
	$i = 0;
	while ($row = spip_fetch_array($result)) {
	  $i++;
	  $vals = '';
	  
	  $id_message = $row['id_message'];
	  $tous_id[] = $id_message;
	  $titre = $row['titre'];
	  $date = $row['date_heure'];
	  $statut = $row['statut'];
	  
	  $vals[] = "<input type='checkbox' name='choses[]' value='$id_message' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  
	  $s .= '<a href="'.generer_url_ecrire('bloogletter',"mode=courrier&id_message=$id_message").'" style="display:block;">';
	  
	  $s .= typo($titre);
	  $s .= "</a>";
	  $s .= "</div>";
	  
	  $vals[] = $s;
	  
	  // La date
	  $s = affdate_jourcourt($date);
	  $vals[] = $s;
	  
	  // Le numero (moche)
	  if ($options == "avancees") {
		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_message</b>";
	  }
	  
	  
	  $table[] = $vals;
	}
	spip_free_result($result);
	
	if ($options == "avancees") { // Afficher le numero (JMB)
	  if ($afficher_auteurs) {
		$largeurs = array(11, '', 80, 100, 35);
		$styles = array('', 'arial2', 'arial1', 'arial1', 'arial1');
	  } else {
		$largeurs = array(11, '', 100, 35);
		$styles = array('', 'arial2', 'arial1', 'arial1');
	  }
	} else {
	  if ($afficher_auteurs) {
		$largeurs = array(11, '', 100, 100);
		$styles = array('', 'arial2', 'arial1', 'arial1');
	  } else {
		$largeurs = array(11, '', 100);
		$styles = array('', 'arial2', 'arial1');
	  }
	}
	afficher_liste($largeurs, $table, $styles);
	
	echo afficher_liste_fin_tableau();
	
  }
}

//=============================AUTEURS=========================================

$choses_possibles['auteurs'] = array(
									  'titre_chose' => 'auteurs',
									  'id_chose' => 'id_auteur',
									  'table_principale' => 'spip_auteurs',
									  'tables_limite' => array(
															   'auteurs' => array(
																				   'table' => 'spip_auteurs',
																				   'nom_id' => 'id_auteur'),
															   'articles' => array(
																				  'table' => 'spip_auteurs_articles',
																				  'nom_id' => 'id_auteur')
															   )
									  );

function afficher_liste_auteurs($choses,$nb_aff=20) {
  
  $query = 'SELECT id_auteur, nom, login, email, extra, statut FROM spip_auteurs as auteurs WHERE auteurs.id_auteur'.((count($choses))?(' IN('.calcul_in($choses).')'):'');
  
  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);

  if($tranches) {
	
	echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, "reply-to-all-24.gif");
	
	echo afficher_liste_debut_tableau();
	
	echo $tranches;

	$result = spip_query($query);
	$i = 0;
	while ($row = spip_fetch_array($result)) {
	  $i++;
	  $vals = '';
	  
	  $id_auteur = $row['id_auteur'];
	  $tous_id[] = $id_auteur;
	  $nom = $row['nom'];
	  $login = $row['login'];
	  $email = $row['email'];
	  $extra = $row['extra'];
	  $statut = $row['statut'];
	  
	  $vals[] = "<input type='checkbox' name='choses[]' value='$id_auteur' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  $s .= '<a href="'.generer_url_ecrire('auteur_edit',"id_auteur=$id_auteur").'" style="display:block;">';
	  $s .= typo($login);
	  $s .= "</a>";
	  $s .= "</div>";
	  $vals[] = $s;
	  
	  $s = "<div>";
	  $s .= " (<a href=\"mailto:$email\">";
	  
	  $s .= typo($nom);
	  $s .= "</a>)";
	  $s .= "</div>";
	  
	  $vals[] = $s;
	  
	  // TODO : extra
	  //	$s = affdate_jourcourt($date);
	  //	$vals[] = $s;
	  
	  // Le numero (moche)
	  if ($options == "avancees") {
		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_auteur</b>";
	  }
	  
	  
	  $table[] = $vals;
	}
	spip_free_result($result);
	
	if ($options == "avancees") { // Afficher le numero (JMB)
	  $largeurs = array(11, '', 100,35);
	  $styles = array('', 'arial2', 'arial1', 'arial1');
	} else {
	  $largeurs = array(11, '', 100);
	  $styles = array('', 'arial2', 'arial1');
	}
	afficher_liste($largeurs, $table, $styles);
	
	echo afficher_liste_fin_tableau();
  }
}


//=============================MOTS=========================================
/*
on ne peut pas vraiment mettre de mots sur les mots comme c'est fait maintenant :(

$choses_possibles['mots'] = array(
									  'titre_chose' => 'mots',
									  'id_chose' => 'id_mot2',
									  'table_principale' => 'spip_mots',
									  'tables_limite' => array(
															   'rubriques' => array(
																				   'table' => 'spip_rubriques',
																				   'nom_id' => 'id_rubrique'),
															   'articles' => array(
																				  'table' => 'spip_articles',
																				  'nom_id' => 'id_article')
															   )
									  );

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
