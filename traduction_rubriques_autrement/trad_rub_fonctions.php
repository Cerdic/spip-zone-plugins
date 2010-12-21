<?php
function donnees_traduction($lang,$id_trad){

	$retour=array();
	$id_trad_parent=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique="'.$id_trad.'"');
	if($id_trad_parent['id_trad']){
		$trads = sql_fetsel('id_parent,id_secteur','spip_rubriques','id_trad='.$id_trad_parent['id_trad'].' AND lang="'.$lang.'"');
		$retour['id_parent']=$trads['id_parent'];
		//$retour['id_secteur']=$trads['id_secteur'];				
		}
	elseif($id_trad_parent['id_parent']){
		$id_trad_parent2=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique="'.$id_trad_parent['id_parent'].'"');
		//echo $id_trad_parent2['id_trad'];
			if($id_trad_parent2['id_trad']){//echo $id_trad_parent2['id_trad'];
				$trads = sql_fetsel('id_rubrique,id_secteur','spip_rubriques','id_trad='.$id_trad_parent2['id_trad'].' AND lang="'.$lang.'"');
				$retour['id_parent']=$trads['id_rubrique'];
				//$retour['id_secteur']=$trads['id_secteur'];		
				}
		}

 	if(!$trads){
 	 	$trads = sql_fetsel('id_rubrique,id_secteur','spip_rubriques','id_parent=0 AND lang="'.$lang.'"');
 	 	$retour['id_parent']=$trads['id_rubrique'];
		//$retour['id_secteur']=$trads['id_secteur'];			
 		}
 	


	return $retour['id_parent'];

}	 
?>