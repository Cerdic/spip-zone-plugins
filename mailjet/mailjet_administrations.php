<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS ['mailjet_installed'] = TRUE;

function mailjet_install($action)
{
    switch ($action){
        case 'test':
            return $GLOBALS ['mailjet_installed'];
        case 'install':
            break;
        case 'uninstall':
            effacer_meta ('mailjet_enabled');
            effacer_meta ('mailjet_test');
            effacer_meta ('mailjet_test_address');
            effacer_meta ('mailjet_username');
            effacer_meta ('mailjet_password');
            effacer_meta ('mailjet_host');
            effacer_meta ('mailjet_port');

            $GLOBALS ['mailjet_installed'] = FALSE;
    }
}

?>