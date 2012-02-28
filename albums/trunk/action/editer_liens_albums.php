<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// EDITION DES LIENS
function action_editer_liens_albums_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($action,$id_album, $objet, $id_objet) = explode('/',$arg);
	
	include_spip('inc/autoriser');
	if (intval($id_album) AND autoriser('modifier','album',$id_album,null,null)){
		//include_spip('action/editer_album');
		if ($action == 'lier')
			lier_album($id_album, $objet, $id_objet);
		elseif ($action == 'delier')
			delier_album($id_album, $objet, $id_objet);
	}
}

// LIER
function lier_album($id_album, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_album
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	AND !sql_getfetsel("id_album", "spip_albums_liens", "id_album=$id_album AND id_objet=$id_objet AND objet=".sql_quote($objet))
	) {
		sql_insertq('spip_albums_liens',
			array('id_album' => $id_album,
				'id_objet' => $id_objet,
				'objet' => $objet));
	}
}

// DELIER
function delier_album($id_album, $objet, $id_objet){
	//$objet = objet_type($objet);
	if ($id_objet AND $id_album
	AND preg_match('/^[a-z0-9_]+$/i', $objet) # securite
	) {
		sql_delete('spip_albums_liens', "id_album=$id_album AND id_objet=$id_objet AND objet=". sql_quote($objet));
	}
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_album/$id_album'");
}

// SUPPRIMER
function supprimer_album($id_album){
	if (intval($id_album)){
		sql_delete("spip_albums_liens", "id_album=".intval($id_album));
		sql_delete("spip_album", "id_album=".intval($id_album));
	}
	$id_album = 0;
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_album/$id_album'");
	return $id_album;
}

?>
