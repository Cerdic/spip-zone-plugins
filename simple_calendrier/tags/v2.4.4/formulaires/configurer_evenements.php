<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2016
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/simplecal_conf');


function formulaires_configurer_evenements_charger_dist(){
	$metas = array(
		"simplecal_autorisation_redac",
		"simplecal_rubrique",
		"simplecal_refobj",
		"simplecal_horaire",
		"simplecal_descriptif",
		"simplecal_texte",
		"simplecal_lieu",
		"simplecal_lien",
		"simplecal_themepublic"
	);
	
	$valeurs = array();
	foreach($metas as $m) {
		$valeurs[$m] = $GLOBALS['meta'][$m];
	}
	return $valeurs;
}

function formulaires_configurer_evenements_verifier_dist(){
	$retour = array();
	return $retour;
}

function formulaires_configurer_evenements_traiter_dist(){
	$res = array('editable'=>true);
	$metas = array(
		"simplecal_autorisation_redac",
		"simplecal_rubrique",
		"simplecal_refobj",
		"simplecal_horaire",
		"simplecal_descriptif",
		"simplecal_texte",
		"simplecal_lieu",
		"simplecal_lien",
		"simplecal_themepublic"
	);
	foreach($metas as $m) {
		if (!is_null($v=_request($m))) {
			ecrire_meta($m, $v);
		}
	}
	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

?>