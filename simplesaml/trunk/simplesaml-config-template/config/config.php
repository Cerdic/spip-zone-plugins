<?php

/**
 * The configuration of SimpleSAMLphp
 *
 * Voir dans la lib : simplesamlphp/config-templates/config.php
 */

// Note : __DIR__ prend en compte les liens symboliques
// Il ne faut donc pas spécialement lui faire confiance pour retrouver tmp/log 
$root_config = $_SERVER["SIMPLESAMLPHP_CONFIG_DIR"];
$root_racine = $_SERVER["DOCUMENT_ROOT"];

$config = array(

    #'loggingdir' => $root_racine . '/tmp/log',
    #'logging.handler' => 'file',

    #'store.type'         => 'sql', // was 'phpsession'
    #'store.sql.dsn'      => "sqlite:$root_racine/config/simplesaml.sqlite",

    'debug' => false,
    'showerrors' => true,
    'errorreporting' => true,
    'debug.validatexml' => false,

    'technicalcontact_name' => 'Administrator',
    'technicalcontact_email' => 'na@example.org',

    /**
     * This password must be kept secret, and modified from the default value 123.
     * This password will give access to the installation page of SimpleSAMLphp with
     * metadata listing and diagnostics pages.
     * You can also put a hash here; run "bin/pwgen.php" to generate one.
     */
    'auth.adminpassword' => '123',
    'admin.protectindexpage' => false,
    'admin.protectmetadata' => false,

    /**
     * This is a secret salt used by SimpleSAMLphp when it needs to generate a secure hash
     * of a value. It must be changed from its default value to a secret value. The value of
     * 'secretsalt' can be any valid string of any length.
     *
     * A possible way to generate a random salt is by running the following command from a unix shell:
     * tr -c -d '0123456789abcdefghijklmnopqrstuvwxyz' </dev/urandom | dd bs=32 count=1 2>/dev/null;echo
     */
    'secretsalt' => 'defaultsecretsalt',

    'metadata.sources' => array(
        array(
            'type' => 'flatfile',
            // les metadata dans ce plugin aussi
            'directory' => __DIR__ . '/../metadata/'
        ),
    ),
);
