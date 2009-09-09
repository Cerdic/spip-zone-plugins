<?php

function action_mesfavoris_addfavorite_dist() {
//Only if logged

if($GLOBALS['auteur_session']) {
	// If not already recorded
	$id_auth = $GLOBALS['auteur_session']['id_auteur'];
	$id_article=$_GET["id_article"];
	$nb = sql_countsel('spip_favtextes',"(id_texte = '".$id_article."') AND (id_auth = '".$id_auth."')");
	$total = sql_countsel('spip_favtextes',"(id_auth = '".$id_auth."')");
	if ($nb == 0 && $total < 100) {
		sql_insertq('spip_favtextes',array('id_auth' => $id_auth, 'id_texte' => $id_article));
	}
}

}


?>