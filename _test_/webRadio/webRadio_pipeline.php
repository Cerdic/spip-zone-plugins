<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_WEBRADIO',(_DIR_PLUGINS.end($p)));

// ajoute un bouton directement dans la barre principale
function webRadio_ajouter_boutons($boutons_admin) {
	// si on est admin ou admin restreint
	if ($GLOBALS['connect_statut'] == "0minirezo") {
	
	$boutons_admin['webradio'] = new Bouton(
			"../"._DIR_PLUGIN_WEBRADIO."img_pack/radio.png",  // icone
			_T('webradio:webradio_radio_titre'), // titre
			generer_url_ecrire('webradio_radio'), // l'adresse du exec
			NULL
			);
	}
	return $boutons_admin;
}

// insertion dans l'entete du script popup (fenetre popup)
function webRadio_insert_head($flux) {
	$javascript = '<script type="text/javascript" src="'._DIR_PLUGIN_WEBRADIO.'scripts/popup.js"></script>';
	return $flux . $javascript;
}

?>