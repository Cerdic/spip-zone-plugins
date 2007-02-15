<?php
	/**
	 *
	 * Gravatar : Globally Recognized AVATAR
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

function balise_GRAVATAR($p) {
	return calculer_balise_dynamique($p, 'GRAVATAR', array());
}

function balise_GRAVATAR_stat($args, $filtres) {
	return array($args[0], $args[1], $args[2]);
}

function balise_GRAVATAR_dyn($email, $size, $gravatar_default) {
	include_spip('inc/distant');
	$md5_email = md5($email);
	$gravatar_cache = sous_repertoire(_DIR_VAR, 'cache-gravatar').$md5_email;

	if(!file_exists($gravatar_cache) OR time()-3600*24 > filemtime($gravatar_cache)) {
		$gravatar = recuperer_page('http://www.gravatar.com/avatar.php?gravatar_id='.$md5_email.'&amp;size='.$size);
		if($gravatar) {
			$file = fopen($gravatar_cache, "w+");
			fputs($file, $gravatar);
			fclose($file);
		} else {
			copy($gravatar_default, $gravatar_cache);
		}
	}

	return '<img src="'.$GLOBALS['meta']['adresse_site'].'/'.$gravatar_cache.'" width="'.$size.'" height="'.$size.'" alt="" />';
}
?>