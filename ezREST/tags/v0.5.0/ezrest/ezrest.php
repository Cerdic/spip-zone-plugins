<?php
/**
 * Ce fichier contient l'ensemble des services de gestion des requêtes, des réponses et de traitement
 * des données des collections et des ressources.
 * Certains de ces services peuvent être personnalisés par l'appel d'un service spécifique du plugin utilisateur.
 *
 * @package SPIP\EZREST\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


// -----------------------------------------------------------------------
// --------------- SERVICES DE GESTION DU CONTEXTE SERVEUR ---------------
// -----------------------------------------------------------------------

/**
 * Détermine si le serveur est capable de répondre aux requêtes.
 * Par défaut, l'API ezREST ne fait aucune vérification. C'est donc au plugin utilisateur de fournir
 * un service spécifique si une vérification globale doit être effectuée afin d'assurer le fonctionnement de
 * l'API.
 *
 * Si une erreur est détectée, le plugin utilisateur ne renvoie le type, l'élément et la valeur qui provoque l'erreur
 * sachant que c'est le service par défaut qui positionne le code.
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 * @param &array $erreur Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function ezrest_api_verifier_contexte($plugin, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// Appel d'un service spéficique au plugin fournisseur pour vérifier si le contexte permet l'utilisation de l'API.
	if ($verifier = ezrest_service_chercher($plugin, 'api_verifier_contexte')) {
		// On initialise l'erreur avec son code 501 et son module, le plugin utilisateur.
		// Par contre, le type est passé à vide parce que c'est au plugin de choisir son identifiant d'erreur 501.
		$erreur = ezrest_erreur_initialiser($plugin, 501, '');
		$est_valide = $verifier($erreur);
	}

	// S'assurer que le bloc d'erreur est vide si aucune erreur n'a été détectée.
	if ($est_valide) {
		$erreur = array();
	}

	return $est_valide;
}


// -----------------------------------------------------------------------
// ------------------ SERVICES DE GESTION DES REPONSES -------------------
// -----------------------------------------------------------------------

/**
 * Initialise le contenu d'une réponse qui se présente comme un tableau associatif.
 * En particulier, la fonction stocke les éléments de la requête et positionne le bloc d'erreur
 * par défaut à ok.
 *
 * Ce service standard n'est pas personnalisable par un plugin utilisateur.
 *
 * @param Symfony\Component\HttpFoundation\Request $requete
 *      Objet requête fourni par le plugin Serveur HTTP abstrait.
 *
 * @return array
 *      Le contenu initial de l'API `ezrest` est un tableau associatif à 3 entrées:
 *      - `requete` : sous-tableau des éléments de la requête
 *      - `erreur`  : sous-tableau des éléments descriptifs d'une erreur (status 200 par défaut)
 *      - `donnees` : le tableau des objets demandés fonction de la requête (vide)
 */
function ezrest_reponse_initialiser_contenu($requete) {

	// Stockage des éléments de la requête
	// -- La méthode
	$demande = array('methode' => $requete->getMethod());
	// -- Les éléments format, collection et ressource
	$demande = array_merge($demande, $requete->attributes->all());
	// -- Les critères de filtre fournis comme paramètres de l'url.
	//    Si on utilise une URL classique avec spip.php il faut exclure certains paramètres.
	$demande['filtres'] = $requete->query->all();
	$demande['filtres'] = array_diff_key(
		$demande['filtres'],
		array_flip(array('action', 'arg', 'lang', 'var_zajax'))
	);
	// -- Le format du contenu de la réponse est toujours le JSON
	$demande['format_contenu'] = 'json';

	// Initialisation du bloc d'erreur à ok par défaut
	$erreur = ezrest_erreur_initialiser('ezrest', 200, 'ok');
	$erreur = ezrest_reponse_expliquer_erreur('ezrest', $erreur, '');

	// On intitialise le contenu avec les informations collectées.
	// A noter que le format de sortie est initialisé par défaut à json indépendamment de la demande, ce qui permettra
	// en cas d'erreur sur le format demandé dans la requête de renvoyer une erreur dans un format lisible.
	$contenu = array(
		'requete' => $demande,
		'erreur'  => $erreur,
		'donnees' => array()
	);

	return $contenu;
}

