<?php
/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007 - 2012
 * $Id: abomailmans_interface.php 31752 2009-09-23 00:09:48Z kent1@arscenic.info $
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Declarer la tache cron de abomailman lente (messagerie de l'espace prive)
 * @param array $taches_generales
 * @return array 
 */
function abomailmans_taches_generales_cron($taches_generales){
	$taches_generales['abomailmans_envois'] = 60 * 10; // toutes les 10 minutes
	return $taches_generales;
}


// Initialise les reglages sous forme de tableau
function abomailmans_go($x) {
	if (!is_array($GLOBALS['abomailmans']	= @unserialize($GLOBALS['meta']['abomailmans'])))
		$GLOBALS['abomailmans'] = array();
	return $x;
}

?>