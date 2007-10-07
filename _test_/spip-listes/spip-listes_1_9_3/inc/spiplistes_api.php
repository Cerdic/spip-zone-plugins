<?php
/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip ("inc/utils");
include_spip ("inc/filtres");    /* email_valide() */
include_spip ("inc/acces");      /* creer_uniqid() */

function spiplistes_log($texte, $level = LOG_WARNING) {
	if(__server_in_private_ip_adresses()
		&& __plugin_lire_s_meta('opt_console_syslog', _SPIPLISTES_META_PREFERENCES)
	) {
		__syslog_trace($texte, $level);
	}
	else if($level < LOG_DEBUG) {
		// Taille du log SPIP trop courte
		// Ne pas envoyer si DEBUG sinon tronque sans cesse
		spip_log($texte, 'spiplistes');
	}
}

/* function privee
 * multi_queries mysql n'est pas en mesure de le faire en natif :-(
 * A tranformer le jour où mysql gerera correctement le multi_query
 * Et a transformer en transanction quand spip utilisera innodb ou autre table transactionnelle
 * @param $queries : requetes separees par des ';'
 */
function __exec_multi_queries($queries) {
	$queries = trim($queries);
	if (substr($queries, -1, 1) == ';') {
		$queries = substr($queries, 0, strlen($queries)-1);
	}
	$_queries = split(';', $queries);
	while( list(,$val) = each($_queries)) {
		$res = spip_query($val);
	}
	return $res;
}

/*
 * Ajouter les abonnes d'une liste a un envoi
 * @param : $id_courrier : reference d'un envoi
 * @param $id_liste : refernce d'une liste
 */
function spiplistes_remplir_liste_envois($id_courrier,$id_liste){
	if($id_liste==0)
		$result_m = spip_query("SELECT id_auteur FROM spip_auteurs ORDER BY id_auteur ASC");
	else
		$result_m = spip_query("SELECT id_auteur FROM spip_auteurs_listes WHERE id_liste=".
							   _q($id_liste));
	
	while($row_ = spip_fetch_array($result_m)) {
		$id_abo = $row_['id_auteur'];
		spip_query("INSERT INTO spip_auteurs_courriers (id_auteur,id_courrier,statut,maj) VALUES (".
				   _q($id_abo).","._q($id_courrier).",'a_envoyer', NOW()) ");
	}
	$res = spip_query("SELECT COUNT(id_auteur) AS n FROM spip_auteurs_courriers WHERE id_courrier=".
					  _q($id_courrier)." AND statut='a_envoyer'");
	if ($row = spip_fetch_array($res))
		spip_query("UPDATE spip_courriers SET total_abonnes=".
				   _q($row['n'])." WHERE id_courrier="._q($id_courrier));
}

/*
	Supprime les abonnes d'une liste à un envoi
*/
function spiplistes_supprime_liste_envois($id_courrier) {
	$result = false;
	$id_courrier = intval($id_courrier);
	if($id_courrier > 0) {
		$result = spip_query("DELETE FROM spip_auteurs_courriers WHERE id_courrier=$id_courrier");
	}
	return($result);
}

// Nombre d'abonnes a une liste, chaine html
function spiplistes_nb_abonnes_liste_str_get ($id_liste, $nb_abos = false) {
	$result = "";
	if(($id_liste > 0) && ($nb_abos == false)) {
		$nb_abos = spiplistes_nb_abonnes_count($id_liste);
	}
	if($nb_abos) {
		//$result = "(" . $nb_abos . spiplistes_singulier_pluriel_str_get($nb_abos, _T('spiplistes:nb_abonnes_sing'), _T('spiplistes:nb_abonnes_plur')) . ")";
		$result = "(" . spiplistes_singulier_pluriel_str_get($nb_abos, _T('spiplistes:nb_abonnes_sing'), _T('spiplistes:nb_abonnes_plur')) . ")";
	}
	return ($result);
}

