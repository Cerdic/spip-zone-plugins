<?php


function action_image_responsive() {
	$img = _request("img");
	$taille = _request("taille");
	$dpr = _request("dpr");
	$xsendfile = _request("xsendfile");

	// Gérer le plugin mutualisation
	if (defined('_DIR_SITE'))
		$img = _DIR_SITE.$img;

	retour_image_responsive($img, $taille, $dpr, $xsendfile, "http");
}
