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

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_FORMS',(_DIR_PLUGINS.end($p)));
include_spip('base/forms');

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

function Forms_generer_url_sondage($id_form) {
	return generer_url_public("sondage","id_form=$id_form",true);
}

if ($GLOBALS['ajout_reponse'] == 'oui' && $GLOBALS['ajout_cookie_form'] == 'oui')
	Forms_poser_cookie_sondage();

// test si un cookie sondage a ete pose
foreach($_COOKIE as $cookie=>$value){
	if (preg_match(",spip_cookie_form_([0-9]+),",$cookie)){
		$idf = preg_replace(",spip_cookie_form_([0-9]+),","\\1",$cookie);
		$res = spip_query("SELECT id_article FROM spip_forms_articles WHERE id_form=$idf");
		while($row=spip_fetch_array($res)){
			$ida = $row['id_article'];
			if (
						(isset($GLOBALS['article'])&&($GLOBALS['article']==$ida))
					||(isset($GLOBALS['id_article'])&&($GLOBALS['id_article']==$ida))
					||(isset($GLOBALS['contexte_inclus']['id_article'])&&($GLOBALS['contexte_inclus']['id_article']==$ida)) ){
					// un article qui utilise le form va etre rendu
					// il faut utiliser le marquer cache pour ne pas polluer la page commune
					$GLOBALS['marqueur'].=":sondage $idf";
					break;
				}
		}
	}
}
?>