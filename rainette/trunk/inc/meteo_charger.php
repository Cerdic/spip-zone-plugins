<?php
/**
 * Ce fichier contient la fonction standard de chargement et fourniture des données météo.
 * Elle s'applique à tous les services et à tous les types de données.
 *
 * @package SPIP\RAINETTE\CACHE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Renvoyer le contenu du fichier cache des données météos correspondant au lieu et au type de données choisis
 * après l'avoir éventuellement mis à jour.
 *
 * Si le fichier cache est obsolète ou absent, on le crée après avoir chargé puis phrasé le flux XML ou JSON
 * et stocké les données collectées et transcodées dans un tableau standardisé. L'appelant doit s'assurer que la
 * périodicité est compatible avec le service; cette fonction ne retourne donc que des erreurs de chargement.
 *
 * @uses cache_nommer()
 * @uses meteo_normaliser()
 * @uses indice2risque_uv()
 *
 * @param string $lieu
 *        Le lieu concerné par la méteo exprimé selon les critères requis par le service.
 * @param string $mode
 *        Le type de données météorologiques demandé :
 *            - `conditions`, la valeur par défaut
 *            - `previsions`
 *            - `infos`
 * @param int $periodicite
 *        La périodicité horaire des prévisions :
 *            - `24`, les prévisions de la journée
 *            - `12`, les prévisions du jour et de la nuit
 *            - `6`, les prévisions de la journée par période de 6h
 *            - `3`, les prévisions de la journée par période de 3h
 *            - `1`, les prévisions de la journée pour chaque heure
 *            - `0`, pour les modes `conditions` et `infos`
 * @param string $service
 *        Le nom abrégé du service :
 *            - `weather` pour le weather.com, la valeur par défaut car elle ne nécessite aucune inscription
 *            - `wwo` pour World Weather Online
 *            - `wunderground` pour Wunderground
 *            - `owm` pour Open Weather Map
 *            - `apixu` pour APIXU
 *            - `weatherbit` pour Weatherbit.io
 *
 * @return array
 *        Le contenu du fichier cache contenant les données à jour demandées.
 */
