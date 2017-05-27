<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

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
 * @param array  $description_page
 * 		La configuration complète d'une page ou composition :
 * 		- `modifier`: la description de la composition virtuelle en cours d'édition
 * 		- `créer`: la description de la page source
 * 		- `dupliquer`: la description de la composition source
 * @param string $redirect
 * 		URL de redirection. La valeur dépend du type d'édition.
 *
 * @return array
 * 		Tableau des champs postés pour l'affichage du formulaire.
 */
function formulaires_editer_page_charger_dist($edition, $description_page, $redirect = '') {

	// Initialisation des données communes à charger dans le formulaire
	$valeurs = array(
		'editable' => true,
		'edition' => $edition,
		'type_page' => '',
		'composition' => '',
		'nom' => '',
		'description' => '',
		'icon' => '',
		'_blocs' => array(),
		'_blocs_defaut' => array(),
	);

	if ($edition == 'modifier') {
		// La page désignée par $page est déjà une composition virtuelle dont on souhaite modifier une
		// partie de la configuration (hors noisettes).
		// L'argument $description_page contient donc la configuration complète de cette page.
		$valeurs['type_page'] = $description_page['type'];
		$valeurs['composition'] = $description_page['composition'];
		$valeurs['nom'] = $description_page['nom'];
		$valeurs['description'] = $description_page['description'];
		$valeurs['icon'] = $description_page['icon'];

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
		// L'argument $description_page contient donc la configuration complète de la page source.
		$valeurs['type_page'] = $description_page['type'];

	} else {
		$valeurs['editable'] = false;
	}

	if ($valeurs['editable']) {
		// Ajout  de la liste des blocs configurables afin de choisir de possibles exclusions.
		// En outre, on initialise les blocs exclus par défaut qui coincident
		// -- pour une modification, à la liste des blocs exclus de la composition en cours de modification
		// -- pour une duplication ou une création de composition, à la liste des blocs exclus de la source.
		// Ainsi cette liste est toujours l'inverse de l'index [blocs] de l'argument $description_page.
		include_spip('noizetier_fonctions');
		$blocs = noizetier_bloc_repertorier();
		foreach ($blocs as $_bloc => $_infos) {
			$valeurs['_blocs'][$_bloc] = $_infos['nom'];
			if (!in_array($_bloc, $description_page['blocs'])) {
				$valeurs['_blocs_defaut'][] = $_bloc;
			}
		}

		// Ajout des héritages possibles en fonction du type de page. L'identifiant de la page permet quant à
		// lui d'initialiser les héritages avec :
		// - soit ceux de la page en cours de modification
		// - soit ceux de la composition source pour une duplication
		$page = $description_page['composition']
			? "{$description_page['type']}-{$description_page['composition']}"
			: '';
		$valeurs = array_merge($valeurs, construire_heritages($valeurs['type_page'], $page));
	}

	return $valeurs;
}

/**
 * @param        $edition
 * @param        $description_page
 * @param string $redirect
 *
 * @return array
 */
function formulaires_editer_page_verifier_dist($edition, $description_page, $redirect = '') {
	$erreurs = array();

	// On vérifie que les champs obligatoires ont été bien saisis
	foreach (array('type_page', 'composition', 'nom') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
		}
	}

	// On vérifie dans le cas d'une nouvelle composition que :
	// - l'identifiant saisi n'est pas déjà utilisé par une autre composition
	// - la syntaxe de l'identifiant qui ne doit contenir ni espace, ni tiret.
	if ($edition != 'modifier') {
		$type_page = _request('type_page');
		$composition = _request('composition');

		include_spip('noizetier_fonctions');
		$pages = noizetier_page_repertorier();
		if (isset($pages[$type_page.'-'.$composition]) and is_array($pages[$type_page.'-'.$composition])) {
			$erreurs['composition'] = _T('noizetier:formulaire_identifiant_deja_pris');
		}
		if (!preg_match('#^[a-z0-9_]+$#', $composition)) {
			$erreurs['composition'] = _T('noizetier:formulaire_erreur_format_identifiant');
		}
	}

	return $erreurs;
}

/**
 * @param        $edition
 * @param        $description_page
 * @param string $redirect
 *
 * @return array
 */
