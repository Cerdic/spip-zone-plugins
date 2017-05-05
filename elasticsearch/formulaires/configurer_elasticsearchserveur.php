<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_configurer_elasticsearchserveur_verifier() {
	
	$erreurs = array();
	
	$serveur = _request('url_serveur');
	include_spip('phpcurl_fonctions');
	$serveur = phpcurl_get($serveur);
	$serveur = json_decode($serveur, true);
	
	// pour spip_version_compare
	include_spip('plugins/installer');
	
	if(!$serveur['version']['number'])
		$erreurs['message_erreur'] = 'Serveur Elasticsearch introuvable';
	elseif(spip_version_compare($serveur['version']['number'], '2.3.0', '<') || spip_version_compare($serveur['version']['number'], '5.*', '>'))
		$erreurs['message_erreur'] = 'Version du serveur Elasticsearch non compatible';
	
	return $erreurs;
}

function formulaires_configurer_elasticsearchserveur_traiter() {
	include_spip('inc/config');
	ecrire_config('elasticsearch_config/url_serveur', _request('url_serveur'));
	$retours = array('message_ok'=>'');
	$retours['redirect'] = self();
	return $retours;
}