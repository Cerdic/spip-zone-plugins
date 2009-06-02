<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function marquepage_revision($id_forum, $colonnes){
	include_spip('actions/crayons_store');
	
	// D'abord on enlève les tags des trucs à mettre à jour
	$colonnes = array_diff_key($colonnes, array('tags' => 'prout'));
	
	// Ensuite on met la table à jour s'il faut
	if(count($colonnes) > 0)
		crayons_update($id_forum, $colonnes, 'forum');
}

?>
