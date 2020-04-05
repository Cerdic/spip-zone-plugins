<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
///  Fichier produit par PlugOnet
// Module: paquet-pb_couleur_rubrique
// Langue: fr
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// P
	'pb_couleur_rubrique_description' => 'Ce plugin permet de choisir une couleur pour chaque rubrique du site. Une fois activ&#233;, il ne demande aucune configuration suppl&#233;mentaire. Il ajoute simplement un pav&#233; dans les pages des rubriques permettant de choisir une couleur. L\'option n\'est accessible qu\'aux administrateurs.
	
		Pour afficher la couleur d\'une rubrique dans un squelette, il suffit d\'utiliser le code : <code>[#(#ID_RUBRIQUE|couleur_rubrique)]</code>.

		Pour afficher la couleur d\'un secteur dans un squelette, il suffit d\'utiliser le code : <code>[#(#ID_RUBRIQUE|couleur_secteur)]</code>.

		Il faut installer en plus le plugin Palette pour s&#233;lectionner visuellement la couleur sur une roue chromatique, sinon il faut utiliser le code hexad&#233;cimal correspondant &#224; la couleur, du type : #C5E41C
		
		Une page de configuration permet d\'interdire le changement de couleur, ou de ne permettre les couleurs que sur les secteurs.',
		
	'pb_couleur_rubrique_slogan' => 'Une couleur pour chaque rubrique',
);