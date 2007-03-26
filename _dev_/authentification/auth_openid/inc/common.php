<?php
/*****
 * Initialisation de l'authent OpenID
 ****/

/****
 * TODO: changer le require_one en include_one et traiter les erreurs
 ****/

require_once "Auth/OpenID/Consumer.php";
require_once "Auth/OpenID/FileStore.php";

/****
 * Répertoire temporaire où auth_openid stocke ses données
 * afin de suivre les sessions.
 ****/
$store_path = (_DIR_TMP . 'auth_openid/');

$rep = sous_repertoire(_DIR_TMP, 'auth_openid' );

$store = new Auth_OpenID_FileStore($store_path);

/**
 * Create a consumer object using the store object created earlier.
 */
global $consumer;
 $consumer = new Auth_OpenID_Consumer($store);

?>