function spiplistes_singulier_pluriel_str_get ($var, $str_sing, $str_plur, $returnvar = true) {
	$result = "";
	if($var) {
		$result = (($returnvar) ? $var : "") . " " . (($var > 1) ? $str_plur : $str_sing);
	}
	return($result);
}

function spiplistes_nb_courriers_en_cours($id_courrier = 0) {
	if($id_courrier) {
		$n =
			(($row = spip_fetch_array(spip_query(
				"SELECT COUNT(id_auteur) AS n 
					FROM spip_auteurs_courriers 
					WHERE id_courrier=$id_courrier AND etat=''"
					)))
				&& $row['n'])
			? intval($row['n'])
			: 0
			;
	}
	else {
		$n =
			(($row = spip_fetch_array(spip_query(
				"SELECT SUM(total_abonnes) AS n 
					FROM spip_courriers 
					WHERE statut='"._SPIPLISTES_STATUT_ENCOURS."'"
					)))
				&& $row['n'])
			? intval($row['n'])
			: 0
			;
	}
	return($n);
}

//taille d'une chaine sans saut de lignes ni espaces
function spip_listes_strlen($out){
	$out = preg_replace("/(\r\n|\n|\r| )+/", "", $out);
	return $out ;
}


//desabonner des listes publiques
function spiplistes_desabonner($id_auteur){
	$listes = spip_query ("SELECT id_liste FROM spip_listes WHERE statut='"._SPIPLISTES_PUBLIC_LIST."'");
			while($row = spip_fetch_array($listes)) {
				$id_liste = $row['id_liste'] ;
				$result=spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur=".
								   _q($id_auteur)." AND id_liste="._q($id_liste));
			}
	return(spiplistes_format_abo_modifier($id_auteur));
}

// suspend les abonnements d'un compte
function spiplistes_suspendre_abos($id_auteur) {
	return(spiplistes_format_abo_modifier($id_auteur));
}

// modifier le format abonné
// si id_auteur, celui-ci uniquement
// sinon, 'tous' pour modifier globalement
function spiplistes_format_abo_modifier ($id_auteur, $format = 'non') {
	$format = !in_array($format, array('html', 'texte', 'non')) ? 'non' : $format;
	if($id_auteur=='tous') {
		$where = "";
	}
	else if(($id = intval($id_auteur)) > 0) {
		$where = " WHERE id_auteur=$id LIMIT 1";
	}
	else {
		return(false);
	}
	return (spip_query("UPDATE spip_auteurs_elargis SET `spip_listes_format`='".$format."' $where"));
}

function spiplistes_format_abo_demande($id_auteur) {
	$id_auteur = intval($id_auteur);
	$result = false;
	if($id_auteur > 0) {
		$sql_query = "SELECT `spip_listes_format` FROM spip_auteurs_elargis WHERE id_auteur=$id_auteur LIMIT 1";
		if($row = spip_fetch_array(spip_query($sql_query))) {
			$result = $row['spip_listes_format'];
			$result = (in_array($result, array('html', 'texte', 'non'))) ? $result : false;
		}
	}
	return($result);
}

/* desabonnement de certaines listes uniquement */
function spiplistes_desabonner_des_listes($id_auteur, $ids_liste) {
	if(!is_array($ids_liste)) {
		$ids_liste = array($ids_liste);
	}
	$query = "";
	while ( list(,$val) = each($ids_liste) ) {
		$query .= "DELETE FROM spip_autheur_listes WHERE id_auteur=".
			_q($id_auteur)." AND id_liste="._q($id_liste).";";
	}
	__exec_multi_queries($query);
}

//function spiplistes_propre($texte)
// passe propre() sur un texte puis nettoye les trucs rajoutes par spip sur du html
// ca s'utilise pour afficher un courrier dans l espace prive
// on l'applique au courrier avant de confirmer l'envoi
function spiplistes_propre($texte){
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	if (isset($style_reg[0])) 
		$style_str = $style_reg[0]; 
	else 
		$style_str = "";
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	$texte = propre_bloog($texte); //nettoyer les spip class truc en trop
	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	//les liens avec double début #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = liens_absolus($texte);
	
	return $texte;
}

