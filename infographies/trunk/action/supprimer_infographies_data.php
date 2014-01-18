<?php


if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Effacer un jeu de données
 *
 * @param null $id_infographies_data
 * @return void
 */
function action_supprimer_infographies_data_dist($id_infographies_data=null) {

	if (is_null($id_infographies_data)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_infographies_data = $securiser_action();
	}

	if (intval($id_infographies_data)){
		/**
		 * Suppression :
		 * -* du jeu de données
		 * -* des auteurs liés au jeu de données
		 * -* des liens du jeu de données
		 */
		sql_delete("spip_infographies_datas", "id_infographies_data=".intval($id_infographies_data));
		sql_delete("spip_auteurs_liens", "objet='infographies_data' AND id_objet=".intval($id_infographies_data));
		sql_delete("spip_infographies_datas_liens", "id_infographies_data=".intval($id_infographies_data));

		// invalider les caches marques de ce jeu de données
		include_spip('inc/invalideur');
		suivre_invalideur("id='infographies_data/$id_infographies_data'");
	}
}

?>
