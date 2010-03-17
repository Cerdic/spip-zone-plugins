<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_noisettes_page_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$page = $arg;
	
	include_spip('inc/noizetier');
	supprimer_noisettes_page_noizetier($page);
	
		// Redirection
	include_spip('inc_headers');
	redirige_par_entete("./?exec=noizetier");
}
?>