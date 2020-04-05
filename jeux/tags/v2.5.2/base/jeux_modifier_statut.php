<?php
include_spip('inc/utils');
function jeu_modifier_statut($id,$statut){
    sql_updateq("spip_jeux", array('statut'=>$statut), "id_jeu=$id");
	spip_log("modifier statut jeu$id, $statut",'jeux');
}
?>