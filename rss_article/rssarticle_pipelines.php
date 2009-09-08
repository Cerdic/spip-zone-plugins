<?php

function rssarticle_taches_generales_cron($taches_generales){
	$taches_generales['rssarticle_copie'] = 60*1; // ts les 15 min 
	return $taches_generales;
}


?>