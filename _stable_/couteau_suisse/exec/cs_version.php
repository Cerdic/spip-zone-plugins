<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

@define('_URL_CS_PLUGIN_XML', 'http://zone.spip.org/trac/spip-zone/browser/_plugins_/_stable_/couteau_suisse/plugin.xml?format=txt');

// compatibilite spip 1.9
if(!function_exists(ajax_retour)) { 
	function ajax_retour($corps) {
		$c = $GLOBALS['meta']["charset"];
		header('Content-Type: text/html; charset='. $c);
		$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
		echo $c, $corps;
		exit;
	}
}

function exec_cs_version_dist() {
	if (!cout_autoriser()) {
		include_spip('inc/minipres');
		echo defined('_SPIP19100')?minipres( _T('avis_non_acces_page')):minipres();
		exit;
	}
	$version = _request('version');

	// pour la version disponible, on regarde toutes les 1h06
	$maj = isset($GLOBALS['meta']['tweaks_maj'])?unserialize($GLOBALS['meta']['tweaks_maj']):array(0, '');
	if ($quiet = $maj[1] && (time()-$maj[0] < 4000)) $distant = $maj[1];
	else {
		include_spip('inc/distant');
		$distant = recuperer_page(_URL_CS_PLUGIN_XML);
		if ($distant) $distant = $maj[1] = preg_match(',<version>([0-9.]+)</version>,', $distant, $regs)?$regs[1]:'';
		$maj[0] = time();
		if ($distant) ecrire_meta('tweaks_maj', serialize($maj));
		ecrire_metas();
	}
	if (!$distant) ajax_retour('');
	include_spip('inc/texte');
	ajax_retour(ptobr(propre($distant==$version?_T('desc:a_jour'):($distant?_T('desc:distant', array('version' => $distant)):''))));
}
?>