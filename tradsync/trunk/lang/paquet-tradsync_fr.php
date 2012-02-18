<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-tradsync
// Langue: fr
// Date: 18-02-2012 10:45:55
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// T
	'tradsync_description' => 'Aide à la synchronisation des traductions.

	  En plus de l\'interface, ce plugin fournit quelques critères :
-* <code>{traductions en}</code> : tous les éléments
	anglais etant des traductions = <code>{!origine_traduction}{lang=en}</code>
-* <code>{!traductions en}</code> : tous les éléments
	d\'origine non traduits en anglais
-* <code>{origine_modifiee}</code> : tous les éléments
	traduits dont la source a été modifiée
	(date plus récente)
-* <code>{!origine_modifiee}</code> : tous les éléments traduits
	dont la source n\'est pas plus récente
-* <code>{polyglotte titre}</code> : tous les elements ayant une expression "multi" dans le titre
-* <code>{polyglotte titre,en}</code> : tous les elements ayant une expression "multi" possédant l\'anglais dans le titre
-* <code>{!polyglotte titre}</code> : tous les elements n\'ayant pas d\'expression "multi"
-* <code>{!polyglotte titre,en}</code> : tous les elements n\'ayant pas d\'expression "multi" dans la langue demandée',
	'tradsync_slogan' => 'Aide à la synchronisation des traductions',
);
?>