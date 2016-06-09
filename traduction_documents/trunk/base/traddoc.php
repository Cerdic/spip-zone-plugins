<?php

/**
 * Fonctions de déclarations des tables dans la bdd
 * et de sélection spécifique de la langue dans la document...
 *
 * @package SPIP\Traddoc\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajouter id_trad à la table documents
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des objets editoriaux
 * @return array
 *     Description des objets editoriaux
 */
function traddoc_declarer_tables_objets_sql($tables) {
	// Extension de la table documents
	$tables['spip_documents']['field']['lang'] = "VARCHAR(10) DEFAULT '' NOT NULL";
	$tables['spip_documents']['field']['langue_choisie'] = "VARCHAR(3) DEFAULT 'non'";
	$tables['spip_documents']['field']['id_trad'] = "bigint(21) DEFAULT '0' NOT NULL";
	$tables['spip_documents']['texte_definir_comme_traduction_objet'] = 'traddoc:texte_definir_comme_traduction_document';
	$tables['spip_documents']['texte_langue_objet'] = 'traddoc:texte_langue_document';
	
	return $tables;
}

/**
 * Ajout lors de l'insertion d'une traduction de document
 * de la langue, qui peut ne pas être connue
 *
 * @param array $flux    Données du pipeline
 * @return array         Données du pipeline
**/
function traddoc_pre_insertion($flux) {
	// pour les documents
	if ($flux['args']['table'] == 'spip_documents') {
		$lang = '';
		$choisie = 'non';

		// La langue a la creation : si les liens de traduction sont autorises
		// dans les documents, on essaie avec la langue de l'auteur,
		// ou a defaut celle du document
		// Sinon c'est la langue du document qui est choisie + heritee
		if (
			$id_document_source = _request('lier_trad')
			and in_array('spip_documents', explode(',', $GLOBALS['meta']['multi_objets']))
		) {
			lang_select($GLOBALS['visiteur_session']['lang']);
			
			if (in_array($GLOBALS['spip_lang'], explode(',', $GLOBALS['meta']['langues_multilingue']))) {
				$lang = $GLOBALS['spip_lang'];
				$choisie = 'oui';
			}
		}
		
		// Sinon la langue par défaut du site
		if (!$lang) {
			$choisie = 'non';
			$lang = $GLOBALS['meta']['langue_site'];
		}

		$flux['data']['lang'] = $lang;
		$flux['data']['langue_choisie'] = $choisie;

		// ici on ignore changer_lang qui est poste en cas de trad,
		// car l'heuristique du choix de la langue est pris en charge ici
		// en fonction de la config du site et du document choisi
		set_request('changer_lang');
	}
	
	return $flux;
}
