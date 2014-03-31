<?php
// ---------------------------------------------
//  Plugin admin_lang
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
//  dani@rezo.net
// ---------------------------------------------

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/lang_trad'); 
include_spip('inc/headers');
include_spip('inc/charsets');
include_spip('inc/enregistrer_fichier_lang');

function exec_enregistrer_fichier_lang() { 
	global $connect_statut, $couleur_foncee, $connect_toutes_rubriques, $spip_lang_right; 
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	global $addition_volume, $file_backup_option;

	parametres_admin_lang();

	if(!$module or !$submit or !@file_exists($master_file_full) or !@file_exists($target_file_full))
		redirige_par_entete(parametre_url(self(),'exec','admin_lang','&'));

	// ---------------------------------------------
	//		TREAT
	// ---------------------------------------------
	$target_table = $_POST['target_table'];
	
	if ($display_debug) {
			print "<H1>TREAT : Enregistrement du fichier langue : $target_file</H1><br />";
	}
	
	if ($mode == 'work') {
			// ---------------------------------------------
			//		backup init spip files if needed
			// ---------------------------------------------
			if(@!is_readable($target_file_full_backup) and $file_backup_option) {
				copy ($target_file_full, $target_file_full_backup);
				// print "<p class='arial2'>" . _T('adminlang:sauvegarde'). " $target_file" . "_bak</p>";	
			}
	}
		// ---------------------------------------------
		//		GET ADDITION
		// ---------------------------------------------
	
	$new_value = $_POST['new_value'];
	$new_trad = $_POST['new_trad'];
		
	if($new_value!=''){
			for ($i = 0; $i < $addition_volume; $i++) {
				/* ici c'est meilleur! revision du 18 juin*/
	
				$new_value_item = $new_value[$i]; 
				$new_trad_item = $new_trad[$i];
				/*
				echo"<span style='font-size:2em; color:red;'>addition!!</span>";
				echo "$new_value[$i]";
				echo "$new_trad[$i]";
				*/
	 
				if ($new_value_item != '' and $new_trad_item != '') {
					//print "nvi : $new_value_item / nti $new_trad_item<br />"; 
					$target_table[$new_value_item]=$new_trad_item;
				
				}
			}

			if(isset($target_table)){ 
				ksort($target_table); 
			}
		}
	
		// ---------------------------------------------
		//		PREPARE AND FORMAT
		//      empty $trad <=> delete from file
		// ---------------------------------------------
		
	$ctr = 0;
	

	if ($display_debug_full) {
			print "<pre>";
			print "<H1>target table</H1>";
			print_r($target_table);
			print "</pre><hr>";
		}
	
	
	foreach($target_table as $item => $trad) {
  
		if (empty($trad)) 
			unset($target_table[$item]);
		else {
			 //traitement pour enregistrement ok
	 		 if ($charset == 'utf-8') {  
				 $trad = unicode2charset(utf_8_to_unicode($trad), 'iso-8859-1'); 
			 } 
	 		 $trans = get_html_translation_table(HTML_ENTITIES);
			 $trad = strtr($trad, $trans);
			 $target_table[$item] = $trad;
		}
	}

	$dir_lang = _request('dir_lang');
	if (!is_dir($dir_lang)) 
		// soit on prend le sous repertoire lang du plugin admin_lang  
		$dir_lang = _DIR_PLUGIN_ADMIN_LANG.'lang';
	enregister_fichier_lang($dir_lang, $target_lang, _request('module'), $target_table, $nompage);
	redirige_par_entete(parametre_url(self(),'exec','traduire_module','&'));

	// ================ end treatment ==========================
}

?>
