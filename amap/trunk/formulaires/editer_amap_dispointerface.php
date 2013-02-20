<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

function formulaires_editer_amap_dispointerface_traiter_dist() {
	// Le numéro du panier dispo
	$id_amap_panier = _request('id_amap_panier');
	// Le numéro de l'amapiens qui a le panier
	$id_auteur = _request('id_auteur');
	// Le numéro du producteur du panier
	$id_producteur = _request('id_producteur');
	// La date de distribution 
	$date_distribution = _request('date_distribution');
	$date_distribution2 = _request('date_distribution2');
	sql_replace("spip_amap_paniers", array("id_amap_panier" => $id_amap_panier, "id_auteur" => $id_auteur, "id_producteur" => $id_producteur, "date_distribution" => $date_distribution2, "dispo" => 1));

	// Valeurs de retours
	$message['message_ok'] = _T('amap:confirmation_envoi', array('date_distribution'=>$date_distribution));
	return $message;
}
?>
