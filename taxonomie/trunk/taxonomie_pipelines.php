<?php
/**
 * Utilisations de pipelines par Taxon
 *
 * @plugin     Taxon
 * @copyright  2014
 * @author     _Eric_
 * @licence    GNU/GPL
 * @package    SPIP\Taxon\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

/**
 * Surcharge de l'action modifier sur un taxon:
 * - positionner l'indicateur d'édition à oui afin que le taxon soit préservé lors d'un éventuel
 * rechargement du règne
 *
 * @param array $flux
 * @return array
 *
**/
function taxonomie_pre_edition($flux) {

	$table = $flux['args']['table'];
	$id = intval($flux['args']['id_objet']);
	$action = $flux['args']['action'];

	// Traitements particuliers de l'objet taxon quand celui-ci est modfifié manuellement
	if (($table == 'spip_taxons')
	AND ($id)) {

		// Modification d'un des champs éditables du taxon
		if ($action == 'modifier') {
			// -- On positionne l'indicateur d'édition à oui, ce qui permettra d'éviter lors
			//    d'un rechargement du règne de perdre les modifications manuelles
			$flux['data']['edite'] = 'oui';
		}
	}

	return $flux;
}


?>