<?php
	/**
	 *
	 * Gravatar : Globally Recognized AVATAR
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

function balise_GRAVATAR($p) {
	return calculer_balise_dynamique($p, 'GRAVATAR', array());
}

function balise_GRAVATAR_stat($args, $filtres) {
	return array($args[0], $args[1], $args[2], $args[3], $args[4]);
}

function balise_GRAVATAR_dyn($email, $gravatar_default, $size, $alt, $class) {
	$md5_email = md5($email);
	$gravatar_cache = _DIR_PLUGIN_GRAVATAR.'cache/'.$md5_email;

	if(!file_exists($gravatar_cache)) {
		$str = @file_get_contents('http://www.gravatar.com/avatar.php?gravatar_id='.$md5_email.'&amp;size='.$size);
		if(!$str) {
			$gravatar_url = $gravatar_default;
		} else {
			$file = fopen($gravatar_cache, "w+");
			fputs($file, $str);
			fclose($file);
			$gravatar_url = $gravatar_cache;
		}
	} else {
		$gravatar_url = $gravatar_cache;
	}

	return '<img src="'.$GLOBALS['meta']['adresse_site'].'/'.$gravatar_url.'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" class="'.$class.'" />';
}
?>