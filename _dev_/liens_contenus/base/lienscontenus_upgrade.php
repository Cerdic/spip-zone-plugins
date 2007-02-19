<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * � 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/meta');

$GLOBALS['lienscontenus_base_version'] = 0.2;

function lienscontenus_upgrade()
{
    $version_base = $GLOBALS['lienscontenus_base_version'];
    $current_version = 0.0;
    if ((isset($GLOBALS['meta']['lienscontenus_base_version']))
            && (($current_version = $GLOBALS['meta']['lienscontenus_base_version']) == $version_base)) {
        return;
    }

    include_spip('base/lienscontenus');
    if ($current_version == 0.0) {
        include_spip('base/create');
        include_spip('base/abstract_sql');
        spip_log('Creation de la base', 'liens_contenus');
        creer_base();
        include_spip('inc/lienscontenus');
        lienscontenus_initialiser();
        $current_version = $version_base;
    }
    if ($current_version < 0.2) {
        // Mise a jour de la base
        spip_log('Mise a jour de la base', 'liens_contenus');
        include_spip('base/abstract_sql');
        spip_query("UPDATE spip_liens_contenus SET type_objet_contenant='syndic' WHERE type_objet_contenant='site'");
        spip_query("UPDATE spip_liens_contenus SET type_objet_contenu='syndic' WHERE type_objet_contenu='site'");
        $current_version = 0.2;
    }
    ecrire_meta('lienscontenus_base_version', $current_version, 'non');
    ecrire_metas();
}

function lienscontenus_vider_tables()
{
    spip_query('DROP TABLE spip_liens_contenus');
    effacer_meta('lienscontenus_base_version');
    ecrire_metas();
}

function lienscontenus_install($action)
{
    switch ($action) {
        case 'test':
            return (isset($GLOBALS['meta']['lienscontenus_base_version'])
                && ($GLOBALS['meta']['lienscontenus_base_version'] >= $GLOBALS['lienscontenus_base_version']));
            break;
        case 'install':
            lienscontenus_upgrade();
            break;
        case 'uninstall':
            lienscontenus_vider_tables();
            break;
    }
}   
?>