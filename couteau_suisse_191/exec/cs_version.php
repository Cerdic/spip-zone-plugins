<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : https://contrib.spip.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('cout_define');
cout_define('distant');

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
	cs_minipres();
	$version = _request('version');
	$force = _request('force')=='oui';

	// pour la version disponible, on regarde toutes les 2h00
	$maj = isset($GLOBALS['meta']['tweaks_maj'])?unserialize($GLOBALS['meta']['tweaks_maj']):array(0, '');
	if (!$force && $maj[1] && (time()-$maj[0] < 2*3600)) $distant = $maj[1];
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
	ajax_retour(ptobr(propre($distant==$version?_T('couteauprive:version_a_jour'):(
		$distant?_T('couteauprive:version_nouvelle', array('version' => "[{$distant}->https://files.spip.net/spip-zone/couteau_suisse.zip]")):''
	))));
}
?>