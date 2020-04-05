<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_facebook_info_dist($token) {
	include_spip('inc/facebook');
	return facebook_profil($token);
}
