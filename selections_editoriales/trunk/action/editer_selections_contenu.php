<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function selections_contenu_supprimer($id_selections_contenu) {
	$ok = true;
	$id_selections_contenu = intval($id_selections_contenu);

	if ($id_selections_contenu > 0) {
		// On récupère le rang et la sélection du truc à supprimer
		$contenu = sql_fetsel(
			'rang, id_selection',
			'spip_selections_contenus',
			'id_selections_contenu = '.$id_selections_contenu
		);

		// On supprime
		$ok = sql_delete(
			'spip_selections_contenus',
			'id_selections_contenu = '.$id_selections_contenu
		);

		// Si c'est bon, il faut décaler le rang de tout ce qui est après
		if ($ok) {
			sql_update(
				'spip_selections_contenus',
				array('rang' => 'rang - 1'),
				array(
					'id_selection = '.$contenu['id_selection'],
					'rang > '.$contenu['rang'],
				)
			);
		}
	}

	return $ok;
}
