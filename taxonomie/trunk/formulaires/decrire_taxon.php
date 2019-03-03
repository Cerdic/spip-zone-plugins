<?php
/**
 * Gestion du formulaire d'initialisation du descriptif ou du texte d'un taxon à partir de Wikipedia.
 *
 * @package    SPIP\TAXONOMIE\TAXON
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire récupère une page wikipedia pour le descriptif du taxon.
 * Le formulaire propose une page par défaut mais aussi une liste d'autres pages qui matchent avec le taxon.
 *
 * @uses wikipedia_get_page()
 * @uses convertisseur_texte_spip()
 *
 * @param int    $id_taxon
 *      Id du taxon concerné.
 * @param string $element
 *      Elément de contenu qui sera initialisé. Prend les valeurs `texte` ou `descriptif`.
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_langues`        : tableau des noms de langue utilisables indexé par le code de langue SPIP (étape 1).
 * 		- `_langue_defaut` : code de langue SPIP par défaut (étape 1).
 * 		- `langue`         : code de langue SPIP choisi lors de l'étape 1
 * 		- `_liens`         : liste des liens possibles pour la recherche (étape 2)
 * 		- `_lien_defaut`   : lien par défaut (étape 2)
 * 		- `_page`          : texte de la page trouvée ou choisie par l'utilisateur (étape 2)
 * 		- `_etapes`        : nombre d'étapes du formulaire, à savoir, 2.
 */
function formulaires_decrire_taxon_charger($id_taxon, $element) {

	// Initialisation du chargement.
	$valeurs = array();

	// On passe l'élément de contenu concerné par l'initialisation.
	$valeurs['_element'] = $element;

	// Langue choisie pour la page wikipedia.
	$valeurs['langue'] = _request('langue');

	// Déterminer si un descriptif ou un texte explicatif existe pour une langue donnée.
	include_spip('inc/filtres');
	$traductions = array();
	if ($contenu = sql_getfetsel($element, 'spip_taxons', array('id_taxon=' . sql_quote($id_taxon)))) {
		$contenu = trim($contenu);
		if (preg_match(_EXTRAIRE_MULTI, $contenu, $match)) {
			$contenu = trim($match[1]);
		}
		$traductions = extraire_trads($contenu);
	}

	// Liste des langues utilisées par le plugin.
	include_spip('inc/config');
	$langues_utilisees = lire_config('taxonomie/langues_utilisees');
	foreach ($langues_utilisees as $_code_langue) {
		$valeurs['_langues'][$_code_langue] = traduire_nom_langue($_code_langue);
		if ($traductions and array_key_exists($_code_langue, $traductions)) {
			$valeurs['_langues'][$_code_langue] .= ' (' . _T('taxonomie:info_element_existe') .')';
		}
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

	// Initialisation des paramètres du formulaire utilisés en étape 2 et mis à jour dans la vérification
	// de l'étape 1.
	$valeurs['_page'] = _request('_page');
	$valeurs['_liens'] = _request('_liens');
	$valeurs['_lien_defaut'] = _request('_lien_defaut');

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 2;

	return $valeurs;
}

/**
 * Vérification de l'étape 1 du formulaire : si une langue est choisie, on charge la page recherchée et les liens
 * vers les autres pages éventuelles. Si aucun page n'est disponible on renvoie un message d'erreur.
 *
 * @uses wikipedia_get_page()
 * @uses convertisseur_texte_spip()
 *
 * @param int    $id_taxon
 *      Id du taxon concerné.
 * @param string $element
 *      Elément de contenu qui sera initialisé. Prend les valeurs `texte` ou `descriptif`.
 *
 * @return array
 * 		Message d'erreur si aucune page n'est disponible ou chargement des champs utiles à l'étape 2 sinon.
 *      Ces champs sont :
 * 		- `_liens`         : liste des liens possibles pour la recherche (étape 2)
 * 		- `_lien_defaut`   : lien par défaut (étape 2)
 * 		- `_page`    : texte de la page trouvée ou choisie par l'utilisateur (étape 2)
 */
function formulaires_decrire_taxon_verifier_1($id_taxon, $element) {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	// Si on a déjà choisi une langue, on peut accéder à Wikipedia avec le nom scientifique et retourner
	// les pages trouvées (étape 2).
	if ($langue = _request('langue')) {
		$valeurs = array();

		// Récupération des informations de base du taxon
		$select = array('tsn', 'nom_scientifique');
		$where = array('id_taxon=' . sql_quote($id_taxon));
		$taxon = sql_fetsel($select, 'spip_taxons', $where);

		// Récupération d'une page wikipedia matchant avec le nom scientifique du taxon.
		// L'API renvoie aussi d'autres pages qui peuvent potentiellement être plus pertinentes.
		include_spip('services/wikipedia/wikipedia_api');
		$recherche = array('name' => $taxon['nom_scientifique'], 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $langue);

		// On convertit le descriptif afin de visualiser un texte plus clair.
		$valeurs['_page'] = '';
		if (!empty($information['text'])) {
			// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
			// Mise en format multi systématique.
			include_spip('inc/filtres');
			$convertir = chercher_filtre('convertisseur_texte_spip');
			$valeurs['_page'] = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];

			// On prépare la liste des choix possibles si le texte récupéré n'est pas le bon.
			$valeurs['_liens'] = array();
			$valeurs['_liens'][$taxon['nom_scientifique']] = _T('taxonomie:label_wikipedia_alternative_defaut');
			if (!empty($information['links'])) {
				foreach ($information['links'] as $_liens) {
					$valeurs['_liens'][$_liens['title']] = _T('taxonomie:label_wikipedia_alternative', array('alternative' => $_liens['title']));
				}
			}
			$valeurs['_lien_defaut'] = $taxon['nom_scientifique'];

			// On fournit ces informations au formulaire pour l'étape 2.
			foreach ($valeurs as $_champ => $_valeur) {
				set_request($_champ, $_valeur);
			}
		} else {
			$erreurs['message_erreur'] = _T('taxonomie:erreur_wikipedia_page');
		}
	}

	return $erreurs;
}


