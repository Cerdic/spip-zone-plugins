<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions de construction du contenu des réponses aux
 * requête à l'API SVP.
 *
 * @package SPIP\EZREST\SERVICE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Initialise le contenu d'une réponse qui se présente comme un tableau associatif.
 * En particulier, la fonction stocke les éléments de la requête, positionne le bloc d'erreur
 * par défaut à ok, et récupère le schéma de données lié à SVP.
 *
 * @param Symfony\Component\HttpFoundation\Request $requete
 *      Objet requête fourni par le plugin Serveur HTTP abstrait.
 *
 * @return array
 *      Le contenu d'une réponse de l'API `ezrest` est un tableau associatif à 3 entrées:
 *      - `requete` : sous-tableau des éléments de la requête
 *      - `erreur`  : sous-tableau des éléments descriptifs d'une erreur (status 200 par défaut)
 *      - `donnees` : le tableau des objets demandés fonction de la requête (vide)
 */
function reponse_ezrest_initialiser_contenu($requete) {

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
	$erreur['status'] = 200;
	$erreur['type'] = 'ok';
	$erreur['element'] = '';
	$erreur['valeur'] = '';
	$erreur['title'] = _T('ezrest:erreur_200_ok_titre');
	$erreur['detail'] = _T('ezrest:erreur_200_ok_message');

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
 * @param $contenu
 * @param $plugin
 *
 * @return array
 */
function reponse_ezrest_informer_plugin($contenu, $plugin) {

	// On met à jour les informations sur le plugin utilisateur maintenant qu'il est connu.
	// -- Récupération du schéma de données et de la version du plugin.
	include_spip('inc/config');
	$schema = lire_config("${plugin}_base_version", null);
	include_spip('inc/filtres');
	$informer = charger_filtre('info_plugin');
	$version = $informer($plugin, 'version', true);

	$contenu = array_merge(
		array(
			'plugin' => array(
				'prefixe' => $plugin,
				'schema'  => $schema,
				'version' => $version,
			)
		),
		$contenu
	);

	return $contenu;
}

/**
 * Complète le bloc d'erreur avec le titre et l'explication de l'erreur.
 *
 * @param array $erreur
 * 		Tableau initialisé avec les éléments de base de l'erreur (`status`, `type`, `element` et `valeur`).
 *
 * @return array
 * 		Tableau de l'erreur complété avec le titre (index `title`) et le descriptif (index `detail`).
 */
function reponse_ezrest_expliquer_erreur($erreur, $collection) {

	// Calcul des paramètres qui seront passés à la fonction de traduction.
	// -- on passe toujours la collection qui est vide uniquement pour l'erreur de serveur.
	$parametres = array(
		'element'    => $erreur['element'],
		'valeur'     => $erreur['valeur'],
		'collection' => $collection
	);
	// -- on complète avec une chaine extra si elle existe que l'on supprime ensuite comme index de la réponse.
	if (isset($erreur['extra'])) {
		$parametres['extra'] = $erreur['extra'];
		unset($erreur['extra']);
	}

	// Traduction du libellé de l'erreur et du message complémentaire.
	$prefixe = 'ezrest:erreur_' . $erreur['status'] . '_' . $erreur['type'];
	$erreur['title'] = _T("${prefixe}_titre", $parametres);
	$erreur['detail'] = _T("${prefixe}_message", $parametres);

	return $erreur;
}


/**
 * Finalise la réponse à la requête en complétant le header et le contenu mis au préalable
 * au format JSON.
 *
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse tel qu'initialisé par le serveur HTTP abstrait.
 * @param array                                     $contenu
 * 		Tableau du contenu de la réponse qui sera retourné en JSON.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Retourne l'objet réponse dont le contenu et certains attributs du header sont mis à jour.
 */
function reponse_ezrest_construire($reponse, $contenu) {

	// Charset UTF-8 et statut de l'erreur.
	$reponse->setCharset('utf-8');
	$reponse->setStatusCode($contenu['erreur']['status']);

	// Format JSON exclusif pour les réponses.
	$reponse->headers->set('Content-Type', 'application/json');
	$reponse->setContent(json_encode($contenu));

	return $reponse;
}


/**
 * Détermine si le serveur est capable de répondre aux requêtes SVP.
 * Pour cela on vérifie si le serveur est en mode run-time ou pas.
 * On considère qu'un serveur en mode run-time n'est pas valide pour
 * traiter les requêtes car la liste des plugins et des paquets n'est
 * pas complète.
 *
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 501
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit `serveur_nok`
 *        - `element` : type d'objet sur lequel porte l'erreur, soit `serveur`
 *        - `valeur`  : la valeur du mode runtime
 *
 * @return bool
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_ezrest_verifier_contexte($plugin, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// Apple d'un service spéficique au plugin fournisseur pour vérifier si le contexte permet l'utilisation de l'API.
	if ($verifier = service_ezrest_chercher($plugin, 'verifier_contexte')) {
		$est_valide = $verifier($erreur);
	}

	return $est_valide;
}


/**
 * Détermine si la collection demandée est valide.
 *
 * @param string $collection
 *        La valeur de la collection demandée
 * @param array  $collections
 *        Configuration des collections disponibles.
 * @param &string   $plugin Préfixe du plugin fournisseur de la collection.
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
function requete_ezrest_verifier_collection($collection, $collections, &$plugin, &$erreur) {

	// Initialise le retour à false par défaut.
	$est_valide = false;

	// Vérification de la disponibilité de la collection demandée.
	foreach ($collections as $_collection => $_configuration) {
		if ($collection==$_collection) {
			// la collection est déclarée, on renvoie le plugin fournisseur et aucune erreur.
			$est_valide = true;
			$plugin = $_configuration['module'];
			$erreur = array();
			break;
		}
	}

	// La collection n'est pas déclarée, on renvoie une erreur et pas de plugin.
	if (!$est_valide) {
		$plugin = '';
		$erreur = array(
			'status'  => 400,
			'type'    => 'collection_nok',
			'element' => 'collection',
			'valeur'  => $collection,
			'extra'   => implode(', ', array_keys($collections))
		);
	}

	return $est_valide;
}


/**
 * Détermine si la valeur de chaque critère de filtre d'une collection est valide.
 * Si plusieurs critères sont fournis, la fonction s'interromp dès qu'elle trouve un
 * critère non admis ou dont la valeur est invalide.
 *
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
function requete_ezrest_verifier_filtres($filtres, $collection, $configuration, &$erreur) {

	$est_valide = true;
	$erreur = array();

	// 1- Vérification de l'absence de critère obligatoire.
	foreach ($configuration['filtres'] as $_filtre) {
		if (!empty($_filtre['est_obligatoire'])
		and (!isset($filtres[$_filtre['critere']]))) {
			$erreur = array(
				'status'  => 400,
				'type'    => 'critere_obligatoire_nok',
				'element' => 'critere',
				'valeur'  => $_filtre['critere']
			);
			$est_valide = false;
			break;
		}
	}

	// 2- Véfification des critères fournis
	if ($est_valide and $filtres) {
		// On arrête dès qu'une erreur est trouvée et on la reporte.
		foreach ($filtres as $_critere => $_valeur) {
			$extra = '';
			// On vérifie si le critère est admis.
			$criteres = array_column($configuration['filtres'], null, 'critere');
			if (!in_array($_critere, array_keys($criteres))) {
				$erreur = array(
					'status'  => 400,
					'type'    => 'critere_nom_nok',
					'element' => 'critere',
					'valeur'  => $_critere,
					'extra'   => implode(', ', array_keys($criteres))
				);
				$est_valide = false;
				break;
			} else {
				// On vérifie si la valeur du critère est valide :
				// -- le critère est vérifié par une fonction spécifique qui est soit liée au critère soit globale à
				//    la fonction. Si cette fonction n'existe pas le critère est réputé valide.
				$module = !empty($criteres[$_critere]['module'])
					? $criteres[$_critere]['module']
					: $configuration['module'];
				include_spip("ezrest/${module}");
				$verifier = "${collection}_verifier_critere_${_critere}";
				if (function_exists($verifier)
				and !$verifier($_valeur, $extra)) {
					$erreur = array(
						'status'  => 400,
						'type'    => 'critere_valeur_nok',
						'element' => $_critere,
						'valeur'  => $_valeur,
						'extra'   => $extra
					);
					$est_valide = false;
					break;
				}
			}
		}
	}

	return $est_valide;
}


/**
 * Détermine si le type de ressource demandée est valide.
 * Le service ne fournit que des ressources de type plugin (`plugins`).
 *
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
function requete_ezrest_verifier_ressource($ressource, $collection, $configuration, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// Vérification de la disponibilité de l'accès à une ressource pour la collection concernée
	if (empty($configuration['ressource'])) {
		// Récupération de la liste des collections disponibles pour lister celles avec ressources dans le message.
		$declarer = charger_fonction('declarer_collections_svp', 'inc');
		$collections = $declarer();
		$ressources = array();
		foreach ($collections as $_collection => $_config) {
			if (!empty($_config['ressource'])) {
				$ressources[] = $_collection;
			}
		}
		$erreur = array(
			'status'  => 400,
			'type'    => 'ressource_nok',
			'element' => 'ressource',
			'valeur'  => $ressource,
			'extra'   => implode(', ', $ressources)
		);
		$est_valide = false;
	} else {
		// Vérification de la validité de la ressource demandée.
		// -- la ressource est vérifiée par une fonction spécifique. Si elle n'existe pas la ressource est
		//    réputée valide.
		$module = $configuration['module'];
		include_spip("svpapi/${module}");
		$verifier = "${collection}_verifier_ressource_{$configuration['ressource']}";
		if (function_exists($verifier)
		and !$verifier($ressource)) {
			$erreur = array(
				'status'  => 400,
				'type'    => "ressource_{$configuration['ressource']}_nok",
				'element' => 'ressource',
				'valeur'  => $ressource,
				'extra'   => $configuration['ressource']
			);
			$est_valide = false;
		}
	}

	return $est_valide;
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
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param bool   $fonction
 *        Nom de la fonction de service à chercher.
 *
 * @return string
 *        Nom complet de la fonction si trouvée ou chaine vide sinon.
 */
function service_ezrest_chercher($plugin, $fonction) {

	include_spip("ezrest/${plugin}");
	$fonction_trouvee = "${plugin}_${fonction}";
	if (!function_exists($fonction_trouvee)) {
		$fonction_trouvee = '';
	}

	return $fonction_trouvee;
}
