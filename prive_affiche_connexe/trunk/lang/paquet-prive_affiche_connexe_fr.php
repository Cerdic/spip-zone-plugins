<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// P
	'prive_affiche_connexe_nom' => 'Espace privé : affiche connexe',
	'prive_affiche_connexe_slogan' => 'Sus à « affiche_milieu » !',
	'prive_affiche_connexe_description' => "Ce plugin expérimental tente de remédier à un point problématique de l'espace privé.

Sur la page d'un objet, le pipeline « affiche_milieu » permet d'ajouter des choses avant le « vrai » contenu éditorial.
Ce sont en principe des choses connexes à ce dernier : des contenus liés, des options de configuration etc.
Plus le pipeline est utilisé par divers plugins, plus le vrai contenu se retrouve repoussé en bas, ce qui perturbe la lecture et l'édition.

Ce plugin déplace tout le contenu ajouté par « affiche_milieu » dans le bloc #extra.
Il a été conçu pour fonctionner au mieux de concert avec le plugin « Espace privé fluide », qui affiche ce bloc en colonne de droite dès qu'il y a la place.
",

);
