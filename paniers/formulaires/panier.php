<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_panier_charger($id_panier=0){
	include_spip('inc/session');
	
	// On commence par chercher le panier du visiteur actuel s'il n'est pas donné
	if (!$id_panier) $id_panier = session_get('id_panier');
	
	$contexte = array(
		'_id_panier' => $id_panier
	);
	
	return $contexte;
}

function formulaires_panier_verifier($id_panier=0){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_panier_traiter($id_panier=0){
	$retours = array();
	
	return $retours;
}

?>
