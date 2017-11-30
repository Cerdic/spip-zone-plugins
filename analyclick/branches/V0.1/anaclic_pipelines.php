<?php
/**
 * Pipelines du plugin Analyclick
 *
 * @plugin     anaclic
 * @copyright  2016
 * @author     Michel @ Vertige ASBL
 * @licence    GNU/GPL
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Ajouter un lien vers les stats sur chaque document
 *
 * @pipeline document_desc_actions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function anaclic_document_desc_actions($flux) {

	if (autoriser('voirstats')) {
		$url_stats = generer_url_ecrire(
			'statistiques_anaclic_v3',
			'id_document=' . intval($flux['args']['id_document'])
		);

		$flux['data'] .= recuperer_fond(
			'prive/squelettes/inclure/lien_stats',
			$flux['args']
		);
	}

	return $flux;
}
