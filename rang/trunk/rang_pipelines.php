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

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Declaration du champ Rang sur les objets sélectionnés
 *
 * @param array $tables
 * @return array
 */
function rang_declarer_tables_objets_sql($tables){
	include_spip('inc/config');

	$rang_objets = rtrim(lire_config('rang_objets'), ',');
	$liste_objets = explode(',', $rang_objets);

	foreach ($liste_objets as  $table) {
		$tables[$table]['field']['rang'] = "SMALLINT NOT NULL";
	}
	return $tables;
}

/**
 * Inserer le JS qui gére le tri par Drag&Drop dans la page ?exec=xxxxx
 *
 * @param array $flux
 * @return array
 */
function rang_recuperer_fond($flux){

	$exec 		= _request('exec');

	
	// Gestion du contexte i.e. page ?exec=xxxx 
	// Par défaut, on peut toujours trier dans une rubrique.
	$contextes	= array(0 => 'rubrique'); 

	// Ajouter automatiquement un contexte
	// pour les objets sans rubrique, on ajoute le contexte ?exec=objet
	$objets_selectionnes = lire_config('rang_objets');
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


			// particularité des objets historiques
			if ($objet == 'art') {
					$objet = 'articles';
					$suffixe_pagination = 'art';
			}

			$id_rubrique = $flux['args']['contexte']['id_rubrique'];

			// Debug
			// echo 'objet : '.$objet.'<br>';
			// echo 'fond : '.$flux['args']['fond'].'<br>';
			// echo 'source : '.$flux['data']['source'].'<br>&nbsp;<br>';
			//echo bel_env($flux);
			
			$ajout_script = recuperer_fond('prive/squelettes/inclure/rang', array('suffixe_pagination' => $suffixe_pagination, 'objet' => $objet, 'id_rubrique' => $id_rubrique ));
			$flux['data']['texte'] = str_replace('</table>', '</table>'. $ajout_script, $flux['data']['texte']);
		
	}

	return $flux;
}

/**
 * construction des chemins de sources vers les listes des objets sélectionnés
 * ce tableau sera ensuite comparé à la valeur $flux['data']['source'] fourni par le pipeline recuperer_fond()
 *
 * @return array
 *     les chemins sources vers les listes où activer Rang
 **/

function rang_get_sources() {
	include_spip('inc/config');
	// mettre en cache le tableau calculé
	static $sources;
	if(is_array($sources)){
		return $sources;
	}
	
	$sources = array();
	$objets_selectionnes = lire_config('rang_objets');
	$objets=explode(',',$objets_selectionnes);

	foreach ($objets as $value) {
		$objet = table_objet($value);
		if (!empty($value)) {
			$source = find_in_path('prive/objets/liste/'.$objet.'.html');
			$sources[] = $source;
		}
	}
	return $sources;
}