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
	if (!preg_match(",^([0-9]+)\/(\w+)?(\/?(\w*))?(\/?(\w*))$,", $arg, $r))
		spip_log("action_tradlang_exporter_langue_dist $arg pas compris","tradlang");

	$id_tradlang_module = intval($r[1]);
	$lang_cible = $r[2];
	$type = $r[4] ? $r[4] : false;
	$tout = ($r[6] == 'non') ? false : true;
	if($lang_cible && intval($id_tradlang_module) && sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($lang_cible))){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_tradlang_module));
		$tradlang_sauvegarde_module = charger_fonction('tradlang_sauvegarde_module','inc');
		$fichier = $tradlang_sauvegarde_module($module,$lang_cible,false,$type,$tout);
		if(file_exists($fichier)){

			// supprimer et vider les buffers qui posent des problemes de memory limit
			// http://www.php.net/manual/en/function.readfile.php#81032
			// Copie du plugin acces restreint action/api_docrestreint.php
			@ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure
			@ini_set("output_buffering","off");
			@ini_set('implicit_flush', 1);
			@ob_implicit_flush(1);
			while ($level--){
				@ob_end_clean();
			}

			switch($type){
				case 'po':
					header('Content-Type: application/x-gettext;');
				break;
				default:
					header('Content-Type: application/x-httpd-php;');
			}
			header('Content-Length: '.filesize($fichier));
			header('Content-Transfer-Encoding: binary'); 
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header('Content-Disposition: attachment; filename="'.basename($fichier).'"');
			readfile($fichier);
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
		$redirect = parametre_url($redirect,'var_lang_crea',$lang_crea,'&');
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}
}
?>