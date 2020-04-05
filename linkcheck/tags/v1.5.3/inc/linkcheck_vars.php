<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Champs des objets susceptibles de contenir des liens,
 * soit au sein d'un contenu (type 0) soit un lien unique (type 1)
 */
function linkcheck_champs_a_traiter($table) {
	$tab_champs = array();
	if (isset($table['field']) && is_array($table['field'])) {
		foreach ($table['field'] as $nom_champ => $type_champ) {
			if (preg_match(',^(tiny|long|medium)?text\s?,i', $type_champ)) {
				if (preg_match('/url/', $nom_champ)) {
					$tab_champs[$nom_champ] = 0;
				} else {
					$tab_champs[$nom_champ] = 1;
				}
			}
		}
	}
	$tab_champs = pipeline('linkcheck_champs_a_traiter',array(
		'args' => array('table' => $table['table_objet']),
		'data' => $tab_champs)
	);
	return $tab_champs;
}

/**
 * Tables de la base de données qui peuvent contenir des liens, et leur singulier
 */
function linkcheck_tables_a_traiter() {
	$tables_spip = lister_tables_objets_sql();
	$tables = array();
	if (is_array($tables_spip)) {
		foreach ($tables_spip as $key => $table) {
			if ($table['principale'] == 'oui' && !in_array($key, array('spip_syndic_articles','spip_paquets','spip_linkchecks', 'spip_tickets'))) {
				$tables[] = array($key => $table);
			}
		}
	}
	return $tables;
}

/**
 * Association d'un etat de lien avec le premier chiffre des codes de statut http (0)
 * et avec le statut d'un objet (1)
 */
function linkcheck_etats_liens() {
	return array(
			0 => array(
				'1' => 'malade',
				'2' => 'ok',
				'3' => 'deplace',
				'4' => 'mort',
				'5' => 'malade'
			),
			1 => array(
				'publie' => 'ok',
				'prepa' => 'malade',
				'prop' => 'malade',
				'refuse' => 'malade',
				'poubelle' => 'mort'
			)
		);
}
