<?php
/**
 * SPIP.icio.us
 * Gestion de tags lies aux auteurs
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * Erational (http://www.erational.org)
 *
 * © 2007-2013 - Distribue sous licence GNU/GPL
 * 
 * Action d'ajout de tags à un objet
 * 
 * @package SPIP\SPIPicious\Actions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;#securite

/**
 * Action d'ajout de tags appelée par le formulaire
 * 
 * @return array $ajouter_tags
 * 		Retourne un tableau composé du message de retour et si on doit invalider le cache 
 */
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
 * Fonction d'ajout de tag à un objet
 * 
 * -* Si le mot clé n'existe pas, on le crée;
 * -* Si le mot clé n'existait pas ou existait mais n'était pas lié à l'objet, 
 * on ajoute une liaison entre les deux
 * -* On ajoute une entrée dans la table de spipicious liant le mot clé, l'objet et l'auteur
 *
 * @param array $tableau_tags
 * 		Un tableau php des tags à analyser
 * @param int $id_auteur
 * 		L'id_auteur de la personne ayant ajouté les tags
 * @param int $id_objet
 * 		L'identifiant numérique de l'objet à tagger
 * @param string $type
 * 		Le type de l'objet à tagger
 * @param int $id_table_objet
 * 		La clé primaire de l'objet à tagger ("id_article","id_rubrique")
 * @param int $id_groupe
 * 		Le groupe de mots des tags
 * @return array 
 * 		Retourne un tableau composé du message de retour et si on doit invalider le cache
 */
function spipicious_ajouter_tags($tableau_tags=array(),$id_auteur,$id_objet,$type,$id_table_objet,$id_groupe){
	$tag_analysed = array();
	$position = 0;
	$statut = 'publie';
	
	if (is_array($tableau_tags)) {
		$table = table_objet_sql($type);
		$statut_objet = sql_getfetsel('statut',$table,"$id_table_objet=$id_objet");
		if($statut_objet && ($statut_objet != 'publie'))
			$statut = 'prop';

		include_spip('action/editer_mot');
		foreach ($tableau_tags as $k=>$tag) {
			$mot_cree = false;
			$tag = trim($tag);
			if(!empty($tag)){
				if (!in_array($tag,$tag_analysed)) {
					$tag_propre = corriger_caracteres($tag);
					/**
					 * doit on creer un nouveau mot inexistant en base ?
					 * 
					 * Si oui, on l'ajoute et on le lie directement à l'objet
					 * On insert la liaison de la triplette id_mot, id_auteur, objet dans spip_spipicious
					 * On met $mot_cree = true pour gagner les deux requêtes sql suivantes
					 */ 
					$id_tag = sql_getfetsel("id_mot","spip_mots","titre=".sql_quote($tag_propre)." AND id_groupe=".intval($id_groupe));
					if (!$id_tag) { // creation tag
						$id_tag = mot_inserer($id_groupe);
						$c = array('titre' => $tag_propre);
						mot_modifier($id_tag, $c);
						mot_associer($id_tag,array($type=>$id_objet));
						sql_insertq("spip_spipicious",array('id_mot' => intval($id_tag),'id_auteur' => intval($id_auteur),'id_objet' => intval($id_objet), 'objet'=>$type, 'position' => intval($position),'statut' => $statut));
						$message = _T('spipicious:tag_ajoute',array('name'=>$tag));
						$invalider = true;
						$mot_cree = true;
					}
				}
				/**
				 * Le mot n'est pas un nouveau mot
				 */ 
				if(!$mot_cree){
					/**
					 * Est il déjà lié à l'objet
					 * Si oui, on ne fait rien, si non, on crée la liaison
					 */
					$result = sql_getfetsel("id_mot",'spip_mots_liens',"id_mot=".intval($id_tag)." AND objet=".sql_quote($objet)." AND id_objet=".intval($id_objet));
					if (!$result)
						mot_associer($id_tag,array($type=>$id_objet));
					/**
					 * La triplette id_mot, id_auteur, objet existe t elle déjà?
					 * Si non on crée le lien dans la table spip_spipicious
					 * Si oui, on vérifie que les statuts soient bon
					 */
					$result_spipicious = sql_fetsel("id_mot,statut","spip_spipicious","id_mot=".intval($id_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type)." AND id_auteur=".intval($id_auteur));
					if(!$result_spipicious['id_mot']){
						sql_insertq("spip_spipicious",array('id_mot' => intval($id_tag),'id_auteur' => intval($id_auteur),'id_objet' => intval($id_objet), 'objet'=>$type, 'position' => intval($position),'statut' => $statut));
						$message = _T('spipicious:tag_ajoute',array('name'=>$tag));
						$invalider = true;
					}
					else if(isset($result_spipicious['statut']) && ($result_spipicious['statut'] != $statut)){
						sql_updateq('spip_spipicious',array('statut'=>$statut),"id_mot=".intval($id_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type)." AND id_auteur=".intval($id_auteur));
						$message = _T('spipicious:tag_deja_present');
					}else
						$message = _T('spipicious:tag_deja_present');
				}
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