<?php
/**
 * Gestion du formulaire de création d'une espèce.
 *
 * @package    SPIP\TAXONOMIE\ESPECE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
};

if (!defined('_TAXONOMIE_RECHERCHE_MAX_ESPECES')) {
	/**
	 * Nombre de réponses maximal toléré pour continuer en étape 2.
	 */
	define('_TAXONOMIE_RECHERCHE_MAX_ESPECES', 35);
}

/**
 * Chargement des données :
 *
 * @uses regne_lister_defaut()
 * @uses regne_existe()
 *
 * @return array
 * 		Tableau des données à charger par le formulaire dans l'étape 1.
 *      - `type_recherche`         : (saisie) type de la recherche par nom scientifique (`scientificname`)
 *                                   ou nom commun (`commonname`).
 * 		- `correspondance`         : (saisie) indique si on doit rechercher le texte exact ou pas.
 * 		- `recherche`              : (saisie) texte de la recherche.
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
	include_spip('inc/taxonomie');
	$regnes = regne_lister_defaut();
	foreach ($regnes as $_regne) {
		if (regne_existe($_regne, $meta_regne)) {
			$valeurs['_regnes'][$_regne] = ucfirst(_T("taxonomie:regne_${_regne}"));
		}
	}
	// On force toujours un règne, le premier de la liste.
	reset($valeurs['_regnes']);
	$valeurs['_regne_defaut'] = key($valeurs['_regnes']);

	// Initialisation des paramètres du formulaire utilisés en étape 2 et 3 et mis à jour dans les vérifications
	// de l'étape 1 et 2.
	// -- Etape 2 (vérification 1)
	$valeurs['_taxons'] = _request('_taxons');
	$valeurs['_taxon_defaut'] = _request('_taxon_defaut');
	// -- Etape 3 (vérification 2)
	$valeurs['tsn'] = _request('tsn');
	$valeurs['_espece'] = _request('_espece');
	$valeurs['_parents'] = _request('_parents');

	// Préciser le nombre d'étapes du formulaire
	$valeurs['_etapes'] = 3;

	return $valeurs;
}

/**
 * Vérification de l'étape 1 du formulaire :
 *
 * @uses itis_search_tsn()
 * @uses itis_get_record()
 *
 * @return array
 *        Message d'erreur si aucun taxon disponible ou si il existe une erreur dans les saisies.
 *        Sinon, chargement des champs utiles à l'étape 2 :
 * 	      - `_taxons`       : (affichage) liste des taxons correspondant à la recherche (tsn, nom scientifique et rang).
 * 	      - `_taxon_defaut` : (affichage) tsn du taxon choisi par défaut.
  */
