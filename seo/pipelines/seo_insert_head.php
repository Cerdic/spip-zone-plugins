<?php
/**
* BouncingOrange SPIP SEO plugin
*
* @category   SEO
* @package    SPIP_SEO
* @author     Pierre ROUSSET (p.rousset@gmail.com)
* @copyright  Copyright (c) 2009 BouncingOrange (http://www.bouncingorange.com)
* @license    http://opensource.org/licenses/gpl-2.0.php  General Public License (GPL 2.0)
*/

function seo_insert_head($flux) {
	
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);
	if ($config['insert_head']['activate'] == 'yes') {
		if (count($GLOBALS['contexte']) == 0) {
			$type_object = 'sommaire';
		} elseif (isSet($GLOBALS['contexte']['id_article'])) {
			$id_object   = $GLOBALS['contexte']['id_article'];
			$type_object = 'article';
		} elseif (isSet($GLOBALS['contexte']['id_rubrique'])) {
			$id_object   = $GLOBALS['contexte']['id_rubrique'];
			$type_object = 'rubrique';
		}
		
		/* META TAGS */
		if ($config['meta_tags']['activate'] == 'yes') {
			$flux .= generer_meta_tags();
		}
		/* META GOOGLE WEBMASTER TOOLS */
		if ($config['webmaster_tools']['activate'] == 'yes' && $type_object == 'sommaire') {
			$flux .= generer_webmaster_tools();
		}
	
		/* CANONICAL URL */
		if ($config['canonical_url']['activate'] == 'yes') {
			$flux .= generer_urls_canoniques();
		}
	
		/* GOOGLE ANALYTICS */
		if ($config['analytics']['activate'] == 'yes') {
			$flux .= generer_google_analytics();
		}

		/* ALEXA */
		if ($config['alexa']['activate'] == 'yes' && $type_object == 'sommaire') {
			$flux .= generer_alexa();
		}
	}
	
	return $flux;
}

