<?php
/**
 * Fonctions utiles au plugin Logos par rôles
 *
 * @plugin     logos_roles
 * @copyright  2016
 * @author     bystrano
 * @licence    GNU/GPL
 */

/**
 * Lister les rôles de logos
 *
 * Le tableau retourné utilise les noms des rôles comme clés, et des tableaux
 * d'options comme valeur, p.ex :
 *
 *	array(
 *		'logo' => array(
 *			'label' => 'Logo',
 *			'objets' => array('articles', 'rubriques', 'syndic'),
 *		),
 *		'logo_survol' => array(
 *			'label' => 'Logo survol',
 *			'objets' => array('articles', 'rubriques'),
 *		),
 *	)
 *
 * @param string|null $objet : Un nom d'objet auquel se restreindre. La fonction
 *        ne retourne alors que les rôles de logos que l'on peut attribuer à cet
 *        objet.
 * @param string|null $role : Un rôle auquel se restreindre. On accepte `on` ou
 * `off` pour la rétro-compatibilité
 *
 * @return array : Retourne le tableau décrivant les rôles de logos. Si on a
 * passé un paramètre rôle, on retourne directement la définition plutôt qu'une
 * liste avec un seul rôle.
 */
function lister_roles_logos($objet = null, $role = null) {

	if ($role === 'on') {
		$role = 'logo';
	} elseif ($role === 'off') {
		$role = 'logo_survol';
	}

	// Logos par défaut
	$roles_logos = pipeline(
		'roles_logos',
		array(
			'logo' => array(
				'label' => 'Logo',
				'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
			),
			'logo_survol' => array(
				'label' => 'Logo survol',
				'objets' => array_map('table_objet', array_keys(lister_tables_objets_sql())),
			),
		)
	);

	include_spip('base/objets');

	/* Filtrer par objet */
	if ($objet = table_objet($objet)) {
		$roles_logos_objet = array();
		foreach ($roles_logos as $cle_role => $options) {
			if ((! is_array($options['objets']))
					or in_array($objet, array_map('table_objet', $options['objets']))) {
				$roles_logos_objet[$cle_role] = $options;
			}
		}

		$roles_logos = $roles_logos_objet;
	}

	/* Filtrer par rôle */
	if (! is_null($role)) {
		return $roles_logos[$role];
	}

	return $roles_logos;
}

/**
 * Trouver les dimensions d'un rôle
 *
 * @param string $role : Le rôle dont on veut connaître les dimensions
 *
 * @return array|null  : Un tableau avec des clés 'hauteur' et 'largeur', rien si
 *                       pas de dimensions définies
 */
function get_dimensions_role($role) {

	$roles_logos = lister_roles_logos();

	if (isset($roles_logos[$role])
			and is_array($roles_logos[$role])
			and isset($roles_logos[$role]['dimensions'])
			and is_array($roles_logos[$role]['dimensions'])) {
		return $roles_logos[$role]['dimensions'];
	}
}

/**
 * Trouve l'identifiant du document associé à un fichier
 *
 * @param string $fichier : le nom du fichier
 *
 * @return integer : l'identifiant du document
 */
function trouver_document_fichier($fichier) {

	$fichier = str_replace(_DIR_IMG, '', $fichier);

	include_spip('base/abstract_sql');

	return sql_getfetsel('id_document', 'spip_documents', 'fichier='.sql_quote($fichier));
}


/**
 * Traitement automatique sur les logos. Permet de compléter le résultat des
 * balises #LOGO_* pour trouver les logos définis par rôles de documents.
 *
 * @param string $logo : le code html du logo
 * @param string $objet : le type d'objet
 * @param int $id_objet : l'identifiant de l'objet
 * @param string $role
 *     le role, ou `on` ou `off` pour la rétro-compatibilité
 *
 * @return string : le code html du logo qui va bien
 */
function trouver_logo_par_role($logo, $objet, $id_objet, $role) {

	if (! $logo) {
		$chercher_logo = charger_fonction('chercher_logo', 'inc/');
		$balise_img = charger_filtre('balise_img');

		$logo = $chercher_logo($id_objet, id_table_objet($objet), $role);
		$logo = $balise_img($logo[0]);
	}

	return $logo;
}

/**
 * Traitement automatique sur les logos. Forcer les dimensions d'un logo suivant
 * les dimensions définies par son rôle.
 *
 * @param string $logo : le code html du logo
 * @param string $objet : le type d'objet
 * @param int $id_objet : l'identifiant de l'objet
 * @param string $role
 *     le role, ou `on` ou `off` pour la rétro-compatibilité
 *
 * @return string : le code html du logo aux bonnes dimensions
 */
function forcer_dimensions_role($logo, $objet, $id_objet, $role) {

	include_spip('inc/filtres');

	if ($dimensions = get_dimensions_role($role)) {
		$image_recadre = charger_filtre('image_recadre');
		$image_passe_partout = charger_filtre('image_passe_partout');
		$logo = $image_recadre(
			$image_passe_partout($logo, $dimensions['largeur'], $dimensions['hauteur']),
			$dimensions['largeur'],
			$dimensions['hauteur']
		);
	}

	return $logo;
}
