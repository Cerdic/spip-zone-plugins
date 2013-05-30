<?php

/**
 * Utilisation de pipelines
 * 
 * @package SPIP\Embed_code\Pipelines
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Insertion dans le pipeline intranet_pages_ok (Plugin Intranet)
 * 
 * @pipeline intranet_pages_ok
 * @param array $pages_ok
 *     Tableau des pages accessibles même en intranet
 * @return array
 *     Tableau des pages accessibles même en intranet complété
 */
function embed_code_intranet_pages_ok($pages_ok){
	$pages_ok[] = 'embed_code';
	return $pages_ok; 
}
?>