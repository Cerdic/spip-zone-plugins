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
include_spip('inc/charsets');


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
	$result =
		($nb_abos)
		? "(" . spiplistes_singulier_pluriel_str_get($nb_abos, _T('spiplistes:nb_abonnes_sing'), _T('spiplistes:nb_abonnes_plur')) . ")"
		: _T('spiplistes:sans_abonne')
		;
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

// CP-20071009
function spiplistes_courriers_casier_count ($statut='tous') {
	$where = ($statut!='tous') ? " WHERE statut='$statut'" : "";
	return(__table_items_count('spip_courriers', 'id_courrier', $where));
}

// CP-20071009
function spiplistes_listes_count ($statut='toutes') {
	$where = ($statut!='toutes') ? " WHERE statut='$statut'" : "";
	return(__table_items_count('spip_listes', 'id_liste', $where));
}

// CP-20071009
function spiplistes_listes_items_get ($keys, $where=false, $limit=false) {
	$where = $where ? " WHERE $where" : "";
	$limit = $limit ? " LIMIT $limit" : "";
	return(__table_items_get('spip_listes', $keys, $where, $limit));
}

//taille d'une chaine sans saut de lignes ni espaces
function spip_listes_strlen($out){
	$out = preg_replace("/(\r\n|\n|\r| )+/", "", $out);
	return $out ;
}

// desabonner des listes (CP-20071016)
// $listes_statuts : array (statuts des listes,..)
function spiplistes_desabonner_listes_statut ($id_auteur, $listes_statuts) {
	if(($id_auteur = intval($id_auteur)) && count($listes_statuts)) {
		$sql_where = " statut='" . implode("' OR statut='", $listes_statuts) . "'";
		$sql_query = "SELECT id_liste FROM spip_listes WHERE $sql_where";
		$sql_result = spip_query ($sql_query);
		$listes = array();
		while($row = spip_fetch_array($sql_result)) {
			$listes[] = intval($row['id_liste']);
		}
		if(count($listes)) {
			$sql_where = " id_auteur=$id_auteur AND (id_liste=" . implode(" OR id_liste=", $listes) . ")";
			$sql_query = "DELETE FROM spip_auteurs_listes WHERE $sql_where";
			$result=spip_query($sql_query);
		}
		return(spiplistes_format_abo_modifier($id_auteur));
	}
	return(false);
}

// suspend les abonnements d'un compte
function spiplistes_suspendre_abos($id_auteur) {
	return(spiplistes_format_abo_modifier($id_auteur));
}

