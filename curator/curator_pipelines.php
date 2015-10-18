<?php
/**
 * Utilisations de pipelines par curator
 *
 * @plugin     curator
 * @copyright  2014
 * @author     ydikoi
 * @licence    GNU/GPL
 * @package    SPIP\Curator\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion des css du plugin dans les pages publiques
 *
 * @param $flux
 * @return mixed
 */
function curator_insert_head_css($flux){
	$flux .="\n".'<link rel="stylesheet" href="'. find_in_path('css/curator.css') .'" />';
	return $flux;
}