<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_tradlang_charger($id_tradlang='aucun',$retour='',$lang_orig=''){
	spip_log('on charge','test');
	$valeurs = formulaires_editer_objet_charger('tradlang',$id_tradlang,0,'',$retour,$config_fonc,$row,$hidden);
	spip_log($valeurs,'test');
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
	spip_log('on envoit','test');
	$ret = formulaires_editer_objet_traiter('tradlang',$id_tradlang,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	spip_log($ret,'test');
	return $ret;
}
?>