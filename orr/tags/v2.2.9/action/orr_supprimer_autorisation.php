<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_orr_supprimer_autorisation_dist(){
	// secu: $id_autorisation OK
	if (!$id_autorisation = intval(_request('arg')))
		exit(_T('orr:id_autorisation_incorrecte'));
	// secu: le bon auteur
	$securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();
    // secu: avec le droit
    include_spip('inc/autoriser');
    if (!autoriser('modifier', 'orr_autorisation', $id_autorisation)) 
		exit(_T('orr:suppression_autorisation_interdite'));
	
	// ne pas supprimer si des ressources sont associées
	if (sql_countsel('spip_orr_autorisations_liens', "id_orr_autorisation = $id_autorisation"))
		exit(_T('orr:suppression_autorisation_impossible_ressources_liees'));
	
	// suppression
	sql_delete('spip_orr_autorisations', "id_orr_autorisation = $id_autorisation");
}

?>
