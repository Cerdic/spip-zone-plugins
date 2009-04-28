<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	
	
	define('_DIR_PLUGIN_FORMULAIRES', (_DIR_PLUGINS.end($p)));
	define('_NOM_PLUGIN_FORMULAIRES', (end($p)));
	define('_NB_HEURES_VALIDITE_COOKIE_FORMULAIRES', 4);
	define('_DUREE_VALIDITE_COOKIE_FORMULAIRES', (60 * 60 * _NB_HEURES_VALIDITE_COOKIE_FORMULAIRES)); // 4 heures
	define('_REGEXP_EMAIL', "^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$");
	define('_DIR_FORMULAIRES', _DIR_IMG.'formulaires/');


	// pose du cookie de test pour savoir si l'internaute accepte les cookies
	include_spip('inc/cookie');
	spip_setcookie('spip_formulaires_test_cookie', 'test', time() + 60 * 60 * 24 * 90); // 90 jours

	
	// on prolonge les cookies tant que l'internaute est connecté
	if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) AND !empty($_COOKIE['spip_formulaires_id_applicant'])) {
		include_spip('formulaires_fonctions');
		$id_applicant = formulaires_identifier_applicant();
		$applicant = new applicant($id_applicant);
		if ($applicant->existe) {
			$applicant->poser_cookies();
		}
	}


?>