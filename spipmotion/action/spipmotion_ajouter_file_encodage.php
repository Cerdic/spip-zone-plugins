<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

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
	spip_log($id.' - '.$id_document.'  - '.$type,'spipmotion');

	spipmotion_supprimer_versions($id_document);
	spipmotion_genere_file($id_document,$type,$id);

	$redirect = urldecode(_request('redirect'));

	return $redirect;
}

function spipmotion_supprimer_versions($id_document){

	$v = sql_select("id_document","spip_documents","id_orig=".intval($id_document));

	include_spip('inc/documents');

	while($version = sql_fetch($v)){
		$liste[] = $version['id_document'];
		spip_log('on supprime le document '.$version['id_document'],'spipmotion');
		supprimer_documents($liste);
	}

	sql_delete("spip_spipmotion_attentes", "id_document=".intval($args['id_document']));
}

function spipmotion_genere_file($id_document,$type,$id){
	$infos_doc = sql_fetsel('extension,id_orig','spip_documents','id_document='.intval($id_document));
	$extension = $infos_doc['extension'];
	$id_orig = $infos_doc['id_orig'];

	$encoder = charger_fonction('encodage','inc');
	/**
	 * Ajout de la vidéo dans la file d'attente d'encodage si besoin
	 */
	if(in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array())) && (!intval($id_orig))){
		spip_log("l'id_orig est $id_orig et l'id_document est $id_document","spipmotion");
		foreach(lire_config('spipmotion/fichiers_videos_sortie',array()) as $extension_sortie){
			$en_file = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","id_document=$id_document AND extension ='$extension_sortie' AND encode IN ('en_cours,non')");
			if(!$en_file){
				$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
				if($document['extension'] != $extension_sortie){
					$id_doc_attente = sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],'extension'=>$extension_sortie));
					spip_log("on ajoute une video dans la file d'attente : $id_doc_attente","spipmotion");
					if(lire_config('spipmotion/encodage_auto') == 'on'){
						$en_cours = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","encode='en_cours'");
						if(!$en_cours){
							spip_log("On est dans le mode encodage auto, on encode $id_doc_attente","spipmotion");
							$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
							$encoder($document,$id_doc_attente);
						}
					}
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
	else if(in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array())) && (!intval($id_orig))){
		spip_log("l'id_orig est $id_orig et l'id_document est $id_document","spipmotion");
		foreach(lire_config('spipmotion/fichiers_audios_sortie',array()) as $extension_sortie){
			$en_file = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","id_document=$id_document AND extension ='$extension_sortie' AND encode IN ('en_cours,non')");
			if(!$en_file){
				$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
				if($document['extension'] != $extension_sortie){
					$id_doc_attente = sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],'extension'=>$extension_sortie));
					spip_log("on ajoute un son dans la file d'attente : $id_doc_attente","spipmotion");
					if(lire_config('spipmotion/encodage_auto') == 'on'){
						$en_cours = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","encode='en_cours'");
						if(!$en_cours){
							spip_log("On est dans le mode encodage auto, on encode $id_doc_attente","spipmotion");
							$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
							$encoder($document,$id_doc_attente);
						}
					}
				}
			}
			else{
				spip_log("Ce son existe deja dans la file d'attente","spipmotion");
			}
		}
	}else if ($id_orig > 0){
		spip_log("l'id_orig est supérieur à 0","spipmotion");
	}
}
?>