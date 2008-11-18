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
function formulaires_editer_kaltura_charger_dist($objet='', $id_objet=0, $redirect='', $kshow_id=null){
	include_spip('inc/kaltura');
	$id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	if (!$kshow_id){
		if (!_request('resetks') AND $GLOBALS['visiteur_session']['kshow_id'])
			$kshow_id = $GLOBALS['visiteur_session']['kshow_id'];
		elseif ((!is_null($kshow_id) AND $kshow_id==0) OR _request('resetks')) {
			// supprimer le precedent si besoin
			if ($GLOBALS['visiteur_session']['kshow_id'])
				kaltura_delete(array('kshow_id'=>$GLOBALS['visiteur_session']['kshow_id']));
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
function formulaires_editer_kaltura_verifier_dist($objet='', $id_objet=0, $redirect='', $kshow_id=null){
	if (_request('resetks'))
		$erreurs['message_ok']=_T('kaltura:nouvelle_video');
	else {
		if (!$id_auteur=$GLOBALS['visiteur_session']['id_auteur']
			OR (!$kshow_id AND !$kshow_id = $GLOBALS['visiteur_session']['kshow_id']))
			$erreurs['message_erreur'] = _T('kaltura:interdit');
	}
	/* test vignette * /
	include_spip('inc/kaltura');
	kaltura_vignette(array('kshow_id'=>$GLOBALS['visiteur_session']['kshow_id']));
	$erreurs['message_erreur'] = $GLOBALS['visiteur_session']['kshow_id'];
	/ * */
	return $erreurs;
}

function formulaires_editer_kaltura_traiter_dist($objet='', $id_objet=0, $redirect='', $kshow_id=null){
	include_spip('inc/kaltura');
	include_spip('base/abstract_sql');
	
	$id_auteur=$GLOBALS['visiteur_session']['id_auteur'];
	// y a plus qu'a la mettre dans la table spip_documents !
	if (!$kshow_id)
		$kshow_id = $GLOBALS['visiteur_session']['kshow_id'];
	$url_distante = kaltura_url_video($kshow_id,$id_auteur);
	$ins = array(
		"extension"	=> "swf",
		"distant"	=> "oui",
		"date"	=> "NOW()",
		"fichier"	=> $url_distante,
		"taille"	=> 0,
		"largeur"	=> 400,
		"hauteur"	=> 320,
		"mode"	=> 'document',
		"titre" => _T("kaltura:video_de_nom",array('nom'=>$GLOBALS['visiteur_session']['nom'])),
	);
	$id_document = sql_insertq('spip_documents',$ins);
	if ($id_document AND $objet AND $id_objet){
		sql_insertq('spip_documents_liens',array('id_document'=>$id_document,'objet'=>$objet,'id_objet'=>$id_objet));
	}
	
	include_spip('inc/invalideur');
	suivre_invalideur("$objet/$id_objet");
	
	// virer le kshow_id de la
	session_set('kshow_id',null);
	$res = array('message_ok'=>'ok','editable'=>true);
	if ($redirect)
		$res['redirect'] = $redirect;
	return $res;
}
?>