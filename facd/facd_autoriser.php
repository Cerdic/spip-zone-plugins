<?php
/**
 * Fichier des autorisations spécifique au plugin
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclarer l'utilisation du pipeline
 * Cela évite de recalculer les pipeline tout le temps
 */
function facd_autoriser(){}

/**
 * Fonction d'autorisation de relance de conversion en erreur
 * Seules les personnes suivantes peuvent relancer l'encodage :
 * -* Les personnes qui ont mis en ligne le document (id_auteur dans spip_facd_conversions)
 * -* Les personnes autorisées à configurer le site
 * 
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 */
function autoriser_relancerconversion_facd_dist($faire, $type, $id, $qui, $opt){
	$id_auteur = sql_getfetsel('id_auteur','spip_facd_conversions','id_facd_conversion='.intval($id));
	return ($qui['id_auteur'] == $id_auteur) OR autoriser('configurer','','',$qui,$opt);
}

/**
 * Surcharges des autorisations du plugin medias 
 * depuis http://zone.spip.org/trac/spip-zone/changeset/89155/_core_#file3
 * On doit lier des docs à d'autres docs
 */
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
function autoriser_dissocierdocuments($faire, $type, $id, $qui, $opt){
	if (intval($id)<0 AND $id==-$qui['id_auteur']){
		return true; 
	}
	return autoriser('modifier',$type,$id,$qui,$opt); 
}
}
?>