<?php
/*****
 * Initialisation de l'authent OpenID
 ****/

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";

/****
 * Répertoire temporaire où auth_openid stocke ses données
 * afin de suivre les sessions.
 ****/
$store_path = (_DIR_TMP . '/auth_openid/');

if (!file_exists($store_path) &&
    !mkdir($store_path)) {
    print "Could not create the FileStore directory '$store_path'. ".
        " Please check the effective permissions.";
    exit(0);
}

$store = new Auth_OpenID_FileStore($store_path);

/**
 * Create a consumer object using the store object created earlier.
 */
global $consumer;
 $consumer = new Auth_OpenID_Consumer($store);

?>
