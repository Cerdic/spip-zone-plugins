<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_configurer_htmlminifier_charger_dist() {

    $valeurs = array();
    $est_valide = _request('valider');

    if(is_null($est_valide)){

        $valeurs = lire_config('htmlminifier', array());

        if(empty($valeurs)) {
            $valeurs = HTMLMinifier::get_presets('super_safe');
        }

        $valeurs = applatir_config($valeurs);

    }
    else {

        $valeurs = array(
            'compression_mode' => _request('compression_mode'),
            'clean_html_comments' => _request('clean_html_comments'),
            'merge_multiple_head_tags' => _request('merge_multiple_head_tags'),
            'merge_multiple_body_tags' => _request('merge_multiple_body_tags'),
            'show_signature' => _request('show_signature'),

            'clean_css_comments' => _request('clean_css_comments'),
            'remove_comments_with_cdata_tags_css' => _request('remove_comments_with_cdata_tags_css'),
            'shift_link_tags_to_head' => _request('shift_link_tags_to_head'),
            'ignore_link_schema_tags' => _request('ignore_link_schema_tags'),
            'shift_meta_tags_to_head' => _request('shift_meta_tags_to_head'),
            'ignore_meta_schema_tags' => _request('ignore_meta_schema_tags'),
            'shift_style_tags_to_head' => _request('shift_style_tags_to_head'),
            'combine_style_tags' => _request('combine_style_tags'),

            'clean_js_comments' => _request('clean_js_comments'),
            'remove_comments_with_cdata_tags_js' => _request('remove_comments_with_cdata_tags_js'),
            'compression_ignored_tags' => _request('compression_ignored_tags'),
            'shift_script_tags_to_bottom' => _request('shift_script_tags_to_bottom'),
            'combine_javascript_in_script_tags' => _request('combine_javascript_in_script_tags'),
            'ignore_async_and_defer_tags' => _request('ignore_async_and_defer_tags'),
        );

    }

    if(_request('message_ok_config', $_GET) == "oui") {
        $valeurs['message_ok_config'] = _T('config_info_enregistree');
    }

    return $valeurs;
    
}


function formulaires_configurer_htmlminifier_verifier_dist() {

	$erreurs = array();
    return $erreurs;
    
}


function formulaires_configurer_htmlminifier_traiter_dist() {

    $res = array();

    $compression_ignored_tags = array();
    $ignored_tags = _request('compression_ignored_tags');
    $elements = explode(",", $ignored_tags);
    foreach ($elements as $key => $element) {
        if(!empty(trim($element)))
            $compression_ignored_tags[] = trim($element);
    }

    $valeurs = array(
        'compression_mode' => _request('compression_mode'),
        'clean_html_comments' => boolval(_request('clean_html_comments')),
        'merge_multiple_head_tags' => boolval(_request('merge_multiple_head_tags')),
        'merge_multiple_body_tags' => boolval(_request('merge_multiple_body_tags')),
        'show_signature' => boolval(_request('show_signature')),

        'clean_css_comments' => boolval(_request('clean_css_comments')) ? array('remove_comments_with_cdata_tags_css' => boolval(_request('remove_comments_with_cdata_tags_css'))) : false,
        'shift_link_tags_to_head' => boolval(_request('shift_link_tags_to_head')) ? array('ignore_link_schema_tags' => boolval(_request('ignore_link_schema_tags'))) : false,
        'shift_meta_tags_to_head' => boolval(_request('shift_meta_tags_to_head')) ? array('ignore_meta_schema_tags' => boolval(_request('ignore_meta_schema_tags'))) : false,
        'shift_style_tags_to_head' => boolval(_request('shift_style_tags_to_head')) ? array('combine_style_tags' => boolval(_request('combine_style_tags'))) : false,

        'clean_js_comments' => boolval(_request('clean_js_comments')) ? array('remove_comments_with_cdata_tags_js' => boolval(_request('remove_comments_with_cdata_tags_js'))) : false,
        'compression_ignored_tags' => $compression_ignored_tags,
        'shift_script_tags_to_bottom' => boolval(_request('shift_script_tags_to_bottom')) ? array('combine_javascript_in_script_tags' => boolval(_request('combine_javascript_in_script_tags')), 'ignore_async_and_defer_tags' => boolval(_request('ignore_async_and_defer_tags'))) : false
    );

    ecrire_config('htmlminifier', $valeurs);

    $res['message_ok'] = _T('config_info_enregistree');

	return $res;
}

function applatir_config(array $array) {

    $return = array();

    foreach ($array as $key => $value) {
        if($key == 'compression_ignored_tags') {
            $return[$key] = implode(", ", $value);
        }
        else if (is_array($value)){
            $return[$key] = "1";
            $return = array_merge($return, applatir_config($value));
        } else {
            $return[$key] = $value;
        }
    }

    return $return;
}