/**
 * Complète l'initialisation du contenu d'une réponse avec des informations sur le plugin utilisateur.
 * REST Factory remplit de façon standard un nouvel index `plugin` du contenu et permet ensuite au plugin
 * utilisateur de personnaliser encore le contenu initialisé, si besoin.
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 * @param $contenu
 *
 * @return array
 *      Le contenu initial de l'API `ezrest` est un tableau associatif à 3 entrées:
 *      - `requete` : sous-tableau des éléments de la requête
 */
function ezrest_reponse_informer_plugin($plugin, $contenu) {

	// On met à jour les informations sur le plugin utilisateur maintenant qu'il est connu.
	// -- A minima on enregistre le préfixe et la version du plugin.
	include_spip('inc/filtres');
	$informer = charger_filtre('info_plugin');
	$version = $informer($plugin, 'version', true);

	$contenu = array_merge(
		array(
			'fournisseur' => array(
				'plugin'  => strtoupper($plugin),
				'version' => $version,
			)
		),
		$contenu
	);

	// -- On ajoute le schéma de données du plugin si il existe.
	include_spip('inc/config');
	$schema = lire_config("${plugin}_base_version", null);
	if (!is_null($schema)) {
		$contenu['fournisseur']['schema'] = $schema;
	}

	// Appel d'un service spécifique au plugin utilisateur pour compléter l'initialisation si besoin.
	if ($completer = ezrest_service_chercher($plugin, 'reponse_informer_plugin')) {
		$contenu = $completer($contenu);
	}

	return $contenu;
}


/**
 * Complète le bloc d'erreur avec le titre et l'explication de l'erreur.
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 * @param array  $erreur Tableau initialisé avec les éléments de base de l'erreur (`status`, `type`, `element` et `valeur`).
 * @param string $collection
 *
 * @return array
 *        Tableau de l'erreur complété avec le titre (index `title`) et le descriptif (index `detail`).
 */
function ezrest_reponse_expliquer_erreur($plugin, $erreur, $collection = '') {

	// Calcul des paramètres qui seront passés à la fonction de traduction.
	// -- on passe toujours la collection qui est vide uniquement pour l'erreur de serveur et l'extra qui peut aussi
	//    être vide parfois.
	$parametres = array(
		'element'    => $erreur['element'],
		'valeur'     => $erreur['valeur'],
		'collection' => $collection,
		'extra'      => $erreur['extra']
	);
	// -- inutile de conserver l'extra dans le bloc d'erreur
	unset($erreur['extra']);

	// Traduction du libellé de l'erreur.
	$item = $erreur['module']['titre'] . ':erreur_' . $erreur['status'] . '_' . $erreur['type'] . '_titre';
	$erreur['titre'] = _T($item, $parametres);
	// Traduction du message complémentaire.
	$item = $erreur['module']['detail'] . ':erreur_' . $erreur['status'] . '_' . $erreur['type'] . '_message';
	$erreur['detail'] = _T($item, $parametres);
	// -- inutile de conserver l'information sur les modules fournissant les items de langue.
	unset($erreur['module']);

	// Appel d'un service spécifique au plugin utilisateur pour compléter le bloc d'erreur si besoin.
	if ($expliquer = ezrest_service_chercher($plugin, 'reponse_expliquer_erreur')) {
		$erreur = $expliquer($erreur);
	}

	return $erreur;
}


/**
 * Finalise la réponse à la requête en complétant le header et le contenu mis au préalable
 * au format JSON.
 *
 * Ce service standard n'est pas personnalisable par un plugin utilisateur.
 *
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse tel qu'initialisé par le serveur HTTP abstrait.
 * @param array                                     $contenu
 * 		Tableau du contenu de la réponse qui sera retourné en JSON.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Retourne l'objet réponse dont le contenu et certains attributs du header sont mis à jour.
 */
