<?php
/**
 * Supprimer les statistiques
 *
 * Surcharge pour prendre en compte tous les objets éditoriaux
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
} // securiser

// faudrait plutot recuperer dans inc_serialbase et inc_auxbase
// mais il faudra prevenir ceux qui affectent les globales qui s'y trouvent
// Afficher la liste de ce qu'on va detruire et demander confirmation 
// ca vaudrait mieux

/**
 * Supprimer les stats
 *
 * @param strinf $titre
 * @param bool $reprise
 * @return string
 */
function base_delete_stats_dist($titre = '', $reprise = '') {
	if (!$titre) {
		return;
	} // anti-testeur automatique
	sql_delete("spip_visites");
	sql_delete("spip_visites_articles");
	sql_delete("spip_visites_objets");
	sql_delete("spip_referers");
	sql_delete("spip_referers_articles");
	sql_delete("spip_referers_objets");
	
	include_spip('base/objets'); // au cas où
	$tables_objets = array_keys(lister_tables_objets_sql());
	$trouver_table = charger_fonction('trouver_table','base');
	foreach($tables_objets as $table){
		$desc = $trouver_table($table);
		if (
			isset($desc['field']['popularite'])
			and isset($desc['field']['visites'])
			and isset($desc['field']['referers'])
		){
			sql_update($table, array('popularite' => 0, 'visites' => 0, 'referers' => 0));
		}
	}

	// un pipeline pour detruire les tables de stats installees par les plugins
	pipeline('delete_statistiques', '');
	spip_log("raz des stats operee redirige vers " . _request('redirect'));
}