/* Demande d'abonnement à une liste d'un visiteur
 * NE VALIDE PAS L'ABONNEMENT : il faut confirmer
 * Retourne une structure array('inser't ou 'update', id_auteur, cookie_oubli)
 * @param $email : clé unique
 * @param $nom : facultatif
 * @param $prenom : facultatif concaténé avant le nom
 * @param $ids_liste : entier ou array d'entier (id d'une spip_liste)
 * @param $statut_abonnement : 'html' (defaut) ou 'texte'
 */
function spiplistes_ajout_inscription($email, $nom, $prenom, $ids_liste, $statut_abonnement) {
	$email = email_valide($email);
	$id_auteur = spiplistes_idauteur_depuis_email($email);
	$cookie = creer_uniqid();
	if ($id_auteur == 0) {
		$nom = trim($nom);
		$prenom = trim($prenom);
		if (!$nom) {
			$nom = $email;
		} else if ($prenom) {
			$nom = $prenom.' '.$nom;
		}
		$query = "INSERT INTO spip_auteurs (nom, email, statut, cookie_oubli)".
			"VALUES ("._q($nom).", "._q($email).", '6forum', "._q($cookie).");";
		spip_query($query);
		$id_auteur = spiplistes_idauteur_depuis_email($email);
		$ret = array('insert', $id_auteur, $cookie);
		if ( ! $id_auteur ) return False; // erreur
	} else if ($id_auteur) {
		$query = "UPDATE spip_auteurs SET cookie_oubli="._q($cookie).
		  " WHERE id_auteur = ".$id_auteur;
		spip_query($query);
		$ret = array('update', $id_auteur, $cookie);
	} else { return False; } // erreur

	if ($format_abonnement != 'html' 
		or $format_abonnement != 'texte' 
		or $format_abonnement != 'non') {
		$format_abonnement = 'html';
	}
	spiplistes_MaJ_auteur_elargi($id_auteur, $format_abonnement);

	if( ! is_array($ids_liste)) {
		$ids_liste = array ($ids_liste);
	}
	while( list(,$val) = each($ids_liste) ){
		$query = "DELETE FROM spip_auteurs_listes WHERE id_auteur=".
			$id_auteur." AND id_liste="._q($val).";\n";
		$query .= "INSERT INTO spip_auteurs_listes ".
			"(id_auteur, id_liste, date_inscription, statut)".
		"VALUES (".$id_auteur.", "._q($val).", now(), 'a_valider');\n";
	}
	//echo '<!-- '.$query.' -->'."\n";
	__exec_multi_queries($query);
	return $ret;
}

/* Savoir si l'auteur elargi a ete cree */
function spiplistes_auteur_elargi_existe($id_auteur) {
	$query = "SELECT 1 FROM spip_auteurs_elargis WHERE id_auteur="._q($id_auteur);
	$result = spip_query($query);
	if (spip_num_rows($result) == 0) { return False; }
	else { return True; }
}

/*
 * Mise a jour de l'auteur elargi (table spip-liste)
 * @param $id_auteur : clé table auteur
 * @param $format : non | texte | html
 */
function spiplistes_MaJ_auteur_elargi($id_auteur, $format) {
	if (!$format) { $format='non'; }
	if (spiplistes_auteur_elargi_existe($id_auteur)) {
		$query = "INSERT INTO spip_auteurs_elargis (id_auteur, spip_listes_format)".
			" VALUES ("._q($id_auteur)." , "._q($format)." ) ";
	} else {
		$query = "UPDATE spip_auteurs_elargis SET spip_listes_format=".q($format).
			" WHERE id_auteur="._q($id_auteur);
	}
	// echo'<!-- '.$query.' -->'."\n";
	spip_query($query);
}

