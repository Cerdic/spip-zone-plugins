<?php

/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));
include_ecrire('inc_forms_base.php');

// Code a rapatrier dans inc-public et inc_forms
// (NB : le reglage du cookie doit se faire avant l'envoi de tout HTML au client)
function Forms_poser_cookie_sondage() {
	if ($id_form = intval($_POST['id_form'])) {
		$nom_cookie = 'spip_cookie_form_'.$id_form;
		// Ne generer un nouveau cookie que s'il n'existe pas deja
		if (!$cookie = $_COOKIE[$nom_cookie]) {
			include_ecrire("inc_session");
			$cookie = creer_uniqid();
		}
		$GLOBALS['cookie_form'] = $cookie; // pour utilisation dans inc_forms...
		// Expiration dans 30 jours
		setcookie($nom_cookie, $cookie, time() + 30 * 24 * 3600);
	}
}

if ($GLOBALS['ajout_reponse'] == 'oui' && $GLOBALS['ajout_cookie_form'] == 'oui')
	Forms_poser_cookie_sondage();

?>