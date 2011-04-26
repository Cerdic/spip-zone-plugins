<?php

function mailcrypt_insert_head($flux){
	$js = find_in_path('mailcrypt.js');
	$flux .= "\n<script type='text/javascript' src='$js'></script>\n";
	return $flux;
}

function mailcrypt_post_propre ($texte) {
	include_spip('mailcrypt_fonctions');
	return mailcrypt($texte);
}


?>