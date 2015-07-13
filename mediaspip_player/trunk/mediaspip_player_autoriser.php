<?php
/**
 * MediaSPIP player
 * Lecteur multimédia HTML5 pour MediaSPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2010-2015 - Distribué sous licence GNU/GPL
 * 
 * Fichier des autorisations
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Surcharges des autorisations du plugin medias 
 * depuis http://zone.spip.org/trac/spip-zone/changeset/89155/_core_#file3
 * On doit lier des docs à d'autres docs
 */
include_spip('plugins/installer');
if(spip_version_compare($GLOBALS['spip_version_branche'], '3.0.20', '<')){
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
		if (intval($id)<0 AND $id==-$qui['id_auteur']){
			return true;
		}
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
	function autoriser_dissocierdocuments($faire, $type, $id, $qui, $opt){
		if (intval($id)<0 AND $id==-$qui['id_auteur']){
			return true; 
		}
		return autoriser('modifier',$type,$id,$qui,$opt); 
	}
	}
}
?>