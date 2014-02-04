<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('inc/actions');

/**
 * Fonction d'ajout dans la file d'attente
 */
function action_spipmotion_ajouter_file_encodage_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(-?)(\d+)\W?(\w*)$,", $arg, $r)){
		spip_log("action_spipmotion_ajouter_file_encodage_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return $redirect;
	}

	action_spipmotion_ajouter_file_encodage_post($r);
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
	}
	return $redirect;
}

function action_spipmotion_ajouter_file_encodage_post($r){
	list(, $sign, $id_document,$format) = $r;
	
	$id_facd = spipmotion_genere_file($id_document,$format);
	
	if(intval($id_facd)){
		$conversion_directe = charger_fonction('facd_convertir_direct','inc');
		$conversion_directe();
	}
}

/**
 * Fonction d'ajout des versions dans la file d'attente
 * 
 * On ajoute des conversions possibles que si le document en cours n'est pas lui-même une conversion
 *
 * @param int $id_document l'id du document original
 */
function spipmotion_genere_file($id_document,$format=''){
	$infos_doc = sql_fetsel('extension,mode','spip_documents','id_document='.intval($id_document));
	$extension = $infos_doc['extension'];
	$mode_orig = $infos_doc['mode'];
	if($mode_orig != 'conversion'){
		include_spip('action/facd_ajouter_conversion');
		include_spip('inc/config');
		if($format && (
				(in_array($format,lire_config('spipmotion/fichiers_videos_sortie',array()))
					&& in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array())))
				OR (in_array($format,lire_config('spipmotion/fichiers_audios_sortie',array()))
					&& in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array())))
			)){
			$en_file = sql_getfetsel("id_facd_conversion","spip_facd_conversions","id_document=".intval($id_document)." AND extension =".sql_quote($format)." AND statut IN ('en_cours,non,erreur')");
			if(!$en_file){
				$id_facd = facd_ajouter_conversion_file($id_document,'spipmotion_encodage',$format,null,'conversion');
			}
			else
				spip_log("Ce document existe deja dans la file d'attente","spipmotion");
		}else if(!$format){
			spip_log('on relance l encodage pour tous les formats','spipmotion');
			/**
			 * Ajout de la vidéo dans la file d'attente d'encodage si besoin
			 */
			if(in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array()))){
				foreach(lire_config('spipmotion/fichiers_videos_sortie',array()) as $format){
					$en_file = sql_getfetsel("id_facd_conversion","spip_facd_conversions","id_document=".intval($id_document)." AND extension ='$extension_sortie' AND statut IN ('en_cours,non')");
					if(!$en_file){
						$id_facd = facd_ajouter_conversion_file($id_document,'spipmotion_encodage',$format,null,'conversion');
					}
					else
						spip_log("Cette video existe deja dans la file d'attente","spipmotion");
				}
			}

			/**
			 * Ajout du son dans la file d'attente d'encodage si besoin
			 */
			else if(in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array()))){
				foreach(lire_config('spipmotion/fichiers_audios_sortie',array()) as $format){
					$en_file = sql_getfetsel("id_facd_conversion","spip_facd_conversions","id_document=".intval($id_document)." AND extension ='$extension_sortie' AND statut IN ('en_cours,non')");
					if(!$en_file){
						$id_facd = facd_ajouter_conversion_file($id_document,'spipmotion_encodage',$format,null,'conversion');
					}
					else
						spip_log("Ce son existe deja dans la file d'attente","spipmotion");
				}
			}
		}
	}
	return $id_facd;
}
?>