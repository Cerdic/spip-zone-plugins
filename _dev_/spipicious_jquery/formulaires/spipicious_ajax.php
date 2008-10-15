<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function formulaires_spipicious_ajax_charger($id_objet,$type='article') {
	global $visiteur_session;
	$autorise = lire_config('spipicious/people');
	spip_log($autorise,'spipicious');
	if (!$visiteur_session['id_auteur'] OR !in_array($visiteur_session['statut'],$autorise)) {
		spip_log('pas auteur pour spipicious');
		return array('editable'=>'');
	} else {
		$auteur_id = $visiteur_session['id_auteur'];
	}
	$id_type = 'id_'.$type;
	$valeurs = array($id_type=>$id_objet,'type'=>$type,'id_objet'=>$id_objet);
	return $valeurs;
}

function formulaires_spipicious_ajax_traiter($id_objet,$type) {
	global $visiteur_session;
	if(!function_exists('sql_get_fetsel')){
		include_spip('base/abstract_sql');	
	}
	$autorise = lire_config('spipicious/people');
	spip_log($autorise,'spipicious');
	if (!$visiteur_session['id_auteur'] OR !in_array($visiteur_session['statut'],$autorise)) {
		spip_log('pas auteur pour spipicious');
		return '';
	} else {
		$auteur_id = $visiteur_session['id_auteur'];
	}
	spip_log('traiter spipicious_ajax','spipicious');
	if(!$type){
		$type = 'article';
	}

	if(table_objet_sql('spip_mots_'.$type)){
		$table_mot = table_objet_sql('spip_mots_'.$type);
	}
	else{
		$table_mot = table_objet_sql('spip_mots_'.$type.'s');
	}
	spip_log("table mots $table_mots",'spipicious');
	
	//recuperation des variables utiles
	$tags = _request('spipicious_tags');
	$type_groupe_tags = lire_config('spipicious/groupe_mot');
	$id_groupe = sql_getfetsel("id_groupe","spip_groupes_mots","titre = '$type_groupe_tags'");
	if(!intval($id_groupe)){
		$id_groupe = 1;
	}
	$add_tags = _request('add_tags');
	$remove_tag = _request('remove_tag');

	spip_log("id_groupe = $id_groupe mots $table_mots",'spipicious');
	spip_log("remove_tag = $remove_tag",'spipicious');
		
	if (intval($remove_tag)
		AND $s = sql_getfetsel("id_mot","spip_spipicious","id_auteur=$auteur_id AND id_${type}=$id_objet AND id_mot=$remove_tag")) {
			// On le vire de notre auteur dans spipicious
			sql_delete("spip_spipicious","id_auteur=$auteur_id AND id_${type}=$id_objet AND id_mot=$remove_tag"); // on efface le mot associe a l'auteur sur l'objet
			spip_log("suppression spipiciousmot (id_$type=$id) id_mot=".$remove_tag."", 'spipicious');
			
			// Utilisation par un autre utilisateur => sinon : il n'est plus du tout utilise =>
			// suppression du mot pure et simple dans spip_mots_$type et spip_mot
			$newt = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".$remove_tag);
			if (!$newt){
				sql_delete("$table_mot","id_mot=".$remove_tag." AND id_".$type."=".$id);
				spip_log("suppression $table_mot (id_article=$id) non utilise id_mot=".$remove_tag, 'spipicious');
				sql_delete("spip_mots","id_mot=$remove_tag"); // on efface le mot si il n'est plus associe a rien
				spip_log("suppression spip_mot non utilise id_mot=$remove_tag", "spipicious");
			}
			else {
				// Utilisation par un autre utilisateur ok mais utilisation sur le meme id_$type
				$newt2 = sql_getfetsel("id_auteur","spip_spipicious","id_mot=$remove_tag AND id_".$type."=$id_objet");
				if(!$newt2){
					sql_delete("$table_mot","id_mot=$remove_tag AND id_".$type."=$id_objet");
					spip_log("suppression $table_mot (id_article=$id) non utilise id_mot=".$remove_tag, 'spipicious');
				}
				spip_log("mot toujours utilise : id_mot=".$remove_tag, 'spipicious');
			}
			$titre_mot = sql_getfetsel("titre","spip_mots","id_mot=$remove_tag");
			$invalider = true;
			$message = _T('spipicious:tag_supprime',array('name'=>$titre_mot));
		}

	else if(!empty($add_tags)){
		$tableau_tags = explode(";",$tags);
		$position = 0;
		if (is_array($tableau_tags)) {
			spip_log($tableau_tags,'spipicious');
			foreach ($tableau_tags as $k=>$tag) {
				$tag = trim($tag);
				if(!empty($tag)){
					if (!in_array($tag,$tag_analysed)) {
						$tag = corriger_caracteres($tag);
		
						// doit on creer un nouveau tag ?
						$id_tag = sql_getfetsel("id_mot","spip_mots","titre=".sql_quote($tag)." AND id_groupe=$id_groupe");
						if (!$id_tag) { // creation tag
							$id_tag = sql_insertq("spip_mots", array('titre' => $tag,'id_groupe' => $id_groupe,'type' => $type_groupe_tags));
							spip_log("creation du nouveau mot est $tag dont l'id est $id_tag","spipicious");
						} else {  // on recupere l'id du tag
								spip_log('id_mot est '.$id_tag.'','spipicious');
						}
					}
					// on lie le mot au couple type (uniquement si pas deja fait)
					$result = sql_getfetsel("id_mot","$table_mot","id_mot=".$id_tag." AND id_".$type."=".$id_objet);
					if (!$result) {
						sql_insertq("$table_mot",array('id_mot' => $id_tag,'id_'.$type.'' => $id_objet));
						spip_log("insertion mot $id_tag in $table_mot","spipicious");
					}
					$result_spipicious = sql_getfetsel("id_mot","spip_spipicious","id_mot=".$id_tag." AND id_".$type."=$id_objet AND id_auteur=$auteur_id");
					if(!$result_spipicious){
						sql_insertq("spip_spipicious",array('id_mot' => $id_tag,'id_auteur' => $auteur_id,'id_'.$type.'' => $id_objet, 'position' => $position));
						spip_log("insertion mot $id_tag in spip_spipicious","spipicious");	
						$message = _T('spipicious:tag_ajoute',array('name'=>$tag));
					}
					else{
						$message = _T('spipicious:tag_deja_present');
					}
					$position++;
				}
				$tag_analysed[] = $tag;
			}
			
			if($position > 1){
				$tags = implode('<br />',$tag_analysed);
				$message = _T('spipicious:tags_ajoutes',array('name'=>$tags));
			}
		}	
	}
	if($invalider){
		include_spip ("inc/invalideur");
		suivre_invalideur("1");
	}
	return array('editable'=>true,'message'=>$message);
}
?>