<?php
function genespip_version(){
    $version_squelette = 0.7; //version actuelle
    $version_plugin = '0.7';

    if (!isset($GLOBALS['meta']['genespip_version_squelette']) or $version_squelette != $GLOBALS['meta']['genespip_version_squelette']) {
        ecrire_meta('genespip_version_squelette',$version_squelette);
        }
    if (!isset($GLOBALS['meta']['genespip_version_plugin']) or $version_plugin != $GLOBALS['meta']['genespip_version_plugin']) {
        ecrire_meta('genespip_version_plugin',$version_plugin);
        }
    ecrire_metas();
}
genespip_version();
?>