<?php
/**
 * Gestion du formulaire de création d'une espèce.
 *
 * @package    SPIP\TAXONOMIE\ESPECE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
};


/**
 * Chargement des données :
 *
 * @uses taxonomie_regne_existe()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire dans l'étape 1.
 *      - `type_recherche`         : (saisie) type de la recherche par nom scientifique (`scientificname`)
 *                                   ou nom commun (`commonname`).
 * 		- `recherche`              : (saisie) texte de la recherche.
 * 		- `correspondance`         : (saisie) indique si on doit rechercher le texte exact ou pas.
 * 		- `regne`                  : (saisie) règne d'appartenance de l'espèce pour limiter le scope de recherche.
 * 		- `_types_recherche`       : (affichage) recherche par nom scientifique ou par nom commun.
 * 		- `_type_recherche_defaut` : (affichage) le type de recherche par défaut est toujours `nom_scientifique`.
 * 		- `_regnes`                : (affichage) liste des règnes déjà chargés dans la base de taxonomie.
 * 		- `_regne_defaut`          : (affichage) le règne par défaut qui est toujours le premier de la liste.
 * 		- `_etapes`                : (affichage) nombre d'étapes du formulaire, à savoir, 3.
 */
function formulaires_creer_espece_charger() {

	// Initialisation du chargement.
	$valeurs = array();

	// Paramètres de saisie de l'étape 1
	// Type et nature de la recherche, texte de la recherche (qui peut-être non vide si on affiche une erreur
	// dans la vérification 1) et règne.
	$valeurs['type_recherche'] = _request('type_recherche');
	$valeurs['recherche'] = _request('recherche');
	$valeurs['correspondance'] = _request('correspondance');
	$valeurs['regne'] = _request('regne');

	// Types de recherche et défaut.
	$types = array(
		'scientificname' => 'nom_scientifique',
		'commonname'     => 'nom_commun'
	);
	foreach ($types as $_type_en => $_type_fr) {
		$valeurs['_types_recherche'][$_type_en] = _T("taxon:champ_${_type_fr}_label");
	}
	$valeurs['_type_recherche_defaut'] = 'scientificname';

	// Types de correspondance et défaut.
	$correspondances = array('exact', 'contenu', 'debut', 'fin');
	foreach ($correspondances as $_correspondance) {
		$valeurs['_correspondances'][$_correspondance] = _T("taxonomie:label_recherche_correspondance_${_correspondance}");
	}
	$valeurs['_correspondance_defaut'] = 'exact';

	// Acquérir la liste des règnes déjà chargés. Si un règne n'est pas chargé il n'apparait pas dans la liste
	// car il ne sera alors pas possible de créer correctement l'espèce avec sa hiérarchie de taxons.
	include_spip('inc/taxonomer');
	include_spip('taxonomie_fonctions');
	$regnes = explode(':', _TAXONOMIE_REGNES);
	foreach ($regnes as $_regne) {
		if (taxonomie_regne_existe($_regne, $meta_regne)) {
			$valeurs['_regnes'][$_regne] = ucfirst(_T("taxonomie:regne_${_regne}"));
		}
	}
	// On force toujours un règne, le premier de la liste.
	reset($valeurs['_regnes']);
	$valeurs['_regne_defaut'] = key($valeurs['_regnes']);

	// Initialisation des paramètres du formulaire utilisés en étape 2 et 3 et mis à jour dans la vérification
	// de l'étape 1.
	// -- Etape 2
	$valeurs['_taxons'] = _request('_taxons');
	$valeurs['_taxon_defaut'] = _request('_taxon_defaut');
	$valeurs['_resume'] = _request('_resume');
	// -- Etape 3
	$valeurs['tsn'] = _request('tsn');
	$valeurs['_espece'] = _request('_espece');
	$valeurs['_parent'] = _request('_parent');

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 3;

	return $valeurs;
}

