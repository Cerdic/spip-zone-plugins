<?php
/**
 * Utilisations de pipelines
 *
 * @package SPIP\Xiti\Pipelines
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Ajoute le formulaire de lien à un niveau deux sur les objets configurés
 *
 * @pipeline affiche_milieu
 * @param array $flux
 * @return array
 */
function xiti_affiche_milieu($flux) {
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		and $en_cours['edition'] !== true // page visu
		and $type = $en_cours['type']
		and $id_table_objet = $en_cours['id_table_objet']
		and isset($flux['args'][$id_table_objet])
		and ($id = intval($flux['args'][$id_table_objet]))
		and (autoriser('lierxitiniveau', $type, $id))
	) {
		$texte = recuperer_fond('prive/squelettes/inclure/xiti_lier', array('id_objet' => $id, 'objet' => $type));
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}
