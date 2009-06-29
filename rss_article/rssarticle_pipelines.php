<?php

function rssarticle_taches_generales_cron($taches_generales){
	$taches_generales['rssarticle_copie'] = 60*20; // ts les 20 min 
	return $taches_generales;
}


?>