function formulaires_creer_espece_verifier_1() {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	// Il est inutile de vérifier à nouveau les valeurs de l'étape 1 si on est dans une étape ultérieure
	// car on ne modifie rien. On gagne du temps en passant outre.
	if (_request('_etape') == 1) {
		// Si on a déjà choisi une langue, on peut accéder à Wikipedia avec le nom scientifique et retourner
		// les pages trouvées (étape 2).
		if ($recherche = ltrim(_request('recherche'))) {
			// On récupère le type de recherche et la correspondance.
			$type_recherche = _request('type_recherche');
			$correspondance = _request('correspondance');
			$recherche_exacte = ($correspondance == 'exact');
			$recherche_commence_par = ($correspondance == 'debut');

			// Si la recherche est de type nom commun on ne peut rien vérifier sur le texte.
			// Si la recherche est de type nom scientifique, on vérifie que le texte de recherche :
			// - contient au moins deux mots
			// - que le deuxième mot n'est pas un 'x' (désigne uniquement un taxon de rang supérieur hybride)
			// - et que le deuxième mot n'est pas entre parenthèses (sous-genre).
			$recherche_conforme = true;
			if ($type_recherche == 'scientificname') {
				$nombre_mots = preg_match_all('#\w+#', $recherche, $mots);
				if (($nombre_mots < 2) and ($recherche_exacte)) {
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
				// -- récupération du règne
				$regne =  _request('regne');
				// -- suppression des espaces en trop dans la chaîne de recherche pour permettre la comparaison
				//    avec le combinedName ou le commonName d'ITIS.
				$recherche = preg_replace('#\s{2,}#', ' ', $recherche);

				// Appel de l'API de recherche d'ITIS en fonction du type et de la correspondance de recherche
				$action = $type_recherche;
				if (($type_recherche == 'commonname') and ($correspondance == 'debut')) {
					$action = 'commonnamebegin';
				} elseif (($type_recherche == 'commonname') and ($correspondance == 'fin')) {
					$action = 'commonnameend';
				}
				include_spip('services/itis/itis_api');
				$taxons = itis_search_tsn($action, $recherche, $recherche_exacte);
				if ($taxons) {
					if ($recherche_exacte) {
						// Si la correspondance est exacte, les informations de chaque taxon sont suffisantes pour limiter
						// d'emblée le nombre de taxon au seul qui correspond.
						// Néanmoins si un seul taxon est renvoyé rien est à faire car on a déjà le bon taxon.
						if (count($taxons) > 1) {
							$taxon_exact = array();
							foreach ($taxons as $_taxon) {
								if ((($type_recherche == 'scientificname') and (strcasecmp($_taxon['nom_scientifique'], $recherche) === 0))
								or (($type_recherche == 'commonname') and (strcasecmp($_taxon['nom_commun'], $recherche) === 0))) {
									$taxon_exact = $_taxon;
									break;
								}
							}
							$taxons = $taxon_exact ? array($taxon_exact) : array();
						}
					} elseif ($recherche_commence_par and ($type_recherche == 'scientificname')) {
						// Si la correspondance est 'commence par' et que l'on recherche par nom scientifique, les informations
						// de chaque taxon sont suffisantes pour limiter d'emblée le nombre de taxons à ceux qui commencent
						// par la recherche.
						foreach ($taxons as $_cle => $_taxon) {
							if (substr_compare($_taxon['nom_scientifique'], $recherche, 0, strlen($recherche), true) !== 0) {
								unset($taxons[$_cle]);
							}
						}
					}

					// Etant donné qu'on a filtré le tableau issu de l'appel au service ITIS on vérifie à nouveau que
					// ce tableau n'est pas vide.
					if ($taxons) {
						// Si le nombre de taxons récupérés est trop important on renvoie une erreur.
						if (count($taxons) <= _TAXONOMIE_RECHERCHE_MAX_ESPECES) {
							// Construire le tableau des taxons trouvés en supprimant:
							// - les taxons qui n'appartiennent pas au règne concerné
							// - ou qui n'ont pas un rang compatible (uniquement pour la recherche par nom commun)
							// - ou qui ne sont pas des appellations valides
							// - ou qui sont déjà créés.
							$valeurs['_taxons'] = array();
							$valeurs['_taxon_defaut'] = 0;
							include_spip('inc/taxonomie');
							foreach ($taxons as $_taxon) {
								if (!sql_countsel('spip_taxons', array('tsn=' . intval($_taxon['tsn'])))) {
									$taxon = itis_get_record($_taxon['tsn']);
									if (($taxon['usage_valide'])
									and (strcasecmp($taxon['regne'], $regne) === 0)
									and (rang_est_espece($taxon['rang_taxon']))) {
										if ($type_recherche == 'scientificname') {
											$valeurs['_taxons'][$taxon['tsn']] = '<span class="nom_scientifique_inline">'
												. $_taxon['nom_scientifique']
												. '</span>'
												. ' - '
												. _T('taxonomie:rang_' . $taxon['rang_taxon']);
											if (strcasecmp($recherche, $_taxon['nom_scientifique']) === 0) {
												$valeurs['_taxon_defaut'] = $taxon['tsn'];
											}
										} else {
											// Vérifier que ce rang est compatible avec une espèce ou un rang inférieur.
											$valeurs['_taxons'][$taxon['tsn']] = $_taxon['nom_commun']
												. " [{$_taxon['langage']}]"
												. ' - '
												. _T('taxonomie:rang_' . $taxon['rang_taxon']);
											if (strcasecmp($recherche, $_taxon['nom_commun']) === 0) {
												$valeurs['_taxon_defaut'] = $taxon['tsn'];
											}
										}
									}
								}
							}

							if ($valeurs['_taxons']) {
								// Si aucun taxon par défaut, on prend le premier taxon de la liste.
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
							$erreurs['message_erreur'] = _T('taxonomie:erreur_recherche_max_reponses', array('nb' => count($taxons)));
						}
					} else {
						$erreurs['message_erreur'] = _T('taxonomie:erreur_recherche_aucun_taxon');
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
	}

	return $erreurs;
}


/**
 * Vérification de l'étape 2 du formulaire : on présente les informations principales du taxon choisi avant
 * que l'utilisateur ne valide définitivement son choix. En particulier, on affiche la hiérarchie du taxon
 * jusqu'au premier taxon de genre et on identifie les taxons qui seront aussi créés dans cette hiérarchie.
 *
 * @uses itis_get_record()
 * @uses itis_get_information()
 * @uses rang_est_espece()
 *
 * @return array
 *        Message d'erreur si le service ITIS ne renvoie pas les informations demandées (a priori jamais).
 *        Sinon, chargement des champs utiles à l'étape 3 :
 * 		- `_espece`  : (affichage) toutes les informations ITIS sur l'espèce.
 * 		- `_parents` : (affichage) toutes les informations ITIS sur l'ascendance de l'espèce jusqu'au genre.
 */
function formulaires_creer_espece_verifier_2() {

	// Initialisation des erreurs de vérification.
	$erreurs = array();

	// Il est cette fois indispensable de vérifier à nouveau les valeurs de l'étape 2 si on est dans une l'étape 3
	// car on a besoin de l'espèce et de ses ascendants jusqu'au genre compris.
	if ($tsn = intval(_request('tsn'))) {
		// On récupère les informations de base du taxon afin de les présenter à l'utilisateur pour validation
		// finale. Ces informations existent forcément car elles ont été demandées à l'étape précédentes et son
		// donc accessibles directement dans un cache.
		include_spip('services/itis/itis_api');
		$espece = itis_get_record($tsn);

		// On passe au formulaire la description de l'espèce après avoir construit la liste des noms communs utiles.
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
		$espece['nom_commun_affiche'] = $nom_commun;
		set_request('_espece', $espece);

		// On récupère la hiérarchie complète du taxon à partir de la base ITIS.
		$ascendants = itis_get_information('hierarchyfull', $espece['tsn']);
		// Comme la hiérarchie intègre aussi le taxon concerné et les descendants on les supprime. Il y a donc a
		// minima toujours une suppression celle du taxon concerné.
		$index = count($ascendants);
		do {
			$index = $index - 1;
			$est_espece = $ascendants[$index]['tsn'] == $tsn;
			unset($ascendants[$index]);
		} while (!$est_espece);

		// On classe la liste des ascendants du plus proche au plus éloigné.
		include_spip('inc/taxonomie');
		$parents = array();
		krsort($ascendants);
		foreach ($ascendants as $_ascendant) {
			// Le premier ascendant est toujours affiché.
			$parent = $_ascendant;
			// On détermine si l'ascendant est un taxon d'espèce ou inférieur,
			// ou si c'est un taxon de rang supérieur à l'espèce.
			$parent['est_espece'] = rang_est_espece($_ascendant['rang_taxon']);
			// On indique si le parent existe déjà ou pas en base
			$parent['deja_cree'] = false;
			if (sql_countsel('spip_taxons', array('tsn=' . intval($_ascendant['tsn'])))) {
				$parent['deja_cree'] = true;
			}
			// On insère l'ascendant dans la liste des parents.
			$parents[] = $parent;
			// On sort si on est arrivé au taxon de genre.
			if ($_ascendant['rang_taxon'] == _TAXONOMIE_RANG_GENRE) {
				break;
			}
		}
		$parents = array_reverse($parents);
		set_request('_parents', $parents);
	} else {
		$erreurs['message_erreur'] = _T('taxonomie:erreur_formulaire_creer_espece');
	}

	return $erreurs;
}


/**
 * Exécution du formulaire : si une page est choisie et existe le descriptif est inséré dans le taxon concerné
 * et le formulaire renvoie sur la page d'édition du taxon.
 *
 * @uses itis_get_record()
 *
 * @return array
 * 		Tableau retourné par le formulaire contenant toujours un message de bonne exécution ou
 * 		d'erreur. L'indicateur editable est toujours à vrai.
 */
function formulaires_creer_espece_traiter() {

	// Initialisation du retour de la fonction
	$retour = array();

	if ($tsn = intval(_request('tsn'))) {
		// Récupération de la liste des champs de la table spip_taxons.
		include_spip('base/objets');
		$description_table = lister_tables_objets_sql('spip_taxons');
		$champs['spip_taxons'] = $description_table['field'];

		// On range la liste des taxons de plus haut rang (genre) à celui de plus petit rang et on ajoute le
		// taxon espèce en fin de liste. La variable _parents est toujours un tableau d'au moins une unité et
		// l'espèce existe toujours.
		$taxons = _request('_parents');
		$taxons[] = _request('_espece');

		// On boucle d'abord sur les parents si nécessaire et ensuite sur l'espèce.
		// De cette façon, on évite d'avoir une base incohérente où un taxon de rang inférieur existerait
		// sans son parent direct.
		// L'espèce concernée est identifiée car son enregistrement ne contient pas les index deja_cree et est_espece.
		$erreurs = array();
		foreach ($taxons as $_index => $_taxon) {
			if (empty($_taxon['deja_cree'])) {
				// Le genre est le premier parent de la liste ainsi triée et est forcément déjà créé.
  				// Les parents non créés sont donc soit des taxons comme les sous-genres etc, soit un taxon de rang
				// espèce ou inférieur.
				// -- On récupère le bloc des informations ITIS du taxon à créer et sa table de destination.
				$table = 'spip_taxons';
				if (isset($_taxon['deja_cree'])) {
					// C'est un ascendant de l'espèce
					$taxon = itis_get_record($_taxon['tsn']);
				} else {
					// C'est l'espèce
					$taxon = $_taxon;
				}
				// -- On ne retient que les index correspondant à des champs de la table concernée.
				$taxon = array_intersect_key($taxon, $champs[$table]);

				// On formate le nom commun en multi.
				$nom_multi = '';
				foreach ($taxon['nom_commun'] as $_langue => $_nom) {
					$nom_multi .= '[' . $_langue . ']' . trim($_nom);
				}
				if ($nom_multi) {
					$nom_multi = '<multi>' . $nom_multi . '</multi>';
				}
				$taxon['nom_commun'] = $nom_multi;

				// Finalisation de l'enregistrement du taxon suivant son rang (ie. sa table).
				// -- tous les taxons créés on des indicateurs d'édition et d'importation à 'non'
				$taxon['edite'] = 'non';
				$taxon['importe'] = 'non';
				if (isset($_taxon['est_espece']) and !$_taxon['est_espece']) {
					// Pour les taxons de rang supérieur à une espèce, on positionne le statut à 'publie'
					// comme pour tous les autres taxons de ce type (ceux importés via le fichier de règne).
					$taxon['espece'] = 'non';
					$taxon['statut'] = 'publie';
				} else {
					// Pour les taxons espèce et de rang inférieur, on positionne le statut à prop
					// (pas de publication par défaut).
					$taxon['espece'] = 'oui';
					$taxon['statut'] = 'prop';
				}

				// Insertion du taxon dans la table idoine.
				$id_taxon = sql_insertq($table, $taxon);
				if ($id_taxon) {
					if (!isset($_taxon['deja_cree'])) {
						$id_espece = $id_taxon;
					}
				} else {
					// En cas d'erreur on sort de la boucle pour éviter de créer des taxons sans parent.
					$erreurs = $taxon;
					break;
				}
			}
		}

		if ($erreurs) {
			$retour['message_erreur'] = _T('taxonomie:erreur_creation_taxon', array('taxon' => $erreurs['nom_scientifique']));
		} else {
			// Redirection vers la page d'édition du taxon
			$retour['redirect'] = parametre_url(generer_url_ecrire('taxon_edit'), 'id_taxon', $id_espece);
		}
	} else {
		$retour['message_erreur'] = _T('taxonomie:erreur_formulaire_creer_espece');
	}

	return $retour;
}
