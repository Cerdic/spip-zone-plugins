<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action permettant de creer une langue cible depuis la langue mère d'un module
 * 
 * @return 
 */
function action_tradlang_creer_langue_cible_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)$,", $arg, $r)){
		spip_log("action_tradlang_creer_langue_cible $arg pas compris",'tradlang.'._LOG_ERREUR);
		return false;
	}

	$id_tradlang_module = intval($arg);

	include_spip('inc/autoriser');
	$lang_crea = _request('lang_crea');
	if($lang_crea && intval($arg) && autoriser('modifier','tradlang')){
		// Import de la langue mere
		$infos_module = sql_fetsel('*','spip_tradlang_modules','id_tradlang_module='.intval($arg));
		if(!$infos_module)
			spip_log('tradlang_creer_langue_cible : infos_module non existant','tradlang.'._LOG_ERREUR);
		else{
			$ajouter_code_langue = charger_fonction('tradlang_ajouter_code_langue','inc');
			$ajouter_code_langue($infos_module,$lang_crea);
		}
	}else
		spip_log("action_tradlang_creer_langue_cible : Module $module_nom inexistant",'tradlang.'._LOG_ERREUR);

	$redirect = _request('redirect');
	if($redirect){
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>