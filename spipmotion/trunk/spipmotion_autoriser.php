<?php

if(!function_exists('autoriser_associerdocuments')){
/** 
 * Autoriser a associer des documents a un objet : 
 * il faut avoir le droit de modifier cet objet
 *  
 * @param $faire 
 * @param $type 
 * @param $id 
 * @param $qui 
 * @param $opt 
 * @return bool 
 */ 
function autoriser_associerdocuments($faire, $type, $id, $qui, $opt){
	return autoriser('modifier',$type,$id,$qui,$opt); 
} 
}

if(!function_exists('autoriser_dissocierdocuments')){
/** 
 * Autoriser a dissocier des documents a un objet : 
 * il faut avoir le droit de modifier cet objet
 *  
 * @param $faire 
 * @param $type 
 * @param $id 
 * @param $qui 
 * @param $opt 
 * @return bool 
 */ 
function autoriser_dissocierdocuments_dist($faire, $type, $id, $qui, $opt){
	if (intval($id)<0 AND $id==-$qui['id_auteur']){
		return true; 
	}
	return autoriser('modifier',$type,$id,$qui,$opt); 
}
}