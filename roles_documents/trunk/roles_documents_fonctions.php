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
 * Lister les rôles de documents distincts pour un objet : ceux possibles, ceux attribués et non attribués
 * 
 * @note
 * Vaguement basé sur la fonction roles_presents_sur_id() de l'API, sauf qu'on retourne des rôles uniques,
 * et on fait le détail entre ceux atribués et non attribués.
 * 
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
 * @param mixed $principaux
 *     true : ne renvoyer que les rôles principaux (logos)
 *     false : exclure les rôles principaux (logos)
 * @return array
 *     Tableau associatif avec 3 clés
 *     - possibles : tous les rôles possibles
 *     - attribués : ceux attribués
 *     - non_attribues : ceux non attribues
 */
function roles_presents_sur_document($objet, $id_objet, $principaux = null) {
	static $done = array();

	// Stocker le résultat
	$hash = "$objet-$id_objet-$principaux";
	if (isset($done[$hash])) {
		return $done[$hash];
	}

	// Liste de tous les rôles possibles
	// Si pas de rôles sur ces objets, on sort
	$infos_roles = roles_presents('document', $objet);
	if (!$infos_roles) {
		return $done['hash'] = false;
	}
	$roles_possibles = $infos_roles['roles']['choix'];

	// Liste des rôles attribués
	$res = sql_allfetsel(
		'distinct(role)',
		'spip_documents_liens',
		array(
			'objet=' . sql_quote($objet),
			'id_objet=' . intval($id_objet),
			"role!=''",
		)
	);
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

	// On renvoie le détail
	$roles = array(
		'possibles'     => $roles_possibles,
		'attribues'     => $roles_attribues,
		'non_attribues' => $roles_non_attribues,
	);

	return $done[$hash] = $roles;
}