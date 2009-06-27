<?php


function rssarticle_taches_generales_cron($taches_generales){
	$taches_generales['rssarticle_copie'] = 60*5; // ts les 20 min pour le dev
	return $taches_generales;
}



?>