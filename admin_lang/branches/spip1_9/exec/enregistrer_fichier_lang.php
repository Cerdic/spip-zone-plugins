<?php
// ---------------------------------------------
//  Plugin admin_lang
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
//  dani@rezo.net
// ---------------------------------------------

include_spip('inc/lang_trad'); 
include_spip('inc/headers');



function exec_enregistrer_fichier_lang() { 
	global $connect_statut, $couleur_foncee, $connect_toutes_rubriques, $spip_lang_right; 
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	global $addition_volume, $file_backup_option;

	parametres_admin_lang();

	$new_file_full 			 = $target_file_full; // could be != if needed 


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
	
	
		while (list($item, $trad) = each ($target_table)) {
  
		if ($trad != '') {
			 $target_to_change = ""; 
			 //traitement pour enregistrement ok
	 		 if ($charset == 'utf-8') {  
				 $trad = unicode2charset(utf_8_to_unicode($trad), 'iso-8859-1'); 
			 } 
	 		 $trans = get_html_translation_table(HTML_ENTITIES);
			 $trad = strtr($trad, $trans);
			 //$trad = addslashes($trad); 
				
			$any_line[$ctr] = "'$item' => '$trad$target_to_change',";
			if ($display_debug) {
				print $any_line[$ctr]."<br />";
			}
			$ctr++;
		}
	}
	
	// remove last ,
	$any_line[$ctr - 1] = substr($any_line[$ctr - 1], 0, -1);
	

	// ---------------------------------------------
	//		WRITE RESULTING FILE
	// ---------------------------------------------
	$f = fopen($new_file_full,"w");
	
	// lock it
	flock ($f, LOCK_EX); 
			
	// ---------------------------------------------
	//		write header
	// ---------------------------------------------
	wf($f, "<?php");
	wf($f, "");
	wf($f, "// This is a SPIP language file  --  Ceci est un fichier langue de SPIP nommé $nompage genere le NOW()");
	wf($f, "// langue / language = $target_lang");
	wf($f, "");
	wf($f, "\$GLOBALS[\$GLOBALS['idx_lang']] = array(");
	
	// ---------------------------------------------
	//		write body
	// ---------------------------------------------
	$prev_letter = '';
	for ($i = 0; $i < count($any_line); $i++) {
		$first_letter = substr($any_line[$i], 1,1);
		
		if ($first_letter != $prev_letter) {
			$first_cap_letter = strtoupper($first_letter);
			wf($f, "");
			wf($f, "");
			wf($f, "// $first_cap_letter");
			$prev_letter = $first_letter;
		}
		
		wf($f, $any_line[$i]);
	}
	wf($f, "");
	
	// ---------------------------------------------
	//		write footer
	// ---------------------------------------------
	wf($f, "");
	wf($f, ");");
	wf($f, "");
	wf($f, "?>");
	
	flock ($f, LOCK_UN);
	fclose ($f);

	$mode = 'work'; // end of create mode, switch to work mode
	redirige_par_entete(parametre_url(self(),'exec','traduire_module','&'));

	// ================ end treatment ==========================


}

?>
