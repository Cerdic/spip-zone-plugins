<?php
/**
 * shortcut_url
 *
 * @plugin     shortcut_url
 * @copyright  2015
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\formulaires\shortcut_url
 */

/**
 * Gestion du formulaire de shortcut_url des sites 
 *
 * @package SPIP\Formulaires
**/
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Environnement du formulaire
**/
function formulaires_shortcut_url_charger_dist($id_shortcut_url='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){

	$valeurs = array();
	$req = sql_fetsel('*', 'spip_shortcut_urls', 'id_shortcut_url=' . intval($id_shortcut_url));

	if($req) {
		foreach ($req as $cle => $valeur) {
			$valeurs["$cle"] = $valeur;
		}
	}

	if(_request('id_shortcut_url_existe'))
		$valeurs['id_shortcut_url_existe'] = _request('id_shortcut_url_existe');

	if($url = _request('url')){
		$valeurs['url'] = $url;
	}
	if($titre = _request('titre')){
		$valeurs['titre'] = $titre;
	}

	return $valeurs;
	
}

/**
 * Vérifications du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Tableau des erreurs
**/
function formulaires_shortcut_url_verifier_dist($id_shortcut_url='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){

	$erreurs = array();
	if (!$url = _request('url'))
		$erreurs['url'] = _T("info_obligatoire");
	// Check si il existe le http://
	else{
		$parsed = parse_url($url );
		if (filter_var($url, FILTER_VALIDATE_URL) === false) {
			$erreurs['url'] = _T("shortcut_url:erreur_url_invalide");
		}
		else{
			// On supprime ?var_mode=recalcul et autres var_mode (cf traiter aussi)
			$url = parametre_url($url,'var_mode','');
			// Check si l'URL existe deja
			if ($url = sql_getfetsel('id_shortcut_url','spip_shortcut_urls', 'url=' . sql_quote($url)) && $id_shortcut_url=="oui") {
				set_request('id_shortcut_url_existe',$url);
				$erreurs['url'] = _T("shortcut_url:erreur_url_exist");
			}
		}
	}
	// On vérifie que l'URL raccourcis n'existe pas
	if(_request('titre')) {
		$titre = sql_getfetsel('id_shortcut_url', 'spip_shortcut_urls', 'titre=' . sql_quote(_request('titre')));
		if($titre){
			set_request('id_shortcut_url_existe',$titre);
			$erreurs['titre'] = _T("shortcut_url:erreur_url_raccourcis_exist");
		}

	}

	return $erreurs;

}

/**
 * Traitement du formulaire de configuration du shortcut_url
 *
 * @return array
 *     Retours du traitement
**/
function formulaires_shortcut_url_traiter_dist($id_shortcut_url='new', $objet='', $id_objet='', $retour='', $ajaxload='oui', $options=''){
	include_spip('inc/distant');
	$recup = recuperer_page(_request('url'), true);
	if (preg_match(',<title[^>]*>(.*),i', $recup, $regs))
		$result['nom_site'] = filtrer_entites(supprimer_tags(preg_replace(',</title>.*,i', '', $regs[1])));

	$set = array();
	$set['id_shortcut_url'] = $id_shortcut_url;
	if(_request('titre'))
		$set['titre'] = _request('titre');
	else
		$set['titre'] = generer_chaine_aleatoire();
	$set['description'] = $result['nom_site'];
	// On supprime ?var_mode=recalcul et autres var_mode
	$set['url'] = parametre_url(_request('url'),'var_mode','');
	$set['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$set['date_modif'] = date('Y-m-d H:i:s');
	$set['maj'] = date('Y-m-d H:i:s');

	if($id_shortcut_url == 'oui') {
		$set['id_shortcut_url'] = sql_insertq('spip_shortcut_urls', $set);
		// Insertion de l'auteur à l'arrache
		$auteur = sql_insertq('spip_auteurs_liens', array('id_auteur' => $GLOBALS['visiteur_session']['id_auteur'], 'id_objet' => $set['id_shortcut_url'], 'objet' => 'shortcut_url'));
		$res = array('redirect' => self());
	} else {
		sql_updateq('spip_shortcut_urls', $set, 'id_shortcut_url=' . intval($id_shortcut_url));
		$res = array('redirect' => self(), 'id_shortcut_url' => $id_shortcut_url);
	}

	return array('editable' => false, 'message_ok'=>_T('config_info_enregistree'), 'redirect'=>$res);

}

?>