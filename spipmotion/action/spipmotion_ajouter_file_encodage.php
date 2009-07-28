<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/charsets');	# pour le nom de fichier
include_spip('base/abstract_sql');
include_spip('inc/actions');

function action_spipmotion_ajouter_file_encodage_dist(){

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(-?)(\d+)\W(\w+)\W?(\d*)\W?(\d*)$,", $arg, $r)){
		spip_log("action_spipmotion_ajouter_file_encodage_dist incompris: " . $arg);
		$redirect = urldecode(_request('redirect'));
		return;
	}
	action_spipmotion_ajouter_file_encodage_post($r);
}

function action_spipmotion_ajouter_file_encodage_post($r){
	global $visiteur_session;
	list(, $sign, $id, $type, $id_document) = $r;
	spip_log($id.' - '.$id_document.'  - '.$type);
	
	$ajouter_file = charger_fonction('spipmotion_ajouter_file','inc');
	spipmotion_genere_file($id_document,$type,$id);
	
	$redirect = urldecode(_request('redirect'));

	return $redirect;
}

function spipmotion_genere_file($id_document,$type,$id){
	$extension = sql_getfetsel('extension','spip_documents','id_document='.intval($id_document));

	if(lire_config('spipmotion/encodage_auto') == 'on'){
		spip_log('On est dans le mode encodage auto','spipmotion');

		$encoder = charger_fonction('encodage','inc');
		/**
		 * Ajout de la vidéo dans la file d'attente d'encodage si besoin
		 */
		if(in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array()))){
			foreach(lire_config('spipmotion/fichiers_videos_sortie',array()) as $extension_sortie){
				$en_file = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","id_document=$id_document AND extension ='$extension_sortie' AND encode IN ('en_cours,non')");
				if(!$en_file){
					$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
					$id_doc_attente = sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],'extension'=>$extension_sortie));
					spip_log("on ajoute une video dans la file d'attente","spipmotion");
					$en_cours = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","encode='en_cours'");
					if(!$en_cours){
						$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
						$encoder($document,$id_doc_attente);
					}
				}
				else{
					spip_log("Cette video existe deja dans la file d'attente","spipmotion");							
				}
			}
		}
		
		/**
		 * Ajout du son dans la file d'attente d'encodage si besoin
		 */
		else if(in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array()))){
			foreach(lire_config('spipmotion/fichiers_audios_sortie',array()) as $extension_sortie){
				$en_file = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","id_document=$id_document AND extension ='$extension_sortie' AND encode IN ('en_cours,non')");
				if(!$en_file){
					$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
					$id_doc_attente = sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],'extension'=>$extension_sortie));
					spip_log("on ajoute un son dans la file d'attente","spipmotion");
					$en_cours = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","encode='en_cours'");
					if(!$en_cours){
						$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
						spip_log($document);
						$encoder($document,$id_doc_attente);
					}							
				}
				else{
					spip_log("Ce son existe deja dans la file d'attente","spipmotion");							
				}
			}
		}
	}
}
?>