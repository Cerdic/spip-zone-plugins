<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions de vérification des éléments d'une requête à
 * l'API SVP.
 *
 * @package SPIP\SVPAPI\REQUETE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
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
function requete_verifier_serveur(&$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	include_spip('inc/svp_phraser');
	if (_SVP_MODE_RUNTIME) {
		$erreur = array(
			'status'  => 501,
			'type'    => 'serveur_nok',
			'element' => 'runtime',
			'valeur'  => _SVP_MODE_RUNTIME
		);
		$est_valide = false;
	}

	return $est_valide;
}


/**
 * Détermine si la collection demandée est valide.
 * Le service ne fournit que les collections plugins (`plugins`) et dépôts (`depots`).
 *
 * @param string $collection
 *        La valeur de la collection demandée
 * @param array  $collections
 *        Configuration des collections disponibles.
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
function requete_verifier_collection($collection, $collections, &$erreur) {

	// Initialise le retour à true par défaut.
	$est_valide = true;

	// Vérification de la disponibilité de la collection demandée.
	if (!in_array($collection, array_keys($collections))) {
		$erreur = array(
			'status'  => 400,
			'type'    => 'collection_nok',
			'element' => 'collection',
			'valeur'  => $collection,
			'extra'   => implode(', ', array_keys($collections))
		);
		$est_valide = false;
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
function requete_verifier_filtres($filtres, $collection, $configuration, &$erreur) {

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
				include_spip("svpapi/${module}");
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
function requete_verifier_ressource($ressource, $collection, $configuration, &$erreur) {

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
