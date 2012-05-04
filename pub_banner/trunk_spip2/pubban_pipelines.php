<?php
/**
 * @name 		Pipelines
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Ajout d'une tache CRON
 * Appel de {@link genie_pubban_cron} une fois par jour
 */
function pubban_taches_generales_cron($taches_generales){
	$taches_generales['pubban_cron'] = 60 * 60 * 24;
	return $taches_generales;
}

?>