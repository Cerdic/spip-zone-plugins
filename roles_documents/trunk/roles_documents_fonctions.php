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
 * Surcharge du critère `logo`
 *
 * Tout comme le critère {logo} par défaut, on permet de sélectionner tous les
 * objets qui ont un logo, quel qu'il soit, au format historique ou au format
 * document.
 *
 * Un unique paramètre optionnel permet de se restreindre à un rôle
 * particulier. Par exemple, {logo accueil} permet de sélectionner les logos
 * dont le rôle est 'logo_accueil'.
 *
 * {!logo} permet d'inverser la sélection, pour avoir les objets qui n'ont PAS
 * de logo.
 *
 * @uses lister_objets_avec_logos()
 *     Pour obtenir les éléments qui ont un logo enregistrés avec la méthode
 *     "historique".
 *
 * @param string $idb Identifiant de la boucle
 * @param array $boucles AST du squelette
 * @param Critere $crit Paramètres du critère dans cette boucle
 * @return void
 */
function critere_logo($idb, &$boucles, $crit) {

	$boucle = &$boucles[$idb];

	// On interprète le premier paramètre du critère, qui nous donne le type de
	// logo
	if (count($crit->param)) {
		$type_logo = calculer_liste(
			array_shift($crit->param),
			array(),
			$boucles,
			$boucle->id_parent
		);
		$type_logo = trim($type_logo, "'");
	}

	// Pour ajouter la jointure qu'il nous faut à la boucle, on lui donne le
	// premier alias L* qui n'est pas utilisé.
	$i = 1;
	while (isset($boucle->from["L$i"])) {
		$i++;
	}
	$alias_jointure = "L$i";

	$alias_table = $boucle->id_table;
	$id_table_objet = $boucle->primary;

	// On fait un LEFT JOIN avec les liens de documents qui correspondent au(x)
	// rôle(s) cherchés. Cela permet de sélectionner aussi les objets qui n'ont
	// pas de logo, dont le rôle sera alors NULL. C'est nécessaire pour pouvoir
	// gérer les logos enregistrés avec l'ancienne méthode, et pour {!logo}.
	$boucle->from[$alias_jointure] = 'spip_documents_liens';
	$boucle->from_type[$alias_jointure] = 'LEFT';
	$boucle->join[$alias_jointure] = array(
		"'$alias_table'",
		"'id_objet'",
		"'$id_table_objet'",
		"'$alias_jointure.objet='.sql_quote('" . objet_type($alias_table) . "')." .
		"' AND $alias_jointure.role LIKE \'logo\_" . ($type_logo ?: '%') . "\''",
	);
	$boucle->group[] = "$alias_table.$id_table_objet";

	// On calcule alors le where qui va bien.
	if ($crit->not) {
		$where = "$alias_jointure.role IS NULL";
	} else {
		$where = array(
			"'LIKE'",
			"'$alias_jointure.role'",
			"'\'logo\_" . ($type_logo ?: '%') . "\''",
		);
	}

	// Rétro-compatibilité : Si l'on ne cherche pas un type de logo particulier,
	// on retourne aussi les logos enregistrés avec la méthode "historique".
	if (! $type_logo) {
		$where_historique =
			'sql_in('
			. "'$alias_table.$id_table_objet', "
			. "lister_objets_avec_logos('$id_table_objet'), "
			. "'')";

		if ($crit->not) {
			$where_historique = array("'NOT'", $where_historique);
		}

		$where = array(
			"'OR'",
			$where,
			$where_historique
		);
	}

	// On ajoute le where à la boucle
	$boucle->where[] = $where;
}
