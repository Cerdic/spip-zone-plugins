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

function seo_affichage_final($flux) {

    $forcer_squelette = lire_config('seo/forcer_squelette');
    if ($forcer_squelette != 'yes' )
        return $flux;

    $meta_tags = calculer_meta_tags();
    $head = array();

    preg_match('/<head>(.*)<\/head>/mis',$flux,$head);
    $head = $head[1];

    foreach($meta_tags as $key => $value) {
        $meta = generer_meta_tags(array($key => $value));
        $head_meta = preg_replace("/(<\s*$key.*?>.*?<\/\s*$key.*?>)/mi",$meta,$head);
        $head_meta = preg_replace("/(<\s*meta\s*name=\"$key\"\s*content=\".*?\".*?>)/mi",$meta,$head_meta);
        if ($head == $head_meta)
            $head_meta .= "\n".$meta;
        $head = $head_meta;
    }
    
    $head = "<head>".$head."</head>";
    
    $flux = preg_replace('/<head>(.*)<\/head>/mis',$head,$flux);
    
    return $flux;
}

