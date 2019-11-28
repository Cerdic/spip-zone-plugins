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
		// On récupère la liste des blocs ayant des noisettes
		include_spip('inc/noizetier_page');
		$blocs = page_noizetier_compter_noisettes($page);

		// Suppression des noisettes concernées en utilisant l'API de vidage d'un conteneur, le conteneur étant
		// chaque bloc de la composition virtuelle.
		if ($blocs) {
			include_spip('inc/ncore_conteneur');
			include_spip('inc/noizetier_conteneur');
			foreach (array_keys($blocs) as $_bloc) {
				// On calcule le conteneur sous sa forme identifiant chaine.
				$id_conteneur = conteneur_noizetier_composer($page, $_bloc);
				conteneur_vider('noizetier', $id_conteneur);
			}
		}

		// On supprime la composition elle-même.
		sql_delete('spip_noizetier_pages', array('page=' . sql_quote($page)));
	}
}
