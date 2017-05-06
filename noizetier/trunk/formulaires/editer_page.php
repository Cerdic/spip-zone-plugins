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
function formulaires_editer_page_charger_dist($page, $edition, $description_page, $retour = '') {

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
		// On crée une nouvelle page from scratch.
		// Toute la configuration de la page est donc vide
		// Il faut constituer la liste des pages dont la composition va s'inspirer afin de proposer ce choix à
		// l'utilisateur.
		$valeurs['_pages_composables'] = array();
		if (defined('_NOIZETIER_COMPOSITIONS_TYPE_PAGE') and _NOIZETIER_COMPOSITIONS_TYPE_PAGE) {
			$valeurs['_pages_composables']['page'] = _T('noizetier:page_autonome');
		}
		// Si on voulait se baser sur la config de compositions, on utiliserait compositions_objets_actives().
		// En fait, à la création d'une composition du noizetier, on modifie la config de compositions.
		// On se base donc sur la liste des objets sur lesquels compositions est activable
		// et qui dispose déjà d'une page dans le noizetier.
		include_spip('base/objets');
		$tables_objet = lister_tables_objets_sql();
		if ($tables_objet) {
			foreach ($tables_objet as $_table) {
				// On ne sélectionne que les tables ayant une page publique configurée et qui appartient
				// à la liste des pages accessibles par le noiZetier.
				if (!empty($_table['page']) and ($configuration = noizetier_page_informer($_table['page']))) {
					$valeurs['_pages_composables'][$_table['page']] = $configuration['nom'];
				}
			}
		}
		// Hack pour les groupes de mots-clés (car ils n'ont pas d'entrée page dans lister_tables_objets_sql()).
		if (isset($tables_objet['groupe_mots'])) {
			$valeurs['_pages_composables']['groupe_mots'] = 'groupe_mots';
		}
	} else {
		$valeurs['editable'] = false;
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
function formulaires_editer_page_verifier_dist($page, $edition, $description_page, $retour = '')
{
	$erreurs = array();
	foreach (array('type_page', 'composition', 'nom') as $champ) {
		if (!_request($champ)) {
			$erreurs[$champ] = _T('noizetier:formulaire_obligatoire');
		}
	}
	// On vérifie, dans le cas d'une nouvelle composition que $composition n'est pas déjà pris
	// On vérifie aussi que $composition ne contient ni espace, ni tiret
	if (_request('new') and _request('composition')) {
		$type_page = _request('type_page');
		$composition = _request('composition');
		$liste_pages = noizetier_lister_pages();
		if (isset($liste_pages[$type_page.'-'.$composition]) and is_array($liste_pages[$type_page.'-'.$composition])) {
			$erreurs['composition'] = _T('noizetier:formulaire_identifiant_deja_pris');
		}
		if (preg_match('#^[a-z0-9_]+$#', $composition) == 0) {
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
function formulaires_editer_page_traiter_dist($page, $edition, $description_page, $retour = '')
{
	if (!autoriser('configurer', 'noizetier')) {
		return array('message_erreur' => _T('noizetier:probleme_droits'));
	}

	$res = array();
	$noizetier_compositions = isset($GLOBALS['meta']['noizetier_compositions']) && unserialize($GLOBALS['meta']['noizetier_compositions']) ? unserialize($GLOBALS['meta']['noizetier_compositions']) : array();
	$type_page = _request('type_page');
	$composition = _request('composition');
	$peupler = ($edition == 'dupliquer') ? true : (($edition == 'creer' and _request('peupler')) ? true : false);

	// Au cas où on n'a pas encore configuré de compositions
	if (!is_array($noizetier_compositions)) {
		$noizetier_compositions = array();
	}

	$noizetier_compositions["${type_page}-${composition}"] = array(
		'nom' => _request('nom'),
		'description' => _request('description'),
		'icon' => _request('icon'),
	);

	$branche = array();
	foreach (heritiers($type_page) as $t => $i) {
		if ($h = _request('heritage-'.$t)) {
			$branche[$t] = $h;
		}
	}
	if (count($branche) > 0) {
		$noizetier_compositions["${type_page}-${composition}"]['branche'] = $branche;
	}

	ecrire_meta('noizetier_compositions', serialize($noizetier_compositions));
	$retours['message_ok'] = _T('noizetier:formulaire_composition_mise_a_jour');

	// On pré-remplit avec les noisettes de la page mère en cas de nouvelle composition,
	// ou avec celles de la page de référence en cas de duplication
	if (
		$peupler
		and $type_page != 'page'
	) {
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
	if (in_array($edition, array('creer', 'dupliquer'))) {
		include_spip('inc/invalideur');
		suivre_invalideur("id='page/$type_page-$composition'");
	}

	$res['message_ok'] = _T('info_modification_enregistree');
	if (in_array($edition, array('creer', 'dupliquer'))) {
		$res['redirect'] = parametre_url(parametre_url(self(), 'new', ''), 'page', $type_page.'-'.$composition);
	} elseif ($retour) {
		if (strncmp($retour, 'javascript:', 11) == 0) {
			$res['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retour, 11).'/*]]>*/</script>';
			$res['editable'] = true;
		} else {
			$res['redirect'] = $retour;
		}
	}

	return $res;
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
