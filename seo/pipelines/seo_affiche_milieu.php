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

function seo_affiche_milieu($vars) {
	include_spip('inc/autoriser');
	include_spip('inc/presentation');
	$config = unserialize($GLOBALS['meta']['seo']);

	// Rubrique
	if (in_array($vars["args"]["exec"], array('naviguer','rubrique')) && $vars["args"]["id_rubrique"] != '') {
		$objet = 'rubrique';
		$id_objet   = $vars["args"]["id_rubrique"];
	// Article
	} elseif (in_array($vars["args"]["exec"],array('articles','article')) && $vars["args"]["id_article"] != '') {
		$objet = 'article';
		$id_objet   = $vars["args"]["id_article"];
	// Other case we quit
	} else {
		return $vars;
	}
	
	// If meta tags are activates
	if ($config['meta_tags']['activate'] != 'yes' || $config['meta_tags']['activate_editing'] != 'yes') {
		return $vars;
	}

	$ret = '';

	$bouton = bouton_block_depliable(_T('seo:meta_tags'), false, "SEO");
	$ret .= debut_block_depliable(false, "SEO");
	
	// List		
	$ret .= recuperer_fond('prive/squelettes/inclure/seo_metas',array('objet'=>$objet,'id_objet'=>$id_objet));
	
	$ret .= fin_block();
		 
	// Create the border with the content
	$ret = debut_cadre_enfonce(_DIR_PLUGIN_SEO.'img_pack/meta_tags-24.png', true, "", $bouton) . $ret . fin_cadre_enfonce(true);

	$vars["data"] .= $ret;
	
	return $vars;
}
