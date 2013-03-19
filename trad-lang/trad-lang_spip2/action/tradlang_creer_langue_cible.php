<?php
/**
 * Action permettant de creer une langue cible depuis la langue mère d'un module
 * 
 * @return 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_tradlang_creer_langue_cible_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)$,", $arg, $r))
		spip_log("action_tradlang_creer_langue_cible $arg pas compris");

	$id_tradlang_module = intval($arg);

	include_spip('inc/autoriser');
	$lang_crea = _request('lang_crea');
	if($lang_crea && $id_tradlang_module && autoriser('modifier','tradlang')){
		// Import de la langue mere
		$infos_module = sql_fetsel('*','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		if(!$infos_module)
			spip_log('tradlang_creer_langue_cible : infos_module non existant','tradlang');
		else{
			$ajouter_code_langue = charger_fonction('tradlang_ajouter_code_langue','inc');
			$ajouter_code_langue($infos_module,$lang_crea);
		}
		/**
		 * Le cache est invalidé dans $ajouter_code_langue
		 */
	}else
		spip_log("action_tradlang_creer_langue_cible : Module $module_nom inexistant","tradlang");

	$redirect = _request('redirect');
	if($redirect){
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>