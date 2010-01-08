<?php
/*
 * Plugin mesfavoris
 * (c) 2009-2010 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

function action_ajouter_favori_dist(){
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();

	$arg = explode("-",$arg);
	$objet = $arg[0];
	$id_objet = $arg[1];
	if (count($arg)>2)
		$id_auteur = $arg[2];
	else
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];

	if ($id_auteur
		AND $id_objet = intval($id_objet)
		AND preg_match(",^\w+$,",$objet)){

		sql_insertq("spip_favoris",array('id_auteur'=>$id_auteur,'id_objet'=>$id_objet,'objet'=>$objet));
		suivre_invalideur("favori/$objet/$id_objet");
		suivre_invalideur("favori/auteur/$id_auteur");
		
	}
	else
		spip_log("erreur ajouter favori $id_objet-$objet-$id_auteur");

	if ($id_favori = intval($id_favori)){
		$row = sql_fetsel('objet,id_objet,id_auteur','spip_favoris','id_favori='.intval($id_favori));
		sql_delete("spip_favoris","id_favori=".intval($id_favori));
		include_spip('inc/invalideur');
	}
}

?>