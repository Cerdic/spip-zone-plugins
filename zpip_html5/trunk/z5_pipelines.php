<?php
/**
 * Plugin Pagimages
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function Z5_insert_head_css($flux){
	
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/spip.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.form.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.comment.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.list.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.petition.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.pagination.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/spip.portfolio.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/clear.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/grid.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/button.css').'" type="text/css" />
	<link rel="stylesheet" href="'.find_in_path('css/layout.css').'" type="text/css" />';

	return $flux;
}

?>
