<?php
/**
 * Gestion du formulaire de chargement du descriptif d'un taxon à partir de Wikipedia.
 *
 * @package    SPIP\TAXONOMIE\TAXON
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire récupère une page wikipedia pour le descriptif du taxon.
 * Le formulaire propose ce descriptif mais aussi une liste d'autres pages qui matchent avec le taxon.
 *
 * @uses taxonomie_regne_existe()
 * @uses taxonomie_regne_lister_rangs()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_actions_regnes`		: (affichage) alias et libellés des actions possibles sur un règne, `charger` et `vider`
 * 		- `_actions_disable`	: (affichage) liste des actions désactivées (`vider` si le règne n`est pas chargé)
 * 		- `_action_defaut`		: (affichage) action sélectionnée par défaut, `charger`
 * 		- `_regnes`				: (affichage) noms scientifiques et libellés des règnes supportés par le plugin
 * 		- `_types_rang`			: (affichage) type de rang à charger parmi principal, secondaire et intercalaire
 * 		- `_type_rang_defaut`	: (affichage) type de rang par défaut, à savoir `principal`
 * 		- `_langues_regne`		: (affichage) codes de langue SPIP et libellés des langues utilisées (configuration)
 * 		- `_langue_defaut`		: (affichage) la première langue de la liste des langues utilisées
 */
function formulaires_decrire_taxon_charger($id_taxon) {
	$valeurs = array();

	// Récupération des informations de base du taxon
	$select = array('tsn', 'nom_scientifique', 'edite', 'descriptif');
	$from = array('id_taxon=' . sql_quote($id_taxon));
	$taxon = sql_fetsel($select, 'spip_taxons', $from);

	// Récupération d'une page wikipédia matchant avec le nom scientifique du taxon.
	// L'API renvoie aussi d'autres pages qui peuvent potentiellement être plus pertinentes ou pas.
	include_spip('services/wikipedia/wikipedia_api');
	$langue = wikipedia_find_language('fr');
	$recherche = array('name' => $taxon['nom_scientifique'], 'tsn' => $taxon['tsn']);
	$information = wikipedia_get_page($recherche, $langue);

	// On convertit le descriptif afin de visualiser un texte plus clair.
	$valeurs['_descriptif'] = '';
	if ($information['text']) {
		// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
		// Mise en format multi systématique.
		include_spip('inc/filtres');
		$convertir = chercher_filtre('convertisseur_texte_spip');
		$valeurs['_descriptif'] = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];
	}

	// On prépare la liste des choix possibles si le texte récupéré n'est pas le bon.
	$valeurs['_liens'] = array();
	if ($information['links']) {
		$valeurs['_liens'][$taxon['nom_scientifique']] = _T('taxonomie:label_descriptif_ok');
		foreach ($information['links'] as $_liens) {
			$valeurs['_liens'][$_liens['title']] = _T('taxonomie:label_alternative', array('alternative' => $_liens['title']));
		}
	}
	$valeurs['_lien_defaut'] = $taxon['nom_scientifique'];

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir une action (`vider` ou `charger`) et
 * un règne.
 * Un rang minimal est toujours sélectionné. La saisie des langues des noms communs est optionnelle.
 *
 * @return array
 * 		Tableau des erreurs sur l'action et/ou le règne ou tableau vide si aucune erreur.
 */
function formulaires_decrire_taxon_verifier() {
	$erreurs = array();

	$obligatoires = array();
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire))
			$erreurs[$_obligatoire] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Exécution du formulaire : le règne choisi est soit vidé, soit chargé jusqu'au rang minimal
 * choisi en y intégrant les traductions des noms communs sélectionnées.
 *
 * @uses taxonomie_regne_existe()
 * @uses taxonomie_regne_vider()
 * @uses taxonomie_regne_charger()
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_decrire_taxon_traiter() {
	$retour = array();

	$retour['editable'] = true;

	return $retour;
}
