<?php 


//	  mots_partout.php
//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL.
//
//    Copyright (C) 2005  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


/***********************************************************************
 * function
 ***********************************************************************/

  
function verifier_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  return (($connect_statut == '0minirezo') AND $connect_toutes_rubriques);
}

function verifier_admin_restreint($id_rubrique) {
  global $connect_id_auteur;
  global $connect_statut, $connect_toutes_rubriques;

}

function verifier_auteur($table, $id_objet, $id) {
  global $connect_id_auteur;
  $select = array('id_auteur');
  
  $from =  array($table);
  
  $where = array("id_auteur = $connect_id_auteur", "$id_objet = $id");
  
  $result = spip_abstract_select($select,$from,$where);
  
  if (spip_abstract_count($result) > 0) {
	spip_abstract_free($result);
	return true;
  }
  spip_abstract_free($result);
  return false;
}


function calcul_numeros($array, $search, $total) {
  if(is_array($array))
	$tt = count(array_keys($array,$search));
  else
	return 0;

  if($tt == 0) return 0;
  if($tt < $total) return 1;
  return 2;
}

function md_afficher_liste($largeurs, $table, $styles = '') {
	global $couleur_claire;
	global $browser_name;
	global $spip_display;
	global $spip_lang_left;

	if (!is_array($table)) return;
	reset($table);
	echo "\n";
	if ($spip_display != 4) {
		while (list(,$t) = each($table)) {
			if (eregi("msie", $browser_name)) $msover = " onMouseOver=\"changeclass(this,'tr_liste_over');\" onMouseOut=\"changeclass(this,'tr_liste');\"";
			echo "<tr class='tr_liste'$msover>";
			reset($largeurs);
			if ($styles) reset($styles);
			while (list($texte, $sel) = each($t)) {
				$style = $largeur = "";
				list(, $largeur) = each($largeurs);
				if ($styles) list(,$style) = each($styles);
				if (!trim($texte)) $texte .= "&nbsp;";
				echo "<td";
				if ($largeur) echo " width=\"$largeur\"";
				if ($style)  echo ' class="'.$style[$sel].'"';
				echo ">$texte</td>";
			}
			echo "</tr>\n";
		}
	} else {
		echo "<ul style='text-align: $spip_lang_left;'>";
		while (list(, $t) = each($table)) {
			echo "<li>";
			reset($largeurs);
			if ($styles) reset($styles);
			while (list(, $texte) = each($t)) {
				$style = $largeur = "";
				list(, $largeur) = each($largeurs);
				
				if (!$largeur) {
					echo $texte." ";
				}
			}
			echo "</li>\n";
		}
		echo "</ul>";
	}
	echo "\n";
}


function find_tables($nom, $tables) {
  $toret = array();
  foreach($tables as $t => $dec) {
	if(ereg($nom,$t)) {
	  $toret[] = $t;
	}
  }
  return $toret;
}

function calcul_in($mots) {
  for($i=0; $i < count($mots); $i++) {
	if($i > 0) $to_ret .= ',';
	$to_ret .= $mots[$i];
  }

  return $to_ret;
}

//======================================================================

