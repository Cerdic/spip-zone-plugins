<?php
/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info)
 * Erational
 *
 * © 2007-2011 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function action_spipicious_ajouter_tags_dist(){
	global $visiteur_session;

	$id_objet = _request('spipicious_id');
	$type = _request('spipicious_type');

	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet,$visiteur_session,$opt)){
		return '';
	}

	$id_auteur = $visiteur_session['id_auteur'];
	$id_groupe = lire_config('spipicious/groupe_mot','1');
	$id_table_objet = id_table_objet($type);
	$table_mot = table_objet_sql('spip_mots_'.table_objet($type));

	$tags = _request('spipicious_tags');
	$tableau_tags = explode(";",$tags);

	$ajouter_tags = spipicious_ajouter_tags($tableau_tags,$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe);
	return $ajouter_tags;
}

/**
 * Ajout de mots à un objet
 *
 * @param array() $tableau_tags
 * @param int $id_auteur
 * @param int $id_objet
 * @param string $type
 * @param int $id_table_objet
 * @param string $table_mot la table de liaison mot / objet
 * @param int $id_groupe
 * @param string $manuel doit on le faire manuellement ou par inc/modifier
 */
function spipicious_ajouter_tags($tableau_tags=array(),$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe,$manuel=false){
	spip_log("$id_auteur,$id_objet,$type,$id_table_objet,$table_mot,$id_groupe,$manuel",'test');
	$tag_analysed = array();
	$position = 0;

	if (is_array($tableau_tags)) {
		include_spip('inc/modifier');
		foreach ($tableau_tags as $k=>$tag) {
			spip_log("ajout du tag $tag",'test');
			$tag = trim($tag);
			if(!empty($tag)){
				if (!in_array($tag,$tag_analysed)) {
					$tag_propre = corriger_caracteres($tag);
					spip_log("ajout du tag bis $tag",'test');
					// doit on creer un nouveau tag ?
					$id_tag = sql_getfetsel("id_mot","spip_mots","titre=".sql_quote($tag_propre)." AND id_groupe=".intval($id_groupe));
					if (!$id_tag) { // creation tag
						if(!$manuel){
							$id_tag = sql_insertq("spip_mots", array('id_groupe' => intval($id_groupe)));
							$c = array('titre' => $tag, 'id_groupe' => intval($id_groupe));
							revision_mot($id_tag, $c);
						}else{
							$row = sql_fetsel("titre", "spip_groupes_mots", "id_groupe=".intval($id_groupe));
							$id_tag = sql_insertq("spip_mots", array('id_groupe' => intval($id_groupe),'titre' => $tag_propre,'type'=> $row['titre']));
						}
					}
				}
				// on lie le mot au couple type (uniquement si pas deja fait)
				$result = sql_getfetsel("id_mot",$table_mot,"id_mot=".intval($id_tag)." AND $id_table_objet=".intval($id_objet));
				if (!$result) {
					sql_insertq("$table_mot",array('id_mot' => intval($id_tag),$id_table_objet => intval($id_objet)));
				}
				$result_spipicious = sql_getfetsel("id_mot","spip_spipicious","id_mot=".intval($id_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type)." AND id_auteur=".intval($id_auteur));
				if(!$result_spipicious){
					sql_insertq("spip_spipicious",array('id_mot' => intval($id_tag),'id_auteur' => intval($id_auteur),'id_objet' => intval($id_objet), 'objet'=>$type, 'position' => intval($position)));
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