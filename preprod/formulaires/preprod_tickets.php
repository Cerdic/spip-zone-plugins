<?php

function formulaires_preprod_tickets_charger_dist($adresse)
{
	$adresse	= htmlspecialchars_decode($adresse);
	$adresse	= preg_replace('/(lang=fr|lang=en|var_mode=calcul|var_mode=recalcul)/', '', $adresse);
	$adresse	= preg_replace('/&+/', '&', $adresse);
	$adresse	= trim($adresse, '&?');
	
	$path		= parse_url($adresse, PHP_URL_PATH);
	
	if (!empty($path))
	{
		if ('/'==$path)
			$adresse= 'accueil';
		$condition = "(exemple LIKE '%".$adresse."%' OR texte LIKE '%".$adresse."%')";
		
		$tickets	= sql_allfetsel('id_ticket', 'spip_tickets', $condition);
		$tickets	= array_map('array_shift', $tickets);
	}
	return array(
		'adresse'	=> $path,
		'tickets'	=> $tickets
	);
}
function formulaires_preprod_tickets_verifier_dist($adresse)
{
	return array();
}
function formulaires_preprod_tickets_traiter_dist($adresse)
{
	return array();
}
?>