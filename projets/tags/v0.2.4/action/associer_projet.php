<?php

function action_associer_projets_dist($id_projet,$objet,$id_objet,$type=''){
	$lien_projet = sql_getfetsel("id_projet","spip_projets_liens","objet=".sql_quote($objet)." AND id_objet=".intval($id_objet));
	if(intval($id_projet)){
		if($lien_projet && ($lien_projet!= $id_projet)){
			sql_updateq("spip_projets_liens",array('id_projet'=>$id_projet),"objet=".sql_quote($objet)." AND id_objet=".sql_quote($id_objet)." AND type=".sql_quote($type));
		}else if($id_projet && !$lien_projet){
			sql_insertq("spip_projets_liens",array('id_projet'=>$id_projet,'id_objet'=>$id_objet,'objet'=>$objet,'type'=>$type));
		}
	}
	else if($lien_projet){
		sql_delete("spip_projets_liens","objet=".sql_quote($objet)." AND id_objet=".sql_quote($id_objet));
	}
}
?>