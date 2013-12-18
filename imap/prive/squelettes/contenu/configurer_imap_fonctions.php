<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

//
// tester la connection imap
//
function imap_test_connexion() {
	include_spip('inc/config');

	$hote_imap = lire_config('imap/hote_imap');
	if ($hote_imap=="") {
		return _T('imap:test_parametres_remplis_notok');
	} else if (!function_exists("imap_open")) {
		return _T('imap:test_librairie_installee_notok');
	} else {
		// test connexion
		$mbox = imap_open_depuis_configuration();

		if (FALSE === $mbox) {
			return _T('imap:test_connexion_notok',array('connexion'=>$connexion));
		} else {
			return _T('imap:test_connexion_ok');
			imap_close($mbox);
		}
	}

	return;
}
