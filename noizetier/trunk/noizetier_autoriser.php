<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Fonction appelée par le pipeline
/**
 *
 */
function noizetier_autoriser() {}

/**
 * Autorisation globale d'accès aux pages de configuration du noiZetier.
 * Cette autorisation est toujours à la base de toutes les autres autorisations du plugin.
 * Par défaut, seuls les webmestres sont autorisés à utiliser le noiZetier.
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
	return autoriser('configurer', 'noizetier', $id, $qui,  $opt);
}

/**
 * Autorisation de configuration d'une page ou d'un objet du noiZetier.
 * Il faut :
 * - être autorisé à configurer le noiZetier
 * - et que la page ou l'objet existe et soit bien accessible pour le noiZetier.
 *
 * @param $faire
 * 		Action configurer
 * @param $type
 * 		Le type est pagenoizetier, c'est à dire une page ou une composition explicite ou virtuelle.
 * @param $id
 * 		Vide
 * @param $qui
 * 		Vide, l'API remplira avec l'auteur connecté
 * @param $opt
 * 		La page ou l'objet concerné sous la forme d'un tableau associatif dont les index sont
 * 		'page' ou 'objet' et 'id_objet'.
 *
 * @return bool
 */
function autoriser_noizetier_configurerpage_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	if (autoriser('configurer', 'noizetier', $id, $qui,  $opt)
	and (is_array($opt) and !empty($opt))
	and ((!empty($opt['page']) and noizetier_page_informer($opt['page']))
		or (is_array($opt)
			and isset($opt['objet']) and isset($opt['id_objet']) and intval($opt['id_objet'])
			and noizetier_objet_informer($opt['objet'], $opt['id_objet'])))) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de création d'une composition virtuelle du noiZetier.
 * Il faut :
 * - être autorisé à configurer le noiZetier
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
function autoriser_noizetier_creercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	if (autoriser('configurer', 'noizetier', $type, $id, $qui,  $opt)
	and defined('_DIR_PLUGIN_COMPOSITIONS')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de modification d'une composition virtuelle du noiZetier.
 * Il faut :
 * - être autorisé à créer une composition virtuelle du noiZetier
 * - et que la composition virtuelle existe bien déjà.
 * Cela revient à tester :
 * - que l'on soit autorisé à configurer la composition virtuelle concernée
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
function autoriser_noizetier_modifiercomposition_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	if (autoriser('configurerpage', 'noizetier', $id, $qui,  $opt)
	and defined('_DIR_PLUGIN_COMPOSITIONS')) {
		$autoriser = true;
	}

	return $autoriser;
}

/**
 * Autorisation de copie d'une page dont le type est un objet pour créer une
 * composition virtuelle du noiZetier.
 * Il faut :
 * - être autorisé à créer une composition virtuelle du noiZetier
 * - que la page source soit celle d'un objet
 * - et qu'elle existe bien déjà.
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

	if (autoriser('creercomposition', 'noizetier', $id, $qui,  $opt)
	and (is_array($opt) and !empty($opt))
	and ((!empty($opt['page']) and $configuration = noizetier_page_informer($opt['page'])))) {

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
