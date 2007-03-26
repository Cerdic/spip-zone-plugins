<?php
/*****
 * Initialisation de l'authent OpenID
 ****/

function init_auth_openid() {

	$cwd = getcwd();
	chdir(dirname(dirname(__FILE__)));
	require_once "Auth/OpenID/Consumer.php";
	require_once "Auth/OpenID/FileStore.php";
	chdir($cwd);

	/****
	 * Répertoire temporaire où auth_openid stocke ses données
	 * afin de suivre les sessions.
	 ****/

	$store = new Auth_OpenID_FileStore(sous_repertoire(_DIR_TMP, 'auth_openid'));

	/**
	 * Create a consumer object using the store object created earlier.
	 */
	return new Auth_OpenID_Consumer($store);
}

global $consumer;

$consumer = init_auth_openid();



?>
