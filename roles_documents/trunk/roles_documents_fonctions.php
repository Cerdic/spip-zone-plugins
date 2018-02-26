<?php
/**
 * Fonctions utiles au plugin Rôles de documents
 *
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Roles_documents\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Lister les rôles de documents attribués à un objet
 * 
 * @note
 * l'API des rôles permet de lister les rôles attribués pour un document précis,
 * mais pas pour TOUS les documents liés à l'objet.
 * Ex. pas possible : roles_presents_sur_id('*', 'document', $objet, $id_objet, 'document')
 *
 * @see
 * roles_presents_sur_id()
 * roles_presents_liaisons()
 * 
 * @param string $objet
 *     Type d'objet lié
 * @param integer $id_objet
 *     Identifiant de l'objet lié
 * @param mixed $logos
 *     true pour filtrer les rôles de logos
 *     false pour filtrer les rôles hors logos
 * @return array
 *     Tableau linéaire avec les rôles
 */
function roles_presents_sur_document($objet, $id_objet, $logos = null) {
	static $done = array();

	// Stocker le résultat
	$hash = "$objet-$id_objet";
	if (isset($done[$hash])) {
		return $done[$hash];
	}

	// Pas de rôles sur ces objets, on sort
	$roles = roles_presents('document', $objet);
	if (!$roles) {
		return $done['hash'] = false;
	}

	// On récupère les rôles
	$res = sql_allfetsel(
		"distinct(role)",
		'spip_documents_liens',
		array(
			'objet=' . sql_quote($objet),
			'id_objet=' . intval($id_objet),
		)
	);
	$roles_presents = array_column($res, 'role');

	// On filtre éventuellement les rôles de logos
	if (is_bool($logos)) {
		$roles_presents = filtrer_roles_logos($roles_presents, $logos);
	}

	return $done[$hash] = $roles_presents;
}


/**
 * Filtrer une liste de rôles de documents pour inclure ou exclure les logos
 *
 * @param array $roles
 *     Tableau associatif [$role => titre]
 * @param boolean $logo
 *     true (défaut) :  uniquement les rôles de logos
 *     false (défaut) : uniquement les rôles non logos
 * @return array Tableau associatif rôle => titre
 */
function filtrer_roles_logos($roles, $logos = true) {

	// Uniquement les logos
	$roles_logos = array_filter($roles, function($v){
		return substr($v, 0, 4) === 'logo';
	});

	// On filtre
	if ($logos) {
		$roles = $roles_logos;
	} else {
		$roles = array_diff($roles, $roles_logos);
	}

	return $roles;
}