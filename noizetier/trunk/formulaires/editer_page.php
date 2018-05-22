<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// TODO : à quoi ça sert vraiment?
if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public

/**
 * Formulaire d'édition d'une page de composition de noisettes.
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @param string $edition
 * 		Type d'édition à savoir :
 * 		- `modifier`: édition de la configuration de base d'une composition virtuelle
 * 		- `créer`: création d'une composition virtuelle à partir d'une page source
 * 		- `dupliquer`: copie d'une composition pour créer une nouvelle composition virtuelle
 * @param string $page
 * 		L'identifiant de la page ou de la composition :
 * 		- `modifier`: la composition virtuelle en cours d'édition
 * 		- `créer`: la page source
 * 		- `dupliquer`: la composition source
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_editer_page_charger_dist($edition, $page, $redirect = '') {

	// Initialisation des données communes à charger dans le formulaire
	$valeurs = array(
		'editable' => true,
		'edition' => $edition,
		'type_page' => '',
		'composition' => '',
		'nom' => '',
		'description' => '',
		'icon' => '',
		'est_virtuelle' => 'oui',
		'_blocs' => array(),
		'_blocs_defaut' => array(),
	);

	include_spip('inc/noizetier_page');
	$description_page = noizetier_page_lire($page, false);
	if ($description_page) {
		if ($edition == 'modifier') {
			// La page désignée par $page est déjà une composition virtuelle dont on souhaite modifier une
			// partie de la configuration (hors noisettes).
			// La variable $description_page contient donc la configuration complète de cette page.
			$valeurs['type_page'] = $description_page['type'];
			$valeurs['composition'] = $description_page['composition'];
			$valeurs['est_virtuelle'] = $description_page['est_virtuelle'];

			if ($valeurs['est_virtuelle'] == 'oui') {
				$valeurs['nom'] = $description_page['nom'];
				$valeurs['description'] = $description_page['description'];
				$valeurs['icon'] = $description_page['icon'];
			}

		} elseif ($edition == 'dupliquer') {
			// La page désignée est la composition source que l'on souhaite dupliquer pour créer une nouvelle
			// composition virtuelle. La nouvelle composition virtuelle aura donc le même type de page et
			// un identifiant de composition différent initialisé à 'copie_composition'.
			// On initialise aussi le nom de la nouvelle composition à 'copie de nom_page_source'.
			$valeurs['type_page'] = $description_page['type'];
			$valeurs['composition'] = "copie_{$description_page['composition']}";
			$valeurs['nom'] = _T('noizetier:copie_de', array('source' => $description_page['nom']));

		} elseif ($edition == 'creer') {
			// On crée une nouvelle composition à partir d'une page source.
			// La variable $description_page contient donc la configuration complète de la page source.
			$valeurs['type_page'] = $description_page['type'];
			$valeurs['nom'] = _T('info_sans_titre');

		} else {
			$valeurs['editable'] = false;
		}
	} else {
		$valeurs['editable'] = false;
	}

	if ($valeurs['editable']) {
		// Ajout de la liste des blocs configurables afin de choisir de possibles exclusions.
		// En outre, on initialise les blocs exclus par défaut qui coincident
		// -- pour une modification, à la liste des blocs exclus de la composition en cours de modification
		// -- pour une duplication ou une création de composition, à la liste des blocs exclus de la source.
		// Ainsi cette liste est toujours l'inverse de l'index [blocs] de l'argument $description_page.
		include_spip('inc/noizetier_bloc');
		$blocs = noizetier_bloc_lister_defaut();
		foreach ($blocs as $_bloc) {
			$valeurs['_blocs'][$_bloc] = noizetier_bloc_lire($_bloc, 'nom');
			if (!in_array($_bloc, $description_page['blocs'])) {
				$valeurs['_blocs_defaut'][] = $_bloc;
			}
		}

		// Ajout des héritages possibles en fonction du type de page. L'identifiant de la page permet quant à
		// lui d'initialiser les héritages avec :
		// - soit ceux de la page en cours de modification
		// - soit ceux de la composition source pour une duplication
		$valeurs['_heritiers'] = array();
		if ($valeurs['est_virtuelle'] == 'oui') {
			$page = $description_page['composition']
				? "{$description_page['type']}-{$description_page['composition']}"
				: '';
			$valeurs = array_merge($valeurs, construire_heritages($valeurs['type_page'], $page));
		}
	}

	return $valeurs;
}

/**
 * @param string $edition
 * 		Type d'édition à savoir :
 * 		- `modifier`: édition de la configuration de base d'une composition virtuelle
 * 		- `créer`: création d'une composition virtuelle à partir d'une page source
 * 		- `dupliquer`: copie d'une composition pour créer une nouvelle composition virtuelle
 * @param string $page
 * 		L'identifiant de la page ou de la composition :
 * 		- `modifier`: la composition virtuelle en cours d'édition
 * 		- `créer`: la page source
 * 		- `dupliquer`: la composition source
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 */
function formulaires_editer_page_verifier_dist($edition, $page, $redirect = '') {
	$erreurs = array();

	// On vérifie que les champs obligatoires ont été bien saisis
	if (_request('est_virtuelle') == 'oui') {
		foreach (array('type_page', 'composition', 'nom') as $champ) {
			if (!_request($champ)) {
				$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
			}
		}
	}

	// On vérifie dans le cas d'une nouvelle composition que :
	// - l'identifiant saisi n'est pas déjà utilisé par une autre composition
	// - la syntaxe de l'identifiant qui ne doit contenir ni espace, ni tiret.
	if ($edition != 'modifier') {
		$type_page = _request('type_page');
		$composition = _request('composition');

		$where = array('composition!=' . sql_quote(''));
		$pages = sql_allfetsel('page', 'spip_noizetier_pages', $where);
		if ($pages) {
			$pages = array_map('reset', $pages);
			if (isset($pages[$type_page.'-'.$composition])) {
				$erreurs['composition'] = _T('noizetier:formulaire_identifiant_deja_pris');
			}
			if (!preg_match('#^[a-z0-9_]+$#', $composition)) {
				$erreurs['composition'] = _T('noizetier:formulaire_erreur_format_identifiant');
			}
		}
	}

	return $erreurs;
}

