<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Ajoute le CSS du plugin dans les pages publiques
 */

function listeimc_css($flux) 
{
	$flux .= '<link rel="stylesheet" href="plugins/liste_imc/css/listeimc.css" type="text/css" />';
	return $flux;
}

/*
 * Met en place le Cron en fonction de la fréquence de génération de la configuration
 */

function listeimc_frequence($flux) 
{
	$frequence = lire_config('listeimc/frequence_fichier');
	$flux['listeimc_cron'] = 3600*$frequence;
	return $flux;
}

?>