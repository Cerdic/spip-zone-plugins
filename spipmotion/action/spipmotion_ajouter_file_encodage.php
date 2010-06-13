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
include_spip('inc/actions');

/**
 * Fonction d'ajout dans la file d'attente
 */
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
	list(, $sign, $id, $type, $id_document) = $r;
	spip_log($id.' - '.$id_document.'  - '.$type,'spipmotion');

	spipmotion_supprimer_versions($id_document);
	spipmotion_genere_file($id_document,$type,$id);

	$redirect = urldecode(_request('redirect'));

	return $redirect;
}

/**
 * Supprime les versions du document que l'on souhaite encoder
 * - Supprime les fichiers existants et leurs insertions en base
 * - Supprime la présence de ces documents dans la file d'attente
 *
 * @param int $id_document L'id_document original
 */
function spipmotion_supprimer_versions($id_document){

	$v = sql_select("id_document,id_vignette,fichier","spip_documents","id_orig=".intval($id_document));

	include_spip('inc/documents');
	/**
	 * Pour chaque version du document original
	 */
	while($version = sql_fetch($v)){
		/**
		 * On ajoute l'id_document dans la liste des documents
		 * à supprimer de la base
		 * On supprime le fichier correspondant
		 */
		$liste[] = $version['id_document'];
		if (@file_exists($f = get_spip_doc($version['fichier']))) {
			supprimer_fichier($f);
		}

		/**
		 * Si le document a une vignette :
		 * - On ajoute l'id_document dans la liste à supprimer
		 * - On supprime le fichier correspondant à la vignette
		 */
		if($version['id_vignette'] > 0){
			$liste[] = $version['id_vignette'];
			$fichier = sql_getfetsel('fichier','spip_documents','id_document='.$version['id_vignette']);
			if (@file_exists($f = get_spip_doc($fichier))) {
				supprimer_fichier($f);
			}
		}

	}
	if(is_array($liste)){
		$in = sql_in('id_document', $liste);
		sql_delete("spip_documents", $in);
		sql_delete("spip_documents_liens", $in);
		sql_delete("spip_spipmotion_attentes", "id_document=".intval($id_document));
	}

	include_spip('inc/invalideur');
	suivre_invalideur(1);
}

/**
 * Fonction d'ajout des versions dans la file d'attente
 *
 * @param int $id_document l'id du document original
 * @param unknown_type $type
 * @param unknown_type $id
 */
function spipmotion_genere_file($id_document,$type,$id){
	$infos_doc = sql_fetsel('extension,id_orig','spip_documents','id_document='.intval($id_document));
	$extension = $infos_doc['extension'];
	$id_orig = $infos_doc['id_orig'];

	/**
	 * Ajout de la vidéo dans la file d'attente d'encodage si besoin
	 */
	if(in_array($extension,lire_config('spipmotion/fichiers_videos_encodage',array())) && ($id_orig == 0)){
		spip_log("l'id_orig est $id_orig et l'id_document est $id_document","spipmotion");
		foreach(lire_config('spipmotion/fichiers_videos_sortie',array()) as $extension_sortie){
			$en_file = sql_getfetsel("id_spipmotion_attente","spip_spipmotion_attentes","id_document=$id_document AND extension ='$extension_sortie' AND encode IN ('en_cours,non')");
			if(!$en_file){
				$document = sql_fetsel("docs.id_document, docs.extension,docs.fichier,docs.mode,docs.distant, L.vu, L.objet, L.id_objet", "spip_documents AS docs INNER JOIN spip_documents_liens AS L ON L.id_document=docs.id_document","L.id_document=".intval($id_document));
				if($document['extension'] != $extension_sortie){
					$id_doc_attente = sql_insertq("spip_spipmotion_attentes", array('id_document'=>$id_document,'objet'=>$document['objet'],'id_objet'=>$document['id_objet'],'encode'=>'non','id_auteur'=> $GLOBALS['visiteur_session']['id_auteur'],'extension'=>$extension_sortie));
					spip_log("on ajoute une video dans la file d'attente : $id_doc_attente","spipmotion");
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