function ezrest_reponse_construire($reponse, $contenu) {

	// Charset UTF-8 et statut de l'erreur.
	$reponse->setCharset('utf-8');
	$reponse->setStatusCode($contenu['erreur']['status']);

	// Format JSON exclusif pour les réponses.
	$reponse->headers->set('Content-Type', 'application/json');
	$reponse->setContent(json_encode($contenu));

	return $reponse;
}


// -----------------------------------------------------------------------
// ------------------ SERVICES DE GESTION DES REQUETES -------------------
// -----------------------------------------------------------------------

/**
 * Détermine si la collection demandée est valide. Par défaut, REST Factory vérifie que la collection est bien
 * déclarée dans la liste des collections. Si c'est le cas, la fonction permet ensuite au plugin utilisateur de
 * compléter la vérification, si besoin.
 *
 * @param string $collection
 *        La valeur de la collection demandée
 * @param array  $collections
 *        Configuration des collections disponibles.
 * @param &string   $plugin Préfixe du plugin fournisseur de la collection. Chaine vide en entrée et en sortie si
 *                          une erreur est détectée.
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit collection_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit collection
 *        - `valeur`  : la valeur de la collection
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function ezrest_collection_verifier($collection, $collections, &$plugin, &$erreur) {

	// Initialise le retour à false par défaut.
	$est_valide = false;

	// Vérification de la disponibilité de la collection demandée.
	foreach ($collections as $_collection => $_configuration) {
		if ($collection == $_collection) {
			// la collection est déclarée, on renvoie le plugin fournisseur et aucune erreur.
			$est_valide = true;
			$plugin = $_configuration['module'];
			break;
		}
	}

	// La collection n'est pas déclarée, on renvoie une erreur et pas de plugin.
	if (!$est_valide) {
		$erreur = ezrest_erreur_initialiser(
			'ezrest',
			400,
			'collection_indisponible',
			'collection',
			$collection,
			implode(', ', array_keys($collections))
		);
	} else {
		// Appel d'un service spécifique au plugin utilisateur pour compléter la vérification si besoin.
		if ($verifier = ezrest_service_chercher($plugin, 'verifier', $collection)) {
			$erreur = ezrest_erreur_initialiser(
				$plugin,
				400,
				'collection_nok',
				'collection',
				$collection
			);
			$est_valide = $verifier($erreur);
		}
	}

	// S'assurer que le bloc d'erreur est vide si aucune erreur n'a été détectée.
	if ($est_valide) {
		$erreur = array();
	}

	return $est_valide;
}


/**
 * Détermine si la valeur de chaque critère de filtre d'une collection est valide.
 * Si plusieurs critères sont fournis, la fonction s'interromp dès qu'elle trouve un
 * critère non admis ou dont la valeur est invalide.
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 * @param array  $filtres
 *        Tableau associatif des critères de filtre (couple nom du critère, valeur du critère)
 * @param string $collection
 *        La collection concernée.
 * @param array  $configuration
 *        Configuration de la collection concernée. L'index `filtres` contient la liste des critères admissibles
 *        et l'index `module` contient le nom du fichier des fonctions de service.
 * @param &array $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit critere_nok
 *        - `element` : nom du critère en erreur
 *        - `valeur`  : valeur du critère
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function ezrest_collection_verifier_filtre($plugin, $filtres, $collection, $configuration, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// 1- Vérification de l'absence de critère obligatoire.
	foreach ($configuration['filtres'] as $_filtre) {
		if (!empty($_filtre['est_obligatoire'])
		and (!isset($filtres[$_filtre['critere']]))) {
			$erreur = ezrest_erreur_initialiser(
				'ezrest',
				400,
				'critere_obligatoire_nok',
				'critere',
				$_filtre['critere']
			);
			$est_valide = false;
			break;
		}
	}

	// 2- Vérification des critères fournis
	if ($est_valide and $filtres) {
		// On arrête dès qu'une erreur est trouvée et on la reporte.
		foreach ($filtres as $_critere => $_valeur) {
			$extra = '';
			// On vérifie si le critère est admis.
			$criteres = array_column($configuration['filtres'], null, 'critere');
			if (!in_array($_critere, array_keys($criteres))) {
				$erreur = ezrest_erreur_initialiser(
					'ezrest',
					400,
					'critere_nom_nok',
					'critere',
					$_critere,
					implode(', ', array_keys($criteres))
				);
				$est_valide = false;
				break;
			} else {
				// On vérifie si la valeur du critère est valide :
				// -- le critère est vérifié par une fonction spécifique qui est soit liée au critère soit globale à
				//    la fonction. Si cette fonction n'existe pas le critère est réputé valide.
				$module = !empty($criteres[$_critere]['module'])
					? $criteres[$_critere]['module']
					: $plugin;
				if ($verifier = ezrest_service_chercher($module, 'verifier_filtre', $collection, $_critere)) {
					$erreur = ezrest_erreur_initialiser(
						$module,
						400,
						'',
						$_critere,
						$_valeur
					);
					if (!$verifier($_valeur, $erreur)) {
						$est_valide = false;
						break;
					}
				}
			}
		}
	}

	// S'assurer que le bloc d'erreur est vide si aucune erreur n'a été détectée.
	if ($est_valide) {
		$erreur = array();
	}

	return $est_valide;
}


/**
 * Détermine si le type de ressource demandée est valide.
 * Le service ne fournit que des ressources de type plugin (`plugins`).
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 * @param string $ressource
 *        La valeur de la ressource demandée. La ressource appartient à une collection.
 * @param string $collection
 *        La collection concernée.
 * @param array  $configuration
 *        Configuration de la collection de la ressource. L'index `ressource` identifie le champ attendu pour désigner
 *        la ressource et l'index `module` contient le nom du fichier des fonctions de service.
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit ressource_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit ressource
 *        - `valeur`  : la valeur de la ressource
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function ezrest_collection_verifier_ressource($plugin, $ressource, $collection, $configuration, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// Vérification de la disponibilité de l'accès à une ressource pour la collection concernée
	if (empty($configuration['ressource'])) {
		// Récupération de la liste des collections disponibles pour lister celles avec ressources dans le message.
		$declarer = charger_fonction('ezrest_declarer_collections', 'inc');
		$collections = $declarer();
		$ressources = array();
		foreach ($collections as $_collection => $_config) {
			if (!empty($_config['ressource'])) {
				$ressources[] = $_collection;
			}
		}
		$erreur = ezrest_erreur_initialiser(
			'ezrest',
			400,
			'ressource_indisponible',
			'ressource',
			$ressource,
			implode(', ', $ressources)
		);
		$est_valide = false;
	} else {
		// Vérification de la validité de la ressource demandée.
		// -- la ressource est vérifiée par une fonction spécifique. Si elle n'existe pas la ressource est
		//    réputée valide.
		if ($verifier = ezrest_service_chercher($plugin, 'verifier_ressource', $collection, $configuration['ressource'])) {
			$erreur = ezrest_erreur_initialiser(
				$plugin,
				400,
				'',
				'ressource',
				$ressource,
				$configuration['ressource']
			);
			if (!$verifier($ressource, $erreur)) {
				$est_valide = false;
			}
		}
	}

	// S'assurer que le bloc d'erreur est vide si aucune erreur n'a été détectée.
	if ($est_valide) {
		$erreur = array();
	}

	return $est_valide;
}


// -----------------------------------------------------------------------
// ------------------ SERVICES DE GESTION DES DONNEES --------------------
// -----------------------------------------------------------------------

/**
 * @param $plugin
 *
 * @return array
 */
