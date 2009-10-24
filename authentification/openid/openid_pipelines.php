<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

/**
 * ajouter un champ openID sur le formulaire CVT editer_auteur
 *
 * @param array $flux
 * @return array
 */
function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		$flux['args']['contexte']['openid'] = sql_getfetsel('openid','spip_auteurs','id_auteur='.sql_quote($flux['args']['contexte']['id_auteur']));
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}

/**
 * ajouter l'open_id soumis lors de la soumission du formulaire CVT editer_auteur
 *
 * @param array $flux
 * @return array
 */
function openid_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if ($openid = _request('openid')) {
			include_spip('inc/openid');
			$flux['data']['openid'] = nettoyer_openid($openid);
		}
	}
	return $flux;
}

/**
 * Afficher l'openid sur la fiche de l'auteur
 * @param array $flux 
 */
function openid_afficher_contenu_objet($flux){
	if ($flux['args']['type']=='auteur'
		AND $id_auteur = $flux['args']['id_objet']
		AND $openid = sql_getfetsel('openid','spip_auteurs','id_auteur='.intval($id_auteur))
	){
		$flux['data'] .= propre("<div><img src='".find_in_path('images/login_auth_openid.gif')
			."' alt='"._T('openid:openid')."' width='16' height='16' />"
			. " [->$openid]</div>");

	}

	return $flux;
}

/**
 * Afficher l'openid sur le formulaire de login
 * Utilise uniquement pour spip 2.0.x
 * @param <type> $flux
 * @return <type>
 */
function openid_recuperer_fond($flux) {
	if ($flux['args']['fond']=='formulaires/login'){
		include_spip('inc/openid');
		$flux['data']['texte'] = openid_login_form($flux['data']['texte'],$flux['data']['contexte']);
	}
	/*if ($flux['args']['fond']=='formulaires/inscription'){
		$insc = pipeline('social_inscription_links','');
		$flux['data']['texte'] = str_replace('<form',$insc . '<form',$flux['data']['texte']);
	}*/
	return $flux;
}

?>