function inc_meteo_charger_dist($lieu, $mode = 'conditions', $periodicite = 0, $service = 'weather') {

	// Initialisation du tableau de sortie.
	$tableau = array();

	// Traitement des cas ou les arguments sont vides (ce qui est différent de non passés à l'appel)
	// On considère à ce stade que la cohérence entre le mode, la périodicité et le service (qui selon ne supporte
	// pas toutes les périodicités) est déjà assurée et n'est donc pas à tester.
	if (!$mode) {
		$mode = 'conditions';
		$periodicite = 0;
	}
	if (!$service) {
		$service = 'weather';
	}

	// En fonction du service, on inclut le fichier d'API.
	// Le principe est que chaque service propose la même liste de fonctions d'interface dans un fichier unique.
	include_spip("services/${service}");

	// Acquérir la configuration statique du service (periode, format, données...)
	$configurer = "${service}_service2configuration";
	$configuration = $configurer($mode);

	// Acquérir la configuration dynamique du service (celle modifiable par l'utilisateur via
	// le formulaire et stockée en BDD dans la table spip_meta) et la merger avec la configuration statique.
	// Cependant, celle-ci pouvant être incomplète on la complète par les valeurs par défaut quand
	// cela est nécessaire.
	include_spip('inc/rainette_normaliser');
	$configuration_utilisateur = parametrage_normaliser($service, $configuration['defauts']);

	// Concaténer l'ensemble des configurations.
	$configuration = array_merge($configuration, $configuration_utilisateur);

	// Si on a demandé le mode 'previsions' sans préciser la periodicité horaire des données, il faut prendre l'intervalle
	// par défaut configuré pour le service.
	if (($mode == 'previsions') and !$periodicite) {
		$periodicite = $configuration['previsions']['periodicite_defaut'];
	}

	// Construire le tableau identifiant le cache
 	$cache = cache_normaliser($lieu, $mode, $periodicite, $configuration);

	// Mise à jour du cache avec les nouvelles données météo si:
	// - le fichier cache n'existe pas
	// - la période de validité du cache est échue
	include_spip('inc/cache');
	if (!$fichier_cache = cache_est_valide('rainette', $cache)) {
		// Construire l'url de la requête
		$urler = "${service}_service2url";
		$url = $urler($lieu, $mode, $periodicite, $configuration);

		// Acquérir le flux XML ou JSON dans un tableau si les limites du service ne sont pas atteintes
		// et traiter les cas d'erreurs du plugin ou du service.
		include_spip('inc/rainette_requeter');
		$erreur = array(
			'type' => '',
			'service' => array(
				'code' => '',
				'message' => ''
			)
		);
		if (!requete_autorisee($configuration['offres']['limites'], $service)) {
			// La requête n'est pas autorisée parce qu'elle excède les limitations d'utilisation du service.
			// On renvoie une erreur pour prévenir les utilisateurs.
			$erreur['type'] = 'limite_service';
		} else {
			$flux = requeter($url, $configuration, $service);
			if (!empty($flux['erreur'])) {
				// Erreur lors du traitement de la requête due à l'URL ou à la conversion en XML ou JSON.
				// Cette erreur n'est pas retournée par le service.
				$erreur['type'] = $flux['erreur'];
			} else {
				// On teste une erreur d'acquisition renvoyée par le service. Pour tous les services, une cle de base
				// est explicitement utilisée pour distinguer les erreurs; sa présence permet d'identifier un cas d'erreur.
				include_spip('inc/filtres');
				$configuration_erreur = $configurer('erreurs');
				$flux_erreur = $flux;
				if (!empty($configuration_erreur['cle_base'])) {
					$flux_erreur = table_valeur($flux, implode('/', $configuration_erreur['cle_base']), null);
				}

				// On normalise le flux en utilisant le mode d'erreur pour vérifier si on obtient bien une erreur.
				$erreur_service = meteo_normaliser($configuration_erreur, 'erreurs', $flux_erreur, -1);
				$verifier = "${service}_erreur_verifier";
				if ($verifier($erreur_service)) {
					// Une erreur est renvoyée par le service, on formate l'erreur correctement.
					$erreur['type'] = 'reponse_service';
					$erreur['service'] = array_merge($erreur['service'], $erreur_service);
				} else {
					// On se positionne sur le niveau de base du flux des données où commence le tableau des données météorologiques.
					if (!empty($configuration['cle_base'])) {
						$flux = table_valeur($flux, implode('/', $configuration['cle_base']), null);
					}

					if (empty($flux)) {
						// Pas d'erreur retournée par le service mais aucune donnée fournie. Ce cas est peut-être impossible
						// mais on le traite tout de même par sécurité avec un type particulier.
						$erreur['type'] = 'aucune_donnee';
					} else {
						// En mode prévisions, le niveau de base est un tableau de n éléments, chaque élément étant un tableau contenant
						// les données météorologiques d'un jour à venir ([0] => jour0[], [1] => jour1[]...).
						// En mode infos ou conditions, on a directement accès aux données météorologiques (jour[]).
						// Pour réaliser un traitement standard, on transforme donc le jour[] en un tableau d'un seul élément ([0] => jour[])
						// qui pourra être traité comme celui des prévisions.
						if (($mode == 'conditions') or ($mode == 'infos')) {
							$flux = array($flux);
						}

						// Convertir le flux en tableau standard pour la mise en cache. Ce traitement se déroule en
						// 3 étapes :
						// -1- initialisation du tableau standard à partir uniquement des données reçues du service
						// -2- complément du tableau avec les données propres à chaque service
						// -3- complément du tableau avec les données communes à tous les services
						foreach ($flux as $_index_jour => $_flux_jour) {
							// Pour les informations et les conditions les données récupérées concernent toute la même "période".
							// Par contre, pour les prévisions on distingue 2 type de données :
							// - celles du jour indépendamment de la période horaire
							// - celles correspondant à une période horaire choisie (24, 12, 6, 3, 1)
							//   Ces données sont stockées à un index horaire de 0 à n qui représente la période horaire.
							// Pour avoir un traitement identique pour les deux types de données on considère que l'index horaire
							// des données jour est égal à -1.
							// On crée donc le tableau des index correspondant au mode choisi et on boucle dessus.
							$periodes_horaires = array(-1);
							if ($periodicite) {
								for ($i = 0; $i < (24 / $periodicite); $i++) {
									$periodes_horaires[] = $i;
								}
							}

							// On détermine le flux heure en fonction du service. Ce flux heure coincide avec le flux jour dans
							// la majeure partie des cas
							$flux_heure = $_flux_jour;
							if ((count($periodes_horaires) > 1) and !empty($configuration['cle_heure'])) {
								$flux_heure = table_valeur($_flux_jour, implode('/', $configuration['cle_heure']), null);
							}

							// On boucle sur chaque période horaire pour remplir le tableau complet.
							foreach ($periodes_horaires as $_periode) {
								// 1- Initialiser le tableau normalisé des informations à partir des données brutes
								//    fournies par le service.
								//    Suivant la période il faut prendre le flux jour ou le flux heure. On calcule donc le flux heure
								//    quand c'est nécessaire.
								$flux_a_normaliser = $_periode == -1 ? $_flux_jour : ($configuration['structure_heure'] ? $flux_heure[$_periode] : $flux_heure);
								$donnees = meteo_normaliser($configuration, $mode, $flux_a_normaliser, $_periode);

								if ($donnees) {
									// 2- Compléments spécifiques au service et au mode.
									//    Si ces compléments sont inutiles, la fonction n'existe pas
									$completer = "${service}_complement2${mode}";
									if (function_exists($completer)) {
										$donnees = $mode == 'previsions' ? $completer($donnees, $configuration, $_periode) : $completer($donnees, $configuration);
									}

									// 3- Compléments standard communs à tous les services mais fonction du mode
									if ($mode == 'conditions') {
										// Vérifier que l'indice uv si celui-ci est fourni
										// Calcul du risque uv à partir de l'indice uv si celui-ci est fourni
										include_spip('inc/rainette_convertir');
										$donnees['risque_uv'] = is_int($donnees['indice_uv']) ? indice2risque_uv($donnees['indice_uv']) : $donnees['indice_uv'];
									}

									// Ajout du bloc à l'index en cours
									if ($_periode == -1) {
										$tableau[$_index_jour] = $donnees;
									} else {
										$tableau[$_index_jour]['heure'][$_periode] = $donnees;
									}
								}
							}
						}
					}
				}
			}
		}

		// 4- Compléments standard à tous les services et tous les modes
		$extras = array();
		$extras['credits'] = $configuration['credits'];
		$extras['config'] = array_merge($configuration_utilisateur, array('source' => configuration_donnees_normaliser($mode, $configuration['donnees'])), array('nom_service' => $configuration['nom']));
		$extras['lieu'] = $lieu;
		$extras['mode'] = $mode;
		$extras['periodicite_cache'] = $periodicite;
		$extras['service'] = $service;
		$extras['erreur'] = $erreur;

		// On range les données et les extras dans un tableau associatif à deux entrées ('donnees', 'extras')
		if ($tableau) {
			// Pour les modes "conditions" et "infos" l'ensemble des données météo est accessible sous
			// l'index 'donnees'. Il faut donc supprimer l'index 0 provenant du traitement commun avec
			// les prévisions.
			// Pour les prévisions l'index 0 à n désigne le jour, il faut donc le conserver
			$tableau = array(
				'donnees' => ($mode != 'previsions' ? array_shift($tableau) : $tableau), 'extras' => $extras
			);
		} else {
			// Traitement des erreurs de flux. On positionne toujours les bloc extra contenant l'erreur,
			// le bloc des données qui est mis à tableau vide dans ce cas à l'index 1.
			$tableau = array(
				'donnees' => array(), 'extras' => $extras
			);
		}

		// Pipeline de fin de chargement des données météo. Peut-être utilisé :
		// -- pour effectuer des traitements annexes à partir des données météo (archivage, par exemple)
		// -- pour ajouter ou modifier des données au tableau (la modification n'est pas conseillée cependant)
		$tableau = pipeline(
			'post_chargement_meteo',
			array(
				'args' => array('lieu' => $lieu, 'mode' => $mode, 'service' => $service),
				'data' => $tableau
			)
		);

		// Mise à jour du cache
		cache_ecrire('rainette', $cache, $tableau);
	} else {
		// Lecture des données du fichier cache valide
		$tableau = cache_lire('rainette', $fichier_cache);
	}

	return $tableau;
}
