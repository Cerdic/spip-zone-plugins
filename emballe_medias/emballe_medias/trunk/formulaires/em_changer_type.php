<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Formulaire de changement de type de medias pour le formulaire
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;
 
/**
 * Fonction de chargement du formulaire
 *
 * On vérifie que l'id_article est bien un int
 * On vérifie les droits de modifification sur l'article
 * On charge l'ensemble des types possiblesen fonction du ou des documents de l'article
 *
 * @return array L'array des valeurs que l'on utilisera dans le formulaire
 * @param object $id_article L'id de l'article dont on souhaite modifier le type
 */
function formulaires_em_changer_type_charger_dist($id_article,$message_erreur='',$redirect=''){
	include_spip('inc/autoriser');

	if((!is_numeric($id_article))
		OR (!autoriser('modifier','article',$id_article))
		OR (!is_array(lire_config('emballe_medias/types/types_dispos')))
	){
		return;
	}

	$valeurs = array();
	$valeurs['em_type'] = sql_getfetsel("em_type","spip_articles","id_article=".intval($id_article));
	if($message_erreur)
		$valeurs['message_erreur'] = filtrer_entites($message_erreur);

	return $valeurs;
}

/**
 *
 * Fonction de vérification du formulaire
 * On vérifie juste si le type a changé depuis l'original
 *
 * @return array Un array de toutes les erreurs
 * @param object $id_article L'id de l'article dont on souhaite modifier le type
 */
function formulaires_em_changer_type_verifier_dist($id_article,$redirect=''){
	$erreurs = array();
	return $erreurs;
}

/**
 *
 * Fonction de traitement du formulaire
 * On modifie le type
 *
 * @return array
 * @param object $id_article L'id de l'article dont on souhaite modifier le type
 */
function formulaires_em_changer_type_traiter_dist($id_article,$redirect=''){
	include_spip('base/abstract_sql');
	$invalider = false;

	$type = _request('type');
	$ancien_type = sql_getfetsel("em_type","spip_articles","id_article=".intval($id_article));

	if($type != $ancien_type){
		sql_updateq("spip_articles",array('em_type'=>$type),"id_article=".intval($id_article));
		$message_ok = _T('emballe_medias:message_type_mis_a_jour');
		if(!test_espace_prive())
			$redirect = parametre_url(self(),'em_type',$type);
		$invalider = true;
	}else
		$message_ok = _T('emballe_medias:message_type_pas_mis_a_jour');

	if($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
	}

	$res['editable'] = true;
	$res['message_ok'] = $message_ok;
	$res['redirect'] = $redirect;

	return $res;
}
?>