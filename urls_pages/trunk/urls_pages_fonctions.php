<?php
/**
 * Fonctions
 *
 * @plugin     URLs Personnalisées étendues
 * @copyright  2013
 * @author     Charles Razack
 * @licence    GNU/GPL
 * @package    SPIP\URLs Personnalisées étendues\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Ré-écriture des urls produites par la balise #URL_PAGE
 * en fonction des valeurs enregistrées dans la meta du plugin
 * spip.php?page=toto => une-belle-url-pour-toto
 * cf. http://www.openstudio.fr/Pages-personnalisees-et-reecriture.html 
 *
 * @param string $url
 *     url de base
 * @return string $url
 *     url éventuellement ré-écrite
**/
function url_perso ( $url ) {
	include_spip('inc/config');
	$rewritebase = lire_config('urls_pages/rewritebase');
	if ( is_array(lire_config('urls_pages')) )
		$liste_pages = array_filter(lire_config('urls_pages'));
	$page = parametre_url($url, 'page');
	if ( $page
	  and is_array($liste_pages)
	  and in_array($page, array_flip($liste_pages)) ) {
		$rewrite = $liste_pages[$page]; // nouvelle url
		$path = parse_url($url, PHP_URL_PATH); // analyse l'url et retourne un tableau des composants
		$url = parametre_url($path, 'page', ''); // vide l'url du parametre 'page'
		$url = str_replace($path, '/'.$rewrite, $url); // ajoute la nouvelle url
		if (isset($rewritebase) AND strlen($rewritebase)) $url = $rewritebase.$url; // ajoute le rewritebase si present
	}

	return $url;
}


?>
