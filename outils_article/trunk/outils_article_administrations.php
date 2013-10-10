<?php
function outils_article_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
    $maj["create"] = array(array("outils_article_corriger_theme"));
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function outils_article_corriger_theme(){
    // éviter de mettre chemin complet du thème
    $theme = lire_config("outils_article/theme");
    if ($theme){
        $theme = substr(strrchr($theme,"/"),1);
        ecrire_config("outils_article/theme",$theme);
        }
    }
?>