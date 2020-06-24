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

// Balises et critères
include_spip('public/roles_documents');

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
 *     Numéro de l'objet lié
 * @param integer $id_document
 *     Numéro d'un document pour renvoyer les rôles de ce document précis
 * @param null|bool|string $principaux
 *     null : ne pas filtrer les rôles principaux
 *     true : ne renvoyer que les rôles principaux
 *     false ou '' : exclure les rôles principaux
  * @return array
 *     Tableau associatif avec 3 clés
 *     - possibles : tous les rôles possibles
 *     - attribues : ceux attribués
 *     - attribuables : ceux non attribues
 */
function roles_documents_presents_sur_objet($objet, $id_objet, $id_document = 0, $principaux = null) {
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
	// Fallback rôles principaux si non déclarés
	if (empty($infos_roles['roles']['principaux'])) {
		$infos_roles['roles']['principaux'] = array('logo', 'logo_survol');
	}

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

	// On trie les rôles attribués dans le même ordre que les rôles possibles,
	// et non dans l'ordre de création des liens dans la base.
	$roles_attribues_ordonnes = array();
	foreach ($roles_possibles as $role) {
		if (in_array($role, $roles_attribues)) {
			$roles_attribues_ordonnes[] = $role;
		}
	}
	$roles_attribues = $roles_attribues_ordonnes;

	// Liste des rôles non attribués
	$roles_attribuables = array_diff($roles_possibles, $roles_attribues);

	// On filtre éventuellement les rôles principaux (=logos)
	// Note : array_values pour remettre les bonnes clés
	if (
		!is_null($principaux)
		and $roles_principaux = $infos_roles['roles']['principaux']
	){
		$filtrer = ($principaux ? 'array_intersect' : 'array_diff');
		$roles_possibles = array_values($filtrer($roles_possibles, $roles_principaux));
		$roles_attribues = array_values($filtrer($roles_attribues, $roles_principaux));
		$roles_attribuables = array_values($filtrer($roles_attribuables, $roles_principaux));
	}

	// On retourne le détail
	$roles = array(
		'possibles'    => $roles_possibles,
		'attribues'    => $roles_attribues,
		'attribuables' => $roles_attribuables,
	);

	return $done[$hash] = $roles;
}

/**
 * Lister tous les rôles de documents déclarés, tous objets confondus
 * 
 * @return array
 */
function roles_documents_presents() {

	static $roles_documents = null;
	if (is_array($roles_documents)) {
		return $roles_documents;
	}

	$roles_documents = array();
	if (
		$roles_presents = roles_presents('document')
	) {
		foreach($roles_presents['roles'] as $objet) {
			$roles_documents = array_merge($roles_documents, $objet['choix']);
		}
		$roles_documents = array_unique($roles_documents);
	}

	return $roles_documents;
}
