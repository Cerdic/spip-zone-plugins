<?php
/**
 * Utilisations de pipelines par Publications HAL
 *
 * @plugin     Publications HAL
 * @copyright  2016
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Hal_pub\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout CSS de HAL PUB
 *
 * @pipeline insert_head
 * @param  string $flux Contenu du head
 * @return string Contenu du head
 */
function hal_pub_insert_head_css($flux) {
	$flux .= "\n<link rel='stylesheet' type='text/css' media='all' href='".find_in_path("css/hal_pub.css")."' />\n";
	return $flux;
}

function hal_pub_insert_head_prive_css($flux) {
	return hal_pub_insert_head_css($flux);
}

/**
 * Ajout des scripts du HAL PUB
 *
 * @pipeline insert_head
 * @param  string $flux Contenu du head
 * @return string Contenu du head
 */
function hal_pub_insert_head($flux) {
	$flux .= "\n<script type='text/javascript' src='".find_in_path("js/hal_pub.js")."'></script>\n";
	return $flux;
}

function hal_pub_insert_head_prive($flux) {
	return hal_pub_insert_head($flux);
}