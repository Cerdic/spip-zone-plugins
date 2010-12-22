<?php
function destination_traduction($lang,$id_trad){
	$id_trad_parent=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique="'.$id_trad.'"');
	if($id_trad_parent['id_trad']){
		$trads = sql_getfetsel('id_parent','spip_rubriques','id_trad='.$id_trad_parent['id_trad'].' AND lang="'.$lang.'"');		
		}
	elseif($id_trad_parent['id_parent']){
		$id_trad_parent2=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique="'.$id_trad_parent['id_parent'].'"');
		if($id_trad_parent2['id_trad']){
				$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_trad='.$id_trad_parent2['id_trad'].' AND lang="'.$lang.'"');
			}
		}
 	if(!$trads){
 	 	$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_parent=0 AND lang="'.$lang.'"');		
 		}
return $trads;
}	 
?>