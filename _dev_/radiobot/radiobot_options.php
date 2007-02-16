<?php

//utiliser le cron pour envoyer les messages en attente
function radiobot_taches_generales_cron($taches_generales){
	$taches_generales['radiobot_cron'] = 60 ;
	return $taches_generales;
}

?>