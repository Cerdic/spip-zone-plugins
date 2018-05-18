<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction appelée par le pipeline
 *
 */
function noizetier_autoriser() {}

/**
 * Autorisation minimale d'accès à toutes les pages du noiZetier sauf celle de configuration
 * du plugin lui-même.
 * Par défaut, seuls les administrateurs complets sont autorisés à utiliser le noiZetier.
 * Cette autorisation est à la base de la plupart des autres autorisations du plugin.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_dist($faire, $type, $id, $qui, $options) {
	return autoriser('defaut');
}

/**
 * Autorisation d'affichage du menu d'accès à la configuration du noiZetier (page=noizetier_pages).
 * Il faut être autorisé à configurer le noiZetier.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_menu_dist($faire, $type, $id, $qui, $options) {
	return autoriser('noizetier');
}

/**
 * Autorisation d'accès à la page de configuration du plugin noiZetier (page=configurer_noizetier).
 * Par défaut, seuls les webmestres sont autorisés à modifier la configuration du noiZetier
 * et en particulier la liste des pages accessibles par les autres utilisateurs.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $options) {
	return autoriser('webmestre');
}

/**
 * Autorisation de configuration d'une page ou d'un objet du noiZetier (page=noizetier_page).
 * La configuration consiste dans tous les cas à manipuler les noisettes des divers blocs de la page et
 * si la page est une composition virtuelle à éditer ses caractéristiques (page=noizetier_page_edit).
 * Il faut :
 * - être autorisé à configurer le noiZetier,
 * - que la page ou l'objet existe et soit bien accessible pour le noiZetier (i.e. plugin Compositions actif
 *   si on est en présence d'une composition),
 * - et que :
 *   - si on est en présence d'un objet, son type soit bien activé dans la configuration,
 *   - ou que si on est en présence d'une composition basée sur un type d'objet, celui-ci
 *     soit bien activé.
 *
 * @param $faire
 * 		L'action se nomme configurerpage
 * @param $type
 * 		Le type est toujours noizetier.
 * @param $id
 * 		Inutilisé car l'identifiant représente soit la page soit l'objet
 * @param $qui
 * 		Inutilisé, l'API utilise l'auteur connecté
 * @param $options
 *      Permet de passer les identifiants de la page ou de l'objet concerné sous la forme
 * 		d'un tableau associatif dont les index sont	'page' ou 'objet' et 'id_objet'.
 *
 * @return bool
 */
function autoriser_noizetier_configurerpage_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	include_spip('inc/noizetier_page');
	include_spip('inc/noizetier_objet');
	if (autoriser('noizetier')
	and (is_array($options) and !empty($options))) {
		if (!empty($options['page']) and ($configuration = noizetier_page_lire($options['page'], false))
		and (!$configuration['composition']
			or (defined('_DIR_PLUGIN_COMPOSITIONS')
			and $configuration['composition']
			and (($configuration['est_page_objet'] == 'non')
				or (($configuration['est_page_objet'] == 'oui') and noizetier_page_composition_activee($configuration['type'])))))) {
			// Cas d'une page
			$autoriser = true;
		} else {
			if (!empty($options['objet']) and isset($options['id_objet']) and ($id_objet = intval($options['id_objet']))
			and noizetier_objet_type_active($options['objet'])) {
				// Cas d'un objet dont le type est activé : on vérifie juste que l'objet existe bien
				include_spip('base/objets');
				if (($from = table_objet_sql($options['objet']))
				and ($id_table_objet = id_table_objet($options['objet']))
				and sql_countsel($from, array($id_table_objet . '=' . $id_objet))) {
					$autoriser = true;
				}
			}
		}
	}

	return $autoriser;
}

