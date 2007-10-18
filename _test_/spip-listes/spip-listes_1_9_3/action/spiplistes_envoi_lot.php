<?php
// action/spiplistes_envoi_lot.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
function action_spiplistes_envoi_lot_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	// envoi d'un lot par la meleuse
	include_spip('inc/spiplistes_meleuse');
	spiplistes_meleuse();
	
	// compter les mail restant a envoyer pour l'affichage
	ajax_retour(spiplistes_nb_grand_total_courriers());
}

?>