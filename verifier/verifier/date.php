<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Une date au format JJ/MM/AAAA (avec séparateurs souples : espace / - .)
 * TODO : introduire via les options le FORMAT de la date, pour accepter différentes écritures
 * On pourrait faire mieux, genre vérifier les jours en fonction du mois
 * Mais c'est pas très important, on reste simple
 *
 * @param string $valeur La valeur à vérifier.
 * @param array $option tableau d'options [NON UTILISE].
 * @return string Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */

function verifier_date_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_date_format');
	$ok = '';
	// On tolère différents séparateurs
	$valeur = preg_replace("#\.|/| #i",'-',$valeur);
	
	// On vérifie la validité du format
	if(!preg_match('#^[0-9]{2}-[0-9]{2}-[0-9]{4}$#',$valeur)) return $erreur;
	// On vérifie vite fait que les dates existent, genre le 32 pour un jour NON, (mais on pourrait aller plus loin et vérifier en fonction du mois)
	list($jour,$mois,$annee) = explode('-',$valeur);
	// validité de la date
	$erreur = _T('verifier:erreur_date');
	if (!checkdate($mois, $jour, $annee)) return $erreur;
	
	return $ok;
}
