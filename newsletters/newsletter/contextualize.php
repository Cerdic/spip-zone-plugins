<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Contextualiser la newsletter en fonction des infos utilisateur
 * Par simplification, les variables contextuelles sont exprimees par un simple @@nom_variable@@
 * mais on peut ecrire des variantes complexes avec le balisage SPIP  #ENV{nom_variable,truc} etc
 * C'est plus complique a ecrire car ces balises ne doivent pes etre valuees au premier hit
 * et donc echappees dans le modele de newsletter :
 *
 * \#ENV{nom} => devient #ENV{nom} apres calcul du modele => devient le vrai nom dans la newsletter contextualisee
 *
 * @param $content
 *   le contenu (html ou texte) de la newsletter
 * @param $context
 *   le contexte des variables
 * @return mixed
 */
function newsletter_contextualize_dist($content,$context){

	// vite si rien a faire !
	if (strpos($content,"@@")==false AND strpos($content,"#ENV")==false)
		return $content;

	// remplacer les @@truc@@ par [(#ENV{truc,''})]
	$content = preg_replace(",@@([\w\d]+)@@,Uims","[(#ENV{\\1,''})]",$content);
	// ecrire le squelette dans un fichier temporaire
	$dir = sous_repertoire(_DIR_CACHE,"newsletters");
	$tmp = tempnam($dir,'context');
	$tmp = $dir . basename($tmp);

	if (file_put_contents($f=$tmp.".html",$content)){
		if (_DIR_RACINE AND strncmp($tmp,_DIR_RACINE,strlen(_DIR_RACINE))==0)
			$tmp = substr($tmp,strlen(_DIR_RACINE));
		$content = recuperer_fond($tmp,$context);
		@unlink($f);
	}

	return $content;
}
