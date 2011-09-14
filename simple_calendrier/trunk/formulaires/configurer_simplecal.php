<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_simplecal_charger_dist(){
	$valeurs = array();
    $valeurs['simplecal_autorisation_redac'] = $GLOBALS['meta']['simplecal_autorisation_redac'];
    $valeurs['simplecal_rubrique'] = $GLOBALS['meta']['simplecal_rubrique'];
	$valeurs['simplecal_refobj'] = $GLOBALS['meta']['simplecal_refobj'];
    $valeurs['simplecal_themepublic'] = $GLOBALS['meta']['simplecal_themepublic'];
    
	return $valeurs;
}

function formulaires_configurer_simplecal_verifier_dist(){
    $retour = array();    
    
    return $retour;
}

function formulaires_configurer_simplecal_traiter_dist(){
	include_spip('inc/config');
	appliquer_modifs_config();
    
	return array('message_ok'=>'');
}

?>