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
 * @uses wikipedia_get_page()
 * @uses convertisseur_texte_spip()
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

	// Initialisation des paramètres du formulaire.
	$valeurs['langue'] = _request('langue');

	// Liste des langues utilisées
	include_spip('inc/config');
	$langues_utilisees = lire_config('taxonomie/langues_utilisees');
	foreach ($langues_utilisees as $_code_langue) {
		$valeurs['_langues'][$_code_langue] = traduire_nom_langue($_code_langue);
	}

	// Langue par défaut: soit la langue en cours si elle existe dans la liste des langues utilisées, soit la
	// première de cette même liste.
	$langue_spip = !empty($GLOBALS['lang']) ? $GLOBALS['lang'] : $GLOBALS['spip_lang'];
	if (array_key_exists($langue_spip, $valeurs['_langues'])) {
		$valeurs['_langue_defaut'] = $langue_spip;
	} else {
		reset($valeurs['_langues']);
		$valeurs['_langue_defaut'] = key($valeurs['_langues']);
	}

	// Récupération des informations de base du taxon
	$select = array('tsn', 'nom_scientifique');
	$from = array('id_taxon=' . sql_quote($id_taxon));
	$taxon = sql_fetsel($select, 'spip_taxons', $from);

	// Si on a déjà choisi une langue, on peut accéder à Wikipedia avec le nom scientifique et retourner
	// les pages trouvées (étape 2).
	if ($valeurs['langue']) {
		// Récupération d'une page wikipedia matchant avec le nom scientifique du taxon.
		// L'API renvoie aussi d'autres pages qui peuvent potentiellement être plus pertinentes.
		include_spip('services/wikipedia/wikipedia_api');
		$recherche = array('name' => $taxon['nom_scientifique'], 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $valeurs['langue']);

		// On convertit le descriptif afin de visualiser un texte plus clair.
		$valeurs['_descriptif'] = '';
		if (!empty($information['text'])) {
			// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
			// Mise en format multi systématique.
			include_spip('inc/filtres');
			$convertir = chercher_filtre('convertisseur_texte_spip');
			$valeurs['_descriptif'] = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];
		}

		// On prépare la liste des choix possibles si le texte récupéré n'est pas le bon.
		$valeurs['_liens'] = array();
		if ($information['links']) {
			$valeurs['_liens'][$taxon['nom_scientifique']] = _T('taxonomie:label_wikipedia_alternative_defaut');
			foreach ($information['links'] as $_liens) {
				$valeurs['_liens'][$_liens['title']] = _T('taxonomie:label_wikipedia_alternative', array('alternative' => $_liens['title']));
			}
		}
		$valeurs['_lien_defaut'] = $taxon['nom_scientifique'];
	}

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 2;

	return $valeurs;
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
function formulaires_decrire_taxon_traiter($id_taxon) {
	$retour = array();

	// Initialisation des saisies.
	$langue = _request('langue');
	$choix_descriptif = _request('choix_descriptif');

	// Récupération des informations de base du taxon
	$select = array('tsn', 'nom_scientifique', 'edite', 'descriptif', 'sources');
	$from = array('id_taxon=' . sql_quote($id_taxon));
	$taxon = sql_fetsel($select, 'spip_taxons', $from);

	// Récupération de la page wikipedia choisie:
	include_spip('services/wikipedia/wikipedia_api');
	if ($choix_descriptif == $taxon['nom_scientifique']) {
		// Le descriptif déjà fourni par défaut est le bon. On ne met pas à jour le cache.
		$recherche = array('name' => $taxon['nom_scientifique'], 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $langue);
	} else {
		// On a choisit une autre page que celle par défaut : on recharge le cache avec la nouvelle recherche.
		$recherche = array('name' => $choix_descriptif, 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $langue, null, array('reload' => true));
	}

	// On convertit le descriptif afin de proposer un texte plus clair.
	if (!empty($information['text'])) {
		// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
		// Mise en format multi systématique et limitation de la chaine à 20000 caractères.
		// TODO : revoir le calcul des 20000
		include_spip('inc/filtres');
		$convertir = chercher_filtre('convertisseur_texte_spip');
		$texte_converti = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];
		$texte_converti = '<multi>'
						  . '[' . $langue . ']'
						  . substr($texte_converti, 0, 20000)
						  . '</multi>';
		// Mise à jour pour le taxon du descriptif et des champs connexes en base de données
		$maj = array();
		// - le texte du descriptif est inséré dans la langue choisie en mergeant avec l'existant
		//   si besoin. On limite la taille du descriptif pour éviter un problème lors de l'update
		include_spip('inc/taxonomer');
		$maj['descriptif'] = taxon_merger_traductions($texte_converti, $taxon['descriptif']);
		// - l'indicateur d'édition est positionné à oui
		$maj['edite'] = 'oui';
		// - la source wikipédia est ajoutée (ou écrasée si elle existe déjà)
		$maj['sources'] = array('wikipedia' => array('champs' => array('descriptif')));
		if ($sources = unserialize($taxon['sources'])) {
			$maj['sources'] = array_merge($maj['sources'], $sources);
		}
		$maj['sources'] = serialize($maj['sources']);
		// - Mise à jour
		sql_updateq('spip_taxons', $maj, 'id_taxon=' . sql_quote($id_taxon));

		// Redirection vers la page d'édition du taxon
		$retour['redirect'] = parametre_url(generer_url_ecrire('taxon_edit'), 'id_taxon', $id_taxon);
	} else {
		$retour['message_erreur'] = _T('taxonomie:erreur_wikipedia_descriptif');
	}

	$retour['editable'] = true;

	return $retour;
}
