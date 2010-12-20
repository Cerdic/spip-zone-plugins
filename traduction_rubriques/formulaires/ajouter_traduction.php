<?php	

function formulaires_ajouter_traduction_charger_dist($id_rubrique){

	$id_trad=sql_getfetsel('id_trad','spip_rubriques','id_rubrique='.$id_rubrique);
	
	if(!$id_trad)$id_trad=$id_rubrique;

	$valeurs=array(
		'id_rubrique'=>$id_rubrique,
		'id_trad'=>$id_trad,		
		);

	$valeurs['_hidden'] .='<input type="hidden" name="id_trad" value="'.$id_trad.'"/>';
	$valeurs['_hidden'] .='<input type="hidden" name="id_rubrique" value="'.$id_rubrique.'"/>';	
	return $valeurs;
	}
	
function formulaires_ajouter_traduction_traiter_dist(){

	$id_trad=_request('id_trad');
	$id_rubrique=explode('|',_request('rubrique_menu'));
	$id_rubrique=$id_rubrique[0];		
	sql_update('spip_rubrique',array('id_trad'=>$id_trad),'id_rubrique='.$id_rubrique);

	return $valeurs;
	}
	 
	 
?>