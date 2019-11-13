<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Retourne les paramètres nécessaires au script d'initialisation de multilang
 *
 * Il s'agit de sélecteurs jQuery pour choisir les éléments à prendre en compte.
 * Retourne un tableau vide si aucun formulaire sélectionné en config ou listé via pipeline.
 *
 * @uses pipeline `multilang_parametres`
 * @return string Tableau associatif (vide si rien en config) :
 *     - root       : (string) sélecteurs jQuery d'éléments racines
 *     - root_opt   : (string) idem, compléments
 *     - forms      : (string) sélecteurs jQuery des formulaires
 *     - fields     : (string) sélecteurs jQuery des champs
 *     - fields_opt : (string) idem, compléments
 */
function multilang_init_parametres() {

	include_spip('inc/config');
	$params = array();
	$config = lire_config('multilang', array());

	// root = éléments racines à prendre en compte
	// Ce sont les conteneurs des formulaires listés dans la config.
	$root = array();
	$formulaires = is_array($config['formulaires']) ? array_filter($config['formulaires']) : array();
	foreach ($formulaires as $formulaire) {
		switch ($formulaire) {
			case 'siteconfig':
				$root = array_merge($root, array('div#configurer-accueil', 'div.formulaire_configurer_identite'));
				break;
			case 'document':
				$root = array_merge($root, array('div#portfolio_portfolio', 'div#portfolio_documents', 'div#liste_documents, div.formulaire_editer_document'));
				break;
			case 'groupe_mots':
				$root[] = 'div.formulaire_editer_groupe_mot'; // le nom du formulaire n’est pas le type !
				break;
			default:
				$root[] = 'div.formulaire_editer_'.$formulaire;
				break;
		}
	}
	// Config : autres formulaires en texte libre
	if (trim($config['formulaires_autres'])) {
		$root = array_merge($root, explode(',', $config['formulaires_autres']));
	}
	$params['root'] = $root;

	// root_opt = compléments : formulaires ayant la classe .multilang
	$params['root_opt'] = array('form:has(.multilang)');

	// fields = champs à prendre en compte
	$params['fields'] = array(
		'textarea:not(textarea#adresses_secondaires, textarea#repetitions)',
		'input:text:not(input#new_login, input#email, #titreparent, input.date, input.heure, input#largeur, input#hauteur, .ac_input, #url_syndic, #url_auto, .rechercher_adresse input, #champ_geocoder, #champ_lat, #champ_lon, #champ_zoom, #places, *.nomulti)',
		'.multilang',
	);

	// fields_opt = compléments : classe .multilang
	$params['fields_opt'] = array('.multilang');

	// forms = formulaires à prendre en compte
	// On exclut les forms d'upload (pour les vignettes de docs, logos...)
	$params['forms'] = array('form[class!="form_upload"][class!="form_upload_icon"]');

	// Permettre aux plugins d'ajouter ou modifier des choses.
	$params = pipeline('multilang_parametres', array(
		'args' => array(),
		'data' => $params,
	));

	// Sécurité : après le pipeline, on vérifie s'il y a des éléments en root.
	// Dans le cas contraire, on vide le reste sinon le script s'activerait sur *tous* les formulaires sans distinction.
	if (
		empty($params['root'])
		and empty($params['root_opt'])
	) {
		unset($params['fields']);
		unset($params['fields_opt']);
		unset($params['forms']);
	}

	// Passage à la moulinette final
	$params = array_map(
		function ($v) {
			return implode(',', $v);
		},
		$params
	);
	$params = array_filter($params);

	return $params;
}


/**
 * Retourne les variables globales nécessaires au script d'initialisation de multilang
 *
 * @return array Tableau associatif
 *     - avail_langs   : (array) langues activées
 *     - dir_langs     : (array) directions des langues
 *     - def_lang      : (string) langue principale
 *     - lang_courante : (string) langue actuelle
 *     - dir_plugin    : (string) chemin du plugin
 */
function multilang_init_globales() {

	include_spip('inc/config');

	// Langues activées
	$langues = explode(',', $GLOBALS['meta']['langues_multilingue']);
	if (is_array($langues_config = lire_config('multilang/langues_utilisees', 'aucune')) && count($langues_config) > 0) {
		$langues = array_intersect($langues, $langues_config);
	}
	$langues_ltr = array();
	foreach ($langues as $langue) {
		$langues_ltr[$langue] = lang_dir($langue);
	}

	$globales = array(
		'avail_langs'   => $langues,
		'dir_langs'     => $langues_ltr,
		'def_lang'      => $GLOBALS['meta']['langue_site'],
		'lang_courante' => $GLOBALS['spip_lang'],
		'dir_plugin'    => _DIR_PLUGIN_MULTILANG,
	);

	return $globales;
}