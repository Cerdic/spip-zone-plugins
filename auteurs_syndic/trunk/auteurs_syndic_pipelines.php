<?php
/**
 * Plugin auteurs_syndic
 * Ajouter des auteurs aux sites syndiqués
 * 
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010/2012 - Distribue sous licence GNU/GPL
 * 
 * Les pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * Insertion dans le pipeline post_instertion (SPIP)
 * Ajoute l'auteur en cours à la table spip_auteurs_liens
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte du pipeline modifié
 */
function auteurs_syndic_post_insertion($flux){
	if(isset($flux['args']['table'])
		AND ($flux['args']['table'] == 'spip_syndic')
		AND isset($GLOBALS['visiteur_session']['id_auteur'])
		AND ($GLOBALS['visiteur_session']['id_auteur'] > 0)){
		include_spip('action/editer_liens');
		objet_associer(array('auteur'=>$GLOBALS['visiteur_session']['id_auteur']), array('site'=>$flux['args']['id_objet']));
	}
	return $flux;
}

/**
 * 
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * Ajoute le formulaire de choix d'auteurs sur la page des site
 * 
 * @param array $flux 
 * 		Le contexte du pipeline
 * @return array $flux 
 * 		Le contexte du pipeline modifié
 */
function auteurs_syndic_recuperer_fond($flux){
	if(isset($flux['args']['fond'])
		AND ($flux['args']['fond'] == 'prive/squelettes/contenu/site')){
		$ins = recuperer_fond('prive/squelettes/inclure/editer_auteurs_site',$flux['args']['contexte']);
		if (($p = strpos($flux['data']['texte'],"<!--affiche_milieu-->")) !== false)
			$flux['data']['texte'] = substr_replace($flux['data']['texte'],$ins,$p,0);
	}
	return $flux;
}
?>