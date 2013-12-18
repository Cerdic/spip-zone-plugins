<?php

function imap_open_depuis_configuration() {
	include_spip('inc/config');

	$email = lire_config('imap/email');
	$email_pwd = lire_config('imap/email_pwd');
	$hote_imap = lire_config('imap/hote_imap');
	$hote_port = lire_config('imap/hote_port');
	$hote_options = lire_config('imap/hote_options');
	$hote_inbox = lire_config('imap/inbox'); 

	$connexion = '{'.$hote_imap.':'.$hote_port.$hote_options.'}'.$hote_inbox;
	return @imap_open($connexion, $email, $email_pwd);
}
