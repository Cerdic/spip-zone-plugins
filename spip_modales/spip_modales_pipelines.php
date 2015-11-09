<?php
/**
 * Utilisations de pipelines par SPIP Modales
 *
 * @plugin     SPIP Modales
 * @copyright  2015
 * @author     XDjuj
 * @licence    GNU/GPL
 * @package    SPIP\Spip_modales\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * CSS
 */
function spip_modales_insert_head_css($flux){
    include_spip('inc/config');
	$config = lire_config('spip_modales/');

	// if($config['magnific_active']){
 //    	$flux .='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/magnific-popup.css').'" />';
 //    }
    if($config['photoswipe_active']){
    	$flux .='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/photoswipe.css').'" />';
    	$flux .='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/default-skin/default-skin.css').'" />';
    }
    return $flux;
}

/**
 * Javascript
 */
function spip_modales_insert_head($flux){
    include_spip('inc/config');
	$config = lire_config('spip_modales/');
	if($config['magnific_active']){
	    $flux .= "<script type='text/javascript' src='".find_in_path('js/jquery.magnific-popup.min.js')."'></script>";
    }
    if($config['photoswipe_active']){
   	    $flux .= "<script type='text/javascript' src='".find_in_path('js/photoswipe.min.js')."'></script>";
	}

	if($config['magnific_active'] || $config['photoswipe_active']){
  	    $flux .= "<script type='text/javascript' src='".find_in_path('js/spip_modales.js')."'></script>";
	}

    return $flux;
}