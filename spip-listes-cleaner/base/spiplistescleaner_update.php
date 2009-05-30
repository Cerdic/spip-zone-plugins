<?php

if (! defined('_ECRIRE_INC_VERSION') ) return;

$GLOBALS['spiplistescleaner_base_version'] = '1.1';

/**
 * Installation and upgrade hook.
 */
function spiplistescleaner_install($install) {
    switch ($install) {
        case 'test':
            return spiplistescleaner_test();
            break;
        case 'install':
            return spiplistescleaner_update();
            break;
        case 'uninstall':
            return spiplistescleaner_uninstall();
            break;
    }
}

function spiplistescleaner_test() {
    global $spiplistescleaner_base_version, $meta;
    return (isset($meta['spiplistescleaner_base_version']) &&  ($meta['spiplistescleaner_base_version'] >= $spiplistescleaner_base_version) );
}

function spiplistescleaner_update() {
    global $meta;
    $code = $GLOBALS['spiplistescleaner_base_version'];
    $curr = '1.1';
    if ( isset($meta['spiplistescleaner_base_version']) ) {
        $curr = $meta['spiplistescleaner_base_version'];
        if ($curr >= $code) {
            return;
        }
    }

    # Install or update the database
    include_spip('base/abstract_sql');

    if ('1.1' == $curr) {

        sql_create('spiplistescleaner_deleted_emails',
            array(
                'id' => "SERIAL NOT NULL",
                'email'        => "VARCHAR( 128 ) NOT NULL",
                'date'      => "DATE NOT NULL",
				),
            array(
                'PRIMARY KEY' => 'id'
            ),
            TRUE
        );


        $curr = $code;
        echo _T('spiplistescleaner:installed', array('version' => $curr));
    }

    ecrire_meta('spiplistescleaner_base_version', $curr);
    ecrire_metas();
    return;
}

function spiplistescleaner_uninstall() {
    include_spip('base/abstract_sql');

    # Delete tables
    sql_drop_table("spiplistescleaner_deleted_emails");

	echo _T('spiplistescleaner:uninstalled', array('version' => $curr));
	
    # Delete settings
    effacer_meta('spiplistescleaner_base_version');  
    ecrire_metas();
	
    return;
}
