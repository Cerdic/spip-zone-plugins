<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_w3cgh_voir_dist()
{
	$nom = _request('nom');
	$url = urldecode(_request('url'));
	include_spip("inc/validateur_api");
	$res = validateur_affiche($nom,$url);
	echo $res;
}

?>