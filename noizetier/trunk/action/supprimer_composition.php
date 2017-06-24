<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Cette action permet à l'utilisateur de supprimer de sa base de données, de façon sécurisée,
 * une composition virtuelle donnée.
 *
 * Cette action est réservée aux webmestres. Elle nécessite en argument l'identifiant de la page.
 *
 * @uses supprimer_noisettes()
 *
 * @return void
 */
function action_supprimer_composition_dist(){

	// Securisation et autorisation.
	// L'argument attendu est le type d'objet à activer
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$page = $securiser_action();

	// Verification des autorisations
	if (!autoriser('supprimercomposition', 'noizetier', 0, '', array('page' => $page))) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	if ($page) {
		sql_delete('spip_noizetier_pages', array('page=' . sql_quote($page)));
		// TODO : ne faudrait-il pas supprimer les noisettes utilisées dans la page ?
	}
}
