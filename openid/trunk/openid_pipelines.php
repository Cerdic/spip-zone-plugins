<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2009 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * ajouter un champ openID sur le formulaire CVT editer_auteur
 *
 * @param array $flux
 * @return array
 */
function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<(div|li) class=["\'][^"\']*editer_email(.*?)</\\2>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}

/**
 * Ajouter la valeur openID dans la liste des champs de la fiche auteur
 *
 * @param array $flux
 */
function openid_formulaire_charger($flux){
	// si le charger a renvoye false ou une chaine, ne rien faire
	if (is_array($flux['data'])){
		if ($flux['args']['form']=='editer_auteur'){
			$flux['data']['openid'] = ''; // un champ de saisie openid !
			if ($id_auteur = intval($flux['data']['id_auteur']))
				$flux['data']['openid'] = sql_getfetsel('openid','spip_auteurs','id_auteur='.intval($id_auteur));
		}
		if ($flux['args']['form']=='inscription'){
			$flux['data']['_forcer_request'] = true; // forcer la prise en compte du post
			$flux['data']['url_openid'] = ''; // un champ de saisie openid !
			$flux['data']['openid'] = ''; // une url openid a se passer en hidden
			if ($erreur = _request('var_erreur'))
				$flux['data']['message_erreur'] = _request('var_erreur');
			elseif(_request('openid') AND (!_request('nom_inscription') OR !_request('mail_inscription')))
				$flux['data']['message_erreur'] = _T('openid:erreur_openid_info_manquantes');
		}
	}
	return $flux;
}


/**
 * Verifier la saisie de l'url openID sur la fiche auteur
 *
 * @param array $flux
 */
function openid_formulaire_verifier($flux){
	if ($flux['args']['form']=='editer_auteur'){
		if ($openid = _request('openid')){
			include_spip('inc/openid');
			$openid = nettoyer_openid($openid);
			if (!verifier_openid($openid))
				$flux['data']['openid']=_T('openid:erreur_openid');
		}
	}
	if ($flux['args']['form']=='inscription'){
		if ($idurl = _request('url_openid')){
			include_spip('inc/openid');
			if (!is_openid($idurl)
				OR !$idurl = nettoyer_openid($idurl)
				OR !verifier_openid($idurl))
				$flux['data']['url_openid']=_T('openid:erreur_openid');
			else {
				// openid valide, il faut renvoyer vers le fournisseur pour identification
				// et recup au retour du nom et de l'email
				$retour = openid_url_retour_insc($idurl,self());
				// lancer l'identification chez openid
				$erreur = demander_authentification_openid($idurl, $retour);
				// si on arrive ici : erreur
				$flux['data']['url_openid']=$erreur;
			}
		}
	}
	return $flux;
}



/**
 * ajouter l'open_id soumis lors de la soumission du formulaire CVT editer_auteur
 * et lors de l'update d'un auteur a l'inscription en 2.1
 * 
 * @param array $flux
 * @return array
 */
function openid_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if (!is_null($openid = _request('openid'))) {
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
		$flux['data'] .= propre("<div class='champ contenu_openid'><img src='".find_in_path('images/openid-16.png')
			."' alt='"._T('openid:openid')."' width='16' height='16' />"
			. " [->$openid]</div>");

	}

	return $flux;
}


?>