function ezrest_indexer($collections) {

	// Initialisation des données de la collection à retourner
	$contenu = array();

	// On contruit la liste des collections disponibles en présentant leur configuration d'une façon la plus
	// explicite pour les utilisateurs. Les collections sont présentées sous chaque plugin fournisseur.
	foreach ($collections as $_collection => $_configuration) {
		// Détermination du plugin fournisseur
		$plugin = $_configuration['module'];

		// Si c'est la première fois qu'on rencontre le plugin alors on stocke quelques informations sur le plugin.
		if (!isset($contenu[$plugin])) {
			include_spip('inc/filtres');
			$informer = charger_filtre('info_plugin');
			$version = $informer($plugin, 'version', true);
			$nom = $informer($plugin, 'nom', true);

			$contenu[$plugin]['fournisseur'] = array(
				'nom'  => extraire_multi($nom),
				'version' => $version,
			);
		}

		// On initialise le contenu de la collection sous l'index du plugin
		$contenu[$plugin]['collections'][$_collection] = array();

		// -- Informer sur la possibilité de demander une ressource
		$contenu[$plugin]['collections'][$_collection]['ressource'] = isset($_configuration['ressource'])
			? _T('ezrest:collection_ressource_oui', array('ressource' => $_configuration['ressource']))
			: _T('ezrest:collection_ressource_non');

		// -- Informer sur les filtres autorisés
		if ($_configuration['filtres']) {
			foreach ($_configuration['filtres'] as $_filtre) {
				$obligatoire = empty($_filtre['est_obligatoire'])
					? _T('ezrest:collection_filtre_facultatif')
					: _T('ezrest:collection_filtre_obligatoire');
				$fournisseur = empty($_filtre['module'])
					? ''
					: ', ' . _T('ezrest:collection_filtre_fournisseur', array('module' => strtoupper($_filtre['module'])));
				$contenu[$plugin]['collections'][$_collection]['filtres'][$_filtre['critere']] = $obligatoire . $fournisseur;
			}
		}
	}

	return $contenu;
}


