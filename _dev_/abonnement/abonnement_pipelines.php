<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function abonnement_I2_cfg_form($flux) {
	include_spip('inc/spiplistes_api_globales');
    $flux .= recuperer_fond('fonds/inscription2_abonnement');
	return ($flux);
}

//utiliser le cron pour gerer les dates de validite des abonnements et envoyer les messages de relance
function abonnement_taches_generales_cron($taches_generales){
	$taches_generales['abonnement_cron'] = 10 ;
	return $taches_generales;
}

?>
