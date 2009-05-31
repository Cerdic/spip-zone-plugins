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
		$couleur = '45CF00';
	else
		$couleur = 'FF1F1F';
	if (_request('var_mode')=='image'){
		include_spip('inc/filtres');
		$img = image_typo($texte, "police=dustismo.ttf","couleur=$couleur", "taille=12");
		$img = extraire_attribut($img,'src');
		header('Content-Type: image/png');
		header('Content-Length: '.filesize($img));
		readfile($img);
		exit();
	}
	$texte = "<span style='color:#$couleur'>$texte</span>";
	ajax_retour($texte);
}
?>