/**
 * @param $plugin
 * @param $collection
 * @param $filtres
 * @param $configuration
 *
 * @return array
 */
function ezrest_contextualiser($plugin, $collection, $complement, $configuration) {

	// Initialisation minimale du contexte : le préfixe du plugin est passé sous le terme plugin_prefixe
	// pour éviter une collision avec des balises #PREFIXE ou #PLUGIN.
	$contexte = array(
		'plugin_prefixe' => $plugin,
		'collection'     => $collection,
		'configuration'  => $configuration
	);

	// Détermination de la fonction de service permettant de récupérer la collection spécifiée
	// filtrée sur les critères éventuellement fournis.
	if ($complement) {
		if (is_array($complement)) {
			// On est en présence d'une collection avec des filtres :
			// -- extraire la configuration des critères pour construire le contexte induit par les filtres
			$criteres = array_column($configuration['filtres'], null, 'critere');
			foreach ($complement as $_critere => $_valeur) {
				$nom_champ = !empty($criteres[$_critere]['champ_nom'])
					? $criteres[$_critere]['champ_nom']
					: $_critere;
				$contexte[$nom_champ] = $_valeur;
			}
		} else {
			// On est en présence d'une ressource :
			// -- on rajoute le champ de la ressource valorisé dans le contexte pour limiter la boucle à cet élément
			$contexte[$configuration['ressource']] = $complement;
		}
	}

	return $contexte;
}


/**
 * @param string $plugin
 * @param string $type_requete
 * @param string $collection
 * @param mixed  $complement
 * @param array  $configuration
 *
 * @return array
 */
