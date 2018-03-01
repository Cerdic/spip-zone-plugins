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
 * Lister les rôles de documents pour un objet : ceux possibles, ceux attribués et non attribués.
 *
 * - Soit les rôles uniques des documents pour un type d'objet (si $id_document == 0)
 * - Soit tous les rôles entre un document et un objet (si $id_document)
 *
 * @note
 * Vaguement basé sur la fonction roles_presents_sur_id() de l'API.
 *
 * @see
 * roles_presents_sur_id()
 * roles_presents_liaisons()
 *
 * @param string $objet
 *     Type d'objet lié
 * @param integer $id_objet
 *     Identifiant de l'objet lié
 * @param integer $id_document
 *     Identifiant d'un document pour renvoyer les rôles de ce document précis
 * @param bool|mixed $principaux
 *     true : ne renvoyer que les rôles principaux (logos)
 *     false : exclure les rôles principaux (logos)
 * @return array
 *     Tableau associatif avec 3 clés
 *     - possibles : tous les rôles possibles
 *     - attribués : ceux attribués
 *     - non_attribues : ceux non attribues
 */
function roles_documents_presents_sur_objet($objet, $id_objet, $id_document=0, $principaux = null) {
	static $done = array();

	// Stocker le résultat
	$hash = "$id_document-$objet-$id_objet-$principaux";
	if (isset($done[$hash])) {
		return $done[$hash];
	}

	// Liste de tous les rôles possibles
	// Si aucun rôle sur cet objet, on sort
	$infos_roles = roles_presents('document', $objet);
	if (!$infos_roles) {
		return $done['hash'] = false;
	}
	$roles_possibles = $infos_roles['roles']['choix'];

	// Liste des rôles attribués
	$select = 'distinct(role)';
	$where = array(
		'objet = ' . sql_quote($objet),
		'id_objet = ' . intval($id_objet),
		"role != ''",
	);
	if ($id_document) {
		$select = 'role';
		$where[] = 'id_document=' . intval($id_document);
	}
	$res = sql_allfetsel($select, 'spip_documents_liens', $where);
	$roles_attribues = array_column($res, 'role');

	// Liste des rôles non attribués
	$roles_non_attribues = array_diff($roles_possibles, $roles_attribues);

	// On filtre éventuellement les rôles principaux (=logos)
	if (!is_null($principaux)
		and !empty($infos_roles['roles']['principaux'])
		and $roles_principaux = $infos_roles['roles']['principaux']
	){
		$filtrer = ($principaux ? 'array_intersect' : 'array_diff');
		$roles_possibles = $filtrer($roles_possibles, $roles_principaux);
		$roles_attribues = $filtrer($roles_attribues, $roles_principaux);
		$roles_non_attribues = $filtrer($roles_non_attribues, $roles_principaux);
	}

	// On retourne le détail
	$roles = array(
		'possibles'     => $roles_possibles,
		'attribues'     => $roles_attribues,
		'non_attribues' => $roles_non_attribues,
	);

	return $done[$hash] = $roles;
}