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


/**
 * Surcharge l'action `instituer` d'un taxon.
 * Si une espèce est instituée à publié, alors ses ascendants de type espèce non encore publiés sont automatiquement
 * publiés.
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
function taxonomie_post_edition($flux) {

	if (isset($flux['args']['table'], $flux['args']['id_objet'], $flux['args']['action'])) {
		$table = $flux['args']['table'];
		$id = intval($flux['args']['id_objet']);
		$action = $flux['args']['action'];

		// Traitements particuliers de l'objet taxon quand celui-ci est institué manuellement
		if (($table == 'spip_taxons') and $id) {
			// Instituer : on ne peut instituer qu'une espèce dont aucun enfant n'est publié. Il est donc inutile de
			// considérer ces cas.
			if ($action == 'instituer') {
				// On vérifie qu'on institue l'espèce de 'prop' à 'publie'. Si c'est le cas, alors on vérifie
				// qu'il est aussi nécessaire d'instituer à 'publie' les enfants de type espèce encore à prop.
				$statut_nouveau = $flux['data']['statut'];
				$statut_ancien = $flux['args']['statut_ancien'];
				if (($statut_ancien == 'prop') and ($statut_nouveau == 'publie')) {
					// On récupère le TSN et le TSN parent de l'espèce concernée.
					$from = 'spip_taxons';
					$select = array('tsn', 'tsn_parent');
					$where = array("id_taxon=$id");
					$taxon = sql_fetsel($select, $from, $where);

					// On récupère les ascendants de type espèce de l'espèce concernée si ils existent.
					include_spip('taxonomie_fonctions');
					$ascendance = taxon_informer_ascendance($id, $taxon['tsn_parent'], 'ascendant');
					if ($ascendance) {
						// On publie les taxons en évitant une ré-entrance (donc sans appeler l'api objet)
						// mais en utilisant directement une mise à jour sql.
//						include_spip('action/editer_objet');
						foreach ($ascendance as $_parent) {
							if ($_parent['espece'] == 'oui') {
								if (($_parent['statut'] <> 'publie')) {
									$maj = array('statut' => $statut_nouveau, 'edite' => 'oui');
									sql_updateq($from, $maj, 'id_taxon=' . intval($_parent['id_taxon']));
//									objet_modifier('taxon', intval($_parent['id_taxon']), array('statut' => $statut_nouveau));
								}
							} else {
								// Dès que l'on est sur un taxon non espèce on peut s'arrêter vu que les ascendants sont
								// classé du parent le plus proche au plus éloigné.
								break;
							}
						}
					}
				}
			}
		}
	}

	return $flux;
}
