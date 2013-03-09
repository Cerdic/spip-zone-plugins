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

if (!defined("_ECRIRE_INC_VERSION")) return;

function seo_insert_head($flux) {
	/* CONFIG */
	$config = unserialize($GLOBALS['meta']['seo']);
	if (isset($config['insert_head']) && $config['insert_head']['activate'] == 'yes') {
		$contexte = $GLOBALS['contexte'];
		unset($contexte['lang']);
		unset($contexte['date']);
		unset($contexte['date_default']);
		unset($contexte['date_redac']);
		unset($contexte['date_redac_default']);
		unset($contexte['lang']);
		if (count($contexte) == 0) {
			$objet = 'sommaire';
		} elseif (isset($contexte['id_article'])) {
			$id_objet = $contexte['id_article'];
			$objet = 'article';
		} elseif (isset($contexte['id_rubrique'])) {
			$id_objet = $contexte['id_rubrique'];
			$objet = 'rubrique';
		}
		/* META TAGS */
		if (isset($config['meta_tags']) && $config['meta_tags']['activate'] == 'yes') {
		    if (!defined('_SEO_FORCER_SQUELETTE')) {
		        $meta_tags = calculer_meta_tags();
			    $flux .= generer_meta_tags($meta_tags);
			 }
		}
		/* META GOOGLE WEBMASTER TOOLS */
		if (isset($config['webmaster_tools']) &&  $config['webmaster_tools']['activate'] == 'yes' && $objet == 'sommaire') {
			$flux .= generer_webmaster_tools();
		}

		if (isset($config['bing']) && $config['bing']['activate'] == 'yes' && $objet == 'sommaire') {
			$flux .= generer_bing();
		}
	
		/* CANONICAL URL */
		if (isset($config['canonical_url']) && $config['canonical_url']['activate'] == 'yes') {
			$flux .= generer_urls_canoniques();
		}
	
		/* GOOGLE ANALYTICS */
		if (isset($config['analytics']) && $config['analytics']['activate'] == 'yes') {
			$flux .= generer_google_analytics();
		}

		/* ALEXA */
		if (isset($config['alexa']) &&  $config['alexa']['activate'] == 'yes' && $objet == 'sommaire') {
			$flux .= generer_alexa();
		}
	}
	
	return $flux;
}

