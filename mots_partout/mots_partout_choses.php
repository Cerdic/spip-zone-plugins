<?php

/***********************************************************************
* Définition des choses sur lesquels on peut vouloir mettre des mots clefs
***********************************************************************/

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

  $query = 'SELECT id_article, titre, id_rubrique, date, statut, lang, descriptif FROM spip_articles as articles WHERE articles.id_article IN('.calcul_in($choses).')';
  
  
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
	  
	  $vals[] = "<input type='checkbox' name='id_choses[]' value='$id_article' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  
	  $s .= "<a href=\"articles"._EXTENSION_PHP."?id_article=$id_article\"$descriptif$dir_lang style=\"display:block;\">";
	  
	  if ($spip_display != 1 AND $spip_display != 4 AND lire_meta('image_process') != "non") {
		include_ecrire("inc_logos"._EXTENSION_PHP);
		$logo = decrire_logo("arton$id_article");
		if ($logo) {
		  $fichier = $logo[0];
		  $taille = $logo[1];
		  $taille_x = $logo[3];
		  $taille_y = $logo[4];
		  $taille = image_ratio($taille_x, $taille_y, 26, 20);
		  $w = $taille[0];
		  $h = $taille[1];
		  $fid = $logo[2];
		  $hash = calculer_action_auteur ("reduire $w $h");
		  
		  $s.= "<div style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>
<img src='../spip_image_reduite"._EXTENSION_PHP."?img="._DIR_IMG."$fichier&taille_x=$w&taille_y=$h&hash=$hash&hash_id_auteur=$connect_id_auteur' alt='$fichier' width='$w' height='$h' border='0'></div>";
		  
		}
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
	echo "<input type='checkbox' name='id_choses[]' id='doc$case' value='$id_document' />";
	
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
  
  $query = "SELECT * FROM spip_documents WHERE id_document IN (".calcul_in($choses).')';

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

  $query = 'SELECT id_message,titre,type,date_heure,statut FROM spip_messages as messages WHERE messages.id_message IN ('.calcul_in($choses).')';
  
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
	  
	  $vals[] = "<input type='checkbox' name='id_choses[]' value='$id_message' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  
	  $s .= "<a href=\"bloogletter"._EXTENSION_PHP."?mode=courrier&id_message=$id_message\" style=\"display:block;\">";
	  
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

function afficher_liste_auteurs($choses) {
  
  $query = 'SELECT id_auteur, nom, login, email, extra, statut FROM spip_auteurs as auteurs WHERE auteurs.id_auteur IN ('.calcul_in($choses).')';
  
  $tranches =  afficher_tranches_requete($query, 3,'debut',false,$nb_aff);

  if($tranches) {
	
	echo "<div style='height: 12px;'></div>";
	echo "<div class='liste'>";
	bandeau_titre_boite2($titre_table, "reply-to-all-24.gif");
	
	echo afficher_liste_debut_tableau();
	
	echo $tranches;

	$result = spip_select($query);
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
	  
	  $vals[] = "<input type='checkbox' name='id_choses[]' value='$id_auteur' id='id_chose$i'/>";
	  
	  // Le titre (et la langue)
	  $s = "<div>";
	  $s .= "<a href=\"auteur_edit"._EXTENSION_PHP."?id_auteur=$id_auteur\" style=\"display:block;\">";
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

$choses_possibles['mots'] = array(
									  'titre_chose' => 'mots',
									  'id_chose' => 'id_mot',
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


?>
