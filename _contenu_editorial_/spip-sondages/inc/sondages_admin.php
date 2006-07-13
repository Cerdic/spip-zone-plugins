<?php


	/**
	 * SPIP-Sondages : plugin de gestion de sondages
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('inc/sondages_fonctions');
	include_spip('inc/plugin');
	global $pas;
	$pas = 20;


	/**
	 * sondages_verifier_base
	 *
	 * @return true
	 * @author Pierre Basson
	 **/
	function sondages_verifier_base() {
		$info_plugin_sondages = plugin_get_infos(_NOM_PLUGIN_SONDAGES);
		$version_plugin = $info_plugin_sondages['version'];
		if (!isset($GLOBALS['meta']['spip_sondages_version'])) {
			creer_base();
#			ecrire_meta('spip_sondages_version', $version_plugin);
			ecrire_metas();
		} else {
			$version_base = $GLOBALS['meta']['spip_sondages_version'];
/*			if ($version_base < 1.1) {
				creer_base();
				ecrire_meta('spip_sondages_version', $version_base = 1.1);
				ecrire_metas();
			}
*/		}
		return true;
	}


	/**
	 * sondages_verifier_droits
	 *
	 * redirige vers l'accueil si l'auteur n'est pas un admin
	 *
	 * @author Pierre Basson
	 **/
	function sondages_verifier_droits() {
		if ($GLOBALS['connect_statut'] != "0minirezo")
			sondages_rediriger_javascript(generer_url_ecrire('accueil')); 
	}
	
	
	/**
	 * sondages_rediriger_javascript
	 *
	 * redirige vers une url
	 *
	 * @param string url
	 * @author Pierre Basson
	 **/
	function sondages_rediriger_javascript($url) {
		echo '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
		exit();
	}


?>