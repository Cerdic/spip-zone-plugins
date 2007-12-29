<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function balise_FORMULAIRE_SPIPICIOUS_AJAX ($p) {
	$p = calculer_balise_dynamique($p,'FORMULAIRE_SPIPICIOUS_AJAX', array('id_document','id_rubrique', 'id_forum', 'id_article', 'id_breve', 'id_syndic'));
	return $p;
}

function balise_FORMULAIRE_SPIPICIOUS_AJAX_dyn($id_document,$id_rubrique,$id_forum,$id_article,$id_breve,$id_syndic) {
	$autorise = lire_config('spipicious/people');
	if (!$autorise) $autorise == '0minirezo';
	
	if (!$GLOBALS["auteur_session"] OR !in_array($GLOBALS['auteur_session']['statut'],$autorise)) {
		return '';
	} else {
		$auteur_id = $GLOBALS['auteur_session']['id_auteur'];
	}

	$ids = array();
	if ($id_rubrique > 0 AND ($id_article OR $id_breve OR $id_syndic))
		$id_rubrique = 0;
	foreach (array('id_article', 'id_breve', 'id_rubrique', 'id_syndic', 'id_forum') as $o) {
		if ($x = intval($$o)) {
			$ids[$o] = $x;
			$id = $x;
			$type = str_replace('id_', '', $o);
		}
	}
	
	if ($id_document) {
		$type = 'document';
		$id = $id_document;
		$ids['id_document'] = $id;
	}
	
	$table_mot = 'spip_mots_'.$type.'s';
	
	//recuperation des variables utiles
	$tags = _request('tags');
	$id_groupe_tags = _request('select_groupe');
	$type_groupe_tags = _request('type_groupe');
	$add_tags = _request('add_tags');
	
	if ($add_tags && $auteur_id){

	if ($tags && $auteur_id) {
		if (substr($tags, -1, 1) == ';'){
			$tags = substr($tags,0,-1);
		}
		$tableau_tags = explode(";",$tags);
		if (is_array($tableau_tags)) {
			$position = 0;
			$tag_analysed = array();
		foreach ($tableau_tags as $k=>$tag) {
			$tag = trim($tag);

			if ($tag!="" && !in_array($tag,$tag_analysed)) {

			// doit on creer un nouveau tag ?
			$result = sql_select("*","spip_mots","titre='".$tag."' AND id_groupe=$id_groupe_tags");
			
				if (sql_count($result) == 0) { // creation tag
					$exist = 'mot inexistant';
					$log = spip_log($exist,'spipicious');
					sql_insertq("spip_mots", array('titre' => $tag,'id_groupe' => $id_groupe_tags,'type' => $type_groupe_tags));
					spip_log('creation du nouveau mot est '.$tag.'','spipicious');
					$result2 = sql_select("*","spip_mots","titre = '".$tag."' AND id_groupe=$id_groupe_tags");
					while($row=sql_fetch($result2)){
						$id_tag = $row['id_mot'];
						spip_log('id_mot du nouveau mot est '.$id_tag.'','spipicious');
					}
				} else {  // on recupere l'id du tag
					$exist = 'mot existant';
					$log = spip_log($exist,'spipicious');
					while($row=sql_fetch($result)){
						$id_tag = $row['id_mot'];
						spip_log('id_mot est '.$id_tag.'','spipicious');
					}
				}
		// on lie le mot au couple type (uniquement si pas deja fait)
			$result = sql_select("id_mot","".$table_mot."","id_mot=".$id_tag." AND id_".$type."=".$id);
			if (sql_count($result) == 0) {
				sql_insertq("".$table_mot."",array('id_mot' => $id_tag,'id_'.$type.'' => $id));
				$log = spip_log('insertion mot '.$id_tag.' in '.$table_mot.'','spipicious');
			}
			
			sql_insertq("spip_spipicious",array('id_mot' => $id_tag,'id_auteur' => $auteur_id,'id_'.$type.'' => $id, 'position' => $position));
			$log = spip_log('insertion mot '.$id_tag.' in spip_spipicious','spipicious');
			$position++;
			}
		$tag_analysed[] = $tag;
		}
		}
		}
		include_spip ("inc/invalideur");
		suivre_invalideur("1");
		return header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	else{
		return array('formulaires/formulaire_spipicious_ajax', 0,
			array_merge($ids,
			array(
				'url' => $script, # ce sur quoi on fait le action='...'
				'id' => $id,
				'auteur_id' => $auteur_id,
				'type' => $type,
		))
		);
	}
}

?>