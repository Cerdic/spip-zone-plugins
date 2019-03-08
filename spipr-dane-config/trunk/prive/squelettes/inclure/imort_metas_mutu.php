<?php
include_spip('inc/config');

function import_metas($meta="theme_mutu/"){
    $metas = array(
        "theme_mutu/defaut/layer"=>"sdc/defaut/layer",
        "theme_mutu/defaut_body/color"=>"defaut/color1",
        "theme_mutu/defaut_body/background-image"=>"defaut/background-image",
        "theme_mutu/defaut_body/background-position"=>"defaut/background-position",
        "theme_mutu/defaut_body/background-size"=>"defaut/background-size",
        "theme_mutu/defaut_body/background-repeat"=>"defaut/background-repeat",
        "theme_mutu/defaut_body/background-attachment"=>"defaut/background-attachment",
        "theme_mutu/defaut_header/background-image"=>"header/background-image",
        "theme_mutu/defaut_header/background-position"=>"header/background-position",
        "theme_mutu/defaut_header/background-size"=>"header/background-size",
        "theme_mutu/defaut_header/background-repeat"=>"header/background-repeat",
        "theme_mutu/defaut_header/background-attachment"=>"header/background-attachment",
        "theme_mutu/defaut_title/color"=>"title/color",
        "theme_mutu/defaut_title/font-family"=>"title/font-family",
        "theme_mutu/defaut_title/font-size"=>"title/font-size",
    )
    foreach($metas as $old=>$new){
        ecrire_config($new, lire_config($old));
    }
}
?>