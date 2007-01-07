<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_w3cgh_test_dist()
{
	$nom = _request('nom');
	$url = urldecode(_request('url'));
	include_spip("inc/validateur_api");
	$res = validateur_test($nom,$url);
	
	$texte = end($res['res']);
	if ($ok = reset($res['res']))
		$texte = "<span style='color:#45CF00'>$texte</span>";
	else
		$texte = "<span style='color:#FF1F1F'>$texte</span>";

	ajax_retour($texte);
}

?>