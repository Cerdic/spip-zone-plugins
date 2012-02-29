<?php

$GLOBALS ['mj_customsmtp_installed'] = TRUE;

function mj_customsmtp_install($action)
{
    switch ($action){
        case 'test':
            return $GLOBALS ['mj_customsmtp_installed'];
        case 'install':
            break;
        case 'uninstall':
            effacer_meta ('mj_customsmtp_enabled');
            effacer_meta ('mj_customsmtp_test');
            effacer_meta ('mj_customsmtp_test_address');
            effacer_meta ('mj_customsmtp_username');
            effacer_meta ('mj_customsmtp_password');
            effacer_meta ('mj_customsmtp_host');
            effacer_meta ('mj_customsmtp_port');

            $GLOBALS ['mj_customsmtp_installed'] = FALSE;
    }
}

?>