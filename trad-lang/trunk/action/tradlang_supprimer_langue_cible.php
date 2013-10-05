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
	
	if (!preg_match(",^(\w+)\/(\w+)$,", $arg, $r)){
		spip_log("action_tradlang_creer_langue_cible $arg pas compris","tradlang");
		return false;
	}
	$id_tradlang_module = intval($r[1]);
	$lang_cible = $r[2];

	include_spip('inc/autoriser');
	if($lang_cible && intval($id_tradlang_module) && autoriser('modifier','tradlang') && !sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible).' AND statut="0K"')){
		/**
		 * Suppression des versions
		 */
		$tradlangs = sql_allfetsel('id_tradlang','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible));
		$tradlangs_supprimer = array();
		foreach($tradlangs as  $tradlang){
			$tradlangs_supprimer[] = $tradlang['id_tradlang'];
		}
		if(count($tradlangs_supprimer)){
			sql_delete('spip_versions','objet="tradlang" AND '.sql_in('id_objet',$tradlangs_supprimer));
			sql_delete('spip_versions_fragments','objet="tradlang" AND '.sql_in('id_objet',$tradlangs_supprimer));
		}
		/**
		 * Suppression des chaînes de langue
		 */
		sql_delete('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible));
		/**
		 * Suppression des bilans de cette langue
		 */
		sql_delete('spip_tradlangs_bilans','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible));
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}else
		spip_log("action_tradlang_supprimer_langue_cible_dist : Module $id_tradlang_module est traduit en $lang_cible","tradlang");

	$redirect = _request('redirect');
	if($redirect){
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>