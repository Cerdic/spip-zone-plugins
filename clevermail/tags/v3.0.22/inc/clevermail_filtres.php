<?php
/**
 * Afficher un message "aucun truc"/"un truc"/"N trucs", inspire de singulier_ou_pluriel
 *
 * @param int $nb
 * @param string $chaine_aucun
 * @param string $chaine_un
 * @param string $chaine_plusieurs
 * @return string
 */
function aucun_ou_un_ou_plusieurs($nb, $chaine_aucun, $chaine_un, $chaine_plusieurs, $vars = array()) {
	$vars = array_merge($vars, array('nb' => intval($nb)));
  if (intval($nb) == 0) {
  	return _T($chaine_aucun, $vars);
  } elseif (intval($nb) == 1) {
  	return _T($chaine_un, $vars);
  } else {
    return _T($chaine_plusieurs, $vars);
  }
}

function cm_date($timestamp) {
  return date('d/m/Y', $timestamp);
}

function cm_heure($timestamp) {
  return date('H:i', $timestamp);
}

/**
 * Filtre `setenv` qui enregistre une valeur dans l'environnement du squelette
 *
 * La valeur pourra être retrouvée avec `#ENV{variable}`.
 * 
 * @example
 *     `[(#CALCUL|setenv{toto})]` enregistre le résultat de `#CALCUL`
 *      dans l'environnement toto et renvoie vide.
 *      `#ENV{toto}` retourne la valeur.
 *
 *      `[(#CALCUL|setenv{toto,1})]` enregistre le résultat de `#CALCUL`
 *      dans l'environnement toto et renvoie la valeur.
 *      `#ENV{toto}` retourne la valeur.
 *
 * @filtre
 *
 * @param array $Pile
 * @param mixed $val Valeur à enregistrer
 * @param mixed $key Nom de la variable
 * @param null|mixed $continue Si présent, retourne la valeur en sortie
 * @return string|mixed Retourne `$val` si `$continue` présent, sinon ''.
 */
include_spip('inc/plugin'); // pour spip_version_compare
if (spip_version_compare($GLOBALS['spip_version_branche'], '3.1.0', '<')) {
	// Fonction qui n'existait pas avant SPIP 3.1
	function filtre_setenv(&$Pile, $val, $key, $continue = null) {
		$Pile[0][$key] = $val;
		return $continue ? $val : '';
	}
}
