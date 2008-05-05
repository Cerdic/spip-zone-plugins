<?php

/***********************************************************************/
/* Definition des choses sur lesquels on peut vouloir mettre des mots clefs*/
/***********************************************************************/

//==========================ARTICLES============================================

function afficher_articles_boucle($row, &$tous_id, $afficher_auteurs, $afficher_langue, $langue_defaut, $voir_logo)
{
  global $connect_id_auteur, $dir_lang, $options, $spip_lang_right;

	$vals = '';

	$id_article = $row['id_article'];
	$tous_id[] = $id_article;
	$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));
	$id_rubrique = $row['id_rubrique'];
	$date = $row['date'];
	$statut = $row['statut'];
	if ($lang = $row['lang']) changer_typo($lang);
	$descriptif = $row['descriptif'];
	if ($descriptif) $descriptif = ' title="'.attribut_html(typo($descriptif)).'"';
	$petition = $row['petition'];

	if ($afficher_auteurs) {
		$les_auteurs = "";
		$result_auteurs = spip_query("SELECT auteurs.id_auteur, nom, messagerie, login, bio FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article=$id_article AND auteurs.id_auteur=lien.id_auteur");


		while ($row = spip_fetch_array($result_auteurs)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = typo($row['nom']);
			$auteur_messagerie = $row['messagerie'];

			if ($bio = texte_backend(supprimer_tags(couper($row['bio'],50))))
				$bio = " title=\"$bio\"";


			$les_auteurs .= ", <a href='" . generer_url_ecrire("auteurs_edit","id_auteur=$id_auteur") . "'$bio>$nom_auteur</a>";

		}
		$les_auteurs = substr($les_auteurs, 2);
	}

	// La petite puce de changement de statut
	$vals[] = puce_statut_article($id_article, $statut, $id_rubrique);

	// Le titre (et la langue)
	$s = "<div>";

	if (acces_restreint_rubrique($id_rubrique))
		$s .= http_img_pack("admin-12.gif", "", "width='12' height='12'", _T('titre_image_admin_article'));

	$s .= "<a href='" . generer_url_ecrire("articles","id_article=$id_article") .
		"'$descriptif$dir_lang style=\"display:block;\">";

	if ($voir_logo) {
		$logo_f = charger_fonction('chercher_logo', 'inc');
		if ($logo = $logo_f($id_article, 'id_article', 'on'))
			if ($logo = decrire_logo("id_article", 'on', $id_article, 26, 20, $logo))
				$s .= "<div style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</div>";
	}

	$s .= typo($titre);
	if ($afficher_langue AND $lang != $langue_defaut)
		$s .= " <font size='1' color='#666666'$dir_lang>(".traduire_nom_langue($lang).")</font>";
	if ($petition) $s .= " <font size=1 color='red'>"._T('lien_petitions')."</font>";
	$s .= "</a>";
	$s .= "</div>";
	
	$vals[] = $s;

	// Les auteurs
	if ($afficher_auteurs) $vals[] = $les_auteurs;

	// La date
	$vals[] = affdate_jourcourt($date);

	// Le numero (moche)
	if ($options == "avancees") {
		$vals[] = "<b>"._T('info_numero_abbreviation')."$id_article</b>";
	}
	

	return $vals;
}

									  
function afficher_liste_articles($choses,$nb_aff=20) {

  $tmp_var = 't_' . substr(md5(join(' ',$choses)), 0, 4);
  $tranches =  afficher_tranches_requete(count($choses), 6,$tmp_var,false,$nb_aff);
	
  echo "<div class='liste'>";
  bandeau_titre_boite2('Articles', "article-24.gif");
  
  echo afficher_liste_debut_tableau();
  if(count($choses) >= $nb_aff) echo $tranches;
  
  
  $deb_aff = intval(_request('t_debut'));

  $query = 'SELECT id_article, titre, id_rubrique, date, statut, lang, descriptif FROM spip_articles as articles WHERE articles.id_article'
	.((count($choses))?(' IN('.calcul_in($choses).')'):'') . " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : "99999");

  $results = spip_query($query);
  
  $table = array();
  $i = 0;
  while ($row = spip_fetch_array($results)) {
	$i++;
	$id_article=$row['id_article'];
	$vals = afficher_articles_boucle($row, $tous_id, true, false, false, $voir_logo);
	$vals[] = "<input type='checkbox' name='choses[]' value='$id_article' id='id_chose$i'/>";
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
	echo afficher_liste($largeurs, $table, $styles);
	
	echo afficher_liste_fin_tableau();
	echo '</div>';

}


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
  
  $deb_aff = intval(_request('t_debut'));

  $query = "SELECT * FROM spip_documents WHERE id_document".((count($choses))?(' IN('.calcul_in($choses).')'):''). " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : "99999");

  $tranches =  afficher_tranches_requete(count($choses), 3,'debut',false,$nb_aff);
  
  if(count($choses) >= $nb_aff) echo $tranches;
  
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

  echo '</table>';
}


