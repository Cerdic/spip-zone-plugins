<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function selection_supprimer($id_selection) {
	include_spip('action/editer_liens');
	$id_selection = intval($id_selection);
	
	if ($id_selection > 0) {
		$ok = sql_delete(
			'spip_selections',
			'id_selection = '.$id_selection
		);
		
		if ($ok) {
			objet_optimiser_liens(array('selection'=>'*'),'*');
		}
	}
	
	return $ok;
}
