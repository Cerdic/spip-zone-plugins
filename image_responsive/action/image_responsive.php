<?php


function action_image_responsive() {
	$img = _request("img");
	$taille = _request("taille");
	$dpr = _request("dpr");
	$xsendfile = _request("xsendfile");

	retour_image_responsive($img, $taille, $dpr, $xsendfile, "http");
}
