<?php
/*
 * +--------------------------------------------+
 * | ACTIVITE DU JOUR
 * +--------------------------------------------+
 * | H. AROUX . Scoty . koakidi.com
 * | D. Chiche . pour la maj 2.0
 * +--------------------------------------------+
 * | Declare pipeline
 * +--------------------------------------------+
 */

// style + js
function actijour_header_prive($flux)
{
    $exec = _request('exec');
    if (preg_match('@^(actijour_).*@i', $exec)) {
        $flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_ACTIJOUR . 'func_js_acj.js"></script>' . "\n";
    }
    return $flux;
}

// repertoire icones ACTIJOUR
if (! defined("_DIR_IMG_ACJR")) {
    define('_DIR_IMG_ACJR', _DIR_PLUGIN_ACTIJOUR . '/prive/themes/spip/images/');
}
