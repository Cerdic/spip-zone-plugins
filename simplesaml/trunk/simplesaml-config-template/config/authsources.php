<?php

/**
 * The configuration of SimpleSAMLphp
 *
 * Voir dans la lib : simplesamlphp/config-templates/authsources.php
 */

// Connexion utilisée par défaut
$config = array(
    // This is a authentication source which handles admin authentication.
    'admin' => array(
        'core:AdminPassword',
    ),

    // Authentification par défaut
    'default-sp' => array(
        'saml:SP',

        // La même URL doit être définie dans ../metadata/saml20-idp-remote.php
        'idp' => "https://connexion-domaine.tld/idp/saml2/metadata",

        // Force persistent NameID
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent',
    ),
);
