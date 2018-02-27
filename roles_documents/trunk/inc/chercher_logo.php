<?php
/**
 * Recherche de logo : Rôles de documents
 *
 * Surcharge : on prend en compte les rôles de documents en plus des logos historiques
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
 * @global formats_logos Extensions possibles des logos
 * @uses type_du_logo()
 *
 * @param int $id
 *     Identifiant de l'objet
 *     Pour le logo du site : 0
 * @param string $id_table_objet
 *     Nom de la clé primaire de l'objet
 *     Pour le logo du site : 'site'
 * @param string $role
 *     - Rôle pour les documents : 'logo' | 'logo_survol' | logo_xxx...
 *     - Mode de survol pour les logos historiques : 'on' | 'off'
 * @return array
 *     - Liste (chemin complet du fichier, répertoire de logos, nom du logo, extension du logo, date de modification, id_document, role)
 *     - array vide aucun logo trouvé.
 */
function inc_chercher_logo($id, $id_table_objet, $role = 'logo') {

	$logo = array();
	$objet = objet_type($id_table_objet);

	// On commence par chercher les logos historiques
	// Attention au cas $id = '0' pour LOGO_SITE_SPIP : utiliser intval()
	$type_logo = type_du_logo($id_table_objet);
	$nom = $type_logo . $role . intval($id);
	foreach ($GLOBALS['formats_logos'] as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format))) {
			$logo = array(
				$d,
				_DIR_LOGOS,
				$nom,
				$format,
				@filemtime($d),
				'',
				$role,
			);
			break; // S'arrêter au 1er fichier trouvé
		}
	}

	// Ensuite cherchons un document avec le rôle demandé
	if (!$logo
		and $document = sql_allfetsel(
		'fichier, extension, titre, maj, l.id_document, l.role',
		'spip_documents AS docs' .
			' INNER JOIN spip_documents_liens AS l' .
			' ON l.id_document = docs.id_document',
		array(
			'objet = ' . sql_quote($objet),
			'id_objet = ' . intval($id),
			'role = ' . sql_quote($role),
			'SUBSTR(role, 1, 4) = ' . sql_quote('logo'), // s'assurer qu'il s'agit d'un role de logo
		)
	)) {
		$document = array_shift($document);
		$dossier = _DIR_PERMANENTS_ACCESSIBLES . substr($document['fichier'], strrpos($document['fichier'], '/'));
		$logo = array(
			$document['fichier'],
			$dossier,
			$document['titre'],
			$document['extension'],
			strtotime($document['maj']),
			$document['id_document'],
			$document['role'],
		);
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
	'id_article' => 'art',
	'id_auteur'   => 'aut',
	'id_rubrique' => 'rub',
	'id_groupe'   => 'groupe',
);
