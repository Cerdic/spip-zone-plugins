<?php
/**
 * Utilisations de pipelines par Crop Image
 *
 * @plugin     Crop Image
 * @copyright  2017
 * @author     tofulm
 * @licence    GNU/GPL
 * @package    SPIP\Jcrop\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Ajouter une action "recadrer" sur les documents
 *
 * @pipeline editer_document_actions
 * @param  array $flux Données du pipeline
 * @return array	   Données du pipeline
 */
function jcrop_document_desc_actions($flux) {

	$flux['data'] .= recuperer_fond( 'prive/squelettes/inclure/recadrer_image', $flux['args']);
	return $flux;
}
