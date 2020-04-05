<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// R
	'rssarticle_description' => 'Ce plugin recopie les flux RSS (articles syndiqués) en articles

-* reprise du contenu du flux;
-* créé l\'auteur s\'il est mentionné dans le flux;
-* ajoute les documents distants présents dans le flux;
-* dans le champs URL de l\'article, on indique l\'adresse de l\'article d\'origine.

Pour éviter les doublons et les imports successifs, une fois l\'article créé, l\'article syndiqué est rejeté (ce qui permet de suivre où en sont les recopiés).',
	'rssarticle_nom' => 'Flux RSS en articles',
	'rssarticle_slogan' => 'Recopie les flux RSS en articles',
);

