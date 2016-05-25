<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Contextualiser la newsletter en fonction des infos utilisateur
 * Par simplification, les variables contextuelles sont exprimees par un simple @nom_variable@
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
 * @return string
 */
function newsletter_contextualize_dist($content,$context){

	// vite si rien a faire !
	if (strpos($content,"@")==false AND strpos($content,"#ENV")==false)
		return $content;

	// remplacer les @@truc@@ par [(#ENV{truc,''})]
	$content = preg_replace(",@([\w\d]+)@,Uims","[(#ENV{\\1,''})]",$content);

	// vite si rien a faire ! (le premier coup on a pu etre trompe par un email en clair)
	if (strpos($content,"@")==false AND strpos($content,"#ENV")==false)
		return $content;

	$content = "#CACHE{0}\n".$content; // pas de cache, on ne va calculer qu'une fois pour chaque contexte !

	// eviter de planter l'evaluation via un <?php restant dans une version texte dans laquelle les < ne sont plus echappes
	// corrolaire : pas de php pour la contextualization (ni de balise dynamique)
	if (strpos($content,'<'.'?')!==false)
		$content = str_replace('<'.'?', "\\<\\@\\?", $content);

	$md5 = md5($content);

	// ecrire le squelette dans un fichier temporaire
	$dir = sous_repertoire(_DIR_CACHE,"newsletters");
	$tmp = $dir."cont".$md5; // un meme contenu = un meme nom = un meme skel compile (evite la recompilation a chaque appel)

	if (file_exists($f=$tmp.".html") OR file_put_contents($f,$content)){
		if (_DIR_RACINE AND strncmp($tmp,_DIR_RACINE,strlen(_DIR_RACINE))==0)
			$tmp = substr($tmp,strlen(_DIR_RACINE));
		// si chemin absolu, l'ajouter au path
		elseif (substr(_DIR_CACHE,0,1)=="/")
			_chemin(_DIR_CACHE);
		$content = recuperer_fond($tmp,$context);

		#@unlink($f); // on le garde pour l'envoi suivant, mais il faudrait purger a un moment !
	}
	if (strpos($content,"\\<\\@\\?")!==false)
		$content = str_replace("\\<\\@\\?", '<'.'?', $content);

	return $content;
}
