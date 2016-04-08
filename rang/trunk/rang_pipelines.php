<?php
/**
 * Utilisations de pipelines par Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Pierre Miquel
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function rang_recuperer_fond($flux){

	$exec 		= _request('exec');

	// Gestion des contexte i.e. page ?exec=xxxx 
	// dans le futur, on doit pouvoir ajouter d'autres contextes 
	// (mots-clefs, documents, 
	// listes hors rubrique pour les objets sans rubrique)
	// contextes spécifiques à certains plugins (ex : pages uniques, etc.)
	$contextes	= array(0=> 'rubrique'); 
	$sources	= get_sources();
	

	// faire archi gaffe à prendre le bon flux....pfiou compliqué :)
	if ( in_array($exec, $contextes) AND 
		 in_array($flux['data']['source'], $sources) AND 
		 strpos($flux['data']['texte'], 'pagination_liste')) {
			

			// récupérer le type de l'objet, quelle que soit le contexte
			preg_match('/pagination_liste_([A-Za-z]+)/', $flux['data']['texte'], $result);
			$objet = $result[1];

			// particularité des objets historiques
			if ($objet == 'art') {
					$prefixe = 'art';
					$objet = 'articles';
				}


			$id_rubrique = $flux['args']['contexte']['id_rubrique'];

			// Debug
			// echo 'objet : '.$objet.'<br>';
			// echo 'fond : '.$flux['args']['fond'].'<br>';
			// echo 'source : '.$flux['data']['source'].'<br>&nbsp;<br>';
			//echo bel_env($flux);
			
			$ajout_script = recuperer_fond('prive/squelettes/inclure/rang', array('prefixe' => $prefixe, 'objet' => $objet, 'id_rubrique' => $id_rubrique ));
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

function get_sources() {

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