<?php
/**
 * Plugin SPIP-Projet
 * Licence GPL
 * Eric Lupinacci, Quentin Drouet
 *
 * Lister les ligatures possibles avec un projet
 *
 */
function projets_lister_ligatures(){
	global $tables_jointures;
	
	$recuperer_infos_tables = charger_fonction('gouverneur_infos_tables','inc');
	$infos_tables = $recuperer_infos_tables();
	foreach($tables_jointures as $table_eventuelle => $sous_tables){
		foreach($sous_tables as $lien => $table_liee){
			if($table_liee == 'projets_liens'){
				$objets[objet_type($table_eventuelle)] = ucfirst(_T($infos_tables[$table_eventuelle]['texte_multiple']));
			}
		}
	}
	return $objets;
}
?>