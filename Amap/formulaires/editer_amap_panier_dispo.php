<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_amap_panier_dispo_charger_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution) {
	$valeurs = array('id_amap_panier'=>$id_amap_panier, 'id_auteur'=>$id_auteur, 'id_producteur'=>$id_producteur, 'date_distribution'=>$date_distribution);
	return $valeurs;
}

function formulaires_editer_amap_panier_dispo_verifier_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution){
	$erreurs = array();
	// verifier que les champs obligatoires sont bien la :
	foreach(array('id_auteur','id_producteur','date_distribution') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = 'Ce champ est obligatoire';
	
	if (count($erreurs))
		$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
	return $erreurs;
}

function formulaires_editer_amap_panier_dispo_traiter_dist($id_amap_panier,$id_auteur,$id_producteur,$date_distribution) {
	// Le n° de panier
	$id_amap_panier = _request('id_amap_panier');
	// L'ahérent du panier
	$id_auteur = _request('id_auteur');
	// Le producteur du panier
	$id_producteur = _request('id_producteur');
	// La date de distribution
	$date_distribution = _request('date_distribution');
	spip_log("Le $id_amap_panier , $id_auteur, $id_producteur, $date_distribution", "amap_installation");

	sql_replace("spip_amap_paniers", array("id_amap_panier" => $id_amap_panier, "id_auteur" => $id_auteur, "id_producteur" => $id_producteur, "date_distribution" => $date_distribution));
	spip_log("Le $id_amap_panier a bien été récupéré par l'adhérent $id_auteur, panier produit par $id_producteur pour la livraison du $date_distribution", "amap_installation");

	// Valeurs de retours
	$message['message_ok'] = _T('Le $id_amap_panier a bien été récupéré par $id_auteur');
	return $message;
}
?>
