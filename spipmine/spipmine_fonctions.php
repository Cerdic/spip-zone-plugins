<?php

/**
 * Plugin Spipmine pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
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
	$montant = ereg_replace("EUR", "&euro;", $montant);
	$montant = ereg_replace(" ", "&nbsp;", $montant);
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
	$tva = ereg_replace("EUR", "&euro;", $tva);
	return $tva;
}
function alerte($nombre) {
	// affiche un nombre avec la CSS alerte s'il est inférieur ou égal à zéro
	if ($nombre < 0) $nombre = "<span class='alerte'>$nombre</span>";
	return $nombre;
}





/******************************************************************/
/*   AUTRES FONCTIONS 
/******************************************************************/
function trouve_quantieme($date_jour) {
	list($jour, $mois, $annee) = explode('/', $date_jour);
	if ($time = mktime( 0, 0, 0, $mois, $jour, $annee)) {
		return date('z', $time)+1;
	}
	return false;
}


function quantieme($date_jour) {
	$date_jour = jour($date_jour)."/".mois($date_jour)."/".annee($date_jour);
	$quantieme = trouve_quantieme($date_jour);
	return $quantieme;
}

function facture($date_jour) {
	$texte = "F-";
	$texte.= substr(annee($date_jour),2,2)."-";
	$texte.= quantieme($date_jour);
	$texte.= "-01";
	return $texte;
}


/******************************************************************/
/*   TRAITEMENTS SPIP
/******************************************************************/
global  $table_des_traitements;

define('_TRAITEMENT_TYPO', 'typo(%s, "TYPO", $connect)'); // comme titre
define('_TRAITEMENT_RACCOURCIS', 'propre(%s, $connect)'); // comme texte

$table_des_traitements['DATE_ACTION'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_DEBUT'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_FACTUTE'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_LIVRAISON'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_LIVRAISON_PREVUE'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_PAYEMENT'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_REGLEMENT'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_SAISIE'][]= 'normaliser_date(%s)';
$table_des_traitements['FIN_VALIDITE'][]= 'normaliser_date(%s)';

$table_des_traitements['ADRESSE_1'][]= _TRAITEMENT_TYPO;
$table_des_traitements['ADRESSE_2'][]= _TRAITEMENT_TYPO;
$table_des_traitements['FONCTION'][]= _TRAITEMENT_TYPO;
$table_des_traitements['NOM'][]= _TRAITEMENT_TYPO;
$table_des_traitements['NOM_CLIENT'][]= _TRAITEMENT_TYPO;
$table_des_traitements['NOM_COURT'][]= _TRAITEMENT_TYPO;
$table_des_traitements['PRENOM'][]= _TRAITEMENT_TYPO;
$table_des_traitements['TYPE_ACTION'][]= _TRAITEMENT_TYPO;
$table_des_traitements['VILLE'][]= _TRAITEMENT_TYPO;

$table_des_traitements['ACTION'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['COMMENTAIRES'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['DESIGNATION'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['ENJEUX'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['IMPORTANCE'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['LIBELLE_FACTURE'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['LIBELLE_FORFAIT'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['METHODE'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['NOTA_BENE'][]= _TRAITEMENT_RACCOURCIS;
$table_des_traitements['OBJECTIF'][]= _TRAITEMENT_RACCOURCIS;


?>