<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Action permettant de récupérer un fichier de langue
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_tradlang_exporter_langue_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(",^(\w+)\/(\w+)$,", $arg, $r)) {
		spip_log("action_tradlang_exporter_langue_dist $arg pas compris","tradlang");
	}
	$id_tradlang_module = intval($r[1]);
	$lang_cible = $r[2];
	include_spip('inc/autoriser');
	if($lang_cible && intval($id_tradlang_module) && autoriser('modifier','tradlang') && sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible))){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$tradlang_sauvegarde_module = charger_fonction('tradlang_sauvegarde_module','inc');
		$fichier = $tradlang_sauvegarde_module($module,$lang_cible);
		if(file_exists($fichier)){
			header('Content-Type: application/x-httpd-php;');
			header('Content-Length: '.filesize($fichier));
			header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache"); 
			readfile($fichier);
		}else{
			include_spip('inc/minipres');
			echo minipres();
		}
	}else{
		include_spip('inc/minipres');
		echo minipres();
	}
	$redirect = _request('redirect');
	if($redirect){
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>