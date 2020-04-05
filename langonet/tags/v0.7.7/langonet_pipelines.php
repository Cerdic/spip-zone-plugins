<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Creation du select des fichiers de langue
 *
 * @param string $sel_l
 * @return string
 */
function langonet_affiche_gauche($flux) {
	include_spip('inc/langonet_utils');

	$exec = $flux["args"]["exec"];
	if ($exec == "langonet_verifier") {
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_log', array('verification' => 'definition', 'fichiers_log' => langonet_lister_fichiers_log('definition')));
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_log', array('verification' => 'utilisation', 'fichiers_log' => langonet_lister_fichiers_log('utilisation')));
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_log', array('verification' => 'fonction_l', 'fichiers_log' => langonet_lister_fichiers_log('fonction_l')));
	}
	if ($exec == "langonet_generer") {
		$flux["data"] .= recuperer_fond('prive/navigation/bloc_fichiers_lang', array('fichiers_lang' => langonet_lister_fichiers_lang()));
	}

	return $flux;
}

?>