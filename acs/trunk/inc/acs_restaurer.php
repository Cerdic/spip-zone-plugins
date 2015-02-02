<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acs_load_vars');

/**
 * Restaure la configuration ACS du site
 * @param text $fichier
 * @return array
 */
function acs_restaurer($fichier) {
	$file = _DIR_DUMP.'acs/'.basename($fichier); // securite
	if (!is_file($file)) { // Ultime securite
		return array('message_erreur' => _T('acs:err_fichier_absent', array('file' => joli_repertoire($file))));
	}
	$admins = $GLOBALS['meta']['ACS_ADMINS'];
	acs_reset_vars();
	ecrire_meta('ACS_ADMINS', $admins);
	$r = acs_load_vars($file);
	if ($r == 'ok') {
		ecrire_meta('acsDerniereModif', time());
		acs_log('inc/acs_restaurer : restauré "'.$archive.'" '.$r);
		return array('message_ok' => _T('acs:restored',array('file' => '"'.basename($fichier,'.php').'"')));
	}
	else 
		return array('message_erreur' => $r);
}
?>