/* retourne l'id auteur depuis l'email */
function spiplistes_idauteur_depuis_email($email) {
	$email = email_valide($email);
	$query = "SELECT id_auteur FROM spip_auteurs WHERE email="._q($email).
		" AND statut<>'5poubelle'";	
	$result = spip_query($query);
	if (spip_num_rows($result) == 0) { return 0; }
	else if (spip_num_rows($result) == 1) {
		$row = spip_fetch_array($result);
		return $row['id_auteur'];
	} else { echo "Erreur get id_auteur from email"; exit(); } //erreur
}

/* inscription directe - import 
 * @param $type_abo = 'html' ou 'texte' ou 'non-change'
 * @param $statut = 'valide' ou 'a_valider'
 */
function spiplistes_ajout_auteur_aux_listes($id_auteur, $ids_liste, $type_abo, $statut) {
  if (!$type_abo) $type_abo = 'html';
  else if ($type_abo == 'txt') $type_abo='texte';
  if ($statut != 'valide')
	$statut = 'a_valider';

  if ($type_abo != 'non-change')
	  spiplistes_MaJ_auteur_elargi($id_auteur, $type_abo);
  
  if(!is_array($ids_liste)){
	$ids_liste = array($ids_liste);
  }
  $query = '';
  while( list(,$val) = each($ids_liste) ) {
	$query .= "DELETE FROM spip_auteurs_listes WHERE id_auteur=".
	  _q($id_auteur)." AND id_liste="._q($val).";\n";
	$query .= "INSERT INTO spip_auteurs_listes ".
	  "(id_auteur,id_liste,format,date_inscription,statut)".
	  " VALUES ("._q($id_auteur).","._q($val).", ".
	  _q($type_abo).", now(), "._q($statut).");\n";
  }
  __exec_multi_queries($query);
  return True;
}

/*
 * validation de l'inscription d'un id_auteur
 * Il faut deja etre inscrit !
 */
function spiplistes_valide_listes($id_auteur, $ids_liste) {
	$query = '';
	if(!is_array($ids_liste)) {
		$ids_liste = array($ids_liste);
	}
	while( list(,$val) = each($ids_liste) ) {
		$query .= "UPDATE spip_auteurs_listes SET ".
			"statut='valide'".
			"WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($val);
	}
	__exec_multi_queries($query);
}


/* validation des inscriptions depuis cookie_oubli */
function spiplistes_valide_listes_depuis_cookie($cookie) {
  // better query, but works only whith mysql>=4 :( 
  $query_mysql4 = "UPDATE spip_auteurs_listes, spip_auteurs".
	" SET spip_auteurs_listes.statut = 'valide',".
	" spip_auteurs.cookie_oubli = ''".
	" WHERE spip_auteurs.cookie_oubli="._q($cookie).
	" AND spip_auteurs_listes.id_auteur = spip_auteurs.id_auteur".
	" AND spip_auteurs.statut<>'5poubelle'";

  // standard queries  (works with mysql3)
  $id_auteur = spiplistes_idauteur_depuis_cookie_oubli($cookie);
  $queries = "UPDATE spip_auteurs_listes SET".
	" statut = 'valide'".
	" WHERE id_auteur = ".$id_auteur.";";
  $queries .= "UPDATE spip_auteurs SET".
	  " cookie_oubli = ''".
	  " WHERE id_auteur = ".$id_auteur.";";

  //echo '<!-- validation = '.$query.' -->'."\n";
  $res = __exec_multi_queries($queries);
  if (! $res) {
	echo "Validation impossible !";
	exit();
  }
}

// termine la page (en affichant message ou retour)
function spiplistes_terminer_page_message ($message) {
	$result = "<p>$message</p>";
	if($return) return($result);
	else echo($result);
}

