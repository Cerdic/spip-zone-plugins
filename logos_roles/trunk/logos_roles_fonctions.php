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
 *     ne retourne alors que les rôles de logos que l'on peut attribuer à cet
 *     objet.
 * @param string|null $role : Un rôle auquel se restreindre. On accepte `on` ou
 *     `off` pour la rétro-compatibilité
 * @param string|null $tous_les_objets : Une liste des objets éditoriaux à
 *     prendre en compte. Ça n'est nécessaire que dans le cas où l'on appelle
 *     cette fonction à un moment où l'API lister_tables_objets_sql n'est pas
 *     encore disponible, notamment dans le pipeline declarer_tables_objets_sql.
 *     Dans les autres cas on utilise l'API pour trouver les tables, pas besoin
 *     de les spécifier.
 *
 * @return array : Retourne le tableau décrivant les rôles de logos. Si on a
 *     passé un paramètre rôle, on retourne directement la définition plutôt
 *     qu'une liste avec un seul rôle.
 */
function lister_roles_logos($objet = null, $role = null, $tous_les_objets = null) {

	if ($role === 'on') {
		$role = 'logo';
	} elseif ($role === 'off') {
		$role = 'logo_survol';
	}

	if (! $tous_les_objets) {
		$tous_les_objets = array_map(
			'table_objet_simple',
			array_filter(array_keys(lister_tables_objets_sql()))
		);
	}

	$conf_logos = lire_config('logos_roles/roles_logos');

	if (! is_array($conf_logos)) {
		if (lire_config('activer_logos') === 'oui') {
			$roles_logos = array(
				'logo' => array(
					'label' => 'logos_roles:logo',
					'objets' => $tous_les_objets,
				)
			);
		}
		if (lire_config('activer_logos_survol') === 'oui') {
			$roles_logos['logo_survol'] = array(
				'label' => 'logo_survol',
				'objets' => $tous_les_objets,
			);
		}
	} else {
		include_spip('inc/filtres');

		$roles_logos = array();
		foreach ($conf_logos as $r) {
			if ($r['etat']) {
				$roles_logos['logo_' . $r['slug']] = array(
					'label' => extraire_multi($r['titre']) ?: $r['slug'],
					'objets' => $r['objets'],
				);

				if (isset($r['dimensions']) and
						isset($r['dimensions']['largeur']) and ($r['dimensions']['largeur'] > 0) and
						isset($r['dimensions']['hauteur']) and ($r['dimensions']['hauteur'] > 0)) {
					$roles_logos['logo_' . $r['slug']]['dimensions'] = $r['dimensions'];
				}
			}
		}

		// Le logo qui est enregistré dans la méta comme 'logo_defaut' doit en
		// fait être 'logo' tout court
		if (isset($roles_logos['logo_defaut'])) {
			$roles_logos = array_merge(
				array('logo' => $roles_logos['logo_defaut']),
				$roles_logos
			);
			unset($roles_logos['logo_defaut']);
		}

		// Les métas des logos ont le dernier mot
		if (isset($roles_logos['logo']) and
				(lire_config('activer_logos') === 'non')) {
			unset($roles_logos['logo']);
		}
		if (isset($roles_logos['logo_survol']) and
				(lire_config('activer_logos_survol') === 'non')) {
			unset($roles_logos['logo_survol']);
		}
	}

	$roles_logos = pipeline('roles_logos', $roles_logos);

	include_spip('base/objets');

	/* Filtrer par objet */
	if ($objet and $objet = table_objet_simple($objet)) {
		$roles_logos_objet = array();
		foreach ($roles_logos as $cle_role => $options) {
			if ((! is_array($options['objets']))
					or in_array($objet, array_map('table_objet_simple', $options['objets']))) {
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
 * Déterminer si un logo est le logo par défaut d'un objet donné
 *
 * @param string $logo : le logo en question
 * @param integer $id_objet : l'identifiant de l'objet
 * @param string $objet : le type de l'objet
 * @param string $role : le rôle du logo
 *
 * @return boolean : true si oui, false sinon…
 */
function est_logo_par_defaut($logo, $id_objet, $objet, $role) {

	$chercher_logo = charger_fonction('chercher_logo', 'inc/');

	$def_logo = lister_roles_logos($objet, $role);

	if (isset($def_logo['defaut'])) {
		$logo_defaut = find_in_path($def_logo['defaut']);
	}

	if (! isset($logo_defaut)) {
		$logo_defaut = $chercher_logo($id_objet, id_table_objet($objet), 'on');
		$logo_defaut = $logo_defaut[0];
	}

	return ($logo === $logo_defaut);
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
		if (isset($logo[0])) {
			$logo = $balise_img($logo[0]);
		} else {
			return '';
		}
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

	if ($role === 'on') {
		$role = 'logo';
	} elseif ($role === 'off') {
		$role = 'logo_survol';
	}

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

/**
 * Modifier le logo d'un objet à partir d'un document
 *
 * @param string $objet
 * @param int $id_objet
 * @param string $role
 *     le role, ou `on` ou `off` pour la rétro-compatibilité
 * @param integer $id_document : l'identifiant du document
 * @return string
 *     Erreur, sinon ''
 */
function logo_modifier_document($objet, $id_objet, $role, $id_document) {

	// Cas du LOGO_SITE_SPIP..
	if (($objet === 'site') and ($id_objet == 0)) {
		include_spip('base/abstract_sql');

		// On supprime d'éventuels liens existants
		sql_delete(
			'spip_documents_liens',
			array('objet="site"', 'id_objet=0', 'role='.sql_quote($role))
		);

		// Puis on insère le nouveau
		sql_insertq(
			'spip_documents_liens',
			array(
				'id_document' => intval($id_document),
				'objet' => 'site',
				'id_objet' => 0,
				'role' => $role,
			)
		);

		// On enregistre les logos du site dans une meta, pour pouvoir les rétablir
		// automatiquement après le passage du CRON d'optimisation, qui efface les
		// liens vers des id_objet qui valent 0.
		include_spip('inc/config');

		$logos_site = lire_config('logos_site') ?: array();
		$logos_site[$role] = intval($id_document);
		ecrire_config('logos_site', $logos_site);

	// Cas des autres logos
	} else {
		include_spip('action/editer_liens');
		objet_associer(
			array('document' => $id_document),
			array($objet => $id_objet),
			array('role' => $role)
		);
	}

	return $erreur = '';
}

/**
 * Une ré-implémentation naïve de table_objet
 *
 * C'est utile parce que la fonction table_objet appelle la fonction
 * lister_tables_objet_sql, et qu'on ne peut donc pas l'utiliser avant que la
 * table des tables ne soit calculée, comme p.ex. dans le pipeline
 * declarer_objets_sql.
 *
 * @param string $type
 *     Nom de la table SQL (le plus souvent)
 *     Tolère un nom de clé primaire.
 * @return string
 *     Nom de l'objet
 */
function table_objet_simple($type) {

	// S'il n'y a pas de "s" à la fin, on le met.
	if (substr($type, -1) !== 's') {
		$type .= 's';
	}

	// S'il y a un "spip_" ou un "id_" au début, on le retire.
	return preg_replace(',^spip_|^id_,', '', $type);
}

/**
 * Gros hack : pour pouvoir fonctionner sans massicot, mais sans non plus
 * s'embêter à traiter les deux cas dans les squelettes, on définit ici la
 * fonction dont on a besoin, dans le cas où le plugin n'est pas installé.
 */
if (! test_plugin_actif('massicot')) {
	function massicoter_objet($fichier, $objet, $id_objet, $role = null) {

		return $fichier;
	}
}
