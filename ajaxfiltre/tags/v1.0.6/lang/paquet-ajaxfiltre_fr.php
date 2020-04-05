<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

		// A
		'ajaxfiltre_description' => 'Ce plugin permet de créer des filtres de recherche multiples dans la colonne de gauche (navigation), qui rechargent une liste d\'objets en ajax.

Fonctionne avec n\'importe quelle liste d\'objets.
Il suffit d\'ajouter le nom {liste-objets} au paramètre ajax dans {prive/squelettes/contenu/patates.html}, 
et de créer un squelette {prive/squelettes/navigation/patates.html} dans lequel on peut utiliser des saisies.

Voir {{[la page de démo->?exec=ajaxfiltre_articles]}} et ses trois squelettes documentés :
-* /ajaxfiltre/prive/objets/liste/ajaxfiltre_articles.html
-* /ajaxfiltre/prive/squelettes/contenu/ajaxfiltre_articles.html
-* /ajaxfiltre/prive/squelettes/navigation/ajaxfiltre_articles.html',
		'ajaxfiltre_nom' => 'Filtres rapides dans le privé',
		'ajaxfiltre_slogan' => 'Des filtres de recherche sur les listes d\'objets',
	);