function ezrest_cache_identifier($plugin, $type_requete, $collection = 'collections', $complement = '', $configuration = array()) {

	// Initialisation du tableau d'identification du cache
	$cache = array();

	// Mise à jour du sous-dossier en fonction du plugin et du type de requete
	$cache['sous_dossier'] = $plugin;

	// Elements du nom du cache
	// -- le type de requête est toujours le préfixe du nom et la collection est mise à 'collections' si le
	//    type de requête est l'index.
	$cache['type_requete'] = $type_requete;
	$cache['collection'] = $collection;

	// -- Si le cache n'est pas celui de l'index on complète le nom avec soit l'identifiant d'une ressource
	//    soit un représentation des filtres.
	//    On hash toujours la ressource et les filtres de façon à ne jamais avoir un problème de nom de fichier.
	//    De fait, on loge aussi toujours la ressource ou les filtres source.
	if (($type_requete != 'index') and $complement) {
		if ($type_requete == 'ressource') {
			if (is_string($complement)) {
				// Identifiant de ressource au format chaine
				$cache['ressource'] = $complement;
			} elseif (is_int($complement)) {
				// Identifiant de ressource au format entier
				$cache['ressource'] = strval($complement);
			}
			$cache['complement'] = md5($cache['ressource']);
		} elseif (($type_requete == 'collection') and is_array($complement)) {
			// Liste de filtres : on concatène les critères et les valeurs et on crypte la chaine obtenue.
			$hash = '';
			foreach ($complement as $_critere => $_valeur) {
				$hash .= "${_critere}${_valeur}";
			}
			// On ajoute le composant complément et aussi les filtres source de façon à pouvoir les loger
			// dans l'index des caches.
			$cache['complement'] = md5($hash);
			$cache['filtres'] = $complement;
		}
	}

	// Durée de conservation du cache : si précisée pour la collection on l'utilise sinon on utilise la valeur
	// par défaut des caches REST Factory (1 jour).
	if (isset($configuration['cache']['duree'])) {
		$cache['conservation'] = $configuration['cache']['duree'];
	}

	return $cache;
}


/**
 * @param $plugin
 * @param $collection
 * @param $filtres
 * @param $configuration
 *
 * @return array
 */
function ezrest_conditionner($plugin, $collection, $filtres, $configuration) {

	// Initialisation des données de la collection à retourner
	$conditions = array();

	// Détermination de la fonction de service permettant de récupérer la collection spécifiée
	// filtrée sur les critères éventuellement fournis.
	if (empty($configuration['sans_condition']) and $filtres) {
		include_spip('base/objets');
		// Extraire la configuration des critères
		$criteres = array_column($configuration['filtres'], null, 'critere');
		foreach ($filtres as $_critere => $_valeur) {
			// On regarde si il y une fonction particulière permettant le calcul du critère ou si celui-ci
			// est calculé de façon standard.
			$module = !empty($criteres[$_critere]['module'])
				? $criteres[$_critere]['module']
				: $plugin;
			if ($conditionner = ezrest_service_chercher($module, 'conditionner', $collection, $_critere)) {
				// La condition est élaborée par une fonction spécifique du plugin utilisateur.
				// Il est donc inutile de fournir autre chose que la valeur à la fonction spécifique car tout le
				// contexte est déjà connu du plugin utilisateur.
				$conditions[] = $conditionner($_valeur);
			} else {
				// La condition est calculée par REST Factory à partir de la configuration du filtre.
				// -- détermination du nom du champ servant de critère
				$nom_champ = !empty($criteres[$_critere]['champ_nom'])
					? $criteres[$_critere]['champ_nom']
					: $_critere;

				// -- détermination de la table à ajouter en préfixe du champ :
				//    - si l'index 'champ_table' n'est pas précisé on utilise le nom de la collection : si elle
				//      correspond à une table on l'utilise en préfixe sinon on ne préfixe pas.
				//    - si l'index 'champ_table' est précisé : si il est vide, on ne préfixe pas, sinon on l'utilise
				//      pour trouver la table et l'utiliser en préfixe.
				$type_objet = !isset($criteres[$_critere]['champ_table'])
					? $collection
					: $criteres[$_critere]['champ_table'];
				$table = table_objet_sql($type_objet);
				$champ_sql = ($type_objet and ($table != $type_objet))
					? "${table}.${nom_champ}"
					: $nom_champ;

				// -- détermination de la fonction à appliquer à la valeur en fonction de son type (défaut string).
				$fonction = (empty($criteres[$_critere]['champ_type']) or ($criteres[$_critere]['champ_type'] == 'string'))
					? 'sql_quote'
					: 'intval';

				// Construction de la condition et ajout en queue du tableau.
				$conditions[] = "${champ_sql}=" . $fonction($_valeur);
			}
		}
	}

	return $conditions;
}

