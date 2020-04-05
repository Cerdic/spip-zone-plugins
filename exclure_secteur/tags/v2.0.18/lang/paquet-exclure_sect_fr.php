<?php
// Ceci est un fichier langue de SPIP -- This is a SPIP language file

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

// E
	'exclure_sect_description' => 'Ce plugin exclut automatiquement les secteurs séléctionnés des boucles ARTICLES / SITES / RUBRIQUES / BREVES / FORUMS (suivant la régle de SPIP, qui ne prend en compte que les forums liés à des articles).

Attention, pour le moment, le plugin ne gère pas les jointures (par exemple, il n\'exclura pas les forums qui appartiennent à un article d\'un secteur exclu).

Si dans une boucle on précise le secteur explicitement avec un critère <code>{id_secteur=valeur}</code>, <code>{id_secteur==regexp}</code> ou <code>{id_secteur IN liste}</code>, le traitement automatique d\'exclusion est désactivé.

On peut désactiver l\'exclusion automatique en utilisant le critère <code>{tout_voir}</code>. 
On peut aussi configurer le plugin pour que si l\'id principal du nom de boucle fait partie d\'un critère inclusif <code>{id_type_objet}</code>, <code>{type_objet==regexp}</code>, <code>{type_objet=valeur}</code> ou <code>{type_objet IN liste}</code> le traitement d\'exclusion soit désactivé (depuis la version 1.2)',
	'exclure_sect_slogan' => 'Exclure automatiquement certains secteurs des boucles SPIP',
);
