<?php
/**
 * Utilisations de pipelines par Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/rang_api');
include_spip('inc/config');

/**
 * Declaration du champ Rang sur les objets sélectionnés
 *
 * @param array $tables
 * @return array
 */
function rang_declarer_tables_objets_sql($tables) {
	
	/* Declaration du champ Rang sur les objets sélectionnés */
	$rang_objets = rtrim(lire_config('rang/rang_objets'), ',');
	$liste_objets = explode(',', $rang_objets);

	foreach ($liste_objets as  $table) {
		$tables[$table]['field']['rang'] = "SMALLINT NOT NULL";
	}

	return $tables;
}


/**
 * Calculer et Inserer le JS qui gére le tri par Drag&Drop dans le bon contexte (la page ?exec=xxxxx)
 *
 * @param	array $flux	Données du pipeline 
 * @return	array 		Données du pipeline 
 */
function rang_recuperer_fond($flux) {
	
	$tables_objets_selectionnes = lire_config('rang/rang_objets');

	
	if (isset($tables_objets_selectionnes) AND !empty($tables_objets_selectionnes)) {
		/* Gestion du contexte : dans quelle page insérer le JS */
		$exec 		= _request('exec');
		$contextes	= pipeline('rang_declarer_contexte', array('rubrique', 'groupe_mots', 'mots'));
		$sources	= rang_get_sources();

		if ( in_array($exec, $contextes)
			AND in_array($flux['args']['fond'], $sources) 
			AND strpos($flux['data']['texte'], 'liste-objets') // cette derniere condition n'est peut etre pas utile, voire contraignante pour ordonner autre chose qu'une liste d'objet ?
		) {
			// recuperer les paramètres pour le calcul du JS correspondant
			preg_match('/liste-objets\s([A-Za-z]+)/', $flux['data']['texte'], $result);
			$type_objet = $result[1];

			$nom_objet  = table_objet($type_objet);

			// récupérer le type de parent…
			$table_objet = table_objet_sql($type_objet);
			$table = lister_tables_objets_sql($table_objet);
			$type_parent = $table['parent']['type'];

			// …puis l'id_parent
			$id = id_table_objet($type_parent);
			$id_parent = $flux['args']['contexte'][$id];

			// Calcul du JS à insérer avec les paramètres
			$ajout_script = recuperer_fond('prive/squelettes/inclure/rang', array(  'suffixe_pagination' => $nom_objet, 
																					'objet' => $type_objet, 
																					'id_parent' => $id_parent ));

			// et hop, on insère le JS calculé
			$flux['data']['texte'] = str_replace('</table>', '</table>'. $ajout_script, $flux['data']['texte']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition pour le classer l'objet quand on le publie
 * @param	array $flux	Données du pipeline 
 * @return	array 		Données du pipeline 
 */
function rang_pre_edition($flux) {

	if ($flux['args']['action']=='instituer' && lire_config('rang/rang_max')) {
		$rang_objets	= rtrim(lire_config('rang/rang_objets'), ',');
		$liste_objets	= explode(',', $rang_objets);
		$table			= $flux['args']['table'];

		// cas des objets avec statut
		if (in_array($table, $liste_objets)) {
			$id_objet	= $flux['args']['id_objet'];
			
			// cas des objets avec statut
			if (isset($flux['data']['statut']) && $flux['data']['statut']=='publie') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
			// cas des mots clés
			if ($table == 'spip_mots') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
		}
	}
	return $flux;
}