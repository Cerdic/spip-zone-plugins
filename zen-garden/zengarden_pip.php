<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2009 Cedric Morin
 *
 */

function zengarden_affichage_final($texte){
	if ($GLOBALS['html'] and isset($GLOBALS['meta']['zengarden_switcher'])){
		include_spip('prive/zengarden_theme_fonctions');
		$code = recuperer_fond('inc/switcher_zen');
		// On rajoute le code du selecteur de squelettes avant la balise </body>
		$texte=str_replace("</body>",$code."</body>",$texte);
	}
	return $texte;
}

function zengarden_insert_head($flux){
	if(isset($GLOBALS['meta']['zengarden_switcher'])){
		//$flux .= "<script src='".find_in_path('switcher_zen.js')."' type='text/javascript'></script>\n";
		$flux .= "<link type='text/css' href='".find_in_path('switcher_zen.css')."' rel='stylesheet' />";
	}
	return $flux;
}    
?>