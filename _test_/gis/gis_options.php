<?php
/*
 * Spip Gis plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */





// Ajout de la spécialisation de gis pour une branche

///////////////////////////////////////
include_spip('base/db_mysql');
include_spip('inc/utils');


if(lire_config('gis/specialisation')){
	$id_rubrique=_request('id_rubrique');
	$id_article=_request('id_article');
			if(!$id_rubrique)
			{	if($id_article){
					$temp=spip_fetch_array(spip_query("select id_rubrique from spip_articles where id_article=".$id_article));
					if($temp[0]['id_rubrique']) $id_rubrique=$temp[0]['id_rubrique'];
				}
			}
			$GLOBALS['id_rubrique']=$id_rubrique;
			$GLOBALS['rubrique_carto']=explode(",",lire_config('gis/rubrique_carto'));
			
			if(lire_config('gis/sous_rubrique')) $GLOBALS['rubrique_carto']=gis_get_ss_rubriques($GLOBALS['rubrique_carto']);
			
			$GLOBALS['rubrique_carto']=array_unique($GLOBALS['rubrique_carto']);
}	
	 
		/////////////////////////////////////////////////////////
		
		 
function gis_get_ss_rubriques($rubriques_parent){
	if(is_array($rubriques_parent))
	foreach ($rubriques_parent as $r) {
		$tab_ss_rubriques[]=$r;


		$temp=spip_fetch_array(spip_query("select id_rubrique from spip_rubriques where id_parent=".$r));
		if($temp)
			foreach ($temp as $t)
			{
				$tab_ss_rubriques[]=$t['id_rubrique'];
				$tab_ss_rubriques=array_merge($tab_ss_rubriques,get_ss_rubriques(array($t['id_rubrique'])));
			}
	}


return array_merge($rubriques_parent,$tab_ss_rubriques);
}


// Ajout de la spécialisation de gis pour une branche
function gis_is_not_rubrique(){
	if(!is_array($GLOBALS['rubrique_carto'])) return false; // dans le cas ou on a pas encore fait la config
	if(!in_array($GLOBALS['id_rubrique'],$GLOBALS['rubrique_carto'])) return false;
	
	return true;
}
?>