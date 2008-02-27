<?php
include_spip('inc/utils');
function jeu_modifier_statut($id,$statut){
	
	spip_query('UPDATE spip_jeux SET statut="'.$statut.'" WHERE id_jeu='.$id);
	spip_log('modifier statut jeu'.$id.','.$statut);
	}
?>