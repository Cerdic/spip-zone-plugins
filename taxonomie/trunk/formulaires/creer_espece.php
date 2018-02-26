<?php
/**
 * Gestion du formulaire de chargement du descriptif d'un taxon à partir de Wikipedia.
 *
 * @package    SPIP\TAXONOMIE\ESPECE
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
};


/**
 * Chargement des données : le formulaire récupère une page wikipedia pour le descriptif du taxon.
 * Le formulaire propose ce descriptif mais aussi une liste d'autres pages qui matchent avec le taxon.
 *
 * @uses taxonomie_regne_existe()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage). Aucune donnée chargée n'est un
 * 		champ de saisie, celle-ci sont systématiquement remises à zéro.
 * 		- `_types_recherche`       : recherche par nom scientifique ou par nom commun.
 * 		- `_type_recherche_defaut` : le type de recherche par défaut est toujours `nom_scientifique`.
 * 		- `_regnes`                : liste des règnes déjà chargés dans la base de taxonomie.
 * 		- `_regne_defaut`          : le règne par défaut qui est toujours le premier de la liste.
 * 		- `_etapes`                : nombre d'étapes du formaulaire, à savoir, 3.
 */
function formulaires_creer_espece_charger() {

	// Initialisation du chargement.
	$valeurs = array();

	// Texte de la recherche (qui peut-être non vide si on affiche une erreur dans la vérification 1).
	$valeurs['recherche'] = _request('recherche');

	// Types de recherche et défaut.
	$types = array(
		'scientificname' => 'nom_scientifique',
		'commonname'     => 'nom_commun'
	);
	foreach ($types as $_type_en => $_type_fr) {
		$valeurs['_types_recherche'][$_type_en] = _T("taxon:champ_${_type_fr}_label");
	}
	$valeurs['_type_recherche_defaut'] = 'scientificname';

	// Acquérir la liste des règnes déjà chargés. Si un règne n'est pas chargé il n'apparait pas dans la liste
	// car il ne sera alors pas possible de créer correctement l'espèce avec sa hiérarchie de taxons.
	include_spip('inc/taxonomer');
	include_spip('taxonomie_fonctions');
	$regnes = explode(':', _TAXONOMIE_REGNES);
	foreach ($regnes as $_regne) {
		if (taxonomie_regne_existe($_regne, $meta_regne)) {
			$valeurs['_regnes'][$_regne] = '<span class="nom_scientifique">' . $_regne . '</span>, ' . _T("taxonomie:regne_${_regne}");
		}
	}
	// On force toujours un règne, le premier de la liste.
	reset($valeurs['_regnes']);
	$valeurs['_regne_defaut'] = key($valeurs['_regnes']);

	// Initialisation des paramètres du formulaire utilisés en étape 2 et mis à jour dans la vérification
	// de l'étape 1.
	$valeurs['taxons'] = array();

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 3;

	return $valeurs;
}

/**
 * Vérification de l'étape 1 du formulaire : si une langue est choisie, on charge la page recherchée et les liens
 * vers les autres pages éventuelles. Si aucun page n'est disponible on renvoie un message d'erreur.
 *
 * @uses wikipedia_get_page()
 * @uses convertisseur_texte_spip()
 *
 * @return array
 * 		Message d'erreur si aucune page n'est disponible ou chargement des champs utiles à l'étape 2 sinon.
 *      Ces champs sont :
 * 		- `_liens`         : liste des liens possibles pour la recherche (étape 2)
 * 		- `_lien_defaut`   : lien par défaut (étape 2)
 * 		- `_descriptif`    : texte de la page trouvée ou choisie par l'utilisateur (étape 2)
 */
