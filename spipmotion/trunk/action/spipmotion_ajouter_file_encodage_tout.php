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
function action_spipmotion_ajouter_file_encodage_tout_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	include_spip('inc/autoriser');
	if(autoriser('configurer')){
		if (preg_match(",^(\w+)$,", $arg, $r)){
			$format = $arg;
			spip_log("Demande de réencodage complet au format: $format","spipmotion");
		}else{
			spip_log("On réencode tous les fichiers","spipmotion");
		}
		action_spipmotion_ajouter_file_encodage_tout_post($format);
	}
	if(_request('redirect')){
		$redirect = str_replace('&amp;','&',urldecode(_request('redirect')));
		$GLOBALS['redirect'] = $redirect;
	}
}

function action_spipmotion_ajouter_file_encodage_tout_post($format=false){
	include_spip('inc/config');
	include_spip('action/spipmotion_ajouter_file_encodage');
	if($format){
		if(in_array($format,lire_config('spipmotion/fichiers_audios_sortie',array()))){
			$formats = lire_config('spipmotion/fichiers_audios_encodage',array());
		}else if(in_array($format,lire_config('spipmotion/fichiers_videos_sortie',array()))){
			$formats = lire_config('spipmotion/fichiers_videos_encodage',array());
		}
	}else{
		$formats = array_merge(lire_config('spipmotion/fichiers_videos_encodage',array()),lire_config('spipmotion/fichiers_audios_encodage',array()));
	}
	$fichiers = sql_select('*','spip_documents',sql_in('extension',$formats).' AND mode!="conversion"');
	while($fichier = sql_fetch($fichiers)){
		spipmotion_genere_file($fichier['id_document'],$format);
	}
	
	$conversion_directe = charger_fonction('facd_convertir_direct','inc');
	$conversion_directe();
}
?>