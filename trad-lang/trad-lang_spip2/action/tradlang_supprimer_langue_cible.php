<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Action permettant de supprimer une langue cible si vide
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_tradlang_supprimer_langue_cible_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)\/(\w+)$,", $arg, $r)) {
		spip_log("action_tradlang_creer_langue_cible $arg pas compris","tradlang");
	}
	$id_tradlang_module = intval($r[1]);
	$lang_cible = $r[2];

	include_spip('inc/autoriser');
	if($lang_cible && intval($id_tradlang_module) && autoriser('modifier','tradlang') && !sql_countsel('spip_tradlang','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible).' AND statut="0K"')){
		sql_delete('spip_tradlang','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible));
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}else{
		spip_log("action_tradlang_supprimer_langue_cible_dist : Module $id_tradlang_module est traduit en $lang_cible","tradlang");
	}
	$redirect = _request('redirect');
	if($redirect){
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>