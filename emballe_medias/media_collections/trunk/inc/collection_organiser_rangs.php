<?php
/**
 * Plugin Collections (ou albums)
 * (c) 2013 kent1
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction de réorganisation d'une collection
 * 
 * À appeler lorsqu'un media est supprimé d'une collection pour éviter des trous dans les rangs.
 * 
 * @param int $id_collection
 * 		Identifiant numérique de la collection à réoganiser
 */
function inc_collection_organiser_rangs_dist($id_collection) {
	$rang = 1;
	$medias = sql_select('*','spip_collections_liens','id_collection='.intval($id_collection),'','rang ASC');
	while($media = sql_fetch($medias)){
		if($media['rang'] != $rang){
			$test = sql_updateq('spip_collections_liens',array('rang' => $rang),'id_collection = '.intval($media['id_collection']).' AND rang='.intval($media['rang']));
		}
		$rang++;
	}
}



?>