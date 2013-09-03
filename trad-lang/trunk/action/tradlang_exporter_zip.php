<?php
/**
 * 
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * Action permettant de récupérer un zip des fichiers de langue d'un module
 * 
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_tradlang_exporter_zip_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if (!preg_match(",^(\w+)$,", $arg, $r))
		spip_log("action_tradlang_exporter_langue_dist $arg pas compris","tradlang");
	
	$id_tradlang_module = intval($r[1]);
	
	include_spip('inc/autoriser');
	if(intval($id_tradlang_module) && autoriser('modifier','tradlang') && sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module))){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$langues = sql_select('lang','spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module),'lang');
		$tradlang_sauvegarde_module = charger_fonction('tradlang_sauvegarde_module','inc');
		$fichiers = array();
		$zip = '';
		
		/**
		 * On crée les différents fichiers de langue
		 */
		while($langue = sql_fetch($langues)){
			$fichier = $tradlang_sauvegarde_module($module,$langue['lang']);
			if(file_exists($fichier))
				$fichiers[] = $fichier;
		}
		
		/**
		 * On crée le zip
		 */
		if(count($fichier) > 0){
			$dir_lang = _DIR_VAR.'cache-lang/'.$module.'/';
			if(!is_dir(_DIR_VAR.'cache-lang/'))
				sous_repertoire(_DIR_VAR,'cache-lang');
			$zip = $dir_lang.$module.'_langues.zip';
			
			include_spip('inc/pclzip');
			if(file_exists($zip))
				spip_unlink($zip);
			$contenu_zip = new PclZip($zip);
			$i = 0;

			$erreur = $contenu_zip->add($fichiers,PCLZIP_OPT_REMOVE_ALL_PATH);

			if ($erreur == 0)
				spip_log("$chemin".$contenu_zip->errorInfo(true),"tradlang");      
		}
		
		
		if(file_exists($zip)){
			header('Content-Type: application/zip;');
			header('Content-Length: '.filesize($zip));
			header('Content-Disposition: attachment; filename="'.basename($zip).'"');
			header("Expires: 0");
			header("Cache-Control: no-cache, must-revalidate");
			header("Pragma: no-cache"); 
			readfile($zip);
			die();
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
		$redirect = parametre_url($redirect,'var_zip_crea','ok','&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>