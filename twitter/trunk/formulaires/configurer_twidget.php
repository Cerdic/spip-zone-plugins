<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/filtres');


function formulaires_configurer_twidget_charger_dist() {
	$config = unserialize($GLOBALS['meta']['twidget']);

	$valeurs = array(
		'search' => "#spip",
		'interval' => "30000",
		'title' => "Du nouveau sur Twitter",
		'subject' => "SPIP",
		'width' => "250",
		'height' => "300",
		'shell_background' => "#8ec1da",
		'shell_color' => "#ffffff",
		'tweets_background' => "#ffffff",
		'tweets_color' => "#444444",
		'tweets_links' => "#1986b5",
		'rpp' => "4",
		'user' => "spip",
		'footer' => "Rejoignez nous",
	);

	foreach($valeurs as $k=>$v){
		if (isset($config[$k]))
			$valeurs[$k] = $config[$k];
	}

	// formulaire configurable ou non ?
	include_spip("inc/twitter");
	if (!twitter_verifier_config(true)){
		$valeurs['_info_config_erreur'] = _T('twitter:erreur_config_pour_widget');
	}

	return $valeurs;
}

/**
 */
function formulaires_configurer_twidget_traiter_dist() {

	$config = array();
	$valeurs = formulaires_configurer_twidget_charger_dist();

	include_spip('inc/meta');
	foreach ($valeurs as $k=>$v){
		if (!is_null(_request($k))) {
				$config[$k] = _request($k);
		}
	}
	ecrire_meta('twidget',serialize($config));

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);

}

?>
