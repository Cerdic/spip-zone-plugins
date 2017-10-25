<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-activite_editoriale
// Langue: fr
// Date: 21-05-2012 16:04:36
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
	'activite_editoriale_description' => 'Ce plugin a pour objectif de compléter les fonctionnalités de suivi de l\'activité éditoriale de SPIP.

	La configuration générale du plugin propose de choisir sur quel champ de date s\'appuyer pour déterminer si le délai est dépassé :
	-* lorsqu\'on modifie le texte ou le titre de la rubrique : le champ #MAJ de la rubrique.
	-* lorsqu\'on modifie les articles de la rubrique : le champ #DATE_MODIF des articles de la rubrique uniquement.
	-* lorsqu\'on modifie les articles de la branche : le champ #DATE_MODIF des articles de la branche entière.
	On peut aussi choisir de prévenir les auteurs des articles.
	
	Dans chaque rubrique on peut paramétrer :
	-* le délai : en nombre de jour, le délai au-delà duquel une alerte est envoyée.
	-* choisir d\'alerter pour chaque article dépassant le délai. Par défaut : une alerte lorsque tous les articles ont dépassé le délai.
	-* à qui envoyer les alertes : on renseigne juste le numéro identifiant des auteurs du site, et/ou des courriels.
	-* la fréquence de relance de l\'alerte.
	
	Dans une nouvelle page accessible sous le menu Activité > Activité Éditoriale, un tableau recense les rubriques suivies en séparant les rubriques à jour et les autres.',
	
	'activite_editoriale_slogan' => 'Un site, c\'est bien ! Un site à jour, c\'est mieux !',
);
