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

/**
 * Bloc sur les encours editoriaux en page d'accueil
 *
 * @param string $texte
 * @return string
 */
function facteurmonitoring_accueil_informations($texte){ 

	// si aucun autre objet n'est a valider, on ne dit rien sur les forum
	if ($GLOBALS['visiteur_session']['statut'] == '0minirezo') {
	
     if (isset($GLOBALS['meta']['facteurmonitoring_etat'])) {
     
        if ($GLOBALS['meta']['facteurmonitoring_etat']=="NOTOK") {
            $texte .= "<div style='color:red;padding:1em;border:1px solid red;background:#FFD8D8;margin:1em 0;'>"._T("facteurmonitoring:erreur_home")."</div>";
        }
     
     }

	}

	return $texte;
}


?>