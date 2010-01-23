<?php
function lm2_insert_head($flux){
	if (!defined('_DIR_LIB_SM')) define('_DIR_LIB_SM', _DIR_RACINE . 'lib/soundmanagerv295a-20090717/');
	$flux .= '<script type="text/javascript" src="'.find_in_path(_DIR_LIB_SM.'script/soundmanager2.js').'"></script>'."\n";
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_playlist_jquery.js').'"></script>'."\n";
	$flux .= '<link rel="stylesheet" href="'.generer_url_public('lm2_player.css').'" type="text/css" media="all" />'."\n";
	$flux .= '<script type="text/javascript">'."\n".
	'soundManager.debugMode = false;'."\n".
	'var REPSWF ="' . _DIR_LIB_SM . 'swf/";'."\n".
	'</script>'."\n" ;
	$flux .= '<script type="text/javascript" src="'.find_in_path('javascript/lm2_inlineplayer.js').'"></script>'."\n";

	return $flux;
}

/**
 * enclosures 
 * ajout d'un rel="enclosure" sur les liens mp3 absolus
 * permet de traiter les [mon son->http://monsite/mon_son.mp3] dans un texte. 
 * Le filtre peut etre appele dans un squelette apres |liens_absolus
 */
 
 function lm2_post_propre($texte) {

	$reg_formats="mp3";

	$texte = preg_replace(
		",<a(\s[^>]*href=['\"]?(http:\/\/[a-zA-Z0-9\s()\/\:\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*)>(.*)</a>,Uims",
		'<a$1 rel="enclosure">$4</a>', 
		$texte);
	
	return $texte;
}

?>