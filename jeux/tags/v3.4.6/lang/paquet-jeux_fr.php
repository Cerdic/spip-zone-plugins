<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/jeux.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// J
	'jeux_description' => 'Des jeux avec SPIP !

Voici pour vous la possibilité d’insérer dans vos articles des mots croisés, des sudokus, des devinettes, des blagues, des poésies, des QCM, des exercices à trous, etc.

Mettez une touche éducative et ludique à votre site !

Deux solutions :
-* Des jeux codés en clair dans les articles entre les balises <code><jeux></code> et <code></jeux></code>.
-* Des jeux codés dans l’espace privé et accessibles grâce au modèle <code><jeuXX></code> où XX est l’identifiant du jeu.

L’enregistrement et la gestion des scores n’est possible qu’en utilisant la seconde solution.

Ce plugin fonctionne de façon optimale sur les squelettes disposant de la balise [#INSERT_HEAD->https://www.spip.net/fr_article1902.html] et peut également être testé dans l’espace privé.

_* Pour les jeux graphiques, il faut avoir les librairies {{GD}} installées sur votre server.
_* Ce plugin est encore en évolution, procédez régulièrement à vos mises à jour.
  
Afin d’éviter les mauvaises surprises de présentation (comme l’affichage des solutions dans les sommaires, rubriques, backends, etc.), pensez bien :
-* soit à placer une {{introduction}} dans votre article entre les balises <code><intro></code> et <code></intro></code>,
-* soit à remplir le {{descriptif}} de l’article.

Crédits :
-* Icones : Jonathan Roche
-* Travaux originaux :
-** QCM : Mathieu Giannecchini
-** Mots croisés et scores : Maïeul Rouquette
-** Diagrammes d’échecs : François Schreuer',
	'jeux_slogan' => 'Créez des jeux ou des exercices de toute sorte.'
);
