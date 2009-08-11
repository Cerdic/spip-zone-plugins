<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip("inc/meta");
include_spip("inc/session");
include_spip("inc/autoriser");
include_spip("base/abstract_sql");


function formulaires_ajouter_auteur_charger_dist($id_article, $_T=array(), $retour=''){
	$valeurs = array('id_article'=>$id_article,'editable'=>true);
	$label_ajouter_auteur = (isset($_T['label_ajouter_auteur']) && $_T['label_ajouter_auteur']) ? $_T['label_ajouter_auteur'] : _T('ajouter_auteur:ajouter_un_auteur');
	$valeurs['_label_ajouter_auteur'] = $label_ajouter_auteur;
	if (!autoriser('modifier','article', $id_article)){
		$valeurs['editable'] = false;
	}
	return $valeurs;

}

function formulaires_ajouter_auteur_verifier_dist($id_article, $_T=array(), $retour=''){
	$erreurs = array();

	$ajouter_id_auteur = _request('ajouter_id_auteur');

	if ($ajouter_id_auteur && ($ajouter_id_auteur = intval($ajouter_id_auteur))){
		$res = sql_select("id_auteur","spip_auteurs_articles","id_article =".intval($id_article)." AND id_auteur=$ajouter_id_auteur");
		if(sql_fetch($res)){
			$erreurs['message_erreur'] = _T('ajouter_auteur:erreur_deja_id_auteur');
		}
	}else{
		$erreurs['message_erreur'] = _T('ajouter_auteur:erreur_pas_id_auteur');
	}
	return $erreurs;
}

function formulaires_ajouter_auteur_traiter_dist($id_article, $_T=array(), $retour=''){
	//recuperer les donnees qui nous interessent
	$ajouter_id_auteur = _request('ajouter_id_auteur');

	include_spip('action/editer_auteurs');
	$ajout = ajouter_auteur_et_rediriger('article', $id_article, $ajouter_id_auteur, '');
	$invalider = true;

	if ($retour) {
		include_spip('inc/headers');
		$res = array('message_ok'=>_T('ajouter_auteur:auteur_ajoute'),
		'redirect'=>parametre_url($retour,'var_mode','calcul'));
	}else{
		$res = array('message_ok'=>_T('ajouter_auteur:auteur_ajoute'), 'editable'=>true);
	}
	return $res;
}
?>