/**
 * Autorisation d'activation des compositions sur un type d'objet. Permet de lancer l'action depuis le noiZetier
 * sans passer par la configuration du plugin Compositions.
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - que la page source existe et ne soit pas une composition
 * - que le plugin Compositions soit bien activé
 * - que l'utilisateur soit autorisé à configurer le plugin Compositions
 * - et que la page soit celle d'un type d'objet et que les compositions ne soient pas déjà activées.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_activercomposition_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	include_spip('inc/noizetier_page');
	if (autoriser('noizetier')
	and (is_array($options) and !empty($options))
	and (!empty($options['page']) and ($configuration = noizetier_page_lire($options['page'], false))
	and !$configuration['composition'])
	and ($configuration['est_page_objet'] == 'oui')
	and !noizetier_page_composition_activee($configuration['type'])
	and autoriser('configurer', 'compositions')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de création d'une composition virtuelle du noiZetier à partir
 * d'une page source (page=noizetier_page_edit).
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - que la page source existe et ne soit pas une composition
 * - que le plugin Compositions soit bien activé
 * - et que si la page est celle d'un type d'objet, que les compositions soient bien activées.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_creercomposition_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	include_spip('inc/noizetier_page');
	if (autoriser('noizetier')
	and (is_array($options) and !empty($options))
	and (!empty($options['page']) and ($configuration = noizetier_page_lire($options['page'], false))
	and !$configuration['composition'])
	and (defined('_DIR_PLUGIN_COMPOSITIONS'))
	and (($configuration['est_page_objet'] == 'non')
		or (($configuration['est_page_objet'] == 'oui') and noizetier_page_composition_activee($configuration['type'])))) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de suppression d'une composition virtuelle du noiZetier.
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - et que la page existe toujours et soit bien une composition virtuelle.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_supprimercomposition_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	include_spip('inc/noizetier_page');
	if (autoriser('noizetier')
	and (is_array($options) and !empty($options))
	and (!empty($options['page']) and ($configuration = noizetier_page_lire($options['page'], false))
	and $configuration['composition'])
	and ($configuration['est_virtuelle'] == 'oui')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de modification certains paramètres d'une page (page=noizetier_page_edit). Suivant que la page est
 * une page explicite ou une composition virtuelle la liste des paramètres éditables varie.
 * Il faut :
 * - que .
 * - et être autorisé à créer une composition virtuelle du noiZetier à partir d'une page source qui
 *   coincide avec le type de la composition virtuelle.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_modifierpage_dist($faire, $type, $id, $qui, $options) {

	$autoriser = autoriser('configurerpage', 'noizetier', 0, '', $options);

	return $autoriser;
}

/**
 * Autorisation de copie d'une composition pour créer une composition virtuelle du noiZetier
 * possédant les mêmes caractéristiques (page=noizetier_page_edit).
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - que la page source existe et soit une composition
 * - et que le plugin Compositions soit bien activé.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_dupliquercomposition_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	include_spip('inc/noizetier_page');
	if (autoriser('noizetier')
	and (is_array($options) and !empty($options))
	and (!empty($options['page']) and ($configuration = noizetier_page_lire($options['page'], false))
	and $configuration['composition'])
	and defined('_DIR_PLUGIN_COMPOSITIONS')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation d'édition d'une noisette déjà ajoutée dans le bloc d'une page ou d'un contenu
 * (page=noisette_edit).
 * Il faut :
 * - que la noisette existe bien,
 * - et être autorisé à configurer la page ou le contenu auquel est rattachée la noisette
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $options
 *
 * @return bool
 */
function autoriser_noizetier_editernoisette_dist($faire, $type, $id, $qui, $options) {
	$autoriser = false;

	if ($id_noisette = intval($id)) {
		// On vérifie que la noisette existe bien et on récupère sa localisation (page ou objet) afin d'appeler
		// l'autorisation de configurer cette page ou objet.
		$select = array('type', 'composition', 'objet', 'id_objet');
		$where = array('id_noisette=' . $id_noisette);
		$noisette = sql_fetsel($select, 'spip_noisettes', $where);
		if ($noisette) {
			if ($noisette['objet'] and intval($noisette['id_objet'])) {
				$options['objet'] = $noisette['objet'];
				$options['id_objet'] = $noisette['id_objet'];
			} else {
				$options['page'] = $noisette['composition']
					? $noisette['type'] . '-' . $noisette['composition']
					: $noisette['type'];
			}
			if (autoriser('configurerpage', 'noizetier', 0, '', $options)) {
				$autoriser = true;
			}
		}
	}

	return $autoriser;
}