function afficher_liste_messages($choses,$nb_aff=20) {

  $query = 'SELECT id_message,titre,type,date_heure,statut FROM spip_messages as messages WHERE messages.id_message'.((count($choses))?(' IN('.calcul_in($choses).')'):''). " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : "99999");
  
  
  $deb_aff = intval(_request('t_debut'));

  $tranches =  afficher_tranches_requete(count($choses), 3,'debut',false,$nb_aff);
  
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2($titre_table, "stock_mail.gif");
  
  echo afficher_liste_debut_tableau();

  if(count($choses) >= $nb_aff) echo $tranches;
  
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


function afficher_liste_auteurs($choses,$nb_aff=20) {
  
  $deb_aff = intval(_request('t_debut'));

  $query = 'SELECT id_auteur, nom, login, email, extra, statut FROM spip_auteurs as auteurs WHERE auteurs.id_auteur'.((count($choses))?(' IN('.calcul_in($choses).')'):''). " LIMIT " . ($deb_aff >= 0 ? "$deb_aff, $nb_aff" : "99999");
  
  $tranches =  afficher_tranches_requete(count($choses), 2,'debut',false,$nb_aff);
	
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2(_T('auteurs'),"auteur-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  if(count($choses) >= $nb_aff) echo $tranches;

  $result = spip_query($query);
  $i = 0;
  $table = array();
  while ($row = spip_fetch_array($result)) {
	$i++;
	$crap = array();
	$vals = affiche_auteur_boucle($row,$crap);
	
	$id_auteur = $row['id_auteur'];
	
	$vals[] = "<input type='checkbox' name='choses[]' value='$id_auteur' id='id_chose$i'/>";
		
	$table[] = $vals;
  }
  spip_free_result($result);

  $largeurs = array('', 100);
  $styles = array('arial2', 'arial1');
  echo afficher_liste($largeurs, $table, $styles);
  
  echo afficher_liste_fin_tableau();
}

function afficher_liste_groupes_mots($choses, $nb_aff=20) {
  $rez = spip_abstract_select(
							  array('id_groupe','titre','descriptif'),
							  array('spip_groupes_mots'),
							  array('id_groupe IN ('.calcul_in($choses).')'),'',array('titre')
							  );

  echo afficher_liste_debut_tableau();
  $table = array();
  $i = 0;
  $table[] = array('','<b>ID</b>','<b>Titre</b>','<b>Descriptif</b>');
  while($row = spip_abstract_fetch($rez)) {
	$i++;
	$id_groupe = $row['id_groupe'];
	$vals = array();
	$vals[] = "<input type='checkbox' name='choses[]' value='$id_groupe' id='id_chose$i'/>";
	$vals[] = $id_groupe;
	$vals[] = $row['titre'];
	$vals[] = $row['descriptif'];
		
	$table[] = $vals;
	
  }
  spip_abstract_free($rez);
  $largeurs = array(2, 2, 26, 70);
  $styles = array('','arial1','arial2', 'arial1');
  echo afficher_liste($largeurs, $table, $styles);
  
  echo afficher_liste_fin_tableau();

  

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