function afficher_horizontal_document_assoc($id_document,$with_check, $case) {
	global $connect_id_auteur, $connect_statut;
	global $spip_lang_left, $spip_lang_right;

	$bord_droit = 2;

	$select = array('*');
	$from = array('spip_documents');
	$where = array("id_document = $id_document");
	$results = spip_abstract_select($select,$from,$where);

	if($document = spip_abstract_fetch($results)) {
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
	  
	  
	  # 'extension', a ajouter dans la base quand on supprimera spip_types_documents
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
}

function afficher_liste_documents($choses) {
  global $spip_lang_left;
  echo "<table width='100%' cellspacing='0' cellpadding='3' style=\"border-top:1px solid black\">\n";
  $i=0;
  foreach($choses as $id_chose) {
	afficher_horizontal_document_assoc($id_chose,true,$i);
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

//======================================================================

function afficher_liste_articles($choses) {
  //  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2('Articles', "article-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_articles as articles');
  $select= array();
  $select[] = 'id_article';
  $select[] = 'titre';
  $select[] = 'id_rubrique';
  $select[] = 'date';
  $select[] = 'statut';
  $select[] = 'lang';
  $select[] = 'descriptif';
  $where = array('articles.id_article IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
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
  echo '</div>';
}

//======================================================================

function afficher_liste_messages($choses) {
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2($titre_table, "stock_mail.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_messages as messages');
  $select= array();
  $select[] = 'id_message';
  $select[] = 'titre';
  $select[] = 'type';
  $select[] = 'date_heure';
  $select[] = 'statut';
  $where = array('messages.id_message IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
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

//======================================================================

function afficher_liste_auteurs($choses) {
  echo "<div style='height: 12px;'></div>";
  echo "<div class='liste'>";
  bandeau_titre_boite2($titre_table, "reply-to-all-24.gif");
  
  echo afficher_liste_debut_tableau();
  
  $from = array('spip_auteurs as auteurs');
  $select= array();
  $select[] = 'id_auteur';
  $select[] = 'nom';
  $select[] = 'login';
  $select[] = 'email';
  $select[] = 'extra';
  $select[] = 'statut';
  $where = array('auteurs.id_auteur IN ('.calcul_in($choses).')');
  
  $result = spip_abstract_select($select,$from,$where);
  $i = 0;
  while ($row = spip_abstract_fetch($result)) {
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

//======================================================================

function afficher_liste_defaut($choses) {
  echo '<table>';
  $i = 0;
  foreach($choses as $id_chose) {
	$i++;
	echo "<td><tr><input type='checkbox' name='id_choses[]' value='$id_chose' id='id_chose$i'/></tr><tr> <label for='id_chose$i'>$id_chose</label></tr></td>";
  }
  echo '</table>';
}

function mots_partout() {

  
  include_ecrire ("inc_presentation");
  include_ecrire ("inc_documents");
  include_ecrire ("inc_abstract_sql");
  include_ecrire ("inc_objet");
  include_ecrire('_libs_/tag-machine/inc_tag-machine.php');
  
  /***********************************************************************
   * Définition des choses sur lesquels on peut vouloir mettre des mots clefs
   ***********************************************************************/
  
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

  /***********************************************************************
   * PREFIXE
   ***********************************************************************/
  $table_pref = 'spip';
  if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];


  /***********************************************************************
   * installation
   * TODO ici on devrait avoir une abstraction et utilisé la table des 
   * choses possibles
   ***********************************************************************/

  if ($_GET['installation']=='oui'){
	spip_query("ALTER TABLE `".$table_pref."_groupes_mots` ADD `documents` CHAR( 3 ) NOT NULL DEFAULT 'non';");
	spip_query("ALTER TABLE `".$table_pref."_groupes_mots` ADD `messages` CHAR( 3 ) NOT NULL DEFAULT 'non';");
	spip_query("ALTER TABLE `".$table_pref."_groupes_mots` ADD `auteurs` CHAR( 3 ) NOT NULL DEFAULT 'non';");
	spip_query("CREATE TABLE `".$table_pref."_mots_documents` (`id_mot` bigint(20) NOT NULL default '0',`id_document` bigint(1) NOT NULL default '0',
  				KEY `id_document` (`id_document`),KEY `id_mot` (`id_mot`)) TYPE=MyISAM;;");
	spip_query("CREATE TABLE `".$table_pref."_mots_messages` (`id_mot` bigint(20) NOT NULL default '0',`id_message` bigint(1) NOT NULL default '0',
  				KEY `id_message` (`id_message`),KEY `id_mot` (`id_mot`)) TYPE=MyISAM;;");
	spip_query("CREATE TABLE `".$table_pref."_mots_auteurs` (`id_mot` bigint(20) NOT NULL default '0',`id_auteur` bigint(1) NOT NULL default '0',
  				KEY `id_auteur` (`id_auteur`),KEY `id_mot` (`id_mot`)) TYPE=MyISAM;;");
  }
  
  /***********************************************************************
   * récuperation de la chose sur laquelle on travaille
   ***********************************************************************/

  $nom_chose = $_POST['nom_chose'];
  if(!isset($choses_possibles[$nom_chose])) {
	list($nom_chose,) = each($choses_possibles);
	reset($choses_possibles);
  }
  $id_chose = $choses_possibles[$nom_chose]['id_chose'];
  $table_principale = $choses_possibles[$nom_chose]['table_principale'];
  $table_auth = $choses_possibles[$nom_chose]['table_auth'];
  $tables_limite = $choses_possibles[$nom_chose]['tables_limite'];

/***********************************************************************
 * action
 ***********************************************************************/
  $mots = $_POST['id_mots'];
  $sans_mots = $_POST['sans_mots'];
  $choses = $_POST['id_choses'];
  $nom_tags = $_POST['nom_tags'];
  $id_groupes = $_POST['id_groupes'];
  $limit =  $_POST['limit'];
  $id_limit =  $_POST['id_limit'];
  //echo "!!!".$nom_chose."!!!";
  //echo "action :".$_POST['switch']."<br>";
  //echo "choses :".serialize($choses)."<br>";
  //echo "mots :".serialize($mots)."<br>";
  //echo "sans_mots :".serialize($sans_mots)."<br>";
  //echo "nom_tags :".serialize($nom_tags)."<br>";
  //echo "id_groupes :".serialize($id_groupes)."<br>";
  //echo "limit :".serialize($limit)."<br>";
  //echo "id_limit :".serialize($id_limit)."<br>";

  if($_POST['switch'] == 'action' && (count($choses) || count($nom_tags))) {
	if(count($mots)) {
	  foreach($mots as $m) {	
		$from = array('spip_mots');
		$select = array('id_groupe');
		$where = array("id_mot = $m");
		$res = spip_abstract_select($select,$from,$where);
		$unseul = false;
		$id_groupe = 0;
		$titre_groupe = '';
		if($row = spip_abstract_fetch($res)) {
		  spip_abstract_free($res);
		  $from = array('spip_groupes_mots');
		  $select = array('unseul','titre');
		  $id_groupe = $row['id_groupe'];
		  $where = array("id_groupe = $id_groupe");
		  $res = spip_abstract_select($select,$from,$where);
		  if($row = spip_abstract_fetch($res)) {
			$unseul = ($row['unseul'] == 'oui');
			$titre_groupe = $row['titre'];
		  }
		}
		spip_abstract_free($res);
		foreach($choses as $d) {
		  if($unseul) {
			$from = array("spip_mots_$nom_chose",'spip_mots');
			$select = array("count('id_mot') as cnt");
			$where = array("id_groupe = $id_groupe","spip_mots_$nom_chose.id_mot = spip_mots.id_mot","$id_chose = $d");
			$group = $id_chose;
			$res = spip_abstract_select($select,$from,$where,$group);
			if($row = spip_abstract_fetch($res)) {	
			  if($row['cnt'] > 0) {
				$warnings[] = array(_T('motspartout:dejamotgroupe',array('groupe' => $titre_groupe, 'chose' => $d)));
				continue; 
			  }
			}
			spip_abstract_free($res);
		  }
		  //		  echo "!!!!!!!action insert:"."$nom_chose(id_mot,$id_chose)($m,$d)"."!!!!!!!!!";
		  
		  spip_abstract_insert("spip_mots_$nom_chose","(id_mot,$id_chose)","($m,$d)");
		}
	  }
	}
	if (count($sans_mots)) {
	  foreach($sans_mots as $m) {
		foreach($choses as $d) {
		  //			echo "!!!!!!!action delete:"."$nom_chose(id_mot,$id_chose)($m,$d)"."!!!!!!!!!";
		  spip_query("DELETE FROM ".$table_pref."_mots_$nom_chose WHERE id_mot=$m AND $id_chose=$d");
		}
	  }
	}
	if (count($nom_tags)){
	  for($iterTag = 0 ; $iterTag<count($nom_tags);$iterTag++) {
		$listetags = $nom_tags[$iterTag];
		$id_groupetags = $id_groupes[$iterTag];
		$tags = new ListeTags($listetags,'',$id_groupetags);
		//		echo "!!!!!!!tag:".serialize($listetags)."!!!!!!!!!";
		foreach($choses as $d) {
		  if($_POST['bouton'] == 'ajouter')
			$tags->ajouter($d,$nom_chose,$id_chose);
		  if($_POST['bouton'] == 'retirer')
			$tags->retirer($d,$nom_chose,$id_chose);
		}
	  }
	}
  }
  /**********************************************************************
* recherche des choses.
***********************************************************************/

  if(count($choses) == 0) {
	$select = array();
	$select[] = "DISTINCT main.$id_chose";
	
	$from = array();
	$where = array();
	$group = '';
	$order = array();
	
	if(isset($limit) && $limit != 'rien') {
	  $table_lim = $tables_limite[$limit]['table'];
	  $nom_id_lim = $tables_limite[$limit]['nom_id'];
	  
	  $from[0] = "$table_lim as main";
	  $where[0] = "main.$nom_id_lim IN ($id_limit)"; 
	  if(count($mots) > 0) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = "table_temp.id_mot IN (".calcul_in($mots).')';
		if($_POST['strict']) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	  if(count($sans_mots) > 0) {
		$from[1] = "spip_mots_$nom_chose as table_temp";
		$where[1] = "table_temp.$id_chose = main.$id_chose";
		$where[] = "table_temp.id_mot not IN (".calcul_in($sans_mots).')';
		if($_POST['strict']) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }	
	} else if((count($mots) > 0)||(count($sans_mots) > 0)){
	  if(count($mots) > 0) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = "main.id_mot IN (".calcul_in($mots).')';
		if($_POST['strict']) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	  if(count($sans_mots) > 0) {
		$from[0] = "spip_mots_$nom_chose as main";
		$where[] = "main.id_mot not IN (".calcul_in($sans_mots).')';
		if($_POST['strict']) {
		  $select[] = 'count(id_mot) as tot';
		  $group = "main.$id_chose";
		  $order = array('tot DESC');
		}
	  }
	} else {
	  $from[] = "$table_principale as main"; 
	}

	//  echo "select :".serialize($select);
	//  echo "from :".serialize($from);
	//  echo "where :".serialize($where);
	//  echo "group :".serialize($group);
	//  echo "order :".serialize($order);

	$res=spip_abstract_select($select,$from,$where,$group,$order);
	
	$choses = array();
	$avec_sans = (count($sans_mots) > 0);
	if($avec_sans) $in_sans = calcul_in($sans_mots);
	while ($row = spip_abstract_fetch($res)) {
	  if(!isset($table_auth) ||
		 (isset($table_auth) &&
		  (verifier_admin() ||
		   verifier_auteur($table_auth,$id_chose,$row[$id_chose])
		   )
		  )
		 ) {
		if($avec_sans) {
		  $test = spip_abstract_select(array($id_chose),array("spip_mots_$nom_chose"),array("id_mot IN ($in_sans)","$id_chose = ".$row[$id_chose]));
		  if(spip_abstract_count($test) > 0) {
			continue;
		  }
		  spip_abstract_free($test);
		}
		if(count($mots) > 0 && $_POST['strict']) {
		  if($row['tot'] >= count($mots)) {
			$choses[] = $row[$id_chose];
		  } else {
			break;
		  }
		} else {
		  $choses[] = $row[$id_chose];
		}
	  }
	}
	spip_abstract_free($res);
  }

  if(count($choses) > 0) {
	$select = array();
	$from = array();
	$where = array();
	$show_mots = array();
	$from[] = "spip_mots_$nom_chose";
	$select[] = "spip_mots_$nom_chose.id_mot";
	$where[] = "spip_mots_$nom_chose.$id_chose IN (".calcul_in($choses).')';
	$res=spip_abstract_select($select,$from,$where);
	while ($row = spip_abstract_fetch($res)) {
	  $show_mots[] = $row['id_mot'];
	}
	spip_abstract_free($res);
  } 

/***********************************************************************
 * affichage
 ***********************************************************************/

  debut_page('&laquo; '._T('motspartout:titre_page').' &raquo;', 'documents', 'mots');
  ?>
	<link rel="stylesheet" type="text/css" href="./ajaxTagMachine.css">
	   <script  type='text/javascript' src="./ajaxTagMachine.js">
	   </script>
<?php

	   echo '<br><br><center>';
  gros_titre(_T('motspartout:titre_page'));
  echo '</center>';

  //Colonne de gauche
  debut_gauche();

  echo '<form method="post" action="mots_partout.php">';


  // choix de la chose sur laquelle on veut ajouter des mots
  debut_cadre_enfonce('',false,'',_T('motspartout:choses'));
  //echo  '<form action="mots_partout.php">';
  echo '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr class=\'tr_liste\'>
<td colspan=2><select name="nom_chose">';
  foreach($choses_possibles as $cho => $m) {
	echo "<option value=\"$cho\"".(($cho == $nom_chose)?'selected':'').'>'._T($m['titre_chose']).'</option>';
  }
  echo '</select></td></tr>
<tr class=\'tr_liste\'><td colspan=2>'.
	_T('motspartout:limite').
	':</td></tr>';
  echo '<tr class=\'tr_liste\'><td><select name="limit">
<option value="rien" selected="true">'.
	_T('motspartout:aucune').
	'</option>';

  foreach($tables_limite as $t => $m) {
	echo "<option value=\"$t\"".(($t == $limit)?'selected':'').">$t</option>";
  }

  echo '</select></td>';
  echo "<td><input type='text' size='3' name='id_limit' value='$id_limit'></td></tr>";
  echo '<tr class=\'tr_liste\'>';
  ?>
	<td colspan=3><button type='submit' name='switch' value='chose'>
	   <?php echo _T('motspartout:voir'); ?>
	   </button>
		   </td>
		   </table></div>
		   <?php
		   fin_cadre_enfonce();
  echo "</form><form method='post' action='mots_partout.php'>
<input type='hidden' name='limit' value='$limit'>
<input type='hidden' name='id_limit' value='$id_limit'>
";

  if(count($choses)) {
	debut_cadre_enfonce('',false,'',_T('motspartout:voir'));
	?>
	  <div class='liste'>
		 <table border=0 cellspacing=0 cellpadding=3 width="100%">
		 <tr class='tr_liste'>
		 <td colspan=2>
		 <!-- TODO traduire -->
		 Voir les mots ou les photos selectionnés.
		 </td>
		 </tr>
		 <tr class='tr_liste'>
		 <td><button type='submit' name='switch' value='voir'>
		 <?php echo _T('motspartout:voir'); ?>
		 </button>
			 </td>
			 <td colspan=2>
			 <input type='checkbox' id='strict' name='strict'/><label for='strict'>
			 <?php echo _T('motspartout:stricte'); ?>
			 </label></td>
				 </tr>
				 </table></div>
				 <?php
				 fin_cadre_enfonce();


	// echo '</form>';

	// 	echo '<a name="action"></a><form action="mots_partout.php#voir">';

	echo '<input type="hidden" name="nom_chose" value="'.$_POST['nom_chose'].'">';  
	//  echo "<input type='hidden' name='id_limit' value='$id_limit'>";
	//  echo "<input type='hidden' name='limit' value='$limit'>";
	//  for($i=0; $i < count($choses); $i++) {
	//	echo "<input type=\"hidden\" name=\"id_choses[]\" value=\"".$choses[$i].'">';
	//  }
	
	// les actions et limitations possibles.
	debut_cadre_enfonce('',false,'',_T('motspartout:action'));
	
	
	echo '<div class=\'liste\'>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">';
	//ca ne sert à rien, on n'utilise jamais ces choix, on utilise directement avec/sans mais ça pourrait être utile pour gérer tag-machine 
	/* echo '<tr class=\'tr_liste\'>
<td><input type=\'radio\' value=\'ajouter\' name="bouton" id=\'ajouter\'><br><label for=\'ajouter\'>'.
	_T('motspartout:ajouter').
	'</label></td>
<td ><input type=\'radio\' value=\'enlever\' name="bouton" id=\'enlever\'><br><label for=\'enlever\'>'.
	_T('motspartout:enlever').
	'</label></td>
</tr>';*/
  ?>
	   <tr class='tr_liste'>
	   <td colspan=2>
<!--TODO traduire-->
		 Ajouter les mots cochés <em>avec</em> et retirer les mots cochés <em>sans</em>
</td>
		   </tr>
	   <tr class='tr_liste'>
	   <td colspan=2><button type='submit' name='switch' value='action'>
		   <?php  echo _T('bouton_valider'); ?>
	   </button></td>
		   </tr>
		   </table>
		   </div>
		   
<?php
		   
  fin_cadre_enfonce();
 creer_colonne_droite();
// affichage de mots clefs.
$select = array('*');
$from = array('spip_groupes_mots');
$order = array('titre');
$m_result_groupes = spip_abstract_select($select,$from,'','',$order);

while ($row_groupes = spip_abstract_fetch($m_result_groupes)) {
  $id_groupe = $row_groupes['id_groupe'];
  $titre_groupe = typo($row_groupes['titre']);
  $unseul = $row_groupes['unseul'];
  $acces_admin =  $row_groupes['minirezo'];
  $acces_redacteur = $row_groupes['comite'];

  if($row_groupes[$nom_chose] == 'oui' && (($GLOBALS['connect_statut'] == '1comite' AND $acces_redacteur == 'oui') OR ($GLOBALS['connect_statut'] == '0minirezo' AND $acces_admin == 'oui'))) {
	// Afficher le titre du groupe
	debut_cadre_enfonce("groupe-mot-24.gif", false, '', $titre_groupe);
	
	//
	// Afficher les mots-cles du groupe
	//
	$result = spip_abstract_select(array('*'),
								  array('spip_mots'),
								  array("id_groupe = '$id_groupe'"),
								  '', array('titre'));
	$table = '';
	
	if (spip_abstract_count($result) > 0) {
	  echo "<div class='liste'>";
	  echo "<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
	  	   <tr class='tr_liste'>
											<td><label for=\"nom_tags_$id_groupe\">liste des mots à ajouter</label></td>
 	   <td colspan=2><div style=\"position:relative;overflow:visible; width: 10em;\">
        <input autocomplete=\"off\" type=\"texte\" id=\"nom_tags_$id_groupe\" name=\"nom_tags[]\" class='forml' cols='40' style=\"width: 100%;\">
        <div id=\"suggest_$id_groupe\" class=\"suggest_list\" style=\"width: 100%;\"></div>
      </div>".http_img_pack("searching.gif", "*", "style='float:left; padding-right: 2em;display:none;' id = 'wait_$id_groupe'")."</td>
		   </tr>
<input type='hidden' name='id_groupes[]' value='$id_groupe'>
  <script  type='text/javascript'> <!--
groupe_$id_groupe = new AjaxSuggestMenu('ajax_mortimer.php', 'titre', 'nom_tags_$id_groupe','suggest_$id_groupe');
groupe_$id_groupe.addVar('id_groupe','$id_groupe');
groupe_$id_groupe.setWaiting('wait_$id_groupe');
		--></script>";
	  $i =0;
	  $table[] = array(
					   ' ' => 0,
					   _T('motspartout:avec') => 0,
					   _T('motspartout:sans') => 0
					   );
	  while ($row = spip_abstract_fetch($result)) {
		$i++;
		$vals = '';
		
		$id_mot = $row['id_mot'];
		$titre_mot = $row['titre'];
		
		$s = typo($titre_mot);
		
		$vals["<label for='id_mot$i'>$s</label>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		
		$vals["<input type='checkbox' name='id_mots[]' id='id_mot$i' value='$id_mot'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		
		$vals["<input type='checkbox' name='sans_mots[]' id='sans_mot$i' value='$id_mot'>"] = calcul_numeros($show_mots,$id_mot,count($choses));
		$table[] = $vals;
	  }
	  
  }
	$largeurs = array(40, 10, 10);
	$styles = array(
					array('arial11',
						  'diff-deplace',
						  'diff-ajoute'),
					array('arial1',
						  'diff-para-deplace',
						  'diff-para-ajoute'),
					array('arial1',
						  'diff-para-deplace',
						  'diff-para-ajoute')
					);
	md_afficher_liste($largeurs, $table, $styles);
	
	echo "</table>";
	echo "</div>";
	spip_abstract_free($result);
	
	fin_cadre_enfonce();
  }
}
spip_abstract_free($m_result_groupes);



}
//Milieu

debut_droite();

if(count($warnings) > 0) {
  debut_cadre_relief('',false,'',_T('motspartout:ATTENTION'));
  echo '<div class="liste"><table border=0 cellspacing=0 cellpadding=3 width=\"100%\">';
  $largeurs = array('100%');
  $styles = array( 'arial11');
  afficher_liste($largeurs, $warnings, $styles);
  echo '</table>';
  echo '</div>';
  fin_cadre_relief();
}

// Affichage de toutes les choses (on pourrait imaginer faire une pagination là)
debut_cadre_relief('',false,'document', _T('portfolio'));
if(count($choses) > 0) {
  $function = "afficher_liste_$nom_chose";
  if(function_exists($function)) 
	$function($choses);
  else
	afficher_liste_defaut($choses);
?>
<!--
<input type="radio" name="selectall" id="all" onclick="selectAll(this.form, 'id_choses[]', 0);"><label for="all">Select All</label>
<input type="radio" name="selectall" id="inverse"  onclick="selectAll(this.form, 'id_choses[]', 1);"><label for="inverse">Inverse All</label>
-->
<?php
} else {
  echo _T('motspartout:pas_de_documents').'.';
}


fin_cadre_relief();
echo '</form>';
?>


<script>
function selectAll(formObj, isInverse) 
{
   for (var i=0;i < formObj.length;i++) 
   {
      fldObj = formObj.elements[i];
      if (fldObj.type == 'checkbox')
      { 
         if(isInverse)
            fldObj.checked = (fldObj.checked) ? false : true;
         else fldObj.checked = true; 
       }
   }
}
</script>

<?php

	fin_page();
}
?>

