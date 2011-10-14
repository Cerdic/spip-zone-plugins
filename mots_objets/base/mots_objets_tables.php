<?php
/**
 * Plugin mots-objets pour Spip 2.0
 * Licence GPL 
 * Adaptation Cyril MARION - (c) 2010 Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function mots_objets_declarer_tables_interfaces($tables_interface){
	include_spip('inc/gouverneur_de_mots');
	$liste = gouverneur_de_mots();
	foreach ($liste as $objet) {
		// mots_auteurs
		$liaison = 'mots_' . $objet->nom;
		
		// -- Prise en compte de la nouvelle table
		$tables_interface['table_des_tables'][$liaison] = $liaison;

		// -- Liaisons mots/auteurs
		// $tables_interface['tables_jointures']['spip_auteurs']['id_auteur']= 'mots_auteurs';
		$tables_interface['tables_jointures'][ $objet->table_sql ][ $objet->_id_objet ]= $liaison;
		$tables_interface['tables_jointures']['spip_mots'][]= $liaison;
		$tables_interface['tables_jointures'][ $objet->table_sql ][]= 'mots';		
	}

	return $tables_interface;
}


function mots_objets_declarer_tables_auxiliaires($tables_auxiliaires){
	include_spip('inc/gouverneur_de_mots');
	$liste = gouverneur_de_mots();
	foreach ($liste as $objet) {
		// mots_xxxxxs
		$liaison = 'mots_' . $objet->nom;
		$desc = 'spip_' . $liaison;
		$key  = 'spip_' . $liaison . 'key';
		$$desc = array(
			"id_mot"			=> "bigint(21) NOT NULL",
			"$objet->_id_objet"	=> "bigint(21) NOT NULL",
		);
		$$key = array(
			"PRIMARY KEY"	=> "$objet->_id_objet, id_mot",
			"KEY id_mot"	=> "id_mot"
		);
		$tables_auxiliaires[$desc] = array(
			'field'=>&$$desc,
			'key'=>$$key
		);
	}
	
	return $tables_auxiliaires;
}

?>
