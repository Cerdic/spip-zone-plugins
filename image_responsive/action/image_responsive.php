<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_image_responsive() {
	$img = _request("img");
	$taille = _request("taille");
	$dpr = _request("dpr");
	$xsendfile = _request("xsendfile");

	// Gérer le plugin mutualisation
	#if (defined('_DIR_SITE'))
	#	$img = _DIR_SITE.$img;

	return retour_image_responsive($img, $taille, $dpr, $xsendfile, "http");
}