// termine la page (à employer qd droits insuffisants)
function spiplistes_terminer_page_non_autorisee ($return = true) {
	spiplistes_terminer_page_message (_T('spiplistes:acces_a_la_page'), $return);
}

// termine page si la donnée n'existe pas dans la base
function spiplistes_terminer_page_donnee_manquante ($return = true) {
	spiplistes_terminer_page_message (_T('spiplistes:Pas_de_donnees'), $return);
}

// returne nombre d'abonnes a une liste ou toutes les listes
// ou par id_auteur
function spiplistes_nb_abonnes_count ($id_liste = 'toutes', $id_auteur = 'tous') {
	$id_liste = ($id_liste=='toutes') ? 0 : intval($id_liste);
	$id_auteur = ($id_auteur=='tous') ? 0 : intval($id_auteur);
	
	$where = (($id_liste > 0) ? "" : " id_liste=$id_liste");
	$where .= (($id_auteur > 0) ? "" : (strlen($where) ? " AND " : "")." id_auteur=$id_auteur");
	if(strlen($where)) $where = " WHERE $where";
	
	$result = spip_fetch_array(spip_query("SELECT COUNT(id_auteur) AS n FROM spip_auteurs_listes $where"));
	$result = ($result && $result['n']) ? $result['n'] : 0;
	return ($result);
}

// retourne la puce qui va bien 
function spiplistes_bullet_titre_liste ($titre, $statut, $return=false, $id=false) {
	$result = $img = "";
	$img = spiplistes_items_get_item('puce', $statut);
	$alt = spiplistes_items_get_item('alt', $statut);
	if($img) {
		$result = "<img src='$img' alt='$alt' ".(!empty($id) ? "id='$id'" : "")." border='0' />\n";
	}
	if($return) return($result);
	else echo($result);
}

// renvoie un élément de définition courriers/listes (icone, puce, alternate text, etc.)
// voir spsiplites_mes_options, tableau $spiplistes_items
function spiplistes_items_get_item($item, $statut) {
	global $spiplistes_items;

	if(isset($spiplistes_items[$statut]) 
		&& isset($spiplistes_items[$statut][$item])
	) {
		return ($spiplistes_items[$statut][$item]);
	}
	else {
		return($spiplistes_items['default'][$item]);
	}
}

// renvoie ID du moderateur de la liste
function spiplistes_mod_listes_get_id_auteur($id_liste) {
	$result = false;
	$id_liste = intval($id_liste);
	if($id_liste>0) {
		$result = spip_query("SELECT id_auteur FROM spip_auteurs_mod_listes WHERE id_liste=$id_liste LIMIT 1");
		$result = (($row = spip_fetch_array($result)) && ($row['id_auteur'] > 0)) ? $row['id_auteur'] : false;
	}
	return($result);
}

// boite information avec juste titre et id
// A placer dans cadre gauche (ex.: exec/spiplistes_listes)
// si $id_objet (par exemple: 'id_auteur') va chercher le logo de l'objet
function spiplistes_boite_info_id ($titre, $id, $return = true, $id_objet = false) {
	global $spip_display;
	$result = "";
	if($id) {
		$logo = "";
		if($id_objet && ($spip_display != 4)) {
			include_spip("inc/iconifier");
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id, $id_objet, 'on')) {
				list($img, $clic) = decrire_logo($id_objet,'on',$id, 170, 170, $logo, $texteon, $script);
				$logo = "<div style='text-align: center;margin:1em 0;'>$img</div>";
			}
		}
		$result = 
			debut_boite_info(true)
			. "\n<div style='font-weight: bold; text-align: center; text-transform: uppercase;' class='verdana1 spip_xx-small'>"
			.  $titre
			. "<br /><span class='spip_xx-large'>"
			. $id
			. "</span></div>"
			. $logo
			. fin_boite_info(true)
			. "<br />"
		;
	}
	if($return) return($result);
	else echo($result);
}

