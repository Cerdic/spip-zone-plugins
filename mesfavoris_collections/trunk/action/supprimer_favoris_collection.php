<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Supprimer une collection de favoris
 *
 * Optionnellement, si la constante _MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS est définie,
 * on conserve les favoris orphelins (ceux qui ne se trouveront plus dans aucune collection).
 *
 * @example
 * #URL_ACTION_AUTEUR{supprimer_favoris_collection, #ID_FAVORIS_COLLECTION, #SELF}
 *
 * @param int|string|null $id_favoris_collection
 *     Identifiant de la collection à supprimer
 */
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
		// Si besoin, avant de procéder, on sort de la collection les favoris
		// qui se retrouveront orphelins, ainsi ils ne seront pas effacés.
		// [FIXME] on devrait pouvoir trouver les (futurs) orphelins en une seule requête.
		if (defined('_MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS')
			and _MESFAVORIS_COLLECTIONS_CONSERVER_ORPHELINS === true
		) {
			if ($favoris = sql_allfetsel('*', 'spip_favoris', array('id_favoris_collection' => $id_favoris_collection))){
				foreach ($favoris as $favori){
					if (!sql_countsel(
						'spip_favoris',
						array(
							'objet = ' . sql_quote($favori['objet']),
							'id_objet = ' . intval($favori['id_objet']),
							'id_favoris_collection != ' . intval($id_favoris_collection),
							//'id_auteur = ' . sql_quote($favori['id_auteur']), // nécessaire ou pas ?
						)
					)){
						sql_updateq(
							'spip_favoris',
							array(
								'id_favoris_collection' => 0,
							),
							array(
								'id_favori = ' . intval($favori['id_favori']),
							)
						);
					}
				}
			}
		}
		mesfavoris_supprimer(array('id_favoris_collection' => $id_favoris_collection));
		
		// Puis on supprime la collection elle-même
		sql_delete('spip_favoris_collections', 'id_favoris_collection = '.$id_favoris_collection);
		
		include_spip('inc/invalideur');
		suivre_invalideur(true);
	}
}
