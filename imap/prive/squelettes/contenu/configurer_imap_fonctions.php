<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

//
// tester la connection imap
//
function imap_test_connexion() {
	include_spip('inc/config');

	$email = lire_config('imap/email');
	$email_pwd = lire_config('imap/email_pwd');
	$hote_imap = lire_config('imap/hote_imap');
	$hote_port = lire_config('imap/hote_port');
	$hote_options = lire_config('imap/hote_options');
	$hote_inbox = lire_config('imap/inbox'); 

	if ($hote_imap=="") {
		return _T('imap:test_parametres_remplis_notok');
	} else if (!function_exists("imap_open")) {
		return _T('imap:test_librairie_installee_notok');
	} else {
		// test connexion
		$connexion = '{'.$hote_imap.':'.$hote_port.$hote_options.'}'.$hote_inbox;
		$mbox = @imap_open($connexion, $email, $email_pwd);

		if (FALSE === $mbox) {
			return _T('imap:test_connexion_notok',array('connexion'=>$connexion));
		} else {
			return _T('imap:test_connexion_ok');
		}
	}

	return;
}
