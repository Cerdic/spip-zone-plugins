<?php
/**
 * Recherche de logo : Rôles de documents
 *
 * Surcharge : on prend en priorité les documents par rapport aux vieux logos
 * 
 * @plugin     Rôles de documents
 * @copyright  2015-2018
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP/Core/Logos
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cherche le logo d'un contenu
 *
 * @note
 * Attention, les fonctions peuvent chercher les anciens états 'on' ou 'off' (balises #LOGO_XXX).
 * Il faut donc faire une correspondance avec les rôles principaux déclarés pour l'objet (on = 1er, off = 2ème).
 *
 * @note
 * Le logo du site est un document lié à un pseudo objet 'site_spip' avec id_objet=-1 et role=logo (ou l'image siteon0.xxx si vieux logo).
 * Dans ce cas, $id_table_objet vaut soit 'id_site_spip' (#FORMULAIRE_LOGO) soit 'id_site' ou 'id_syndic' (#LOGO_SITE_SPIP). Youpi.
 *
 * @global formats_logos Extensions possibles des logos
 * @uses type_du_logo()
 *
 * @param int $id
 *     Identifiant de l'objet
 * @param string $id_table_objet
 *     Nom de la clé primaire de l'objet
 * @param string $role
 *     Voir @note
 *     - Rôle pour les documents : 'logo' | 'logo_survol' | ...
 *     - Mode de survol pour les vieux logos : 'on' | 'off'
 * @param boolean $historique
 *     true pour chercher exclusivement les vieux logos
 * @return array
 *     - Liste (chemin complet du fichier, répertoire de logos, nom du logo, extension du logo, date de modification, id_document, role)
 *     - array vide si aucun logo trouvé.
 */
function inc_chercher_logo($id_objet, $id_table_objet, $role = 'on', $historique = false) {

	$logo = array();
	$objet = objet_type($id_table_objet);
	$etats = array('on', 'off'); // vieux états
	$etat = $role; // pour les vieux logos

	// ===================================
	// Cherchons en priorité les documents
	// ===================================
	if (!$historique) {

		include_spip('inc/roles');
		include_spip('inc/utils');

		// Retrouver les rôles principaux pour cet objet
		$roles = roles_presents('document', $objet);
		$roles_principaux = !empty($roles['roles']['principaux']) ? $roles['roles']['principaux'] : array('logo', 'logo_survol');

		// Correspondance vieux états / rôles principaux (voir @note)
		if ($role == $etats[0]) {
			$role = $roles_principaux[0];
		} elseif ($role == $etats[1]) {
			$role = isset($roles_principaux[1]) ? $roles_principaux[1] : $roles_principaux[0];
		}

		// Hack : ajustement pour le logo du site (voir @note)
		if (in_array($id_table_objet, array('id_site_spip', 'id_site', 'id_syndic'))
			and intval($id_objet) <= 0
		) {
			$objet = 'site_spip';
			$id_objet = -1;
		}

		if ($document = sql_allfetsel(
			'fichier, extension, titre, maj, liens.id_document, liens.role',
			'spip_documents AS docs' .
				' INNER JOIN spip_documents_liens AS liens' .
				' ON liens.id_document = docs.id_document',
			array(
				'objet = ' . sql_quote($objet),
				'id_objet = ' . intval($id_objet),
				'role = ' . sql_quote($role),
				sql_in('role', $roles_principaux), // too much ?
			)
		)) {
			$document = array_shift($document);
			$chemin = (substr($document['fichier'], 0, 4) === 'http' ?
				$document['fichier'] :
				find_in_path(_NOM_PERMANENTS_ACCESSIBLES . $document['fichier']));
			$dossier = find_in_path(_NOM_PERMANENTS_ACCESSIBLES . substr($document['fichier'], 0, strrpos($document['fichier'], '/')));
			$logo = array(
				$chemin,
				$dossier,
				$document['titre'],
				$document['extension'],
				strtotime($document['maj']),
				$document['id_document'],
				$document['role'],
			);
		}
	}

	// ========================================
	// Si rien trouvé cherchons les vieux logos
	// ========================================
	if (!$logo
		and in_array($etat, $etats)
	) {

		// Hack : ajustement pour le logo du site (voir @note)
		if (in_array($id_table_objet, array('id_site_spip', 'id_site', 'id_syndic'))
			and intval($id_objet) <= 0
		) {
			$id_table_objet = 'id_site';
			$id_objet = 0;
		}

		// Retrouver le nom du fichier
		// Il y a des noms raccourcis pour certains objets (raisons historiques)
		$type_logo = type_du_logo($id_table_objet);
		$nom = $type_logo . $etat . intval($id_objet);

		// Chercher les fichiers correspondants
		foreach ($GLOBALS['formats_logos'] as $format) {
			if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format))) {
				$logo = array(
					$d,
					_DIR_LOGOS,
					$nom,
					$format,
					@filemtime($d),
					'',
					$etat,
				);
				break; // S'arrêter au 1er fichier trouvé
			}
		}
	}

	return $logo;
}

/**
 * Retourne le type de logo tel que `art` depuis le nom de clé primaire
 * de l'objet
 *
 * C'est par défaut le type d'objet, mais il existe des exceptions historiques
 * déclarées par la globale `$table_logos`
 *
 * @global table_logos Exceptions des types de logo
 *
 * @param string $id_table_objet
 *     Nom de la clé primaire de l'objet
 * @return string
 *     Type du logo
 **/
function type_du_logo($id_table_objet) {
	return isset($GLOBALS['table_logos'][$id_table_objet])
		? $GLOBALS['table_logos'][$id_table_objet]
		: objet_type(preg_replace(',^id_,', '', $id_table_objet));
}

// Exceptions standards (historique)
$GLOBALS['table_logos'] = array(
	'id_article'  => 'art',
	'id_auteur'   => 'aut',
	'id_rubrique' => 'rub',
	'id_groupe'   => 'groupe',
);
