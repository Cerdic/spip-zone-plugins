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

include_spip('inc/actions');
include_spip('inc/presentation');
function inc_spipmotion_infos_videos_dist($id, $id_document,$type,$script='',$ignore_flag = false) {
	$corps = recuperer_fond('prive/prive_infos_video', $contexte=array('id_document'=>$id_document));

	// Si on a le droit de modifier les documents, on affiche les icones pour récupérer les infos et le logo
	if(autoriser('joindredocument',$type, $id)){
		$texte = _T('spipmotion:recuperer_logo');
		$texte2 = _T('spipmotion:recuperer_infos');
		$texte3 = _T('spipmotion:encoder_video');
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");
		$infos_doc = sql_fetsel("extension,id_orig", "spip_documents","id_document=".sql_quote($id_document));

		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
			$action2 = ajax_action_auteur('spipmotion_infos', "$id/$type/$id_document", $script, "type=$type&id_$type=$id&show_infos_docs=$id_document#spipmotion_infos_plus-$id_document", array($texte2));

			/**
			 * On vérifie si le document est tout d'abord transcodable (les flvs et mp3 ne sont pas forcément nécessaires)
			 * - S'il l'est et qu'il n'est pas dans la file d'attente, on propose à l'utilisateur de l'encoder ou réencoder
			 * TODO : Proposer peut être un formulaire dans le futur pour modifier certains réglages de base
			 * - S'il l'est et qu'il est dans la file d'attente :
			 * -** On indique qu'il est en train d'être encodé si sont statut = en_cours
			 * -** Sinon on indique qu'il est dans la file d'attente
			 */
			if(in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action3 = '';
					$texte3 = _T('spipmotion:document_en_cours_encodage');
				}elseif ($statut_encodage == 'non'){
					$action3 = '';
					$texte3 = _T('spipmotion:document_dans_file_attente');
				}else{
					$action3 = generer_action_auteur('spipmotion_ajouter_file_encodage', "$id/$type/$id_document", $redirect);
					$action3 = "<a href='$action3'>$texte3</a>";
				}
			}
		}
		else{
			$redirect = str_replace('&amp;','&',$redirect);
			$action = generer_action_auteur('spipmotion_logo', "$id/$type/$id_document", $redirect);
			$action = "<a href='$action'>$texte</a>";
			$action2 = generer_action_auteur('spipmotion_infos', "$id/$type/$id_document", $redirect);
			$action2 = "<a href='$action2'>$texte2</a>";
			$action3 = generer_action_auteur('spipmotion_ajouter_file_encodage', "$id/$type/$id_document", $redirect);
			$action3 = "<a href='$action3'>$texte3</a>";
		}
		$icone = find_in_path('images/spipmotion-24.png');
		$corps .= icone_horizontale($texte, $action, $icone, "creer.gif", false);
		$corps .= icone_horizontale($texte2, $action2, $icone, "creer.gif", false);
		if(($infos_doc['id_orig'] == 0) && in_array($infos_doc['extension'],lire_config('spipmotion/fichiers_videos_encodage',array()))){
			if($action3)
				$corps .= icone_horizontale($texte3, $action3, $icone, "creer.gif", false);
			else
				$corps .= $texte3;
		}
	}
	return ajax_action_greffe("spipmotion_infos_plus", $id_document, $corps);
}
?>