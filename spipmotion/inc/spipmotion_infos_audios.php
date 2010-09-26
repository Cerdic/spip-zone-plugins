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
		$script = $type.'s';
		$redirect =  generer_url_ecrire($script,"id_$type=$id#portfolio_documents");
		$extension = sql_getfetsel("extension", "spip_documents","id_document=".sql_quote($id_document));

		// Inspire de inc/legender
		if (test_espace_prive()){
			$redirect = str_replace('&amp;','&',$redirect);

			/**
			 * On vérifie si le document est tout d'abord transcodable (les flvs et mp3 ne sont pas forcément nécessaires)
			 * - S'il l'est et qu'il n'est pas dans la file d'attente, on propose à l'utilisateur de l'encoder ou réencoder
			 * TODO : Proposer peut être un formulaire dans le futur pour modifier certains réglages de base
			 * - S'il l'est et qu'il est dans la file d'attente :
			 * -** On indique qu'il est en train d'être encodé si sont statut = en_cours
			 * -** Sinon on indique qu'il est dans la file d'attente
			 */
			$sorties = lire_config('spipmotion/fichiers_audios_sortie',array());
			$sorties = array_diff($sorties,array($extension));
			if(
				in_array($extension,lire_config('spipmotion/fichiers_audios_encodage',array()))
				&& (count($sorties)>0)
			){
				$statut_encodage = sql_getfetsel('encode','spip_spipmotion_attentes','id_document='.intval($id_document).' AND encode IN ("en_cours","non")');
				if($statut_encodage == 'en_cours'){
					$action = false;
					$texte = _T('spipmotion:document_dans_file_attente');
				}elseif ($statut_encodage == 'non'){
					$action = false;
					$texte = _T('spipmotion:document_dans_file_attente');
				}else{
					$texte = _T('spipmotion:encoder_son');
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
			if($action){
				$corps .= icone_horizontale($texte, $action, find_in_path('images/spipmotion-24.png'), "creer.gif", false);
			}else{
				$corps .= "<p>".$texte."</p>";
			}
		}
	}
	return $corps;
}
?>