<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2010 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

function action_supprimer_favori_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_favori = $securiser_action();

	if ($id_favori = intval($id_favori)){
		$row = sql_fetsel('objet,id_objet,id_auteur','spip_favoris','id_favori='.intval($id_favori));
		sql_delete("spip_favoris","id_favori=".intval($id_favori));
		include_spip('inc/invalideur');
		suivre_invalideur("favori/".$row['objet']."/".$row['id_objet']);
		suivre_invalideur("favori/auteur/".$row['id_auteur']);
	}
}

?>