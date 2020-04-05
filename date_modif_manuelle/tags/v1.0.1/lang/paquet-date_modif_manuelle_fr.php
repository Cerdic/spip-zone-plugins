<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// D
	'date_modif_manuelle_description' => 'Par défaut la date de modification (champ date_modif) d\'un article est calculée automatiquement à chaque modification faite sur un contenu de l\'article. Ce plugin ajoute un champ date_modif_manuelle, et permet aux utilisateurs de saisir manuellement une date de modification de l\'article, qui ne change pas à chaque édition. 

Une écriture sympa dans un squelette peut être de faire des tris <code>{!par GREATEST(date,date_modif_manuelle)}</code> qui trient en fonction soit de la date de publication, soit de modification manuelle, du plus récent au plus ancien.',
	'date_modif_manuelle_nom' => 'Date de modification manuelle',
	'date_modif_manuelle_slogan' => 'Ajoute une date de modification sur les articles à saisir',
);
