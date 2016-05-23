<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline affiche_milieu (SPIP)
 *
 * @param array $flux
 * @return array
 */
function rezosocios_affiche_milieu($flux) {
	// si on est sur une page ou il faut inserer les réseaux socios...
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		and $en_cours['edition']!==true // page visu
		and $type = $en_cours['type']
		and $id_table_objet = $en_cours['id_table_objet']
		and ($id = intval($flux['args'][$id_table_objet]))) {
		$texte = recuperer_fond(
			'prive/objets/editer/liens',
			array(
				'table_source'=>'rezosocios',
				'objet'=>$type,
				'id_objet'=>$id,
			)
		);
		if ($p=strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}
	return $flux;
}
