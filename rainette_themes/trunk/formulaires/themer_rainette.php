<?php
/**
 * Gestion du formulaire de visualisation des thèmes du plugin Rainette.
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * On surcharge la fonction de chargement par défaut afin de fournir le service, sa configuration
 * et son état d'exécution au formulaire.
 *
 * @return array
 *        Tableau des données à charger par le formulaire.
 */
function formulaires_themer_rainette_charger() {

	// Initialisation des paramètres du formulaire.
	$valeurs['operation'] = _request('operation');
	$valeurs['service'] = _request('service');
	$valeurs['theme'] = _request('theme');
	$valeurs['theme_2'] = _request('theme_2');
	$valeurs['icone'] = _request('icone');
	$valeurs['periode'] = _request('periode');

	// Consolidation des données nécessaires à l'affichage des formulaires.
	// -- Liste des operations.
	$valeurs['_operations']['afficher_theme'] = _T('rainette:label_operation_afficher_theme');
	$valeurs['_operations']['comparer_theme'] = _T('rainette:label_operation_comparer_theme');
	$valeurs['_operations']['comparer_icone'] = _T('rainette:label_operation_comparer_icone');
	include_spip('rainetheme_fonctions');
	if (_RAINETTE_DEBUG_TRANSCODAGE) {
		$valeurs['_operations']['transcoder_weather'] = _T('rainette:label_operation_transcoder_weather');
	}

	// -- Liste des services : on ne sélectionne que ceux qui possèdent des thèmes et on initialise le défaut
	//    au premier service de la liste. On prend les thèmes actifs et inactifs car ils peuvent servir de référence.
	include_spip('rainette_fonctions');
	$services = rainette_lister_services('tableau', false);
	foreach ($services as $_service => $_data) {
		$activite = $_data['actif']
			? _T('rainette:service_actif')
			: _T('rainette:service_inactif');
		if (rainette_lister_themes($_service, 'local')) {
			$valeurs['_services'][$_service] = $_data['nom'] . " (${activite})";
		}
	}
	reset($valeurs['_services']);
	$valeurs['_service_defaut'] = key($valeurs['_services']);

	// -- Si on a déjà choisi un service, on ajoute la liste des thèmes ou des codes d'icones suivant
	//    le type d'opération choisi (étape 2).
	if ($valeurs['operation'] and $valeurs['service']) {
		if (($valeurs['operation'] != 'comparer_icone')) {
			$valeurs['_themes'] = rainette_lister_themes($valeurs['service'], 'local');
			if (($valeurs['operation'] == 'transcoder_weather')) {
				$valeurs['_themes_weather'] = rainette_lister_themes('weather', 'local');
			}
		} else {
			$valeurs['_icones'] = rainette_lister_codes($valeurs['service']);
		}
	}

	// -- Si on a déjà choisi un service on détermine si il propose ou pas des icones jour / nuit :
	//    weather et darksky ne fournissent pas un jeu d'icones par période
	if ($valeurs['service']) {
		$valeurs['_icone_journuit'] = true;
		if (in_array($valeurs['service'], array('weather', 'darksky'))) {
			$valeurs['_icone_journuit'] = false;
		}
	}

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 2;

	return $valeurs;
}


function formulaires_themer_rainette_traiter() {

	$retour = array();

	$operation = _request('operation');
	$service = _request('service');
	$periode = _request('periode');
	if ($periode == null) {
		$periode = 0;
	}

	include_spip('rainetheme_fonctions');
	include_spip('rainette_fonctions');
	if (($operation != 'comparer_icone')) {
		// Récupération de la liste des icônes.
		$theme = _request('theme');
		$icones = rainette_lister_icones($service, $theme, $periode);
		$retour['message_ok']['icones'][] = $icones;
		// Détermination du squelette à utiliser pour l'affichage.
		$retour['message_ok']['inclusion'] = 'afficher_theme';
	}

	if ($operation == 'comparer_theme') {
		// Récupération de la liste des icônes.
		$theme = _request('theme_2');
		$icones = rainette_lister_icones($service, $theme, $periode);
		$retour['message_ok']['icones'][] = $icones;
	}

	if ($operation == 'comparer_icone') {
		// Récupérer la liste des thèmes.
		$themes = rainette_lister_themes($service, 'local');

		// Construire la liste des icones
		$icone = _request('icone');
		foreach ($themes as $_theme => $_titre) {
			$icones[$_theme]['resume'] = $_titre;
			$icones[$_theme]['icone']['code'] = $icone;
			$nom_icone = $GLOBALS['rainette_icones'][$service][$icone]['icone'][$periode];
			$icones[$_theme]['icone']['source'] = find_in_path(icone_local_normaliser("${nom_icone}.png", $service, $_theme));
		}
		$retour['message_ok']['icones'] = $icones;
		$retour['message_ok']['inclusion'] = 'comparer_icone';
	}

	if ($operation == 'transcoder_weather') {
		// Récupération du thème de weather.
		$theme = _request('theme_2');

		// Récupération et normalisation des données de configuration utilisateur afin de disposer du transcodage
		// weather.com.
		include_spip("services/${service}");
		$configurer = "${service}_service2configuration";
		$configuration = $configurer('service');

		// Construire la liste des icones
		$traduire = charger_fonction('traduire', 'inc');
		$icones = array();
		foreach (array_keys($retour['message_ok']['icones'][0]) as $_code) {
			$code_weather = $configuration['transcodage_weather'][$_code][$periode];
			$icones[$_code]['resume'] = $traduire("rainette:meteo_${code_weather}", 'en');;
			$icones[$_code]['icone']['code'] = $code_weather;
			$icones[$_code]['icone']['source'] = find_in_path(icone_local_normaliser("${code_weather}.png", 'weather', $theme));
		}
		$retour['message_ok']['icones'][] = $icones;
	}

	$retour['editable'] = true;

	return $retour;
}
