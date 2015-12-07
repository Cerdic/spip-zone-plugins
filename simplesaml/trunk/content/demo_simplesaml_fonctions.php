<?php
/**
 * Fonciton de démo pour Authentification SAML
 *
 * @plugin     Authentification SAML
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\SimpleSaml\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function simplesaml_testeur() {
	$res = array();
	if (!class_exists('SimpleSAML_Auth_Simple')) {
		$res[] = "Classes SimpleSAML introuvables";
		return $res;
	}

	$simplesaml = new SimpleSAML_Auth_Simple('default-sp');
	if ($simplesaml->isAuthenticated()) {
		$res[] = "Je suis authentifié sur le SSO";
		$attributs = $simplesaml->getAttributes();
		$res[] = "Voici mes attributs : ";
		$res[] = $attributs;
	} else {
		$res[] = "Je NE suis PAS authentifié sur le SSO";
	}

	return $res;
}
