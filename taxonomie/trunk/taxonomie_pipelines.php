<?php
/**
 * Ce fichier contient les cas d'utilisation de certains pipelines par le plugin Taxonomie.
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

/**
 * Surcharge l'action `modifier` d'un taxon en positionnant l'indicateur d'édition à `oui`
 * afin que les modifications manuelles du taxon soient préservées lors d'un prochain
 * rechargement du règne.
 *
 * @pipeline pre_edition
 * @package SPIP\TAXONOMIE\TAXON
 *
 * @param array		$flux
 * 		Données du pipeline fournie en entrée (chaque pipeline possède une structure de donnée propre).
 *
 * @return array
 * 		Données du pipeline modifiées pour refléter le traitement.
 *
**/
function taxonomie_pre_edition($flux) {

	$table = $flux['args']['table'];
	$id = intval($flux['args']['id_objet']);
	$action = $flux['args']['action'];

	// Traitements particuliers de l'objet taxon quand celui-ci est modifié manuellement
	if (($table == 'spip_taxons') and $id) {
		// Modification d'un des champs éditables du taxon
		if ($action == 'modifier') {
			// -- On positionne l'indicateur d'édition à oui, ce qui permettra d'éviter lors
			//    d'un rechargement du règne de perdre les modifications manuelles pour les taxons importés
			//    via le fichier ITIS.
			//    On met aussi à jour les taxons créés lors d'un ajout d'une espèce et donc non importés,
			//    même si cet indicateur n'a que peu d'intérêt dans ce cas.
			$flux['data']['edite'] = 'oui';
		}
	}

	return $flux;
}
