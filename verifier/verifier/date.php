<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Une date au format JJ/MM/AAAA (avec séparateurs souples : espace / - .)
 * TODO : introduire via les options le FORMAT de la date, pour accepter différentes écritures
 * On pourrait faire mieux, genre vérifier les jours en fonction du mois
 * Mais c'est pas très important, on reste simple
 */

function verifier_date_dist($valeur, $options=array()){
	$erreur = _T('verifier:erreur_date');
	$ok = '';
	// On tolère différents séparateurs
	$valeur = ereg_replace("#\.|/| #i",'-',$valeur);
	
	// On vérifie la validité du format
	if(!preg_match('#^[0-9]{2}-[0-9]{2}-[0-9]{4}$#',$valeur)) return $erreur;
	// On vérifie vite fait que les dates existent, genre le 32 pour un jour NON, (mais on pourrait aller plus loin et vérifier en fonction du mois)
	list($jour,$mois,$annee) = explode('-',$valeur);
	if(($jour > 31)|| ($jour < 1) || ($mois > 12) || ($mois < 1) || ($annee < 1800)) return $erreur; // 1800, je crois qu'avant les gens ne sont plus vivants °_°
	
	return $ok;
}
