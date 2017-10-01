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

/**
 * Declaration du champ Rang sur les objets sélectionnés
 * + Definir la relation a l‘objet parent dans la declaration de l‘objet (en attendant https://core.spip.net/issues/3844)
 *
 * @param array $tables
 * @return array
 */
function rang_declarer_tables_objets_sql($tables) {
	include_spip('inc/config');

	/* Declaration du champ Rang sur les objets sélectionnés */
	$rang_objets = rtrim(lire_config('rang/rang_objets'), ',');
	$liste_objets = explode(',', $rang_objets);

	foreach ($liste_objets as  $table) {
		$tables[$table]['field']['rang'] = "SMALLINT NOT NULL";
	}
	
	/* Definir la relation a l‘objet parent dans la declaration de l‘objet (en attendant https://core.spip.net/issues/3844) */
	// pour les articles
	$tables['spip_articles']['parent'] = array('type' => 'rubrique', 'champ' => 'id_rubrique');

	// pour les rubriques
	$tables['spip_rubriques']['parent'] = array('type' => 'rubrique', 'champ' => 'id_rubrique');

	//pour les mots-clés
	$tables['spip_mots']['parent'] = array('type' => 'groupe_mot', 'champ' => 'id_groupe');

	return $tables;
}

/**
 * Inserer le JS qui gére le tri par Drag&Drop dans la page ?exec=xxxxx
 *
 * @param array $flux
 * @return array
 */
function rang_recuperer_fond($flux) {

	$exec 		= _request('exec');
	
	// Gestion du contexte i.e. page ?exec=xxxx 
	// Par défaut, on peut toujours trier dans une rubrique où dans un groupe de mot
	$contextes	= array(0 => 'rubrique', 1 => 'groupe_mots'); 

	// Ajouter automatiquement un contexte
	// pour les objets sans rubrique, on ajoute le contexte ?exec=objet
	include_spip('inc/config');
	$objets_selectionnes = lire_config('rang/rang_objets');
	$liste = lister_tables_objets_sql();
	foreach ($liste as $key => $value) {
		if ($value['editable'] == 'oui' AND !isset($value['field']['id_rubrique'])) {
			$objet = table_objet($key);
			if (strpos($objets_selectionnes,$objet)) {
				$contextes[] = $objet;
			}
		}
	}

	// dans le futur, on doit pouvoir ajouter d'autres contextes 
	// -> mots-clefs, 
	// -> contextes spécifiques à certains plugins (ex : pages uniques, Albums, etc.)



	// faire archi gaffe à prendre le bon flux....pfiou compliqué :)
	$sources	= rang_get_sources();

	if ( in_array($exec, $contextes) AND 
		 in_array($flux['data']['source'], $sources) AND 
		 strpos($flux['data']['texte'], 'pagination_liste')) {

			// récupérer le type de l'objet, quelle que soit le contexte
			preg_match('/pagination_liste_([A-Za-z]+)/', $flux['data']['texte'], $result);
			$objet = $result[1];
			$suffixe_pagination = table_objet($objet);

			$id_parent = $flux['args']['contexte']['id_rubrique'];

			// particularité des objets historiques
			switch ($objet) {
				case 'art':
					$objet = 'articles';
					$suffixe_pagination = 'art';
					break;
				case 'mots':
					$objet = 'mots';
					$suffixe_pagination = 'mot';
					$id_parent = $flux['args']['contexte']['id_groupe'];
					break;
				default:
					$id_parent = $flux['args']['contexte']['id_rubrique'];
					break;
			}
			
			$ajout_script = recuperer_fond('prive/squelettes/inclure/rang', array('suffixe_pagination' => $suffixe_pagination, 'objet' => $objet, 'id_parent' => $id_parent ));
			$flux['data']['texte'] = str_replace('</table>', '</table>'. $ajout_script, $flux['data']['texte']);
		
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition pour le classer l'objet quand on le publie
 * @param array $flux
 * @return array
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