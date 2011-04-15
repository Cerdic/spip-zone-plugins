<?php
/**
 * Plugin factures - Facturer avec Spip 2.0
 * (c) 2009-2011
 * Licence GPL 
 * par Cyril Marion - Camille Lafitte
 */

/******************************************************************/
/*   FONCTIONS D'AFFICHAGE (filtres simples)
/******************************************************************/
function horaire($horaire) {
	// affiche un nombre d'heures correctement formatté
	$horaire = ($horaire && $horaire !=='')? $horaire." h":"";
	return $horaire;
}
function monetaire($montant) {
	// affiche un montant en euro correctement formatté
	setlocale(LC_MONETARY, 'fr_FR');
	$montant = money_format('%i', $montant);
	$montant = preg_replace("/EUR/", "&euro;", $montant);
	$montant = preg_replace("/ /", "&nbsp;", $montant);
	return $montant;
}

@define('tvaNormale', 1.196);
@define('tvaReduite', 1.055);
function tva($montant) {
	$tva = $montant * (tvaNormale -1);
	return $tva;
}
function ttc($montant) {
	$ttc = $montant * tvaNormale;
	return $ttc;
}
function prix_ttc($ht, $taux='tvaNormale') {
	// calcule un montant TTC à partir d'un montant HT
	setlocale(LC_MONETARY, 'fr_FR');
	$taux = constant($taux);
	$ttc = $ht * $taux;
	$ttc = money_format('%i', $ttc);
	$ht  = money_format('%i', $ht);
	$tva = $ht . " HT" . " (" . $ttc . " TTC)";
	$tva = preg_replace("/EUR/", "&euro;", $tva);
	return $tva;
}
function alerte($nombre) {
	// affiche un nombre avec la CSS alerte s'il est inférieur ou égal à zéro
	if ($nombre < 0) $nombre = "<span class='alerte'>$nombre</span>";
	return $nombre;
}


?>