// modifier le format abonné
// si id_auteur, celui-ci uniquement
// sinon, 'tous' pour modifier globalement
function spiplistes_format_abo_modifier ($id_auteur, $format = 'non') {
	$format = spiplistes_format_est_correct($format) ? $format : false;
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
			$result = spiplistes_format_est_correct($result) ? $result : false;
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

// retourne nombre d'abonnes a une liste ou toutes les listes
// ou par id_auteur
function spiplistes_nb_abonnes_count ($id_liste = 'toutes', $id_auteur = 'tous') {
	$id_liste = ($id_liste=='toutes') ? 0 : intval($id_liste);
	$id_auteur = ($id_auteur=='tous') ? 0 : intval($id_auteur);
	
	$where = (($id_liste == 0) ? "" : " id_liste=$id_liste");
	$where .= (($id_auteur == 0) ? "" : (strlen($where) ? " AND " : "")." id_auteur=$id_auteur");
	if(strlen($where))  {
		$where = " WHERE $where";
	}
	$sql_query = "SELECT COUNT(id_auteur) AS n FROM spip_auteurs_listes $where";
	$result = spip_fetch_array(spip_query($sql_query));
	$result = ($result && ($result['n']>0)) ? $result['n'] : 0;
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
			else {
				$logo = "";
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

// renvoie liste des patrons en excluant les sous-versions (texte, lang) (CP-20071012)
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
	sort($result);
	return($result);
}

// construit la boite de selection patrons (CP-20071012)
function spiplistes_boite_selection_patrons ($patron="", $return=false, $chemin="patrons/", $select_nom="patron", $size_select=10, $width='34ex') {
	global $couleur_claire;
	$result = "";
	// va chercher la liste des patrons
	$liste_patrons = spiplistes_liste_des_patrons ($chemin);
	// boite de sélection du patron
	$result  .= "<select style='width:$width;'  name='". $select_nom . "' name='". $select_nom . "' class='verdana1' size='" . $size_select . "'>\n";
	// par defaut, selectionne le premier
	$selected = (empty($title_selected) ? "selected='selected'" : ""); 
	foreach($liste_patrons as $titre_option) {
		$selected =
			($titre_option == $patron)
			? " selected='selected' style='background:$couleur_claire;' "
			: ""
			;
		$result .= "<option $selected value='" . $titre_option . "'>" . $titre_option . "</option>\n";
		if (!empty($selected)) {
			$selected = "";
		}
	}
	$result  .= "</select>\n";

	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_boite_patron ($id_liste, $exec_retour, $nom_bouton_valider, $chemin_patrons, $titre_boite = ""
	, $msg_patron = false, $patron = "", $return = false) {
	// bloc sélection patron
	$result = ""
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."patron-24.png", true)
		. "<div class='verdana1' style='text-align: center;'>\n"
		;
	$titre_boite = "<strong>$titre_boite</strong>\n";
	// inclusion du script de gestion des layers de SPIP
	if(($patron === true) || (is_string($patron) && empty($patron))) {
		$result  .= ""
			. bouton_block_visible(md5($nom_bouton_valider))
			. $titre_boite
			. debut_block_visible(md5($nom_bouton_valider))
			;
	}
	else {
		$result  .= ""
			. bouton_block_invisible(md5($nom_bouton_valider))
			. $titre_boite
			. debut_block_invisible(md5($nom_bouton_valider))
			;
	}
	$result .= "\n"
		. "<form action='".generer_url_ecrire($exec_retour, "id_liste=$id_liste")."' method='post' style='margin:1ex;'>\n"
		. spiplistes_boite_selection_patrons ($patron, true, $chemin_patrons)
		. "<div style='margin-top:1em;text-align:right;'><input type='submit' name='$nom_bouton_valider' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
		. "</form>\n"
		. fin_block()
		. "<div style='text-align:center'>\n"
		. ($msg_patron ? $msg_patron : "<span style='color:gray;'>&lt;"._T('spiplistes:aucun')."&gt;</span>\n")
		. "</div>\n"
		. "</div>\n"
		. fin_cadre_relief(true);
		;

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
// de retour du formulaire pour les dates et renvoie une date formatée correcte
function spiplistes_formate_date_form($annee, $mois, $jour, $heure, $minute) {
	if(!empty($jour) && !empty($mois) && !empty($annee) && (intval($heure) >= 0) && (intval($minute) >= 0)) {
		foreach(array('mois', 'jour', 'heure', 'minute') as $k) {
			if($$k < 10) {
				$$k = str_pad($$k, 2, "0", STR_PAD_LEFT);
			}
		}
		return($annee."-".$mois."-".$jour." ".$heure.":".$minute.":00");
	}
	return(false);
}

// traduit charset
// complète caracteres manquants dans SPIP
function spiplistes_translate_2_charset ($texte, $charset='AUTO') {
	
	$texte = charset2unicode($texte);
	$texte = unicode2charset($texte, $charset);
	if($charset != "utf-8") {
		$remplacements = array(
			"&#8217;"=>"'"	// quote
			, "&#8220;"=>'"' // guillemets
			, "&#8221;"=>'"' // guillemets
			, "&#255;" => "ÿ" // &yuml
			, "&#159;" => "Ÿ" // &Yuml
			, "&#339;" => "œ"	// e dans o
			)
			;
		$texte = strtr($texte, $remplacements);
	}
	return($texte);
}

// donne contenu tampon au format html (CP-20071013)
// tampon_patron: nom du tampon (fichier, sans extension)
function spiplistes_tampon_html_get ($tampon_patron) {
	$contexte_patron = array();
	foreach(explode(",", _SPIPLISTES_TAMPON_CLES) as $key) {
		$contexte_patron[$key] = __plugin_lire_s_meta($key, 'spiplistes_preferences');
	}
	include_spip('public/assembler');
	return(recuperer_fond(_SPIPLISTES_PATRONS_TAMPON_DIR.$tampon_patron, $contexte_patron));
}

// donne contenu tampon au format texte (CP-20071013)
// tampon_patron: nom du tampon (fichier, sans extension)
// tampon_html: contenu html converti en texte si pas de contenu
function spiplistes_tampon_texte_get ($tampon_patron, $tampon_html) {
	$contexte_patron = array();
	$result = false;
	foreach(explode(",", _SPIPLISTES_TAMPON_CLES) as $key) {
		$contexte_patron[$key] = __plugin_lire_s_meta($key, 'spiplistes_preferences');
	}
	$f = _SPIPLISTES_PATRONS_TAMPON_DIR.$tampon_patron;
	if (find_in_path($f."_texte.html")){
		$result = recuperer_fond($f, $contexte_patron);
	}
	if(!$result) {
		$result = version_texte($tampon_html);
	}
	return($result);
}

// donne contenu lien_courrier au format html (CP-20071014)
// lien_patron: nom du tampon (fichier, sans extension)
function spiplistes_lien_courrier_html_get ($lien_patron, $url_courrier) {
	$contexte_patron = array('url_courrier'=>$url_courrier);
	include_spip('public/assembler');
	return(recuperer_fond(_SPIPLISTES_PATRONS_TETE_DIR.$lien_patron, $contexte_patron));
}

// donne contenu lien_courrier au format texte (CP-20071014)
// lien_patron: nom du lien_courrier (fichier, sans extension)
// lien_html: contenu html converti en texte si pas de contenu
function spiplistes_lien_courrier_texte_get ($lien_patron, $lien_html, $url_courrier) {
	$contexte_patron = array('url_courrier'=>$url_courrier);
	$result = false;
	$f = _SPIPLISTES_PATRONS_TETE_DIR.$tampon_patron;
	if (find_in_path($f."_texte.html")){
		$result = recuperer_fond($f, $contexte_patron);
	}
	if(!$result) {
		$result = version_texte($tampon_html);
	}
	return($result);
}

// donne contenu pied_page au format html (CP-20071014)
// lien_patron: nom du tampon (fichier, sans extension)
function spiplistes_pied_page_html_get ($pied_patron) {
	$contexte_patron = array();
	include_spip('public/assembler');
	return(recuperer_fond(_SPIPLISTES_PATRONS_PIED_DIR.$pied_patron, $contexte_patron));
}

function spiplistes_onglets ($rubrique, $onglet, $return = false) {

	$result = "";
	
	if ($rubrique == _SPIPLISTES_RUBRIQUE){
		$result = ""
			. "<br />"
			. debut_onglet()
			. onglet(_T('spiplistes:Casier_a_courriers'), generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_hyperlink-mail-and-news-24.gif")
			. onglet(_T('spiplistes:Listes_de_diffusion'), generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif")
			. onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."addressbook-24.gif")
			. fin_onglet()
		;
	}

	if($return) return($result);
	else echo($result);
}


function spiplistes_boite_autocron ($return = false) { 
	@define('_SPIP_LISTE_SEND_THREADS',1);
	include_spip('genie/spiplistes_cron');
	if (cron_spiplistes_cron($time)) return; // rien a faire
	
	// initialise les options
	foreach(array('opt_simuler_envoi') as $key) {
		$$key = __plugin_lire_s_meta($key, 'spiplistes_preferences');
	}

/*
	$res = spip_query("SELECT COUNT(a.id_auteur) AS n 
		FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='"._SPIPLISTES_STATUT_ENCOURS."'");
	$n = 0;
*/
	$res = spip_query("SELECT SUM(c.total_abonnes) AS n 
		FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='"._SPIPLISTES_STATUT_ENCOURS."'");
	if ($row = spip_fetch_array($res))
		$n = intval($row['n']);
spiplistes_log("AUTOCRON nb courries prets envoi $n", LOG_DEBUG);

	if($n > 0) {
		$result = ""
			. "<br />"
			. debut_boite_info(true)
			. "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>"
			. "<div style='padding : 10px;text-align:center'><img src='"._DIR_PLUGIN_SPIPLISTES."img_pack/48_import.gif'></div>"
			. "<div id='meleuse'>"
			.	(
					($total = spiplistes_nb_courriers_en_cours())
					?	""
						. "<p align='center' id='envoi_statut'>"._T('spiplistes:envoi_en_cours')." "
						. "<strong id='envois_restants'>$n</strong>/<span id='envois_total'>$total</span> (<span id='envois_restant_pourcent'>"
						. round($n/$total*100)."</span>%)</p>"
					:	""
				)
			// message si simulation d'envoi	
			.	(
					($opt_simuler_envoi == 'oui') 
					? "<div style='color:white;background-color:red;text-align:center;line-height:1.4em;'>"._T('spiplistes:Mode_simulation')."</div>\n" 
				: ""
				)
			;
		
		$href = generer_action_auteur('spiplistes_envoi_lot','envoyer');

		for ($i=0;$i<_SPIP_LISTE_SEND_THREADS;$i++) {
			$result .= "<span id='proc$i' class='processus' name='$href'></span>";
		}
		if (_request('exec')==_SPIPLISTES_EXEC_COURRIERS_LISTE) {
			$result .= "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."' id='redirect_after'></a>";
		}
		$result .= ""
			. "</div>"
			. "<script><!--
		var target = $('#envois_restants');
		var total = $('#envois_total').html();
		var target_pc = $('#envois_restant_pourcent');
		function redirect_fin(){
			redirect = $('#redirect_after');
			if (redirect.length>0){
				href = redirect.attr('href');
				setTimeout('document.location.href = \"'+href+'\"',0);
			}
		}
		jQuery.fn.runProcessus = function(url) {
			var proc=this;
			var href=url;
			$(target).load(url,function(data){
				restant = $(target).html();
				pourcent=Math.round(restant/total*100);
				$(target_pc).html(pourcent);
				if (Math.round(restant)>0)
					$(proc).runProcessus(href);
				else
					redirect_fin();
			});
		}
		$('span.processus').each(function(){
			var href = $(this).attr('name');
			$(this).html(ajax_image_searching).runProcessus(href);
			//run_processus($(this).attr('id'));
		});
		//--></script>"
			. "<p>"._T('spiplistes:texte_boite_en_cours')."</p>" 
			. "<p align='center'><a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,'change_statut=publie&id_courrier='.$id_mess)."'>["._T('annuler')."]</a></p>"
			. fin_boite_info(true)
			;
	}

	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_debut_raccourcis ($titre = "", $raccourcis = true, $return = false) {
  
  $result = ""
		. ($raccourcis ? creer_colonne_droite('', true) : "")
		. debut_cadre_enfonce('', true)
		. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>$titre</span>"
		. "<br />"
		;
	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_fin_raccourcis ($return = false) {
	$result = ""
		. fin_cadre_enfonce(true)
		;
	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_boite_raccourcis ($return = false) {
	global $connect_id_auteur;
	
	$result = ""
		// Les raccourcis
		. spiplistes_debut_raccourcis(_T('titre_cadre_raccourcis'), true)
		. "<ul class='verdana2' style='list-style: none;padding:1ex;margin:0;'>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouveau_courrier')
			, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'new=oui&type=nl')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_brouillon-24.png"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouvelle_liste_de_diffusion')
			, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'new=oui')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:import_export')
			, generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT)
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."listes_inout.png"
			,""
			,false
			)
		. "</li>\n"
		;
	if($connect_id_auteur == 1) {
		$result .= ""
			. "<li>"
			. icone_horizontale(
				_T('titre_admin_tech')
				, generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)
				, "administration-24.gif"
				,""
				,false
				)
			. "</li>\n"
			;
	}
	$result .= ""
		. "</ul>\n"
		. spiplistes_fin_raccourcis(true)
		;
	
	if($return) return($result);
	else echo($result);
}

function spiplistes_boite_info_spiplistes($return=false) {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. debut_boite_info(true)
		. _T('spiplistes:_aide')
		. fin_boite_info(true)
		;
	if($return) return($result);
	else echo($result);
}

// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com
 

// Afficher l'arbo
function  spiplistes_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
	global $ran;
	$ran ++;
	
	$marge="&nbsp;&nbsp;&nbsp;|";
	for ($g=0;$g<$ran;$g++) {
		if (($ran-1)==0) {
			$marge="&bull;";
		}
		else {
			$marge .="-"; 
		}
	}
	$marge .="&nbsp;";

	$rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
	while ($row = spip_fetch_array($rqt_rubriques)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
		$arbo .= spiplistes_arbo_rubriques($id_rubrique,   $rslt_id_parent);
	}
	
	return $arbo;
	
}

function spiplistes_pied_de_page_liste($id_liste = 0, $lang = false) {
	$result = false;
	if(!$lang) {
		$lang = $GLOBALS['spip_lang'];
	}
	if(($id_liste = intval($id_liste)) > 0){
		if($row = spip_fetch_array(spip_query("SELECT pied_page FROM spip_listes WHERE id_liste=$id_liste LIMIT 1"))) {
			$result = $row['pied_page'];
		}
	}
	if(!$result) {
		include_spip('public/assembler');
		$contexte_pied = array('lang'=>$lang);
		$result = recuperer_fond(_SPIPLISTES_PATRONS_PIED_DEFAUT, $contexte_pied);
	}
	return ($result);
}

function spiplistes_format_est_correct ($format) {
	return(in_array($format, array("non", "texte", "html")));
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