/**
 *
 *
 * @param string $edition
 * 		Type d'édition à savoir :
 * 		- `modifier`: édition de la configuration de base d'une composition virtuelle
 * 		- `créer`: création d'une composition virtuelle à partir d'une page source
 * 		- `dupliquer`: copie d'une composition pour créer une nouvelle composition virtuelle
 * @param string $page
 * 		L'identifiant de la page ou de la composition :
 * 		- `modifier`: la composition virtuelle en cours d'édition
 * 		- `créer`: la page source
 * 		- `dupliquer`: la composition source
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 */
function formulaires_editer_page_traiter_dist($edition, $page, $redirect = '') {

	$retour = array();
	$description = array();

	// Identifiant de la page ou de la composition.
	$type_page = _request('type_page');
	$composition = _request('composition');
	$identifiant = $composition ? "${type_page}-${composition}" : $type_page;

	// Déterminer si la page est explicite ou est une composition virtuelle.
	// En effet, pour les pages explicites seuls les blocs exclus peuvent être modifiés.
	$est_virtuelle = _request('est_virtuelle');

	// Récupération des champs descriptifs et de l'icone.
	if ($est_virtuelle == 'oui') {
		$description['nom'] = _request('nom');
		$description['description'] = _request('description');
		$description['icon'] = _request('icon');

		// Traitement des branches éventuelles pour la composition virtuelle résultante
		$branche = array();
		$heritages = construire_heritages($type_page);
		foreach ($heritages['_heritiers'] as $_objet => $_composition) {
			if ($composition_heritee = _request("heritage-${_objet}")) {
				$branche[$_objet] = $composition_heritee;
			}
		}
		$description['branche'] = $branche;
	}

	// Traitement des blocs configurables
	$blocs_exclus = _request('blocs_exclus');
	$description['blocs_exclus'] = $blocs_exclus;
	// TODO : si on exclut des blocs il faut supprimer leurs éventuelles noisettes.
	// TODO : une autre solution serait d'interdire l'exclusion d'un bloc contenant une noisette

	if ($edition != 'modifier') {
		// Initialisation de la description pour une composition virtuelle.
		$description_defaut = array(
			'page'           => $identifiant,
			'type'           => $type_page,
			'composition'    => $composition,
			'nom'            => $identifiant,
			'description'    => '',
			'icon'           => 'page-24.png',
			'blocs_exclus'   => array(),
			'necessite'      => array(),
			'branche'        => array(),
			'est_virtuelle'  => 'oui',
			'est_page_objet' => 'non',
			'signature'      => '',
		);

		// Identifie si la page est celle d'un objet SPIP
		include_spip('base/objets');
		$tables_objets = array_keys(lister_tables_objets_sql());
		$description['est_page_objet'] = in_array(table_objet_sql($type_page), $tables_objets) ? 'oui' : 'non';

		// Complétude de la description avec les valeurs par défaut
		$description = array_merge($description_defaut, $description);
	}

	// On termine en sérialisant les tableaux des blocs exclus, necessite et branche.
	$description['blocs_exclus'] = serialize($description['blocs_exclus']);
	if ($est_virtuelle == 'oui') {
		$description['branche'] = serialize($description['branche']);
		if (isset($description['necessite'])) {
			$description['necessite'] = serialize($description['necessite']);
		}
	}

	// Mise ou insertion de la composition virtuelle
	if ($edition == 'modifier') {
		// -- Update de la composition modifiée
		$where = array('page=' . sql_quote($identifiant));
		$retour_sql = sql_updateq('spip_noizetier_pages', $description, $where);
	} else {
		// -- Insertion de la nouvelle composition
		$retour_sql = sql_insertq('spip_noizetier_pages', $description);

		// Pour une création ou un duplication, il faut traiter le peuplement automatique des noisettes
		// de la page source si requis.
		// -- on préremplit avec les noisettes de la page source, systématiquement en cas de duplication
		//    ou si demandé, en cas de création.
		if ($retour_sql) {
			if (($type_page != 'page')
			and (($edition == 'dupliquer') or (($edition == 'creer') and _request('peupler')))) {
				// Récupération des noisettes de la page source
				$select = array('plugin', 'rang_noisette', 'type', 'composition', 'bloc', 'type_noisette', 'parametres');
				$from = 'spip_noisettes';
				$where = array(
					'plugin=' . sql_quote('noizetier'),
					'type=' . sql_quote($type_page),
					'composition=' . sql_quote(noizetier_page_extraire_composition($page))
				);
				$noisettes_source = sql_allfetsel($select, $from, $where);
				// Injection des noisettes de la source dans la composition virtuelle en cours de création qui diffère
				// uniquement par l'identifiant de composition.
				if ($noisettes_source) {
					foreach ($noisettes_source as $_index => $_noisette) {
						$noisettes_source[$_index]['composition'] = $composition;
					}
					sql_insertq_multi($from, $noisettes_source);
				}
			}

			// On invalide le cache en cas de création ou  de duplication
			include_spip('inc/invalideur');
			suivre_invalideur("id='page/$identifiant'");
		}
	}

	if ($retour_sql === false) {
		$retour['message_nok'] = _T('noizetier:formulaire_composition_erreur');
	} else {
		$retour['message_ok'] = _T('noizetier:formulaire_composition_mise_a_jour');
		if (in_array($edition, array('creer', 'dupliquer'))) {
			$retour['redirect'] = $redirect;
		} elseif ($redirect) {
			if (strncmp($redirect, 'javascript:', 11) == 0) {
				$retour['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($redirect, 11).'/*]]>*/</script>';
				$retour['editable'] = true;
			} else {
				$retour['redirect'] = $redirect;
			}
		}
	}

	return $retour;
}


/**
 * Détermine les compositions héritables par le type de page et les héritages définis
 * pour chaque type d'objet concerné par la composition désignée par $page.
 *
 * @param string $type
 * 		Type d'objet pouvant posséder des enfants.
 * @param string $page
 * 		Identifiant de la composition dont les héritages doivent être renvoyés.
 *
 * @return array
 */
function construire_heritages($type, $page = '') {

	// Initialisation du tableau
	$heritages = array('_heritiers' => array());

	// Récupération des compositions explicites et virtuelles
	$select = array('page', 'type', 'composition', 'nom', 'branche');
	$where = array('composition!=' . sql_quote(''));
	$compositions = sql_allfetsel($select, 'spip_noizetier_pages', $where);
	if ($compositions) {
		// On réindexe le tableau entier par l'identifiant de la page
		$compositions = array_column($compositions, null, 'page');

		// Récupération des types d'objet (objets héritiers) possédant un type d'objet parent.
		// Par exemple, article qui à a un parent rubrique.
		include_spip('compositions_fonctions');
		$objets_heritiers = array_keys(compositions_recuperer_heritage(), $type);
		if ($objets_heritiers) {
			foreach ($compositions as $_page => $_configuration) {
				// Pour chaque composition répertoriée par le noiZetier, on détecte si celle-ci est affectable à
				// un des types d'objet ayant un parent et on liste les compositions par objet héritier.
				if (in_array($_configuration['type'], $objets_heritiers)) {
					if (empty($heritages['_heritiers'][$_configuration['type']]['-'])) {
						// Ne pas oublier d'insérer une fois la page par défaut de l'objet
						$heritages['_heritiers'][$_configuration['type']]['-'] =
							ucfirst(_T('compositions:composition_defaut')) . ' (' . $_configuration['type'] . ')';
					}
					$heritages['_heritiers'][$_configuration['type']][$_configuration['composition']] =
						$_configuration['nom'] . ' (' . $_configuration['type'] . '-' . $_configuration['composition'] . ')';
					// On initialise, pour la page concernée si besoin, la composition affectée.
					if (($_page == $page) and ($branches = unserialize($_configuration['branche'])) and !empty($branches)) {
						foreach ($branches as $_objet => $_composition) {
							$heritages["heritage-${_objet}"] = $_composition;
						}
					}
				}
			}
		}
	}

	return $heritages;
}
