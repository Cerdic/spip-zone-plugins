<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function badge_don_insert_head($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('badge_don.css').'" type="text/css" media="projection, screen, tv" />';
	return $flux;
}

function badge_don_affichage_final($page) {
	// ne rien faire si la page en cours n'est pas du html
	if (!$GLOBALS['html']) return $page;
	
	$badge = recuperer_fond('modeles/badge_don', array());
	// Insertion du badge avant la fermeture du body
	if (!strpos($page, 'id="badge_don"'))
		$page = preg_replace(',</body>,i', "$badge\n".'\0', $page, 1);

	return $page;
}

?>