<?php

/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return string
 */
function langonet_affiche_gauche($flux) {

	$exec = $flux["args"]["exec"];

	if ($exec == "langonet_verifier") {
		include_spip('inc/langonet_utils');

		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_log',  array('verification' => 'definition', 'fichiers_log' => langonet_lister_fichiers_log('definition')));
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_log',  array('verification' => 'utilisation', 'fichiers_log' => langonet_lister_fichiers_log('utilisation')));
	}
	if ($exec == "langonet_generer") {
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_lang',  array());
	}

	return $flux;
}

?>