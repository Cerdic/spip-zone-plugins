<?php
/**
 * Utilisations de pipelines par Menu animé
 *
 * @plugin     Menu animé
 * @copyright  2015
 * @author     Louis Possoz
 * @licence    GNU/GPL
 * @package    SPIP\Menu_anime\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function menu_anime_insert_head_css($flux){
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/menu_anime.css').'" type="text/css" media="projection, screen, tv" />';
	}
	return $flux;
}
?>