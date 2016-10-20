<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_supprimer_favoris_collection_dist($id_favoris_collection=null) {
	if (is_null($id_favoris_collection)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_favoris_collection = $securiser_action();
	}
	
	include_spip('inc/mesfavoris');
	include_spip('inc/autoriser');
	
	if(
		$id_favoris_collection = intval($id_favoris_collection)
		and $id_favoris_collection > 0
		and autoriser('supprimer', 'favoris_collection', $id_favoris_collection)
	) {
		// D'abord on supprime tout l'intérieur
		mesfavoris_supprimer(array('id_favoris_collection' => $id_favoris_collection));
		
		// Puis on supprime la collection elle-même
		sql_delete('favoris_collection', 'id_favoris_collection = '.$id_favoris_collection);
	}
}
