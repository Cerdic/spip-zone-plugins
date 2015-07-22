<?php
/**
 * Utilisations de pipelines par Monitoring du Facteur
 *
 * @plugin     Monitoring du Facteur
 * @copyright  2015
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Facteurmonitoring\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function facteurmonitoring_taches_generales_cron($taches_generales){
  include_spip('inc/config');
  $frequence = intval(lire_config("facteurmonitoring/frequence",24)) * 3600;
  if ($frequence <3600)
        $frequence = 3600;

	$taches_generales['facteurmonitoring'] = $frequence; 
	
		
	return $taches_generales;
}


?>