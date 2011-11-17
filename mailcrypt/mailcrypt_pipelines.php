<?php

function mailcrypt_insert_head($flux){
	$js = find_in_path('mailcrypt.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}

function mailcrypt_post_propre($texte) {
	include_spip('mailcrypt_fonctions');
	return mailcrypt($texte);
}

function mailcrypt_facteur_pre_envoi($facteur) {
	include_spip('mailcrypt_fonctions');
	$facteur->Body = maildecrypt($facteur->Body);
	$facteur->AltBody = maildecrypt($facteur->AltBody);
	return $facteur;
}

?>