<?php
function rubrique_traduction($lang,$id_rubrique){

	$id_trad_parent=sql_getfetsel('id_trad','spip_rubriques','id_rubrique="'.$id_rubrique.'"');
	
	if($id_trad_parent){
		$trad = sql_getfetsel('id_rubrique','spip_rubriques','id_trad='.$id_trad_parent.' AND lang="'.$lang.'"');
		}
 	else{
 	 	$trad = sql_getfetsel('id_rubrique,id_secteur','spip_rubriques','id_parent=0 AND lang="'.$lang.'"');	
 		}

	return $trad;
}
?>