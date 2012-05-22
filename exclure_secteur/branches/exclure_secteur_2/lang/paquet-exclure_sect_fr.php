<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-exclure_sect
// Langue: fr
// Date: 18-12-2011 12:10:03
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// E
	'exclure_sect_description' => 'Ce plugin exclut automatiquement les secteurs séléctionnés des boucles ARTICLES / SITES / RUBRIQUES / BREVES / FORUMS (suivant la régle de SPIP, qui ne prend en compte que les forums liés à des articles).

Attention, pour le moment, ne le fait pas sur les jointures (par exemple, n\'exclura pas les forums qui appartiennent à un article du secteur).

On peut faire une exception avec le critére <code>{tout_secteur}</code>. 
Si on précise le secteur explicitement, par <code>{id_secteur=x}</code>, <code>{id_secteur==x}</code> ou <code>{id_secteur IN X}</code>, le réglage ne sera pas pris en compte.

Possibilité aussi de configurer le plugin pour que <code>{tout}</code> soit équivalent à <code>{tout_voir}</code>.

On peut aussi configurer pour que que si l\'id principal du nom de boucle est passée via <code>{id_boucle}</code> ou <code>{id_boucle==X}</code> ou <code>{id_boucle=X}</code> ou <code>{id_boucle IN X}</code>, cela fait sauter l\'exclusion des secteurs. (Depuis la 1.2)',
	'exclure_sect_slogan' => 'Exclure automatiquement des boucles certains secteurs',
);
?>