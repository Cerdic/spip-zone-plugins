<?php

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
 * Cette autorisation est toujours à la base de toutes les autres autorisations du plugin.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('defaut');
}

/**
 * Autorisation d'affichage du menu d'accès à la configuration du noiZetier.
 * Il faut être autorisé à configurer le noiZetier.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_menu_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('noizetier');
}

/**
 * Autorisation d'accès à la page de configuration du plugin noiZetier.
 * Par défaut, seuls les webmestres sont autorisés à modifier la configuration du noiZetier
 * et en particulier la liste des pages accessibles par les utilisateurs.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre');
}

/**
 * Autorisation de configuration d'une page ou d'un objet du noiZetier.
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
 * @param $opt
 *      Permet de passer les identifiants de la page ou de l'objet concerné sous la forme
 * 		d'un tableau associatif dont les index sont	'page' ou 'objet' et 'id_objet'.
 *
 * @return bool
 */
function autoriser_noizetier_configurerpage_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if (autoriser('noizetier')
	and (is_array($opt) and !empty($opt))) {
		if (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
		and (!$configuration['composition']
			or (defined('_DIR_PLUGIN_COMPOSITIONS')
			and $configuration['composition']
			and (($configuration['est_page_objet'] == 'non')
				or (($configuration['est_page_objet'] == 'oui') and noizetier_page_composition_activee($configuration['type'])))))) {
			// Cas d'une page
			$autoriser = true;
		} else {
			if (!empty($opt['objet']) and isset($opt['id_objet']) and intval($opt['id_objet'])
			and ($configuration = noizetier_objet_informer($opt['objet'], $opt['id_objet']))
			and noizetier_objet_type_active($opt['objet'])) {
				// Cas d'un objet dont le type est activé
				$autoriser = true;
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
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_activercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if (autoriser('noizetier')
	and (is_array($opt) and !empty($opt))
	and (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
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
 * d'une page source.
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
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_creercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if (autoriser('noizetier')
	and (is_array($opt) and !empty($opt))
	and (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
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
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_supprimercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if (autoriser('noizetier')
	and (is_array($opt) and !empty($opt))
	and (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
	and $configuration['composition'])
	and ($configuration['est_virtuelle'] == 'oui')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de modification d'une composition virtuelle du noiZetier.
 * Il faut :
 * - que la composition existe bien et soit virtuelle.
 * - et être autorisé à créer une composition virtuelle du noiZetier à partir d'une page source qui
 *   coincide avec le type de la composition virtuelle.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_modifiercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if ((is_array($opt) and !empty($opt))
	and (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
	and ($configuration['est_virtuelle'] == 'oui'))
	and autoriser('creercomposition', 'noizetier', $id, $qui,  array('page' => $configuration['type']))) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de copie d'une composition pour créer une composition virtuelle du noiZetier
 * possédant les mêmes caractéristiques.
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - que la page source existe et soit une composition
 * - et que le plugin Compositions soit bien activé.
 *
 * @param $faire
 * @param $type
 * @param $id
 * @param $qui
 * @param $opt
 *
 * @return bool
 */
function autoriser_noizetier_dupliquercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	include_spip('noizetier_fonctions');
	if (autoriser('noizetier')
	and (is_array($opt) and !empty($opt))
	and (!empty($opt['page']) and ($configuration = noizetier_page_informer($opt['page']))
	and $configuration['composition'])
	and defined('_DIR_PLUGIN_COMPOSITIONS')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation pour configurer les noisettes d'un contenu précis
 *
 * Avoir le droit de modifier l'objet et avoir configuré cet objet pour pouvoir personnaliser ses noisettes
 **/
function autoriser_configurernoisettes_dist($faire, $type, $id, $qui, $opt) {
	include_spip('inc/config');
	$liste_objets_noisettes = lire_config('noizetier/objets_noisettes', array());

	return
		autoriser('modifier', $type, $id)
		and in_array(table_objet_sql($type), $liste_objets_noisettes);
}
