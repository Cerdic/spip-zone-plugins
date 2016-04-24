<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Détermine si la valeur du crtière compatibilité SPIP est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être un numéro de version ou de branche.
 *
 * @param string	$valeur
 * 		La valeur du critère compatibilite SPIP
 * @param array		$erreur
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_format($valeur, &$erreur) {
	$format_valide = true;

	if (!in_array($valeur, array('json', 'xml'))) {
		$erreur = array(
			'status'	=> 400,
			'type'		=> 'format_nok',
			'element'	=> 'format',
			'valeur'	=> $valeur);
		$format_valide = false;
	}

	return $format_valide;
}


/**
 * Détermine si la collection demandée est valide.
 * Le service ne fournit que la collection plugins.
 *
 * @param string $valeur
 * 		La valeur de la collection demandée
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_collection($valeur, &$erreur) {
	$collection_valide = true;

	if (!in_array($valeur, array('plugins'))) {
		$erreur = array(
			'status'	=> 400,
			'type'		=> 'collection_nok',
			'element'	=> 'collection',
			'valeur'	=> $valeur);
		$collection_valide = false;
	}

	return $collection_valide;
}


/**
 * Détermine si la collection demandée est valide.
 * Le service ne fournit que la collection plugins.
 *
 * @param string $valeur
 * 		La valeur de la collection demandée
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_ressource($valeur, &$erreur) {
	$ressource_valide = true;

	if (!in_array($valeur, array('plugin'))) {
		$erreur = array(
			'status'	=> 400,
			'type'		=> 'ressource_nok',
			'element'	=> 'ressource',
			'valeur'	=> $valeur);
		$ressource_valide = false;
	}

	return $ressource_valide;
}


/**
 * Détermine si la valeur du préfixe de plugin est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * celui d'un nom de variable.
 *
 * @param string $valeur
 * 		La valeur du préfixe
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_prefixe($valeur, &$erreur) {
	$prefixe_valide = true;

	if (!preg_match('#^(\w){2,}$#', strtolower($valeur))) {
		$erreur = array(
			'status'	=> 400,
			'type'		=> 'prefixe_nok',
			'element'	=> 'prefixe',
			'valeur'	=> $valeur);
		$prefixe_valide = false;
	}

	return $prefixe_valide;
}


function requete_verifier_criteres($criteres, &$erreur) {

	$critere_valide = true;
	$erreur = array();

	if ($criteres) {
		// On vérifie pour chaque critère :
		// -- si le critère est valide
		// -- si la valeur du critère est valide
		// On arrête dès qu'une erreur est trouvée et on la reporte
		foreach ($criteres as $_critere => $_valeur) {
			$verifier = "requete_verifier_critere_${_critere}";
			if (!$verifier($_valeur)) {
				$erreur = array(
					'status'	=> 400,
					'type'		=> 'critere_nok',
					'element'	=> $_critere,
					'valeur'	=> $_valeur);
				$critere_valide = false;
				break;
			}
		}
	}

	return $critere_valide;
}


/**
 * Détermine si la valeur de la catégorie est valide.
 * La fonction fait appel à un filtre de SVP pour récupérer la liste des catégories autorisées.
 *
 * @param string $valeur
 * 		La valeur du critère catégorie
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_critere_categorie($valeur) {
	$critere_valide = true;

	include_spip('inc/svp_phraser');
	if (!in_array($valeur, $GLOBALS['categories_plugin'])) {
		$critere_valide = false;
	}

	return $critere_valide;
}


/**
 * Détermine si la valeur du critère compatibilité SPIP est valide.
 * La fonction compare uniquement la structure de la chaine passée qui doit être cohérente avec
 * un numéro de version ou de branche.
 *
 * @param string $valeur
 * 		La valeur du critère compatibilite SPIP
 *
 * @return boolean
 * 		True si la valeur est valide, false sinon.
 */
function requete_verifier_critere_compatible_spip($valeur) {
	$critere_valide = true;

	if (!preg_match('#^(\d+)(\.\d+){0,2}$#', $valeur)) {
		$critere_valide = false;
	}

	return $critere_valide;
}
