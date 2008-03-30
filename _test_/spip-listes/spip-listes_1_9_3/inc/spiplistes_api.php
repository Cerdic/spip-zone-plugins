<?php

// inc/spiplistes_api.php

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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip ("inc/utils");
include_spip ("inc/filtres");    /* email_valide() */
include_spip ("inc/acces");      /* creer_uniqid() */
include_spip('inc/charsets');

include_spip('base/abstract_sql');

include_spip('inc/spiplistes_api_abstract_sql');
include_spip('inc/plugin_globales_lib');
include_spip('inc/spiplistes_api_globales');

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

// desabonner des listes (CP-20071016)
// $listes_statuts : array (statuts des listes,..)
function spiplistes_listes_desabonner_statut ($id_auteur, $listes_statuts) {
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

// CP-20080324 : abonner un id_auteur à une id_liste
function spiplistes_listes_abonner ($id_auteur, $id_liste) {
	if(
		(($id_auteur = intval($id_auteur)) > 0) 
		&& (($id_liste = intval($id_lste)) > 0)
	) {
		$sql_table = "spip_auteurs_listes";
		$sql_champs = array('id_auteur' => $id_auteur, 'id_liste' => $id_liste);
		return(
			spiplistes_listes_deabonner($id_auteur, $id_liste)
			&& sql_insertq($sql_table, $sql_champs)
		);
	}
	return(false);
}

// CP-20080324 : desabonner un id_auteur d'une id_liste
function spiplistes_listes_desabonner ($id_auteur, $id_liste) {
	if(
		(($id_auteur = intval($id_auteur)) > 0) 
		&& (($id_liste = intval($id_liste)) > 0)
	) {
		$sql_table = "spip_auteurs_listes";
		$sql_where = array('id_auteur' => $id_auteur, 'id_liste' => $id_liste);
		return(
			sql_delete($sql_table, $sql_where)
		);
	}
	return(false);
}

// CP-20080330 : renvoie la liste des abonnements pour id_auteur
function spiplistes_listes_abonnements_auteur ($id_auteur) {
	$result = array();
	$sql_result = sql_select ("id_liste", "spip_auteurs_listes", "id_auteur=".sql_quote($id_auteur));
	while ($row = spip_fetch_array($sql_result)) {
		$result[] = $row['id_liste'];
	}
	return($result);
}

//taille d'une chaine sans saut de lignes ni espaces ni punct
function spiplistes_strlen($out){
	$out = preg_replace("/([[:space:]]|[[:punct:]])+/", "", $out);
	return (strlen($out));
}


// suspend les abonnements d'un compte
function spiplistes_format_abo_suspendre ($id_auteur) {
	return(spiplistes_format_abo_modifier($id_auteur));
}

// modifier le format abonné
// si id_auteur, celui-ci uniquement
// sinon, 'tous' pour modifier globalement (uniquement ceux ayant déjà un format)
function spiplistes_format_abo_modifier ($id_auteur, $format = 'non') {
	if($format = (spiplistes_format_est_correct($format) ? $format : false)) {
		$sql_table = "spip_auteurs_elargis";
		$sql_champs = array('`spip_listes_format`' => $format);
		if($id_auteur=='tous') {
			$sql_where = "";
		}
		else if(($id_auteur = intval($id_auteur)) > 0) {
			if(!spiplistes_format_abo_demande($id_auteur)) {
				$sql_champs['id_auteur'] = $id_auteur;
				return(sql_insertq($sql_table, $sql_champs));
			} else {
				$sql_where = "id_auteur=$id_auteur LIMIT 1"; 
			}
		}
		else {
			return(false);
		}
		return(sql_updateq($sql_table, $sql_champs, $sql_where));
	}
	return(false);
}

// renvoie le format d'abonnement d'un auteur
function spiplistes_format_abo_demande($id_auteur) {
	$id_auteur = intval($id_auteur);
	$result = false;
	if($id_auteur > 0) {
		/**/
		if(spiplistes_spip_est_inferieur_193()) {
			$result = sql_getfetsel("`spip_listes_format`", "spip_auteurs_elargis", "id_auteur=".sql_quote($id_auteur));
		} else {
			$result = sql_fetsel("`spip_listes_format`", "spip_auteurs_elargis", "id_auteur=".sql_quote($id_auteur));
			$result = $result['spip_listes_format'];
		}
		/**/
		/* Code à valider. Si ok, supprimer ci-dessus.
		$GLOBALS['mysql_rappel_nom_base'] = false;
		$result = sql_getfetsel("spip_listes_format", "spip_auteurs_elargis", "id_auteur=".sql_quote($id_auteur));
		$result = spiplistes_format_est_correct($result) ? $result : false;
		/**/
	}
	return($result);
}


/* retourne l'id auteur depuis l'email */
function spiplistes_idauteur_depuis_email ($email) {
	if($email = email_valide($email)) {
		if($result = spiplistes_sql_select_simple ("id_auteur", "spip_auteurs", "email='$email' AND statut<>'5poubelle'")) {
			$row = spip_fetch_array($result);
			return($row['id_auteur']);
		}
	}
	return(false);
}

// CP-20080324 : renvoie le premier élément sql demandé
function spiplistes_sql_select_simple ($sql_select, $sql_table, $sql_where = "", $only_first = true) {
	$only_first = $only_first ? 1 : "";
	return(sql_select($sql_select, $sql_table, $sql_where, "", "", $only_first));
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

// renvoie id_auteur du courier (CP-20071018)
function spiplistes_courrier_id_auteur_get ($id_courrier) {
	if(($id_courrier = intval($id_courrier)) > 0) {
		if($sql_result = spip_query("SELECT id_auteur FROM spip_courriers WHERE id_courrier=$id_courrier LIMIT 1")) {
			if($row = spip_fetch_array($sql_result)) {
				return($row['id_auteur']);
			}
		}
	}
	return(false);
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
	$result  .= "<select style='width:$width;'  name='". $select_nom . "' class='verdana1' size='" . $size_select . "'>\n";
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
function spiplistes_boite_patron ($flag_editable, $id_liste, $exec_retour, $nom_bouton_valider, $chemin_patrons, $titre_boite = ""
	, $msg_patron = false, $patron = "", $return = false) {
	// bloc sélection patron
	$result = ""
		. debut_cadre_relief(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."patron-24.png", true)
		. "<div class='verdana1' style='text-align: center;'>\n"
		;
	$titre_boite = "<strong>$titre_boite</strong>\n";
	
	if($flag_editable) {
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
	}
	else {
		$result  .= $titre_boite;
	}
	if($flag_editable) {
		$result .= "\n"
			. "<form action='".generer_url_ecrire($exec_retour, "id_liste=$id_liste")."' method='post' style='margin:1ex;'>\n"
			. spiplistes_boite_selection_patrons ($patron, true, $chemin_patrons)
			. "<div style='margin-top:1em;text-align:right;'><input type='submit' name='$nom_bouton_valider' value='"._T('bouton_valider')."' class='fondo' /></div>\n"
			. "</form>\n"
			. fin_block()
			;
	}
	else {
	}
	$result .= "\n"
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
		$contexte_patron[$key] = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}
	include_spip('public/assembler');
	return(recuperer_fond(_SPIPLISTES_PATRONS_TAMPON_DIR.$tampon_patron, $contexte_patron));
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
		$result = spiplistes_version_texte($lien_html);
	}
	return($result);
}


// Petit formulaire dans la boite autocron (CP-20071018)
function spiplistes_boite_autocron_form($titre, $option, $value) {
	global $connect_id_auteur;
	$result = "";
	// n'apparaît que si super_admin et pas sur la page de config (doublon de form)
	if($connect_id_auteur == 1) {
		if(_request('exec')!=_SPIPLISTES_EXEC_CONFIGURE) {
			$result = ""
				. "<!-- bouton annulation option -->\n"
				. "<form name='form_$option' id='id_form_$option' method='post' action='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."'"
					. " style='margin:0.5em 0;text-align:center;'>\n"
				. "<input type='hidden' name='$option' id='id_$option' value='$value' />\n"
				. "<label for='id_$option' style='display:none;'>$titre option</label>\n"
				. "<input type='submit' name='Submit' value='$titre' id='Submit' class='fondo' />\n"
				. "</form>\n"
				;
		}
		else {
			$result = ""
				. "<p class='verdana2'>"._T('spiplistes:Utilisez_formulaire')."</p>\n"
				;
		}
	}
	return($result);
}

// Petite boite info pour l'autocron (CP-20071018)
function spiplistes_boite_autocron_info ($icone = "", $return = false, $titre_boite = '', $bouton = "", $texte = "", $nom_option = "", $icone_alerte = false) {
	$result = ""
		. debut_cadre_couleur($icone, $return, $fonction, $titre_boite)
		. ($icone_alerte ? "<div style='text-align:center;'><img alt='' src='$icone_alerte' border='0' /></div>" : "")
		. ($texte ? "<p class='verdana2' style='margin:0;'>$texte</p>\n" : "")
		. ($bouton ? spiplistes_boite_autocron_form($bouton, $nom_option, 'non') : "")
		. fin_cadre_couleur($return)
		;
	if($return) return($result);
	else echo($result);
}

// Renvoie le nombre total de courriers en attente (CP-20071018)
// Cumul des total_abonnes
function spiplistes_nb_grand_total_courriers () {
	$sql_query = "SELECT SUM(total_abonnes) AS n FROM spip_courriers WHERE statut='"._SPIPLISTES_STATUT_ENCOURS."'";
	$sql_result = spip_query($sql_query);
	$result = 
		($row = spip_fetch_array($sql_result))
		? intval($row['n'])
		: 0
		;
	return($result);
}

function spiplistes_boite_autocron ($return = false) { 
	@define('_SPIP_LISTE_SEND_THREADS',1);
	
	global $connect_id_auteur;
	
	// initialise les options
	foreach(array(
		'opt_suspendre_trieuse'
		,'opt_suspendre_meleuse'
		) as $key) {
		$$key = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}

	$result = "";
	
	// initialise les options
	foreach(array('opt_simuler_envoi') as $key) {
		$$key = __plugin_lire_key_in_serialized_meta($key, _SPIPLISTES_META_PREFERENCES);
	}

	// Informe sur l'état de la trieuse
	if($opt_suspendre_trieuse == 'oui') {
		if(_request('opt_suspendre_trieuse')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_suspendre_trieuse', $opt_suspendre_trieuse = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:Trieuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_timer.gif", true
				, _T('spiplistes:trieuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:trieuse_suspendue_info'), 'opt_suspendre_trieuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe sur l'état de la meleuse
	if($opt_suspendre_meleuse == 'oui') {
		if(_request('opt_suspendre_meleuse')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_suspendre_meleuse', $opt_suspendre_meleuse = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:Meleuse_reactivee')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true
				, _T('spiplistes:meleuse_suspendue'), _T('bouton_annuler')
				, _T('spiplistes:meleuse_suspendue_info'), 'opt_suspendre_meleuse', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	// Informe si mode simulation en cours
	if($opt_simuler_envoi == 'oui') {
		if(_request('opt_simuler_envoi')=='non') {
			if($connect_id_auteur == 1) {
				__plugin_ecrire_key_in_serialized_meta ('opt_simuler_envoi', $opt_simuler_envoi = 'non', _SPIPLISTES_META_PREFERENCES);
				$result .= "<p class='verdana2' style='margin-bottom:1em;'>"._T('spiplistes:simulation_desactive')."</p>\n";
			}
		}
		else {
			$result .= spiplistes_boite_autocron_info(_DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_envoyer-24.png", true
				, _T('spiplistes:Mode_simulation'), _T('bouton_annuler')
				, _T('spiplistes:mode_simulation_info'), 'opt_simuler_envoi', _DIR_IMG_PACK."warning-24.gif"
				);
		}
	}
	
	include_spip('genie/spiplistes_cron');
	if($ii = cron_spiplistes_cron($time) > 0) { 
	// le CRON n'a rien a faire. Pas de boite autocron
		if($return) return($result);
		else {
spiplistes_log("AUTOCRON no jobs ! $ii", _SPIPLISTES_LOG_DEBUG);
			echo($result);
			return;
		}
	}
	
	$n = spiplistes_nb_grand_total_courriers();
spiplistes_log("AUTOCRON nb courriers prets envoi $n", _SPIPLISTES_LOG_DEBUG);

	if($n > 0) {
		$result .= ""
			. "<br />"
			. debut_boite_info(true)
			. "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>"
			. "<div style='padding : 10px;text-align:center'><img alt='' src='"._DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_distribution-48.gif' /></div>"
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
					? "<div style='color:white;background-color:red;text-align:center;line-height:1.4em;'>"._T('spiplistes:mode_simulation')."</div>\n" 
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
			. "<p class='verdana2'>"._T('spiplistes:texte_boite_en_cours')."</p>" 
			. fin_boite_info(true)
			;
	}

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

function spiplistes_ecrire_metas() {
	return(__ecrire_metas());
}

// charge les vieilles def nécessaires si besoin
if(!spiplistes_spip_est_inferieur_193()) { 
	include_spip("inc/spiplistes_api_vieilles_defs");
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