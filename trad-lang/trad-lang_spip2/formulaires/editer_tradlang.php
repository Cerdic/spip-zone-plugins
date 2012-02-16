<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_tradlang_charger($id_tradlang='aucun',$retour='',$lang_orig=''){
	$valeurs = formulaires_editer_objet_charger('tradlang',$id_tradlang,0,'',$retour,$config_fonc,$row,$hidden);
	if (!intval($id_tradlang)) {
		$valeurs['editable'] = false;
		$valeurs['message_erreur'] = _T('tradlang:erreur_id_tradlang_numerique');
	}
	/**
	 * Si on ne donne pas de langue original, on va chercher la langue mère
	 */
	$valeurs['lang_mere'] = sql_getfetsel('lang_mere','spip_tradlang_modules','module='.sql_quote($valeurs['module']));
	if(!$lang_orig){
		$valeurs['lang_orig'] = $valeurs['lang_mere'];
	}else{
		$valeurs['lang_orig'] = $lang_orig;
	}
	return $valeurs;
}

function formulaires_editer_tradlang_verifier($id_tradlang='aucun',$retour='',$lang_orig=''){
	$erreurs = formulaires_editer_objet_verifier('tradlang',0,array('str','statut'));
	return $erreurs;
}

function formulaires_editer_tradlang_traiter($id_tradlang='aucun',$retour='',$lang_orig=''){
	$res = formulaires_editer_objet_traiter('tradlang',$id_tradlang,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	if(!test_espace_prive()){
		$tradlang = sql_fetsel('*','spip_tradlangs','id_tradlang='.intval($id_tradlang));
		$module = sql_fetsel('module,nom_mod,lang_mere','spip_tradlang_modules','id_tradlang_module='.intval($tradlang['id_tradlang_module']));
		$lang_orig = $lang_orig ? $lang_orig:$module['lang_mere'];
		$url_module = parametre_url(parametre_url(generer_url_entite($tradlang['id_tradlang_module'],'tradlang_module'),'lang_orig',$lang_orig),'lang_cible',$tradlang['lang']);
		$res['redirect'] = '';
		if($id_tradlang_suivant = sql_getfetsel('id_tradlang','spip_tradlangs','id_tradlang_module='.intval($tradlang['id_tradlang_module']).' AND lang='.sql_quote($tradlang['lang']).' AND statut != "OK" AND id_tradlang > '.intval($id_tradlang))){
			$url_suivant = 	parametre_url(parametre_url(generer_url_entite($id_tradlang_suivant,'tradlang'),'lang_orig',$lang_orig),'lang_cible',$tradlang['lang']);
		}else if($id_tradlang_suivant = sql_getfetsel('id_tradlang','spip_tradlangs','id_tradlang_module='.intval($tradlang['id_tradlang_module']).' AND statut != "OK" AND lang='.sql_quote($tradlang['lang']))){
			$url_suivant = 	parametre_url(parametre_url(generer_url_entite($id_tradlang_suivant,'tradlang'),'lang_orig',$lang_orig),'lang_cible',$tradlang['lang']);
		}
		$res['message_ok'] .= '<br />';
		if(isset($url_suivant)){
			$res['message_ok'] .= '<a href="'.$url_suivant.'"">'._T('tradlang:lien_traduire_suivant_str_module',array('module'=>$module['nom_mod'])).'</a>';
		}else{
			$res['message_ok'] .= _T('tradlang:info_module_traduit_pc',array('pc'=>'100'));
		}
		$res['message_ok'] .= '<br />';
		$res['message_ok'] .= '<a href="'.$url_module.'"">'._T('tradlang:lien_retour_module',array('module'=>$module['nom_mod'])).'</a>';
		$res['editable'] = true;
	}
	return $res;
}
?>