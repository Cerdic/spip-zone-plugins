<?php
/**
 * Utilisations de pipelines par Publication par email
 *
 * @plugin     Publication par email
 * @copyright  2013
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Emailtospip\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

// les taches crons
function emailtospip_taches_generales_cron($taches_generales){   
  $taches_generales['emailtospip'] = 60*15;  // tous les 15 min		
	return $taches_generales;
}



?>