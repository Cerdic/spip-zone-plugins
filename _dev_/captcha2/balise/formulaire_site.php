<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_CAPTCHA2',(_DIR_PLUGINS.end($p)));

//
// surcharge des balises FORMULAIRE_SITE pour integrer le captcha
// 

// Le contexte indique dans quelle rubrique le visiteur peut proposer le site


// http://doc.spip.org/@balise_FORMULAIRE_SITE
function balise_FORMULAIRE_SITE ($p) {
  return calculer_balise_dynamique($p,'FORMULAIRE_SITE', array('id_rubrique'));
}

// http://doc.spip.org/@balise_FORMULAIRE_SITE_stat
function balise_FORMULAIRE_SITE_stat($args, $filtres) {

	// Pas d'id_rubrique ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SITE',
					'motif' => 'RUBRIQUES')), '');

	// Verifier que les visisteurs sont autorises a proposer un site

	return (($GLOBALS['meta']["proposer_sites"] != 2) ? '' : $args);
}

// http://doc.spip.org/@balise_FORMULAIRE_SITE_dyn
function balise_FORMULAIRE_SITE_dyn($id_rubrique) {
  session_start();

	if ($nom = _request('nom_site')) {
	  // Captcha
	  if (_request('captcha2')!=$_SESSION['captcha2']) 
	    $message_erreur = _T("captcha2:erreur_captcha");

		// Tester le nom du site
		if (strlen ($nom) < 2)
			$message_erreur = _T('form_prop_indiquer_nom_site');

		// Tester l'URL du site
		include_spip('inc/sites');
		$url = _request('url_site');
		if (!recuperer_page($url))
			$message_erreur = _T('form_pet_url_invalide');

		$desc = _request('description_site');

		// Integrer a la base de donnees
		if (!$message_erreur) {
			spip_abstract_insert('spip_syndic', "(nom_site, url_site, id_rubrique, descriptif, date, date_syndic, statut, syndication)", "(" . _q($nom) . ", " . _q($url) . ", " . intval($id_rubrique) .", " . _q($desc) . ", NOW(), NOW(), 'prop', 'non')");
			$message_ok = _T('form_prop_enregistre');
		}
	}
	

	return array('formulaires/site', $GLOBALS['delais'],
		array(
			'self' => str_replace('&amp;', '&', self()),
			'message_ok' => $message_ok,
			'message_erreur' => $message_erreur,
			'nom_site' => $nom,
			'url_site' => $url ? $url : 'http://',
			'descriptif_site' => $desc,			
			'url_plugin' => _DIR_PLUGIN_CAPTCHA2
		)
	);

}

?>
