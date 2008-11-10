<?php
/**
 * Plugin Kaltura
 * (c) 2008 Cedric MORIN, www.yterium.com
 *
 */

/**
 * charger
 *
 * @param unknown_type $kshow_id
 * @param unknown_type $id_auteur
 * @return unknown
 */
function formulaires_editer_kaltura_charger_dist($kshow_id=0,$id_auteur=0){
	include_spip('inc/kaltura');
	if (!$id_auteur=intval($id_auteur)
		// une creation est *toujours* par le visiteur loge
	OR !$kshow_id)
		$id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	if (!$kshow_id){
		if (!_request('resetks') AND $GLOBALS['visiteur_session']['kshow_id'])
			$kshow_id = $GLOBALS['visiteur_session']['kshow_id'];
		else {
			$inst = kaltura_instancie(array('id_auteur'=>$id_auteur));
			if (!$inst)
				return array('editable'=>false);
			list($kshow_id,$id_auteur) = $inst;
			include_spip('inc/session');
			session_set('kshow_id',$kshow_id);
		}
	}
	return array('kshow_id'=>$kshow_id,'user_id'=>$id_auteur);
}

/**
 * Enter description here...
 *
 * @param unknown_type $kshow_id
 * @param unknown_type $id_auteur
 */
function formulaires_editer_kaltura_verifier_dist($kshow_id=0,$id_auteur=0){
	if (_request('resetks'))
		$erreurs['message_ok']=_T('kaltura:nouvelle_video');
	return $erreurs;
}

function formulaires_editer_kaltura_charger_dist($kshow_id=0,$id_auteur=0){
	// y a plus qu'a la mettre dans la table spip_documents !
	
}
?>