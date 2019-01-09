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

	if (lire_config('activer_logos') === 'oui') {
		// Logos par défaut
		$roles_logos = array(
			'logo' => array(
				'label' => 'logos_roles:logo',
				'objets' => $tous_les_objets,
			)
		);

		if (lire_config('activer_logos_survol') === 'oui') {
			$roles_logos['logo_survol'] = array(
				'label' => 'logo_survol',
				'objets' => $tous_les_objets,
			);
		}
	} else {
		$roles_logos = array();
	}


	if (is_array(lire_config('logos_roles/roles_logos'))) {
		include_spip('inc/filtres');
		foreach (lire_config('logos_roles/roles_logos') as $r) {
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