/**
 * Exécution du formulaire : si une page est choisie et existe le descriptif est inséré dans le taxon concerné
 * et le formulaire renvoie sur la page d'édition du taxon.
 *
 * @uses wikipedia_get_page()
 * @uses convertisseur_texte_spip()
 * @uses taxon_merger_traductions()
 *
 * @param int    $id_taxon
 *      Id du taxon concerné.
 * @param string $element
 *      Elément de contenu qui sera initialisé. Prend les valeurs `texte` ou `descriptif`.
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_decrire_taxon_traiter($id_taxon, $element) {
	$retour = array();

	// Initialisation des saisies.
	$langue = _request('langue');
	$choix_page = _request('choix_page');

	// Récupération des informations de base du taxon
	$select = array('tsn', 'nom_scientifique', 'edite', $element, 'sources');
	$where = array('id_taxon=' . intval($id_taxon));
	$taxon = sql_fetsel($select, 'spip_taxons', $where);

	// Récupération de la page wikipedia choisie:
	include_spip('services/wikipedia/wikipedia_api');
	if (strtolower($choix_page) == strtolower($taxon['nom_scientifique'])) {
		// La page déjà fournie par défaut est la bonne. On ne met pas à jour le cache.
		$recherche = array('name' => $taxon['nom_scientifique'], 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $langue);
	} else {
		// On a choisit une autre page que celle par défaut : on recharge le cache avec la nouvelle recherche.
		$recherche = array('name' => $choix_page, 'tsn' => $taxon['tsn']);
		$information = wikipedia_get_page($recherche, $langue, null, array('reload' => true));
	}

	// On convertit le contenu de la page afin de proposer un texte plus clair possible.
	if (!empty($information['text'])) {
		// Si le plugin Convertisseur est actif, conversion du texte mediawiki vers SPIP.
		include_spip('inc/filtres');
		$convertir = chercher_filtre('convertisseur_texte_spip');
		$texte_converti = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];

		// Mise en format multi systématique et limitation de la chaîne en fonction du nombre de langues utilisées.
		include_spip('inc/config');
		$langues_utilisees = lire_config('taxonomie/langues_utilisees');
		$limite_texte = floor(65535 / (count($langues_utilisees) + 1));
		$texte_converti = '<multi>'
						  . '[' . $langue . ']'
						  . ($element=='descriptif' ? substr($texte_converti, 0, $limite_texte) : $texte_converti)
						  . '</multi>';
		// Mise à jour pour le taxon de l'élément textuel concerné et des champs connexes.
		$maj = array();
		// - le texte ou le descriptif est inséré dans la langue choisie en mergeant avec l'existant
		//   si besoin. On limite la taille du descriptif pour éviter un problème lors de l'update
		include_spip('inc/taxonomie');
		$maj[$element] = taxon_merger_traductions($texte_converti, $taxon[$element]);
		// - la source wikipedia est ajoutée (ou écrasée si elle existe déjà) et on met à jour la liste des champs
		$maj['sources'] = unserialize($taxon['sources']);
		if (isset($maj['sources']['wikipedia'])) {
			if (!in_array($element, $maj['sources']['wikipedia']['champs'])) {
				$maj['sources']['wikipedia']['champs'][] = $element;
			}
		} else {
			$maj['sources']['wikipedia'] = array('champs' => array($element));
		}
		$maj['sources'] = serialize($maj['sources']);
		// - Mise à jour
		include_spip('action/editer_objet');
		objet_modifier('taxon', intval($id_taxon), $maj);

		// Redirection vers la page d'édition du taxon
		$retour['redirect'] = parametre_url(generer_url_ecrire('taxon_edit'), 'id_taxon', $id_taxon);
	} else {
		$retour['message_erreur'] = _T('taxonomie:erreur_wikipedia_page');
	}

	return $retour;
}