// renvoie liste des patrons en excluant les sous-versions (texte, lang)
function spiplistes_liste_des_patrons ($chemin) {
	$liste_patrons = find_all_in_path($chemin, "[.]html$");
	$result = array();
	foreach($liste_patrons as $key => $value) {
		if (
			!ereg("_[a-z][a-z].html$", $value)
			&& !ereg("_texte.html$", $value)
			&& !ereg("_[a-z][a-z]_texte.html$", $value)
			) {
			$result[] = basename($value, ".html");
		}
	}
	return($result);
}

// construit la boite de selection patrons
function spiplistes_boite_selection_patrons ($current_titre="", $return=false, $select_nom="patron", $size_select=10) {
	$result = "";
	// va chercher la liste des patrons
	$liste_patrons = spiplistes_liste_des_patrons ("patrons/");
	// boite de sélection du patron
	$result  .= "<select style='width:34ex;' name='". $select_nom . "' class='verdana1' size='" . $size_select . "'>";
	// par defaut, selectionne le premier
	$selected = (empty($title_selected) ? "selected='selected'" : ""); 
	foreach($liste_patrons as $titre_option) {
		//$titre_option = basename($titre_option, ".html");
		if($titre_option == $current_titre) {
			$selected = "selected='selected'";
		}	
		$result .= "<option $selected value='" . $titre_option . "'>" . $titre_option . "</option>\n";
		if (!empty($selected)) {
			$selected = "";
		}
	}
	$result  .= "</select>\n";

	if($return) return($result);
	else echo($result);
}

//function spiplistes_texte_propre($texte)
// passe propre() sur un texte puis nettoie les trucs rajoutes par spip sur du html
// 	Remplace spiplistes_propre() qui est à supprimer après vérif.
function spiplistes_texte_propre($texte){
	$temp_style = ereg("<style[^>]*>[^<]*</style>", $texte, $style_reg);
	if (isset($style_reg[0])) 
		$style_str = $style_reg[0]; 
	else 
		$style_str = "";
	$texte = ereg_replace("<style[^>]*>[^<]*</style>", "__STYLE__", $texte);
	//passer propre si y'a pas de html (balises fermantes)
	if( !preg_match(',</?('._BALISES_BLOCS.')[>[:space:]],iS', $texte) ) 
	$texte = propre($texte); // pb: enleve aussi <style>...  
	
	// Corrections complémentaires
	$patterns = array();
	$replacements = array();
	// html
	$patterns[] = "#<br>#i";
	$replacements[] = "<br />";
	$patterns[] = "#<b>([^<]*)</b>#i";
	$replacements[] = '<strong>\\1</strong>';
	$patterns[] = "#<i>([^<]*)</i>#i";
	$replacements[] = '<em>\\1</em>';
	// spip class
	$patterns[] = "# class=\"spip\"#";
	$replacements[] = "";	
	
	$texte = preg_replace($patterns, $replacements, $texte);

	$texte = ereg_replace("__STYLE__", $style_str, $texte);
	
	//les liens avec double début #URL_SITE_SPIP/#URL_ARTICLE
	$texte = ereg_replace($GLOBALS['meta']['adresse_site']."/".$GLOBALS['meta']['adresse_site'], $GLOBALS['meta']['adresse_site'], $texte);
	$texte = liens_absolus($texte);
	
	return ($texte);
}

function spiplistes_titre_propre($titre){
	$titre = spiplistes_texte_propre($titre);
	$titre = substr($titre, 0, 128); // Au cas où copié/collé
	return($titre);
}

// complète les dates chiffres (jour, heure, etc.)
// de retour du formulaire pour les dates 
// et renvoie une date formattée correcte
function spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute) {
	foreach(array('mois', 'jour', 'heure', 'minute') as $k) {
		if($$k < 10) {
			$$k = str_pad($$k, 2, "0", STR_PAD_LEFT);
		}
	}
	return($annee."-".$mois."-".$jour." ".$heure.":".$minute.":00");
}



/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>