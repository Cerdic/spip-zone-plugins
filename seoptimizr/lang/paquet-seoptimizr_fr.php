<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

///  Fichier produit par PlugOnet
// Module: paquet-seoptimizr
// Langue: fr
// Date: 16-07-2017 22:37:32
// Items: 2

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
        'seoptimizr_description' => '
        	!!! HAUTEMENT EXPERIMENTAL !!!
        	Prenez en main le référencement de votre site SPIP (version 3 minimum) avec ce plugin
        
        	A l\'activation de ce plugin :

        	- il télécharge (meta plugin) et active plusieurs autres plugins SPIP reconnus d\'utilité pour l\'amélioration et/ou le pilotage du référencement

        	- offre un jeu de squelettes \'page\' ( spip.php?page=panel-articles ; spip.php?page=panel-rubriques ; spip.php?page=panel-mots</code> ) permettant la modifications de masse (grâce à crayons) sur les informations les plus courament manipulées par les référenceurs (Titles, metas, urls, Titrailles, ...)

        	- il ajoute à la base de données des champs extras techniques pour pouvoir controller le référencement de CHAQUE objet éditorial de type rubriques, articles, mots clés (pour l\'instant !)
        
        	- il proposera (peut-être à terme) d\'appliquer des reset HTML spécifiques sur certains objets 
        		(ex: reset mots-cles : https://codepen.io/loiseau2nuit/pen/aFcnL )

        	pré-requis :
        
        	- dans l\'immédiat, devrait logiquement fonctionner tel quel sur tout type de squelettes Z 

        	- Pour tout autre type de squelettes (dist ou ...) des modifications sont à prévoir dans votre code pour que le plugin fonctionne efficacement ! (en savoir + bientôt)
        ',
        'seoptimizr_slogan' => 'Prenez en main le référencement de votre site SPIP'
);
?>
