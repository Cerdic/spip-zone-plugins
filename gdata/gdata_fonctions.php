<?php


// initier la connexion a google documents
function gdocs_init() {
	static $client;

	if (isset($client)) return $client;

	# ces valeurs sont a definir dans mes_options.php
	# todo : les passer en CFG

	defined('GDATA_USER') or define('GDATA_USER', 'recifs@gmail.com');
	defined('GDATA_PASS') or define('GDATA_PASS', 'totoro99');
	defined('GDATA_USER') or define('GDATA_USER', 'user@gmail.com');
	defined('GDATA_PASS') or define('GDATA_PASS', 'xxxxx');

	require_once 'Zend/Loader.php';
	Zend_Loader::loadClass('Zend_Gdata');
	Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	Zend_Loader::loadClass('Zend_Http_Client');
	Zend_Loader::loadClass('Zend_Gdata_Query');
	Zend_Loader::loadClass('Zend_Gdata_Feed');
	Zend_Loader::loadClass('Zend_Gdata_Docs');

	return $client = Zend_Gdata_ClientLogin::getHttpClient(
		GDATA_USER, GDATA_PASS, Zend_Gdata_Docs::AUTH_SERVICE_NAME
	);
}

// choper un document a partir de son url
function gdocs_contenu($url, $format='html') {
	$client = gdocs_init();
	$client->setUri($url);# . '&exportFormat='.$format);
	$zou  = $client->request();
	return $zou->getBody();
}



?>