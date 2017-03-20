<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Recherche de logo
 *
 * @package SPIP\Core\Logos
 **/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cherche le logo d'un élément d'objet
 *
 * @global formats_logos Extensions possibles des logos
 * @uses type_du_logo()
 *
 * @param int $id
 *     Identifiant de l'objet
 * @param string $_id_objet
 *     Nom de la clé primaire de l'objet
 * @param string $mode Mode de survol du logo désiré (on ou off), ou alors un
 *     rôle dont l'identifiant commence par « logo ».
 * @return array
 *     - Liste (chemin complet du fichier, répertoire de logos, nom du logo, extension du logo, date de modification)
 *     - array vide aucun logo trouvé.
 **/
function inc_chercher_logo_dist($id, $_id_objet, $mode = 'on') {

	$type = type_du_logo($_id_objet);

	/* Si le paramètre $mode est un rôle correspondant aux logos historique, on
	 * cherche quand même un logo enregistré avec la méthode historique. */
	if ($mode === 'logo') {
		$mode = 'on';
	} elseif ($mode === 'logo_survol') {
		$mode = 'off';
	}

	/* On commence par chercher via le mécanisme historique */
	$nom = $type . $mode . intval($id);

	foreach ($GLOBALS['formats_logos'] as $format) {
		if (@file_exists($d = (_DIR_LOGOS . $nom . '.' . $format))) {
			return array($d, _DIR_LOGOS, $nom, $format, @filemtime($d));
		}
	}

	/* Si on n'a rien trouvé, on cherche un document lié avec le bon rôle */
	$logo = chercher_logo_document($id, $_id_objet, $mode);

	if ($logo) {
		return $logo;
	}

	# coherence de type pour servir comme filtre (formulaire_login)
	return array();
}

/**
 * Trouver un logo enregistré en tant que document
 *
 * Fonction à usage interne, utiliser chercher_logo pour trouver des logos
 */
function chercher_logo_document($id, $_id_objet, $mode) {

	if ($mode === 'on') {
		$role = 'logo';
	} elseif ($mode === 'off') {
		$role = 'logo_survol';
	} else {
		$role = $mode;
	}

	include_spip('base/abstract_sql');
	$ligne = sql_fetsel(
		'fichier, extension',
		'spip_documents as D '
		. 'INNER JOIN spip_documents_liens as L ON D.id_document=L.id_document',
		array(
			'L.objet='.sql_quote(objet_type($_id_objet)),
			'L.id_objet='.intval($id),
			'L.role='.sql_quote($role),
		)
	);

	if ($ligne['fichier']) {

		$fichier = _DIR_IMG . $ligne['fichier'];
		$extension = $ligne['extension'];

		return array(
			$fichier,
			dirname($fichier) . '/',
			basename($fichier, '.' . $extension),
			$extension,
			@filemtime($fichier),
		);
	} else if ($mode !== 'on') {
		// S'il n'y a pas de logo avec le bon rôle, on se rabat sur le logo de base
		return inc_chercher_logo_dist($id, $_id_objet, 'on');
	}
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
 * @param string $_id_objet
 *     Nom de la clé primaire de l'objet
 * @return string
 *     Type du logo
 **/
function type_du_logo($_id_objet) {
	return isset($GLOBALS['table_logos'][$_id_objet])
		? $GLOBALS['table_logos'][$_id_objet]
		: objet_type(preg_replace(',^id_,', '', $_id_objet));
}

// Exceptions standards (historique)
$GLOBALS['table_logos'] = array(
	'id_article' => 'art',
	'id_auteur' => 'aut',
	'id_rubrique' => 'rub',
	'id_groupe' => 'groupe',
);
