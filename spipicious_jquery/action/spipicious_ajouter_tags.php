<?php
	function action_spipicious_ajouter_tags_dist(){
		global $visiteur_session;

		$autorise = lire_config('spipicious/people');
		if (!$visiteur_session['id_auteur'] OR !in_array($visiteur_session['statut'],$autorise)) {
			spip_log('pas auteur pour spipicious');
			return '';
		}

		$id_auteur = $visiteur_session['id_auteur'];

		$id_groupe = lire_config('spipicious/groupe_mot','1');

		$id_objet = _request('spipicious_id');
		$type = _request('spipicious_type');
		$id_table_objet = id_table_objet($type);
		$table_mot = table_objet_sql('spip_mots_'.table_objet($type));

		$tags = _request('spipicious_tags');
		$tableau_tags = explode(";",$tags);

		$ajouter_tags = spipicious_ajouter_tags($tableau_tags,$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe);
		return $ajouter_tags;
	}

	function spipicious_ajouter_tags($tableau_tags,$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe){
		$invalider = false;

		$tag_analysed = array();
		$position = 0;

		if (is_array($tableau_tags)) {
			include_spip('inc/modifier');
			foreach ($tableau_tags as $k=>$tag) {
				$tag = trim($tag);
				if(!empty($tag)){
					if (!in_array($tag,$tag_analysed)) {
						$tag = corriger_caracteres($tag);
						// doit on creer un nouveau tag ?
						$id_tag = sql_getfetsel("id_mot","spip_mots","titre=".sql_quote($tag)." AND id_groupe=$id_groupe");
						if (!$id_tag) { // creation tag
							$id_tag = sql_insertq("spip_mots", array('id_groupe' => $id_groupe));
							$c = array('titre' => $tag, 'id_groupe' => $id_groupe);
							revision_mot($id_tag, $c);
						}
					}
					// on lie le mot au couple type (uniquement si pas deja fait)
					$result = sql_getfetsel("id_mot",$table_mot,"id_mot=".$id_tag." AND $id_table_objet=".$id_objet);
					if (!$result) {
						sql_insertq("$table_mot",array('id_mot' => $id_tag,$id_table_objet => $id_objet));
					}
					$result_spipicious = sql_getfetsel("id_mot","spip_spipicious","id_mot=".$id_tag." AND $id_table_objet=$id_objet AND id_auteur=$id_auteur");
					if(!$result_spipicious){
						sql_insertq("spip_spipicious",array('id_mot' => $id_tag,'id_auteur' => $id_auteur,$id_table_objet => $id_objet, 'position' => $position));
						$message = _T('spipicious:tag_ajoute',array('name'=>$tag));
						$invalider = true;
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
		return array($message,$invalider,'');
	}
?>