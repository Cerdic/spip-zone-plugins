<?php
/**
 * Plugin auteurs_syndic
 * par kent1
 * Les pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Insertion dans le pipeline post_instertion
 * Ajoute l'auteur en cours à la table spip_auteurs_syndic
 * 
 * @param array $flux Le contexte du pipeline
 */
function auteurs_syndic_post_insertion($flux){
	if(($flux['args']['table'] == 'spip_syndic') && ($GLOBALS['visiteur_session']['id_auteur'] > 0)){
		sql_insertq('spip_auteurs_syndic',array('id_auteur'=>$GLOBALS['visiteur_session']['id_auteur'],'id_syndic'=>$flux['args']['id_objet']));
	}
	return $flux;
}
?>