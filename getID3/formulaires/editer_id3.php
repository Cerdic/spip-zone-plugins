<?php
/**
 * Plugin GetID3
 * (c) 2008-2010 BoOz et kent1
 * Distribue sous licence GPL
 * Formulaire d'édition des tags ID3 d'un fichier sonore
 */


/**
 * Chargement des donnees du formulaire
 *
 * @param int $id l'id du document
 * @return array
 */
function formulaires_editer_id3_charger($id){
	$valeurs = array();
	$infos_doc = sql_fetsel('*','spip_documents','id_document='.intval($id));
	
	if(!in_array($infos_doc['extension'],array('mp3'))){
		$valeurs['message_erreur'] = _T('getid3:message_extension_invalide_ecriture');
	}else if($infos_doc['distant'] == 'oui'){
		$valeurs['message_erreur'] = _T('getid3:message_erreur_document_distant_ecriture');
	}
	if(isset($valeurs['message_erreur'])){
		$valeurs['editable'] = false;
	}else{
		/**
		 * Récupération des tags habituels:
		 * - title
		 * - artist
		 * - group
		 * - year
		 * - album
		 */
		include_spip('inc/documents');
		$fichier = get_spip_doc($infos_doc['fichier']);
		$recuperer_id3 = charger_fonction('recuperer_id3','inc');
		$valeurs = $recuperer_id3($fichier);
		foreach($valeurs as $valeur => $info){
			if(preg_match('/cover/',$valeur)){
				$valeurs['covers'][] = $info;
				$valeurs['_hidden'] .= "<input type='hidden' name='old_cover' id='old_cover' value='$info' />"; 
			}else{
				$valeurs[$valeur] = filtrer_entites($info);
			}
		}
		$valeurs['id_document'] = $id;
	}
	return $valeurs;
}

/**
 * Traitement du formulaire
 *
 * @param int $id
 * @return array
 */
function formulaires_editer_id3_traiter($id){
	$valeurs = array();
	
	$infos = array('title','artist','album','year','genre','comment');
	foreach($infos as $info){
		$valeurs[$info] = _request($info);
	}

	$post = isset($_FILES) ? $_FILES : $GLOBALS['HTTP_POST_FILES'];
	$files = null;
	if (is_array($post)){
		if (!($post['cover']['error'] == 4) && in_array($post['cover']['type'],array('image/png','image/jpeg','image/gif'))){
			include_spip('inc/getdocument');
			$dest = _DIR_TMP.$post['cover']['name'];
			deplacer_fichier_upload($post['cover']['tmp_name'],$dest);
			$files[] = $dest;
		}elseif(_request('old_cover')){
  			$files[] = _request('old_cover');
  		}
  	}

	$ecrire_id3 = charger_fonction('getid3_ecrire_infos','inc');
	$err = $ecrire_id3($id,$valeurs,$files);

	if(is_array($files)){
		foreach($files as $file){
			supprimer_fichier($file);
		}
	}
	return array('message_ok'=>_T('getid3:message_fichier_maj'),'editable'=>true);
}