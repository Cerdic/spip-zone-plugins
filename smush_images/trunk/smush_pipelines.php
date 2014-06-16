<?php
/**
 * Plugin Smush
 * 
 * Auteur :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 
 * @package SPIP\Smushit\Pipelines
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline post_image_filtrer (SPIP)
 * 
 * On passe toutes les images dans le filter image_smush sauf si :
 * -* une constante _SMUSH_INTERDIRE_AUTO est définier
 * -* la case eviter_traitement_auto de la config est cochée
 * 
 * @param string $flux
 * 		Le tag image (<img src...>) à réduire
 * @return string $flux
 * 		Le nouveau tag image
 */
function smush_post_image_filtrer($flux) {
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	if((!isset($GLOBALS['meta']['smush_casse']) || $GLOBALS['meta']['smush_casse'] != 'oui') && !defined('_SMUSH_INTERDIRE_AUTO') && (lire_config('smush/eviter_traitement_auto','off') != 'on')){
		$smush = charger_fonction('smush_image','inc');
		$flux = $smush($flux);
	}
	return $flux;
}

/**
 * Pipeline taches_generales_cron de Smush (SPIP)
 *
 * Vérifie la présence à intervalle régulier des logiciels présents
 * 
 * @param array $taches_generales 
 * 		Un array des tâches du cron de SPIP
 * @return array $taches_generales
 * 		L'array des taches complété
 */
function smush_taches_generales_cron($taches_generales){
	$taches_generales['smush_taches_generales'] = 24*60*60;
	return $taches_generales;
}
?>