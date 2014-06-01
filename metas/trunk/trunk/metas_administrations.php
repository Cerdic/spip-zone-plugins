<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// fonction d'installation, mise a jour de la base
function metas_upgrade($nom_meta_base_version, $version_cible)
{
    // cas particulier :
    // si plugin pas installe mais que la table existe
    // considerer que c'est un upgrade depuis v 1.0.0
    // pour gerer l'historique des installations SPIP <=2.1
    if (!isset($GLOBALS['meta'][$nom_meta_base_version])) {
        $trouver_table = charger_fonction('trouver_table', 'base');
        if ($desc = $trouver_table('spip_signatures')
            AND isset($desc['field']['id_article'])
        ) {
            ecrire_meta($nom_meta_base_version, '1.0.0');
        }
        // si pas de table en base, on fera une simple creation de base
    }


    $maj = array();
    $maj['create'] = array(
        array('maj_tables', array('spip_metas','spip_metas_liens')),
    );

    // comme c'est un ajout de colonne, pas besoin d'utiliser un sqal_alter
    $maj['1.2'] = array(
        array('sql_alter',"TABLE spip_metas ADD canonical TEXT NOT NULL DEFAULT ''"),
    );

    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);

}

// fonction de desinstallation
function metas_vider_tables($nom_meta_base_version)
{
    sql_drop_table("spip_metas");
    sql_drop_table("spip_metas_liens");
    effacer_meta('spip_metas_title');
    effacer_meta('spip_metas_description');
    effacer_meta('spip_metas_mots_importants');
    effacer_meta('spip_metas_mots_keywords');
    effacer_meta($nom_meta_base_version);
}

?>
