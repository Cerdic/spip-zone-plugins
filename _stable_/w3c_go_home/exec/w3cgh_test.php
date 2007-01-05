<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_w3cgh_test_dist()
{
	$nom = _request('nom');
	$url = urldecode(_request('url'));
	include_spip("inc/validateur_api");
	$res = validateur_test($nom,$url);

	ajax_retour(end($res['res']));
}

?>