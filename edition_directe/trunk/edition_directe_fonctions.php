<?php
// teste si l'objet est en mode edition directe ou non
function objets_edition_directe(){
	include_spip('inc/config');

	// Sie rien n'est choisit, tout est en édition directe	
	$objets=array();
	$objets=lire_config('edition_directe/objets');

	if(count($objets)<1){
		$objets=lister_objets();
	}

	$pipeline= pipeline('edition_directe_controle',array(
		    'args'=>array(
			'objet'=>$objet
		    ), 
		    'data'=>$objets
		));
	return $objets;
}

	

function lister_objets(){
	include_spip('base/objets');	
	$liste_objets=lister_tables_objets_sql();
	$objets=array();
	foreach($liste_objets AS $o=>$valeur){
		if($valeur['editable'] AND $valeur['page'])$objets[]=$valeur['page'];
		}
	return $objets;	
	}

	
	
?>
