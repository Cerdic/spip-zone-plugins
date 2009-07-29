<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et sons directement dans spip
 *
 * Auteurs :
 * Quentin Drouet
 * 2006-2009 - Distribué sous licence GNU/GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_spipmotion_infos_audios_dist($id, $id_document,$type,$script='',$ignore_flag = false) {
	if(_AJAX){
		include_spip('public/assembler');
		include_spip('inc/presentation');
	}
	/**
	 * Contrairement aux videos la partie de gestion des metas des sons est gérée
	 * par le plugin getID3
	 * Ici on ajoute simplement un lien vers l'encodage
	 */
	
	if(autoriser('joindredocument',$type, $id)){
		$texte = _T('spipmotion:encoder_son');
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");
		$extension = sql_getfetsel("extension", "spip_documents","id_document=".sql_quote($id_document));

		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
			$action2 = ajax_action_auteur('spipmotion_infos', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#infosdoc-$id_document", array($texte2));
			
			/**
			 * On vérifie si le document est tout d'abord transcodable (les flvs et mp3 ne sont pas forcément nécessaires)
			 * - S'il l'est et qu'il n'est pas dans la file d'attente, on propose à l'utilisateur de l'encoder ou réencoder 
			 * TODO : Proposer peut être un formulaire dans le futur pour modifier certains réglages de base
			 * - S'il l'est et qu'il est dans la file d'attente :
			 * -** On indique qu'il est en train d'être encodé si sont statut = en_cours
			 * -** Sinon on indique qu'il est dans la file d'attente 
			 */
			if(in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array()))){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action = false;
					$texte = _T('spipmotion:document_dans_file_attente');
				}elseif ($statut_encodage == 'non'){
					$action = false;
					$texte = _T('spipmotion:document_dans_file_attente');
				}else{
					$action = generer_action_auteur('spipmotion_ajouter_file_encodage', "$id/$type/$id_document", $redirect);
					$action = "<a href='$action'>$texte</a>";
				}
			}
		}
		else{
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_ajouter_file_encodage', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
		}
		if(!_AJAX){
			if(in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array()))){
				if($action){
					$corps .= icone_horizontale($texte, $action, $supp, "creer.gif", false);
				}else{
					$corps .= "<p>".$texte."</p>";
				}
			}
		}
	}
	//return ajax_action_greffe("spipmotion", $id_document, $corps);
	return $corps;
}
?>