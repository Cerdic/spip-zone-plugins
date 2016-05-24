<?php
/**
 * Pipelines du plugin Analyclick
 *
 * @plugin     anaclic
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 */

/**
 * Ajouter un lien vers les stats sur chaque document
 *
 * @pipeline document_desc_actions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function anaclic_document_desc_actions($flux) {

	$url_stats = generer_url_ecrire(
		'statistiques_anaclic_v3',
		'id_document=' . intval($flux['args']['id_document'])
	);

	$flux['data'] .= recuperer_fond(
		'prive/squelettes/inclure/lien_stats',
		$flux['args']
	);

	return $flux;
}