/**
 * @param $plugin
 * @param $collection
 * @param $conditions
 * @param $filtres
 * @param $configuration
 *
 * @return array
 */
function ezrest_collectionner($plugin, $collection, $conditions, $filtres, $configuration) {

	// Initialisation des données de la collection à retourner
	$contenu = array();

	// Détermination de la fonction de service permettant de récupérer la collection spécifiée
	// filtrée sur les critères éventuellement fournis.
	if ($collectionner = ezrest_service_chercher($plugin, 'collectionner', $collection)) {
		$contenu = $collectionner($conditions, $filtres, $configuration);
	}

	return $contenu;
}

/**
 * @param $plugin
 * @param $collection
 * @param $ressource
 *
 * @return array
 */
function ezrest_ressourcer($plugin, $collection, $ressource) {

	// Initialisation des données de la collection à retourner
	$contenu = array();

	// Détermination de la fonction de service permettant de récupérer la collection spécifiée
	// filtrée sur les critères éventuellement fournis.
	if ($ressourcer = ezrest_service_chercher($plugin, 'ressourcer', $collection)) {
		$contenu = $ressourcer($ressource);
	}

	return $contenu;
}

// -----------------------------------------------------------------------
// -------------------- UTILITAIRE PROPRE AU PLUGIN ----------------------
// -----------------------------------------------------------------------

/**
 * Cherche une fonction donnée en se basant sur le plugin appelant.
 * Si le plugin utilisateur ne fournit pas la fonction demandée la chaîne vide est renvoyée.
 *
 * @internal
 *
 * @param string $plugin Préfixe du plugin utilisateur de ezrest et donc fournisseur de la collection.
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param string $fonction
 *        Nom de la fonction de service à chercher.
 * @param string $prefixe
 *        Nom de la fonction de service à chercher.
 * @param string $suffixe
 *        Nom de la fonction de service à chercher.
 *
 * @return string
 *        Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function ezrest_service_chercher($plugin, $fonction, $prefixe = '', $suffixe = '') {

	$fonction_trouvee = '';

	// Eviter la réentrance si on demande explicitement le plugin ezrest.
	if ($plugin != 'ezrest') {
		include_spip("ezrest/${plugin}");
		$fonction_trouvee = $prefixe ? ($suffixe ? "${prefixe}_${fonction}_${suffixe}" : "${prefixe}_${fonction}") : "${plugin}_${fonction}";
		if (!function_exists($fonction_trouvee)) {
			$fonction_trouvee = '';
		}
	}

	return $fonction_trouvee;
}

/**
 * Initilise le bloc d'erreur complet.
 *
 * @internal
 *
 * @param string $plugin  Préfixe du plugin utilisateur de ezrest.
 * @param int    $code    Code d'erreur standard d'une requête HTTP
 * @param string $type    Identifiant unique de l'erreur
 * @param string $element Elément sur lequel porte l'erreur
 * @param string $valeur  Valeur de l'élément en erreur
 *
 * @return array
 *        Bloc d'erreur complet initialisé avec le code, le plugin et le type.
 */
function ezrest_erreur_initialiser($plugin, $code, $type, $element = '', $valeur = '', $extra = '') {

	// On initialise tous les index d'un bloc d'erreur
	$erreur['status'] = $code;
	$erreur['type'] = $type;
	$erreur['element'] = $element;
	$erreur['valeur'] = $valeur;
	$erreur['extra'] = $extra;
	$erreur['titre'] = '';
	$erreur['detail'] = '';

	// Si le type est précisé c'est que son item de langue est fourni par ezREST.
	// Sinon, c'est que son item de langue est fourni par le plugin passé en paramètre.
	$erreur['module']['titre'] = $type ? 'ezrest' : $plugin;
	$erreur['module']['detail'] = $plugin;

	return $erreur;
}
