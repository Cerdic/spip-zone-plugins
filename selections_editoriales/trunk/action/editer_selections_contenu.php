<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function selections_contenu_supprimer($id_selections_contenu) {
	$ok = true;
	$id_selections_contenu = intval($id_selections_contenu);
	
	if ($id_selections_contenu > 0) {
		$ok = sql_delete(
			'spip_selections_contenus',
			'id_selections_contenu = '.$id_selections_contenu
		);
	}
	
	return $ok;
}
