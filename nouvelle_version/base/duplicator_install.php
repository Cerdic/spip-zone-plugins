    <?php
    if (!defined("_ECRIRE_INC_VERSION")) return;
    function duplicator_upgrade($nom_meta_base_version,$version_cible){
            $current_version = 0.0;
            if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
            || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
                    include_spip('base/duplicator');
                    // cas d'une installation
                    if ($current_version==0.0){
                            include_spip('base/create');
                            maj_tables('spip_articles');
                            ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
                    }
            }
    }
    function duplicator_vider_tables($nom_meta_base_version) {
            sql_alter("TABLE spip_articles DROP version_of");
            effacer_meta($nom_meta_base_version);
    }
    ?>