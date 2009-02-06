<?php
include_spip('inc/utils');
function jeu_modifier_statut($id,$statut){
	if(function_exists('sql_update'))
		sql_update("spip_jeux", array('statut'=>$statut), "id_jeu=$id");
	else 
		spip_query('UPDATE spip_jeux SET statut="'.$statut.'" WHERE id_jeu='.$id);
	spip_log('modifier statut jeu'.$id.', '.$statut);
}
?>