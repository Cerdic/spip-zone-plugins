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
 * Action de suppression de tags sur un objet
 * 
 * @package SPIP\SPIPicious\Actions
 */

if (!defined("_ECRIRE_INC_VERSION")) return;#securite

/**
 * Action de suppression de tags appelée par le formulaire
 * 
 * @return array $suppression
 * 		Retourne un tableau composé du message de retour et si on doit invalider le cache 
 */
function action_spipicious_supprimer_tags_dist(){
	$id_objet = _request('spipicious_id');
	$type = _request('spipicious_type');

	include_spip('inc/autoriser');
	if(!autoriser('tagger_spipicious',$type,$id_objet))
		return false;

	$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
	$id_table_objet = id_table_objet($type);

	$remove_tags = _request('remove_tags');

	$suppression = spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet);
	return $suppression;
}

/**
 * Fonction de suppression de tags à un objet
 * 
 * -* On supprime l'élément dans spipicious qui lie le mot à l'objet et l'auteur
 * -* Si le mot n'est plus utilisé sur l'objet par aucun autre auteur, on supprime la liaison du mot à l'objet
 * -* Si le mot n'est plus utilisé du tout, on le supprimer définitivement
 * 
 * @param array $remove_tags
 * 		Un tableau php des tags à enlever
 * @param int $id_auteur
 * 		L'id_auteur de la personne ayant ajouté les tags
 * @param int $id_objet
 * 		L'identifiant numérique de l'objet à tagger
 * @param string $type
 * 		Le type de l'objet à tagger
 * @param int $id_table_objet
 * 		La clé primaire de l'objet à tagger ("id_article","id_rubrique")
 * @return array 
 * 		Retourne un tableau composé du message de retour et si on doit invalider le cache
 */
function spipicious_supprimer_tags($remove_tags,$id_auteur,$id_objet,$type,$id_table_objet){
	$compte = 0;
	$tags_removed = array();
	foreach($remove_tags as $remove_tag){
		include_spip('action/editer_mot');
		/**
		 * Suppression dans spip_spipicious du lien entre notre auteur, le mot et l'objet
		 */
		sql_delete("spip_spipicious","id_auteur=".intval($id_auteur)." AND id_objet=".intval($id_objet)." AND id_mot=".intval($remove_tag)." AND objet=".sql_quote($type)); // on efface le mot associe a l'auteur sur l'objet

		/**
		 * On vérifie si le tag est utilisé par un autre utilisateur
		 * 
		 * -* Si non, on supprime le mot clé définitivement
		 * -* Si oui, on vérifie si le mot est utilisé par un autre utilisateur sur le même objet:
		 * -** Si non, on dissocie le mot de l'objet
		 * -** Si oui, on ne fait rien de plus
		 */
		$tag_utilise = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag));
		if (!$tag_utilise)
			mot_supprimer($remove_tag);
		else {
			$tag_utilise_2 = sql_getfetsel("id_auteur","spip_spipicious","id_mot=".intval($remove_tag)." AND id_objet=".intval($id_objet)." AND objet=".sql_quote($type));
			if(!$tag_utilise_2)
				mot_dissocier($remove_tag,array($type=>$id_objet));
		}
		
		/**
		 * On crée notre message
		 */
		$titre_mot = sql_getfetsel("titre","spip_mots","id_mot=".intval($remove_tag));
		$message = _T('spipicious:tag_supprime',array('name'=>$titre_mot));
		$tags_removed[] = $titre_mot;
		$compte++;
	}

	/**
	 * Si on a quelque chose => on invalide le cache
	 */
	if($compte > 0)
		$invalider = true;
	
	if($compte > 1){
		$tags = implode('<br />',$tags_removed);
		$message = _T('spipicious:tags_supprimes',array('name'=>$tags,'nb'=>$compte));
	}

	return array($message,$invalider,'');
}
?>