<?php

function action_mesfavoris_delfavorite_dist() {
//Only if logged
if($GLOBALS['auteur_session']) {
	$id_auth = $GLOBALS['auteur_session']['id_auteur'];
	$id_article=$_GET["id_article"];
	sql_delete('spip_favtextes',"id_auth='".$id_auth."' AND id_texte='".$id_article."'");
}

}


?>