function formulaires_creer_espece_verifier_1() {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	// Si on a déjà choisi une langue, on peut accéder à Wikipedia avec le nom scientifique et retourner
	// les pages trouvées (étape 2).
	if ($recherche = trim(_request('recherche'))) {
		// On récupère le type de recherche.
		$type_recherche = _request('type_recherche');

		// Si la recherche est de type nom common on ne peut rien vérifier sur le texte.
		// Si la recherche est de type nom scientifique, on vérifie que le texte de recherche :
		// - contient au moins deux mots
		// - que le deuxième mot n'est pas un 'x' (désigne uniquement un taxon de rang supérieur hybride)
		// - et que le deuxième mot n'est pas entre parenthèses (sous-genre).
		$recherche_conforme = true;
		if ($type_recherche == 'scientificname') {
			$nombre_mots = preg_match_all('#\w+#', $recherche, $mots);
			if ($nombre_mots < 2) {
				$recherche_conforme = false;
			} elseif ($nombre_mots == 2) {
				if ((strtolower($mots[0][1]) == 'x')
				or ((substr($mots[0][1], 0, 1) == '(') and (substr($mots[0][1], -1) == ')'))) {
					$recherche_conforme = false;
				}
			}
		}

		if ($recherche_conforme) {
			// On recherche le ou les taxons correspondant au texte saisi.
			// -- récupération des autres variables
			$recherche_stricte = _request('recherche_stricte') == 'on';
			$regne =  _request('regne');
			// -- suppression des espaces en trop dans la chaîne de recherche pour permettre la comparaison
			//    avec le combinedName d'ITIS
			$recherche = preg_replace('#\s{2,}#', ' ', $recherche);

			include_spip('services/itis/itis_api');
			$taxons = itis_search_tsn($type_recherche, $recherche, $recherche_stricte);
			if ($taxons) {
				// On construit le tableau des taxons trouvés en supprimant les taxons qui n'appartiennent pas
				// au règne concerné.
				foreach ($taxons as $_taxon) {
					if (strcasecmp($_taxon['regne'], $regne) === 0) {
						$valeur['_taxons'][$_taxon['tsn']] = $_taxon['nom_scientifique'];
					}
				}
			} else {
				$erreurs['message_erreur'] = _T('taxonomie:erreur_recherche_aucun_taxon',
					array('texte' => $recherche, 'regne' => '<span class="nom_scientifique">' . $regne . '</span>'));
			}
		} else {
			$erreurs['recherche'] = _T('taxonomie:erreur_recherche_nom_scientifique');
		}
	} else {
		$erreurs['recherche'] = _T('info_obligatoire');
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
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_creer_espece_traiter() {
	$retour = array();

	// Initialisation des saisies.
	$langue = _request('langue');
	$choix_descriptif = _request('choix_descriptif');

	// Récupération des informations de base du taxon
	$select = array('tsn', 'nom_scientifique', 'edite', 'descriptif', 'sources');
	$where = array('id_taxon=' . sql_quote($id_taxon));
	$taxon = sql_fetsel($select, 'spip_taxons', $where);

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
		include_spip('inc/filtres');
		$convertir = chercher_filtre('convertisseur_texte_spip');
		$texte_converti = $convertir ? $convertir($information['text'], 'MediaWiki_SPIP') : $information['text'];

		// Mise en format multi systématique et limitation de la chaîne en fonction du nombre de langues utilisées.
		include_spip('inc/config');
		$langues_utilisees = lire_config('taxonomie/langues_utilisees');
		$limite_texte = floor(65535 / (count($langues_utilisees) + 1));
		$texte_converti = '<multi>'
						  . '[' . $langue . ']'
						  . substr($texte_converti, 0, $limite_texte)
						  . '</multi>';
		// Mise à jour pour le taxon du descriptif et des champs connexes en base de données
		$maj = array();
		// - le texte du descriptif est inséré dans la langue choisie en mergeant avec l'existant
		//   si besoin. On limite la taille du descriptif pour éviter un problème lors de l'update
		include_spip('inc/taxonomer');
		$maj['descriptif'] = taxon_merger_traductions($texte_converti, $taxon['descriptif']);
		// - l'indicateur d'édition est positionné à oui
		$maj['edite'] = 'oui';
		// - la source wikipedia est ajoutée (ou écrasée si elle existe déjà)
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

	return $retour;
}
