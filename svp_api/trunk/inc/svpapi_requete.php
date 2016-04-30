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
 * traiter les requêtes.
 *
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 501
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit `serveur_nok`
 *        - `element` : type d'objet sur lequel porte l'erreur, soit `serveur`
 *        - `valeur`  : la valeur du mode runtime
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_serveur(&$erreur) {
	$valide = true;

	include_spip('inc/svp_phraser');
	if (!_SVP_MODE_RUNTIME) {
		$erreur = array(
			'status'  => 501,
			'type'    => 'serveur_nok',
			'element' => 'runtime',
			'valeur'  => _SVP_MODE_RUNTIME
		);
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si la valeur du format de sortie est valide.
 * Seul le format JSON est accepté.
 *
 * @param string $valeur
 *        La valeur du format de sortie
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit format_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit format
 *        - `valeur`  : la valeur du format
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_format($valeur, &$erreur) {
	$valide = true;

	if (!in_array($valeur, array('json'))) {
		$erreur = array(
			'status'  => 400,
			'type'    => 'format_nok',
			'element' => 'format',
			'valeur'  => $valeur
		);
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si la collection demandée est valide.
 * Le service ne fournit que les collections plugins et dépôts.
 *
 * @param string $valeur
 *        La valeur de la collection demandée
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit collection_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit collection
 *        - `valeur`  : la valeur de la collection
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_collection($valeur, &$erreur) {
	$valide = true;

	if (!in_array($valeur, array('plugins', 'depots'))) {
		$erreur = array(
			'status'  => 400,
			'type'    => 'collection_nok',
			'element' => 'collection',
			'valeur'  => $valeur
		);
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si le type de ressource demandée est valide.
 * Le service ne fournit que des ressources de type plugin.
 *
 * @param string $valeur
 *        La valeur de la collection demandée
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit ressource_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit ressource
 *        - `valeur`  : la valeur de la ressource
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_ressource($valeur, &$erreur) {
	$valide = true;

	if (!in_array($valeur, array('plugin'))) {
		$erreur = array(
			'status'  => 400,
			'type'    => 'ressource_nok',
			'element' => 'ressource',
			'valeur'  => $valeur
		);
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si la valeur du préfixe de plugin est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * celui d'un nom de variable.
 *
 * @param string $valeur
 *        La valeur du préfixe
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit prefixe_nok
 *        - `element` : type d'objet sur lequel porte l'erreur, soit prefixe
 *        - `valeur`  : la valeur du préfixe
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_prefixe($valeur, &$erreur) {
	$valide = true;

	if (!preg_match('#^(\w){2,}$#', strtolower($valeur))) {
		$erreur = array(
			'status'  => 400,
			'type'    => 'prefixe_nok',
			'element' => 'prefixe',
			'valeur'  => $valeur
		);
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si la valeur de chaque critère de filtre d'une collection est valide.
 * Si plusieurs critères sont fournis, la fonction s'interromp dès qu'elle trouve un
 * critère invalide.
 *
 * @uses verifier_critere_categorie()
 * @uses verifier_critere_compatible_spip()
 *
 * @param array $criteres
 *        Tableau associatif des critères (couple nom du critère, valeur du critère)
 * @param &array    $erreur
 *        Tableau initialisé avec les index identifiant l'erreur ou vide si pas d'erreur.
 *        Les index mis à jour sont:
 *        - `status`  : le code de l'erreur HTTP, soit 400
 *        - `type`    : chaine identifiant l'erreur plus précisément, soit critere_nok
 *        - `element` : nom du critère en erreur
 *        - `valeur`  : valeur du critère
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function requete_verifier_criteres($criteres, &$erreur) {

	$valide = true;
	$erreur = array();

	if ($criteres) {
		// On vérifie pour chaque critère :
		// -- si le critère est valide
		// -- si la valeur du critère est valide
		// On arrête dès qu'une erreur est trouvée et on la reporte
		foreach ($criteres as $_critere => $_valeur) {
			$verifier = "verifier_critere_${_critere}";
			if (!$verifier($_valeur)) {
				$erreur = array(
					'status'  => 400,
					'type'    => 'critere_nok',
					'element' => $_critere,
					'valeur'  => $_valeur
				);
				$valide = false;
				break;
			}
		}
	}

	return $valide;
}


/**
 * Détermine si la valeur de la catégorie est valide.
 * La fonction récupère dans le plugin SVP la liste des catégories autorisées.
 *
 * @param string $valeur
 *        La valeur du critère catégorie
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function verifier_critere_categorie($valeur) {
	$valide = true;

	include_spip('inc/svp_phraser');
	if (!in_array($valeur, $GLOBALS['categories_plugin'])) {
		$valide = false;
	}

	return $valide;
}


/**
 * Détermine si la valeur du critère compatibilité SPIP est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * un numéro de version ou de branche.
 *
 * @param string $valeur
 *        La valeur du critère compatibilite SPIP
 *
 * @return boolean
 *        `true` si la valeur est valide, `false` sinon.
 */
function verifier_critere_compatible_spip($valeur) {
	$valide = true;

	if (!preg_match('#^(\d+)(\.\d+){0,2}$#', $valeur)) {
		$valide = false;
	}

	return $valide;
}
