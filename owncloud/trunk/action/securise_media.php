<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('owncloud_fonctions');

/**
 * Sécurisé les médias à l'ouverture dans la liste
 * 
 * @param string $arg l'URL cible
 * @return string
 */
function action_securise_media_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$securise = securise_identifiants($arg, true);

	if (_request('type')) {
		header('Content-Type: ' . _request('type'));
		$f = basename($securise);
		header("Content-Disposition: attachment; filename=\"$f\";");
		readfile($securise);
	} else {
		include_spip('inc/filtres_images_mini');
		$balise_img = charger_filtre('balise_img');
		$texte = image_reduire($balise_img($securise), 800, 800);
		echo $texte;
	}
}
