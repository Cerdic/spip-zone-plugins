<?php
/**
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spiplistes_api_globales');
include_spip('inc/actions');
include_spip('inc/spiplistes_api');

// appelé par la boite autocron en espace privé
// lance au passage la meleuse
// renvoie le nombre d'étiquettes en attente

function action_spiplistes_envoi_lot_dist ()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));

	if($nb_etiquettes > 0) {
		// envoi d'un lot par la meleuse
		include_spip('inc/spiplistes_meleuse');
		$last_time = time();
		spiplistes_meleuse($last_time);
		$nb_etiquettes = spiplistes_courriers_en_queue_compter("etat=".sql_quote(''));
	}
	
	// nb etiquettes restant a envoyer pour l'affichage
	ajax_retour($nb_etiquettes);
}

