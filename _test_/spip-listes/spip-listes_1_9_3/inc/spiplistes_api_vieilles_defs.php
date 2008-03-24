<?php

	// inc/spiplistes_api_vieilles_defs.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

if(spiplistes_spip_est_inferieur_193()) { 
	return(false);
}

// conflit (doublons) avec plugins important vieilles defs...
// en attendant de tout nettoyer
$included_files = get_included_files();
foreach ($included_files as $filename) {
	if(basename($filename) == "vieilles_defs.php") {
		return(true);
	}
}

if(!function_exists('debut_page')) {
	function debut_page ($titre = "", $rubrique = "accueil", $sous_rubrique = "accueil") {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($titre, $rubrique, $sous_rubrique, $id_rubrique);
	}
}

if(!function_exists('spip_num_rows')) {
	function spip_num_rows ($r) {
		return sql_count($r);
	}
}

if(!function_exists('spip_insert_id')) {
	function spip_insert_id () {
		return mysql_insert_id();
	}
}

if(!function_exists('debut_block_visible')) {
	function debut_block_visible ($id="") {
		include_spip('inc/layer');
		return debut_block_depliable(true,$id);
	}
}

if(!function_exists('debut_block_invisible')) {
	function debut_block_invisible ($id="") {
		include_spip('inc/layer');
		return debut_block_depliable(false,$id);
	}
}

if(!function_exists('bouton_block_invisible')) {
	function bouton_block_invisible ($nom_block, $icone='') {
		include_spip('inc/layer');
		return bouton_block_depliable(_T("info_sans_titre"),false,$nom_block);
	}
}

if(!function_exists('bouton_block_visible')) {
	function bouton_block_visible ($nom_block) {
		include_spip('inc/layer');
		return bouton_block_depliable(_T("info_sans_titre"),true,$nom_block);
	}
}

// utilisé en 192C (inc/boutons.php)
// toujours a cette valeur a present en 193
$GLOBALS['options'] = 'avancees';

if(!function_exists('bandeau_titre_boite2')) {
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
}

if(!function_exists('creer_objet_multi')) {
	function creer_objet_multi ($r, $l) {
		sql_multi($r, $l);
	}
}

if(!function_exists('envoyer_mail')) {
	function envoyer_mail ($email, $sujet, $texte, $from = "", $headers = "") {
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		return $envoyer_mail($email,$sujet,$texte,$from,$headers);
	}
}

if(!function_exists('spip_abstract_showtable')) {
	function spip_abstract_showtable ($table, $serveur='', $table_spip = false) {
		vieilles_log('spip_abstract_showtable()');
		 return sql_showtable($table, $serveur, $table_spip);
	}
}

// utilisé en 192C (base/db_mysql.php)
// constantes spip pour mysql_fetch_array() qui est encore dans inc/utils.php en 193
@define('SPIP_BOTH', MYSQL_BOTH);
@define('SPIP_ASSOC', MYSQL_ASSOC);
@define('SPIP_NUM', MYSQL_NUM);


?>