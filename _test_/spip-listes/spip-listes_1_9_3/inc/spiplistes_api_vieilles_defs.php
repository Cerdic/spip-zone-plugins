<?php

	// inc/spiplistes_api_vieilles_defs.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
	return(false);
}

function debut_page ($titre = "", $rubrique = "accueil", $sous_rubrique = "accueil") {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($titre, $rubrique, $sous_rubrique, $id_rubrique);
}

function spip_num_rows ($r) {
	return sql_count($r);
}

function spip_insert_id () {
	return mysql_insert_id();
}

function debut_block_visible ($id="") {
	include_spip('inc/layer');
	return debut_block_depliable(true,$id);
}

function debut_block_invisible ($id="") {
	include_spip('inc/layer');
	return debut_block_depliable(false,$id);
}

function bouton_block_invisible ($nom_block, $icone='') {
	include_spip('inc/layer');
	return bouton_block_depliable(_T("info_sans_titre"),false,$nom_block);
}

function bouton_block_visible ($nom_block) {
	include_spip('inc/layer');
	return bouton_block_depliable(_T("info_sans_titre"),true,$nom_block);
}

// toujours a cette valeur a present
$GLOBALS['options'] = 'avancees';

function bandeau_titre_boite2 ($titre, $logo="", $fond="toile_blanche", $texte="ligne_noire") {
	global $spip_lang_left, $spip_display, $browser_name;
	
	if (strlen($logo) > 0 AND $spip_display != 1 AND $spip_display != 4) {
		$ie_style = ($browser_name == "MSIE") ? "height:1%" : '';

		return "\n<div style='position: relative;$ie_style'>"
		. "\n<div style='position: absolute; top: -12px; $spip_lang_left: 3px;'>"
		. http_img_pack($logo, "", "")
		. "</div>"
		. "\n<div style='padding: 3px; padding-$spip_lang_left: 30px; border-bottom: 1px solid #444444;' class='verdana2 $fond $texte'>$titre</div>"
		. "</div>";
	} else {
		return "<h3 style='padding: 3px; border-bottom: 1px solid #444; margin: 0px;' class='verdana2 $fond $texte'>$titre</h3>";
	}
}

function creer_objet_multi ($r, $l) {
	sql_multi($r, $l);
}

function envoyer_mail ($email, $sujet, $texte, $from = "", $headers = "") {
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	return $envoyer_mail($email,$sujet,$texte,$from,$headers);
}

function spip_abstract_showtable ($table, $serveur='', $table_spip = false) {
	vieilles_log('spip_abstract_showtable()');
    return sql_showtable($table, $serveur, $table_spip);
}

//constantes spip pour mysql_fetch_array()
define('SPIP_BOTH', MYSQL_BOTH);
define('SPIP_ASSOC', MYSQL_ASSOC);
define('SPIP_NUM', MYSQL_NUM);


?>