/**
 * Vérification de l'étape 1 du formulaire :
 *
 * @uses itis_search_tsn()
 * @uses itis_get_information()
 * @uses itis_get_record()
 *
 * @return array
 *        Message d'erreur si aucun taxon disponible ou si il existe une erreur dans les saisies ou
 *        chargement (set_request) des champs utiles à l'étape 2 sinon. Ces champs sont :
 * 	      - `_taxons`       : (affichage) liste des taxons correspondant à la recherche (tsn, nom scientifique et rang).
 * 	      - `_taxon_defaut` : (affichage) tsn du taxon choisi par défaut.
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
			$correspondance = _request('correspondance');
			$recherche_exacte = ($correspondance == 'exact');
			$regne =  _request('regne');
			// -- suppression des espaces en trop dans la chaîne de recherche pour permettre la comparaison
			//    avec le combinedName ou le commonName d'ITIS.
			$recherche = preg_replace('#\s{2,}#', ' ', $recherche);

			// Appel de l'API de recherche d'ITIS en fonction du type et de la correspondance de recherche
			include_spip('services/itis/itis_api');
			$action = $type_recherche;
			if (($type_recherche == 'commonname') and ($correspondance == 'debut')) {
				$action = 'commonnamebegin';
			} elseif (($type_recherche == 'commonname') and ($correspondance == 'fin')) {
				$action = 'commonnameend';
			}
			$taxons = itis_search_tsn($action, $recherche, $recherche_exacte);

			if ($taxons) {
			// Construire le tableau des taxons trouvés en supprimant les taxons qui n'appartiennent pas
				// au règne concerné ou qui n'ont pas un rang compatible (uniquement pour la recherche par nom commun).
				$valeurs['_taxon_defaut'] = 0;
				foreach ($taxons as $_taxon) {
					if ($type_recherche == 'scientificname') {
						if (strcasecmp($_taxon['regne'], $regne) === 0) {
							// On recherche le rang de chaque taxon pour l'afficher avec le nom scientifique mais
							// aussi pour vérifier que ce rang est compatible avec une espèce si on cherche par nom commun.
							$rang = itis_get_information('rankname', $_taxon['tsn']);
							$valeurs['_taxons'][$_taxon['tsn']] = '<span class="nom_scientifique_inline">'
								. $_taxon['nom_scientifique']
								. '</span>'
								. ' - '
								. _T('taxonomie:rang_' . $rang);
							if (strcasecmp($recherche, $_taxon['nom_scientifique']) === 0) {
								$valeurs['_taxon_defaut'] = $_taxon['tsn'];
							}
						}
					} else {
						$record = itis_get_record($_taxon['tsn']);
						if (strcasecmp($record['regne'], $regne) === 0) {
							$valeurs['_taxons'][$_taxon['tsn']] = $_taxon['nom_commun']
								. " [{$_taxon['langage']}]"
								. ' - '
								. _T('taxonomie:rang_' . $record['rang']);
							if (strcasecmp($recherche, $_taxon['nom_commun']) === 0) {
								$valeurs['_taxon_defaut'] = $_taxon['tsn'];
							}
						}
					}
				}
				if (!$valeurs['_taxon_defaut']) {
					reset($valeurs['_taxons']);
					$valeurs['_taxon_defaut'] = key($valeurs['_taxons']);
				}

				// On fournit ces informations au formulaire pour l'étape 2.
				foreach ($valeurs as $_champ => $_valeur) {
					set_request($_champ, $_valeur);
				}
			} else {
				$erreurs['message_erreur'] = _T('taxonomie:erreur_recherche_aucun_taxon');
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
 * Vérification de l'étape 2 du formulaire : on présente les informations principales du taxon choisi avant
 * que l'utilisateur ne valide définitivement son choix.
 *
 * @uses itis_get_record()
 *
 * @return array
 *        Message d'erreur si le service ITIS ne renvoie pas les informations demandées (a priori jamais) ou
 *        chargement (set_request) des champs utiles à l'étape 3 sinon. Ces champs sont :
 * 		- `_espece` : (affichage) toutes les informations ITIS sur l'espèce.
 * 		- `_parent` : (affichage) toutes les informations ITIS sur le parent direct de l'espèce.
 */
function formulaires_creer_espece_verifier_2() {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	if ($tsn = intval(_request('tsn'))) {
		// On récupère les informations de base du taxon afin de les présenter à l'utilisateur pour validation
		// finale.
		include_spip('services/itis/itis_api');
		$espece = itis_get_record($tsn);
		if ($espece) {
			// On passe la description de l'espèce après avoir construit la liste des noms communs utiles.
			$nom_commun = '';
			if ($espece['nom_commun']) {
				include_spip('inc/config');
				$langues_utilisees = lire_config('taxonomie/langues_utilisees');
				foreach ($espece['nom_commun'] as $_langue => $_nom) {
					if (in_array($_langue, $langues_utilisees)) {
						$nom_commun .= ($nom_commun ? '<br />': '') . '[' . $_langue .'] ' . $_nom;
					}
				}
			}
			$espece['nom_commun_utilise'] = $nom_commun;
			set_request('_espece', $espece);

			// Détermination du parent : on récupère son record complet
			$parent = itis_get_record($espece['tsn_parent']);
			if ($parent) {
				set_request('_parent', $parent);
			} else {
				$erreurs['message_erreur'] = _T('taxonomie:erreur_acces_taxon');
			}
		} else {
			$erreurs['message_erreur'] = _T('taxonomie:erreur_acces_taxon');
		}
	} else {
		$erreurs['message_erreur'] = _T('taxonomie:erreur_acces_taxon');
	}

	return $erreurs;
}


/**
 * Vérification de l'étape 3 du formulaire : on vérifie que l'espèce n'existe pas déjà dans la base de données
 * taxonomique.
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
function formulaires_creer_espece_verifier_3() {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	if ($tsn = intval(_request('tsn'))) {
		// On vérifie que l'espèce n'a pas déjà été créée et possède un statut autre que refusé ou poubelle.
		if (sql_countsel('spip_especes', array('tsn=' . $tsn))) {
			$erreurs['message_erreur'] = _T('taxonomie:erreur_espece_deja_creee');
		}
	} else {
		$erreurs['message_erreur'] = _T('taxonomie:erreur_acces_taxon');
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

	if ($tsn = intval(_request('tsn'))) {
		// Vérification de l'existence du parent.
		// Si le parent n'existe pas en base c'est soit une erreur si le rang supérieur ou égal au genre soit
		// normal si le rang est un sous-genre. En effet, les sous-genres sont uniquement créés au coups par coups
		// quand on crée les espèces.

		// Ajout de l'espèce en base
		// Si le rang est bien espèce alors on ajoute que ce taxon. Sinon il faut ajouter toute l'arborecence jusqu'au
		// taxon d'espèce.

		// Ajout du parent si nécessaire.

		// Redirection vers la page d'édition du taxon
		$id_espece = 0;
		$retour['redirect'] = parametre_url(generer_url_ecrire('espece_edit'), 'id_espece', $id_espece);
	} else {
		$retour['message_erreur'] = _T('taxonomie:erreur_inconnue');
	}

	return $retour;
}
