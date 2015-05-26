<?php

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

$GLOBALS['spiplistescleaner_base_version'] = '1.1';

/**
 * Installation and upgrade hook.
 */
function spiplistescleaner_install($install)
{
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

function spiplistescleaner_test()
{
    global $spiplistescleaner_base_version, $meta;

    return (isset($meta['spiplistescleaner_base_version']) &&  ($meta['spiplistescleaner_base_version'] >= $spiplistescleaner_base_version));
}

function spiplistescleaner_update()
{
    global $meta;
    $code = $GLOBALS['spiplistescleaner_base_version'];
    $curr = '0.0'; // default value if never installed before
    if (isset($meta['spiplistescleaner_base_version'])) {
        $curr = $meta['spiplistescleaner_base_version'];
        if ($curr >= $code) {
            return;
        }
    }

    # Install or update the database
    include_spip('base/abstract_sql');

    // Create the database for new version (never installed before)
    if ('0.0' == $curr) {
        sql_create('spiplistescleaner_deleted_emails',
            array(
                'id' => 'SERIAL NOT NULL',
                'email' => 'VARCHAR( 128 ) NOT NULL',
                'date' => 'DATE NOT NULL',
                ),
            array(
                'PRIMARY KEY' => 'id',
            ),
            true
        );

        // Set the default config
        ecrire_config('spiplistescleaner/nb_deleted_mails', '0');
        ecrire_config('spiplistescleaner/nb_deleted_mails_last_export', '0');
        ecrire_config('spiplistescleaner/server_address', 'pop.myserver.com:110');
        ecrire_config('spiplistescleaner/server_username', 'superman');
        ecrire_config('spiplistescleaner/server_password', 'mypassword');
        ecrire_config('spiplistescleaner/server_mailbox', 'INBOX');
        ecrire_config('spiplistescleaner/option_delete_bounce', 'no');
        ecrire_config('spiplistescleaner/option_delete_row', '5poubelle');

        $curr = '1.2';
        echo _T('spiplistescleaner:installed', array('version' => $curr));
    }

    // Changement since the 1.1 version
    if ('1.2' > $curr) {

        // Set the default config
        ecrire_config('spiplistescleaner/option_delete_bounce', 'no');
        ecrire_config('spiplistescleaner/option_delete_row', '5poubelle');

        $curr = '1.2';
        echo _T('spiplistescleaner:installed', array('version' => $curr));
    }

    ecrire_meta('spiplistescleaner_base_version', $curr);
    ecrire_metas();

    return;
}

function spiplistescleaner_uninstall()
{
    include_spip('base/abstract_sql');

    # Delete tables
    sql_drop_table('spiplistescleaner_deleted_emails');

    // Delete the config
    effacer_config('spiplistescleaner');

    echo _T('spiplistescleaner:uninstalled', array('version' => $curr));

    # Delete settings
    effacer_meta('spiplistescleaner_base_version');
    ecrire_metas();

    return;
}
