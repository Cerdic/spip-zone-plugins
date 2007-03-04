<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
function action_smslist_envoi_lot_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$smslist_envoyer = charger_fonction('smslist_envoyer','inc');
	$smslist_envoyer();
	
	// compter les SMS restant a envoyer pour l'affichage
	
	list($total,$restant) = smslist_compter_spool();
	ajax_retour($restant);
}

?>