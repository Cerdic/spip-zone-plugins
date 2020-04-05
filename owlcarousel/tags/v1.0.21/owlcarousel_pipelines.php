<?php
/**
 * Plugin Owl Carousel
 * (c) 2013 Mist. GraphX
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function owlcarousel_insert_head_css($flux){
    include_spip('inc/config');
    if(lire_config('owlcarousel/css', 0)){
        $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.carousel.css').'" media="screen" />'."\n";
        $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.theme.css').'" media="screen" />'."\n";
        $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.modeles.css').'" media="screen" />'."\n";
    }
    return $flux;
}

function owlcarousel_insert_head($flux){
     $flux.='<script src="'.find_in_path('javascript/owl.carousel.js').'" type="text/javascript"></script>'."\n";
     return $flux;
}

function owlcarousel_header_prive($flux){
  include_spip('inc/config');
  if(lire_config('owlcarousel/header_prive', 0)){
    $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.carousel.css').'" media="screen" />'."\n";
    $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.theme.css').'" media="screen" />'."\n";
    $flux.='<link rel="stylesheet" type="text/css" href="'.find_in_path('css/owl.modeles.css').'" media="screen" />'."\n";
    $flux = owlcarousel_insert_head($flux);
  }
  return $flux;
}

/**
 * ieconfig
 * http://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation#reply460680
*/
function owlcarousel_ieconfig_metas($table){
    $table['owlcarousel']['titre'] = _T('owlcarousel:cfg_titre_page_configurer_owlcarousel');
    $table['owlcarousel']['icone'] = 'prive/themes/spip/images/owlcarousel-16.png';
    $table['owlcarousel']['metas_serialize'] = 'owlcarousel';
    return $table;
}
