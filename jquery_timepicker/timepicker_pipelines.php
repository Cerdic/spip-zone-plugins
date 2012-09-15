<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * insertion du css pour timepicker
 **/
function timepicker_insert_head_css($flux){
    $css   = find_in_path('css/jquery-timepicker.css');
    $flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;
}

/**
 * insertion du js pour timepicker
 **/
function timepicker_insert_head($flux){
    $flux .= <<<EOF
    <script type="text/javascript">
    $(document).ready(function(){
        jQuery(".datetimepicker").datetimepicker({
            addSliderAccess: true,
            sliderAccessArgs: { touchonly: false }
        });
        jQuery(".timepicker").timepicker({
            addSliderAccess: true,
            sliderAccessArgs: { touchonly: false }
        });
    });
    </script>
EOF;
    return $flux;
}
/**
 * insertion des scripts du timepickers ui
 **/
function timepicker_jquery_plugins($scripts){
    $scripts[] = "js/jquery-ui-timepicker.js";
    $scripts[] = "js/jquery-ui-sliderAccess.js";
    $scripts[] = "js/i18n/jquery-ui-timepicker-fr.js";
    return $scripts;
}

/**
 * Activation des scripts jquery ui de SPIP 
 **/
function timepicker_jqueryui_plugins($scripts){
    $scripts[] = "jquery.ui.datepicker";
    $scripts[] = "i18n/jquery.ui.datepicker-fr";
    $scripts[] = "jquery.ui.slider";
    $scripts[] = "jquery.ui.button";
    return $scripts;
}
?>
