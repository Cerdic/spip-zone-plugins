<?php
/**
 * spip.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational
 *
 * © 2007-2012 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

function action_spipicious_ajouter_tags_dist(){
	$id_objet = _request('spipicious_id');
	$type = _request('spipicious_type');

	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet))
		return false;

	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	if(!function_exists('lire_config'))
		include_spip('inc/config');
	$id_groupe = lire_config('spipicious/groupe_mot','1');
	$id_table_objet = id_table_objet($type);

	$tags = _request('spipicious_tags');
	$tableau_tags = explode(",",$tags);

	$ajouter_tags = spipicious_ajouter_tags($tableau_tags,$id_auteur,$id_objet,$type,$id_table_objet,$id_groupe);
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
 * @param int $id_groupe
 * @param string $manuel doit on le faire manuellement ou par inc/modifier
 */
function spipicious_ajouter_tags($tableau_tags=array(),$id_auteur,$id_objet,$type,$id_table_objet,$id_groupe){
	$tag_analysed = array();
	$position = 0;
	$statut = 'publie';
	
	if (is_array($tableau_tags)) {
		$table = table_objet_sql($type);
		$infos_objets = sql_fetsel('*',$table,"$id_table_objet=$id_objet");
		if(isset($infos_objets['statut']) && ($infos_objets['statut'] != 'publie'))
			$statut = 'prop';

		include_spip('action/editer_mot');
		foreach ($tableau_tags as $k=>$tag) {
			$tag = trim($tag);
			if(!empty($tag)){
				if (!in_array($tag,$tag_analysed)) {
					$tag_propre = corriger_caracteres($tag);
					// doit on creer un nouveau tag ?
					$id_tag = sql_getfetsel("id_mot","spip_mots","titre=".sql_quote($tag_propre)." AND id_groupe=".intval($id_groupe));
					if (!$id_tag) { // creation tag
						$id_tag = mot_inserer($id_groupe);
						$c = array('titre' => $tag_propre);
						mot_modifier($id_tag, $c);
					}
				}
				// on lie le mot au couple type (uniquement si pas deja fait)
				$result = sql_getfetsel("id_mot",'spip_mots_liens',"id_mot=".intval($id_tag)." AND objet=".sql_quote($objet)." AND id_objet=".intval($id_objet));
				if (!$result)
					mot_associer($id_tag,array($type=>$id_objet));

				$result_spipicious = sql_fetsel("*","spip_spipicious","id_mot=".intval($id_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type)." AND id_auteur=".intval($id_auteur));
				if(!$result_spipicious['id_mot']){
					sql_insertq("spip_spipicious",array('id_mot' => intval($id_tag),'id_auteur' => intval($id_auteur),'id_objet' => intval($id_objet), 'objet'=>$type, 'position' => intval($position),'statut' => $statut));
					$message = _T('spipicious:tag_ajoute',array('name'=>$tag));
					$invalider = true;
				}
				else if(isset($result_spipicious['statut']) && ($result_spipicious['statut'] != $statut))
					sql_updateq('spip_spipicious',array('statut'=>$statut),"id_mot=".intval($id_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type)." AND id_auteur=".intval($id_auteur));
				else
					$message = _T('spipicious:tag_deja_present');
				$position++;
			}
			$tag_analysed[] = $tag;
		}

		if($position > 1){
			$tags = implode('<br />',$tag_analysed);
			$message = _T('spipicious:tags_ajoutes',array('name'=>$tags,'nb'=>$position));
		}
	}
	return array($message,$invalider,'');
}
?>