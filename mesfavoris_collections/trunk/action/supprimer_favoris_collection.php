<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Supprimer une collection de favoris
 *
 * Optionnellement, on peut demander de conserver les favoris orphelins
 * (qui ne se trouvent plus dans aucune collection après cette action).
 *
 * @example
 * #URL_ACTION_AUTEUR{supprimer_favoris_collection, #ID_FAVORIS_COLLECTION, #SELF}
 * Idem, mais en conservant les orphelins :
 * #URL_ACTION_AUTEUR{supprimer_favoris_collection, #ID_FAVORIS_COLLECTION-orphelins, #SELF}
 *
 * @param string|null $arg
 *     paramètres séparés par des tirets : param1-param2-paramN
 *     id_favoris_collection : identifiant de la collection à supprimer
 *     orphelins             : pour indiquer de conserver les favoris orphelins
 */
function action_supprimer_favoris_collection_dist($arg=null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	list($id_favoris_collection, $garder_orphelins) = explode('-', $arg);
	
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
		if ($garder_orphelins) {
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
	}
}