function formulaires_editer_page_traiter_dist($edition, $description_page, $redirect = '') {

	$retour = array();

	// Identifiant de la composition résultante.
	// -- on le recalcule systématiquement même si pour une modification il correspond à $page
	$type_page = _request('type_page');
	$composition = _request('composition');
	$identifiant = "${type_page}-${composition}";

	// Mise à jour ou création des données de base de la composition virtuelle résultante
	include_spip('inc/config');
	$compositions_virtuelles = lire_config('noizetier_compositions', array());
	$compositions_virtuelles[$identifiant] = array(
		'nom' => _request('nom'),
		'description' => _request('description'),
		'icon' => _request('icon'),
	);

	// Traitement des blocs configurables
	$blocs_exclus = _request('blocs_exclus');
	if ($blocs_exclus) {
		$compositions_virtuelles[$identifiant]['blocs_exclus'] = $blocs_exclus;
		// TODO : si on exclut des blocs il faut supprimer leurs éventuelles noisettes.
		// Une autre solution serait d'interdire l'exclusion d'un bloc contenant une noisette
	}

	// Traitement des branches éventuelles pour la composition virtuelle résultante
	$branche = array();
	$heritages = construire_heritages($type_page, $identifiant);
	foreach ($heritages['_heritiers'] as $_objet => $_infos) {
		if ($heritage = _request("heritage-${_objet}")) {
			$branche[$_objet] = $heritage;
		}
	}
	$compositions_virtuelles[$identifiant]['branche'] = $branche;

	// Mise à jour de la composition virtuelle dans la meta
	ecrire_config('noizetier_compositions', serialize($compositions_virtuelles));

	// Pour une modification, le traitement s'arrête ici.
	if ($edition != 'modifier') {
		// Pour une création ou un duplication, il faut traiter le peuplement automatique des noisettes
		// de la page source si requis.
		// -- on préremplit avec les noisettes de la page source, systématiquement en cas de duplication
		//    ou si demandé, en cas de création.
		if (($type_page != 'page')
		and (($edition == 'dupliquer') or (($edition == 'creer') and _request('peupler')))) {
			// Récupération des noisettes de la page source
			$select = array('rang', 'type', 'composition', 'bloc', 'noisette', 'parametres');
			$from = 'spip_noisettes';
			$where = array('type=' . sql_quote($type_page), 'composition=' . sql_quote($description_page['composition']));
			$noisettes_source = sql_allfetsel($select, $from, $where);
			// Injection des noisettes de la source dans la composition virtuelle en cours de création qui diffère
			// uniquement par l'identifiant de composition.
			if ($noisettes_source) {
				foreach ($noisettes_source as $_index => $_noisette) {
					$noisettes_source[$_index]['composition'] = $composition;
				}
				sql_insertq_multi($from, $noisettes_source);
			}

			// On vérifie également que les compositions sont actives sur ce type d'objet
			// TODO : que signifie ce code ? Il existe une meta 'compositions_types' qui ressemble mais pas 'compositions'
			$compositions_actives = compositions_objets_actives();
			if (!in_array($type_page, $compositions_actives)) {
				$compositions_config = unserialize($GLOBALS['meta']['compositions']);
				include_spip('base/objets');
				$compositions_config['objets'][] = table_objet_sql($type_page);
				ecrire_meta('compositions', serialize($compositions_config));
			}
		}

		// On invalide le cache en cas de création ou  de dpulication
		include_spip('inc/invalideur');
		suivre_invalideur("id='page/$identifiant'");
	}

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
function construire_heritages($type, $page) {

	// Initialisation du tableau
	$heritages = array('_heritiers' => array());

	// Récupération des compositions explicites et virtuelles
	$filtres = array('est_composition' => true);
	$compositions = noizetier_page_repertorier($filtres);

	// Récupération des types d'objet (objets héritiers) possédant un type d'objet parent.
	// Par exemple, article qui à a un parent rubrique.
	include_spip('compositions_fonctions');
	$objets_heritiers = array_keys(compositions_recuperer_heritage(), $type);
	if ($objets_heritiers) {
		foreach ($compositions as $_page => $_configuration) {
			// Pour chaque composition répertoriée par le noiZetier, on détecte si celle-ci est affectable à
			// un des types d'objet ayant un parent et on liste les compositions par objet héritier.
			if (in_array($_configuration['type'], $objets_heritiers)) {
				$heritages['_heritiers'][$_configuration['type']][$_configuration['composition']] = $_configuration['nom'];
				// On initialise, pour la page concernée si besoin, la composition affectée.
				if (($_page == $page) and !empty($_configuration['branche'])) {
					foreach ($_configuration['branche'] as $_objet => $_composition) {
						$heritages["heritage-${_objet}"] = $_composition;
					}
				}
			}
		}
	}

	return $heritages;
}
