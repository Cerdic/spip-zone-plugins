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
	include_spip('inc/filtres_images');
	include_spip('inc/gravatar');
	return image_reduire(sinon(gravatar($email),$gravatar_default), $size ? $size : 80);
}
?>