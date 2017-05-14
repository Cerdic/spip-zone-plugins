<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('noizetier_fonctions');
if (!function_exists('autoriser')) {
	include_spip('inc/autoriser');
}     // si on utilise le formulaire dans le public

/**
 * Formulaire d'édition d'une page de composition de noisettes.
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * 3 possibilités en fonction des paramètres :
 *
 * - Créer une page :     $new
 * - Modifier une page :  $page
 * - Dupliquer une page : $new + $page (on veut une nouvelle page basée sur une existante)
 *
 * @param string $page
 *                       identifiant d'une composition
 * @param string $new
 *                       pour créer une nouvelle composition
 * @param string $retour
 *                       URL de redirection
 */
function formulaires_editer_page_charger_dist($page, $edition, $description_page, $redirect = '') {

	// Initialisation des données communes à charger dans le formulaire
	$valeurs = array(
		'editable' => true,
		'edition' => $edition,
		'page' => $page,
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
		$valeurs = array_merge($valeurs, construire_heritages($page, $valeurs['type_page']));

	} elseif ($edition == 'dupliquer') {
		// La page désignée est la page source que l'on souhaite dupliquer pour créer une nouvelle
		// composition virtuelle. La nouvelle composition virtuelle aura donc le même type de page et
		// un nom de composition différent.
		// On propose le nom de la nouvelle composition en 'copie de nom_page_source'.
		$valeurs['type_page'] = $description_page['type'];
		$valeurs['composition_source'] = $description_page;
		$valeurs['nom'] = _T('noizetier:copie_de', array('source' => $description_page['nom']));
		$valeurs = array_merge($valeurs, construire_heritages($page, $valeurs['type_page']));

	} elseif ($edition == 'creer') {
		// On crée une nouvelle composition à partir d'une page source.
		// L'argument $description_page contient donc la configuration complète de la page source.
		$valeurs['type_page'] = $description_page['type'];
		$valeurs = array_merge($valeurs, construire_heritages($page, $valeurs['type_page']));
	} else {
		$valeurs['editable'] = false;
	}

	// Ajout  de la liste des blocs configurables afin de choisir de possibles exclusions.
	// En outre, on initialise les blocs exclus par défaut qui coincident
	// pour une modification, à la liste des blocs exclus de la composition en cours de modification
	// pour une duplication ou une création de composition, à la liste des blocs exclus de la source.
	// Ainsi cette liste est toujours l'iverse de l'index [blocs] de l'argument $description_page.
	if ($valeurs['editable']) {
		$blocs = noizetier_bloc_repertorier();
		foreach ($blocs as $_bloc => $_infos) {
			$valeurs['_blocs'][$_bloc] = $_infos['nom'];
			if (!in_array($_bloc, $description_page['blocs'])) {
				$valeurs['_blocs_defaut'][] = $_bloc;
			}
		}
	}

	return $valeurs;
}

/**
 * Formulaire d'édition d'une page de composition de noisettes.
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @param string $page
 *                       identifiant d'une composition
 * @param string $edition
 *                       pour créer une nouvelle composition
 * @param string $retour
 *                       URL de redirection
 */
function formulaires_editer_page_verifier_dist($page, $edition, $description_page, $redirect = '')
{
	$erreurs = array();

	// On vérifie que les champs obligatoires ont été bien remplis
	foreach (array('type_page', 'composition', 'nom') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
		}
	}

	// On vérifie, dans le cas d'une nouvelle composition que l'identifiant saisi n'est pas déjà pris
	// par une autre composition. La syntaxe de l'identifiant est aussi vérifiée (ni espace, ni tiret).
	if ($edition != 'modifier') {
		$type_page = _request('type_page');
		$composition = _request('composition');
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
 * Formulaire d'édition d'une page de composition de noisettes.
 *
 * Traiter les champs postés
 *
 * @param string $page
 *                       identifiant d'une composition
 * @param string $edition
 *                       pour créer une nouvelle composition
 * @param string $retour
 *                       URL de redirection
 */
function formulaires_editer_page_traiter_dist($page, $edition, $description_page, $redirect = '') {

	$retour = array();

	// Identifiant de la composition résultante.
	// -- on le recalcule systématiquement même si pour une modification il correspond à $page
	$type_page = _request('type_page');
	$composition = _request('composition');
	$identifiant = "${type_page}-${composition}";

	// Mise à jour ou création des données de base de la composition virtuelle résultante
	$compositions_virtuelles = lire_config('noizetier_compositions', array());
	$compositions_virtuelles[$identifiant] = array(
		'nom' => _request('nom'),
		'description' => _request('description'),
		'icon' => _request('icon'),
	);

	// Traitement des blocs configurables
	$compositions_virtuelles[$identifiant]['blocs'] = noizetier_bloc_defaut();
	$blocs_exclus = _request('blocs_exclus');
	if ($blocs_exclus) {
		$compositions_virtuelles[$identifiant]['blocs'] = array_diff($compositions_virtuelles[$identifiant]['blocs'], $blocs_exclus);
	}


	// Traitement des branches éventuelles pour la composition virtuelle résultante
	$branche = array();
	$heritages = construire_heritages($identifiant, $type_page);
	foreach ($heritages['_heritiers'] as $_objet => $_infos) {
		if ($heritage = _request("heritage-${_objet}")) {
			$branche[$_objet] = $heritage;
		}
	}
	$compositions_virtuelles[$identifiant]['branche'] = $branche;

	// Mise à jour de la composition virtuelle dans la meta
	ecrire_config('noizetier_compositions', serialize($compositions_virtuelles));

	// Pour une modification, le traitement s'arrête ici.
	// Pour une création ou un diplication, il faut traiter le peuplement automatique des noisettes
	// de la page source si requis.
	// -- on préremplit avec les noisettes de la page source, systématiquement en cas de duplication
	//    ou si demandé, en cas de création.
	if (in_array($edition, array('creer', 'dupliquer'))) {
		$copier_noisettes = ($edition == 'dupliquer') ? true : (($edition == 'creer' and _request('peupler')) ? true : false);
		if ($copier_noisettes and $type_page != 'page') {
			$composition_ref = ($edition == 'creer') ? '' : noizetier_page_composition($page); // si on ne créé pas, c'est qu'on duplique
			include_spip('base/abstract_sql');
			$config_mere = sql_allfetsel(
				'rang, type, composition, bloc, noisette, parametres',
				'spip_noisettes',
				'type='.sql_quote($type_page).' AND composition="'.$composition_ref.'"'
			);
			if (count($config_mere) > 0) {
				foreach ($config_mere as $cle => $noisette) {
					$config_mere[$cle]['composition'] = $composition;
				}
				sql_insertq_multi('spip_noisettes', $config_mere);
			}
			// On vérifie également que les compositions sont actives sur ce type d'objet
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


// Détermine les compositions héritables par ce type de page et les héritages pour chaque branche
function construire_heritages($page, $type) {
	$heritages = array('_heritiers' => array());
	foreach (compositions_recuperer_heritage() as $_objet => $_parent) {
		if ($_parent == $type) {
			$heritages['_heritiers'] = array_merge($heritages['_heritiers'], compositions_lister_disponibles($_objet));
		}
	}

	if ($heritages['_heritiers']) {
		$compositions_virtuelles = lire_config('noizetier_compositions', array());
		foreach ($heritages['_heritiers'] as $_objet => $_infos) {
			if (isset($compositions_virtuelles[$page]['branche'][$_objet])) {
				$heritages["heritage-.${_objet}"] = $compositions_virtuelles[$page]['branche'][$_objet];
			}
		}
	}

	return $heritages;
}
