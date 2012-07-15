<?php
// teste si l'objet est en mode edition directe ou non
function objets_edition_directe(){
	include_spip('inc/config');
	include_spip('inc/session');

	// Sie rien n'est choisit, tout est en édition directe	

	$objets=lire_config('edition_directe/objets');

	//Récupère les préférence de l'auteurs, pour éventuellement désactiver un objet
	$prefs=session_get('prefs');

	if(!is_array($prefs))$prefs=unserialize($prefs);	
	

	if(count($objets)<1){
		$objets=lister_objets($prefs);
		}
	else{
		$objets2=array();
		foreach($objets AS $objet){
		if($prefs['edition_directe'][$objet]!='inactive')$objets2[]=$objet;
			}	
		$objets=$objets2;
		}
	if(is_array($prefs['edition_directe'])){
		$objets_prefs=array();
		foreach($prefs['edition_directe'] AS $o=>$pref){
			if($pref!='inactive')$objets_prefs[]=$o;
			}
		$objets=array_merge($objets,$objets_prefs);
		}
	$pipeline= pipeline('edition_directe_controle',array(
		    'args'=>array(
			'objet'=>$objet
		    ), 
		    'data'=>$objets
		));
	return $objets;
}

	
// Liste les objets disponible pour l'édition directe
function lister_objets($prefs){
	include_spip('base/objets');		
	$liste_objets=lister_tables_objets_sql();

	
	$objets=array();
	foreach($liste_objets AS $o=>$valeur){
		if($valeur['editable'] AND $valeur['page'] AND $prefs['edition_directe'][$valeur['page']]!='inactive')$objets[]=$valeur['page'];
		}
	return $objets;	
	}

	
	
?>
