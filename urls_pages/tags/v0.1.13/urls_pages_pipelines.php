<?php
/**
 * Plugin Urls Pages Etendues
 * (c) 2013 Charles Razack
 * Licence GNU/GPL
 */


/**
 * formulaire configurer_url
 */
function urls_pages_affiche_milieu($flux){
	$texte = "";
	$exec = $flux['args']['exec'];

	if ( $exec == 'configurer_urls' )
		$texte = recuperer_fond('prive/squelettes/contenu/configurer_urls_pages');

	if ($texte)
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;

	return $flux;
}


?>
