<?php
/**
 * Insertion dans le pipeline rechercher_liste_des_champs
 * 
 * Permet de réaliser des recherches dans la table spip_plugins
 * @param Array $tables
 */
function step_rechercher_liste_des_champs($tables){
	$tables['plugin']['nom'] = 8;
	$tables['plugin']['prefixe'] = 8;
	$tables['plugin']['shortdesc'] = 6;
	$tables['plugin']['description'] = 3;
	$tables['plugin']['categorie'] = 3;
	$tables['plugin']['tags'] = 1;
	return $tables;
}

/**
 * Insertion dans le pipeline taches_generales_cron (SPIP)
 *
 * @return L'array des taches complété
 * @param array $taches_generales Un array des tâches du cron de SPIP
 */
function step_taches_generales_cron($taches_generales){
	$taches_generales['step_actualise'] = 24*60*60;
	return $taches_generales;
}
?>
