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


function exec_gerer_master() { 
				 
	global $connect_statut, $couleur_foncee, $connect_toutes_rubriques, $spip_lang_right; 
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	global $addition_volume, $file_backup_option;

	parametres_admin_lang();

	if(!$module or !@file_exists($master_file_full))
		redirige_par_entete(parametre_url(self(),'exec','admin_lang','&'));

	if ($target_lang and $target_lang != $master_lang) 
			redirige_par_entete(parametre_url(self(),'target_lang',$master_lang,'&'));

	##############""//////////// DEBUT PAGE ///////////////#######################"
	debut_page(_T('adminlang:gerer_master_module').": $module","administration", "langues");

	echo '<br />';
	echo gros_titre(_T('adminlang:gerer_module_langue').": $module"); 
	echo _T('adminlang:fichier_langue_dans_repertoire', array('fichier_lang' => $master_file, 'dir_lang' => $dir_lang)),'<br />';
	
	debut_gauche();
	echo debut_cadre_relief();	
	
	echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>",
					_T('adminlang:module_fichiers_langues'),":</b></div><br>\n";

	echo _T('adminlang:on_gere_le_module'),'<br />';
	echo '<b>',$module,'</b><br />';
	echo _T('adminlang:dans_la_langue');
	$menu_langues_master = menu_langues_trad('master_lang',$master_lang);  //  
	echo  "<div>",$menu_langues_master,"</div>\n";


	echo fin_cadre_relief();

	echo '<br />';

	echo debut_cadre_relief();	

	echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>",
					_T('adminlang:traduire_ce_module'),":</b></div><br>\n";
					
	echo _T('adminlang:on_traduit_a');
	$menu_langues_target = menu_langues_trad('target_lang','', '', '', parametre_url(self(), 'exec', 'traduire_module', '&'));  
	echo  "<div>",$menu_langues_target,"</div><br />\n";

	echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>",
					_T('adminlang:autres_modules_langues'),":</b></div><br>\n";
					
		// choisir le module de langue: 
	echo _T('adminlang:choisir_module'),'<br />';		
//	$url_module = .generer_url_ecrire("ad"&target_lang=$target_lang&mode=work);
	foreach ($modules as $nom_module) {
//		if ($nom_module == $module) echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><b>$nom_module</b></div>";
		if ($nom_module != $module) echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'>
				<a href='".parametre_url(self(), 'module', $nom_module)."'>$nom_module</a></div>";
	}

	if(!in_array('local', $modules)) {
		echo _T('adminlang:creer_module_local'),'<br />';		
		echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'>
						<a href='".parametre_url(self(), 'module', 'local')."'>local</a></div>\n";
	}

	echo _T('adminlang:creer_nouveau_module'),'<br />';		
	print "<form name='newmodule' method='post' action='".generer_url_ecrire("redir_admin_lang")."'>\n";
	print "<input type='hidden' name='mode' value='work'>\n";
	print "<input type='text' size='8' name='module'>\n";
	print "<input type='hidden' name='master_lang' value='".$master_lang."'>\n";
	print "<input type='hidden' name='target_lang' value='".$target_lang."'>\n";
	print "<input type='submit' name='creer' value='"._T('adminlang:autre_module')."'>\n";
	print "</form>\n";
			

	echo fin_cadre_relief();

	debut_droite();
	

	//		FORM, FOR ANY MODULE AND LANG
	// ---------------------------------------------
	//echo "<div style='background-color:pink;'>";
//	print "<span class='arial2'><strong>"._T('adminlang:fichier_modifiables')."</strong> : $bloc_lang_work</span>";
//	print "<p class='arial2'><strong>"._T('adminlang:fichier_a_creer')."</strong> : $bloc_lang_create<br />";
//	print "<em>"._T('adminlang:langue_possible')." 
//	<a href=\"?exec=config_multilang\">"._T('adminlang:panneau_multilingue')."</a></em></p>";
//	print "<p class='arial2'>"._T('adminlang:choix_module')."</p>";

		// ---------------------------------------------
		//		get initial data
		//      include file => current lang table = $GLOBALS[$GLOBALS['idx_lang']]
		//      copy $GLOBALS[$GLOBALS['idx_lang']] in new table to keep it active
		// ---------------------------------------------
	 
		$protect_idx_lang = $GLOBALS['idx_lang'];
		$protect_main_lang_table = $GLOBALS[$GLOBALS['idx_lang']]; 
		$GLOBALS['idx_lang'] = 'i18n' . '_' . $module . '_' . $master_lang; 
		include($master_file_full);
		$master_table = $GLOBALS[$GLOBALS['idx_lang']];
		if(isset($master_table)){ 
			ksort($master_table);
		}
	
		if ($mode == 'work') { 
			if ($display_debug) {
				echo "master_file =$master_lang+ $master_file / target_file =$target_lang+ $target_file<br /><br />"; 
			}
			$GLOBALS['idx_lang'] = 'i18n' . '_' . $module . '_' . $target_lang;
			include($target_file_full);
			$target_table = $GLOBALS[$GLOBALS['idx_lang']];
			if(isset($target_table)){ 
				ksort($target_table);
			}
		} else {
			$target_table = array();
		}
	
		//reset idx_lang in global
		$GLOBALS['idx_lang'] = $protect_idx_lang; //$GLOBALS['idx_lang']='i18n_'.$module.'_'.$lang;
		$GLOBALS[$GLOBALS['idx_lang']] = $protect_main_lang_table;
	
		if ($display_debug) {
			print "RESET PROTECTED : $protect_idx_lang<br />";
		}
	
		if ($display_debug_full) {
			print "<pre>";
			print "<H1>master table</H1>";
			print_r($master_table);
			print "<H1>target table</H1>";
			print_r($target_table);
			print "</pre>";
		}
	
		$table_color = '#CCCCCC'; 
		$index_color = $couleur_foncee;
		$normal_color = '#ffffff';
		$altern_color = '#EEEEEE';
		
		$switch_color = false;
		$action_valider = parametre_url(parametre_url(self(),'exec', 'enregistrer_fichier_lang'), 'target_lang', $master_lang) ;
		print "<form name='form1' method='post' action='$action_valider'>\r";
		
		if (count($master_table) > 0) { // do not display update form if target file is empty
			echo "<table style='border:0px; width:660px;' summary='"._T('adminlang:tableau_des_raccourcis_modifiables')."'>";
					
			echo "<tr>
					<td class='verdana1' ><strong>"._T('adminlang:module_raccourci')."</strong></td>
					<td class='verdana1' ><strong>"._T('adminlang:texte_du_master')."</strong></td>
					<td class='verdana1' ><strong>"._T('adminlang:champ_de_traduction_language')." $target_lang_lib </strong></td>
					</tr>
					";
					
					
			@reset($master_table);
			@reset($target_table);
			$first_letter = '';
			while (list($value_to_change, $master_to_change) = @each ($master_table)) {  
				$bgcolor = ($switch_color) ? $normal_color : $altern_color;  
				$first_letter = substr($value_to_change,0,1);
				
				if ($first_letter != $prev_letter) {
					$first_cap_letter = strtoupper($first_letter); 
					echo "<tr class='verdana3' style='background-color: ".$couleur_foncee."; color:white; font-weight:bold;'> 
 						  <th>".$first_cap_letter."</th> 
					 	  <th>"._T('adminlang:master')."</th> 
					 	  <th style='text-align:right'><a href='#save_lang_file' style='color:white; font-family:verdana;' title='"
							._T('adminlang:allerbasdepage')."'>"._T('adminlang:traduction')."  </a></th>
					 	  </tr>
						  ";
					$prev_letter = $first_letter;
				}
			
				$master_to_change = stripslashes($master_to_change);
				$target_to_change = $target_table["$value_to_change"];
			
				if ($target_to_change == '') {$style_master_2 = "color:darkred; font-weight:bold;";
				} else {$style_master_2 = "color:#000000; font-weight:normal;";}
			 
			   echo "<tr bgcolor='$bgcolor'> 
						<td style='width:20%;  vertical-align:top;' class='verdana2' >
						<strong><:$value_to_change:></strong>
						</td>
						";
				
				print "<td style='".$style_master_2."width:30%;  vertical-align:top;' class='arial2'>\r";	
				//$trans="moïse ou ètais tu à l'évènement?" ;
				//$trans="mo&iuml;se ou &egrave;tais tu &agrave; l'&eacute;v&egrave;nement"; 
				print $master_to_change; 
				print "</td>";
				
				print "<td>";
				//print "<input type='text' name=\"trad_from_file[$value_to_change]\" value=\"$trad_to_change\" size='40'
				//style=\"font-size:10px; background:$bgcolor\">";
				$target_to_change = stripslashes($target_to_change); 
				if ($connect_statut =='0minirezo'  AND $connect_toutes_rubriques) {
					print "
							<textarea name='target_table[$value_to_change]' cols='30' rows='1' wrap='soft'>$target_to_change</textarea>
							";
				} else {
					print "<span class='verdana2'>$target_to_change</span>"; 
				}
				print "</td>\r"; 
				print "</tr>\r";
				$switch_color = !$switch_color;
			}
		
		
			print "<tr style='background-color: ".$couleur_foncee."; '>\r"; 
			print "<td colspan='3' style='text-align:right'>";
			if ($connect_statut =='0minirezo'  AND $connect_toutes_rubriques) {
				print  "<input id='save_lang_file' type='submit' name='submit' value=\""._T('adminlang:validation')."\">";	
			}
			//print  "<a href=\"javascript:window.close();\" class=\"boutonBOform\">Fermer cette fen&ecirc;tre</a>";
			print "</td></tr></table>";
		
		} // end master file empty
		print "<!-- stop here -->"; 
		if ($connect_statut =='0minirezo' AND $connect_toutes_rubriques) {//			if ($master_lang == $target_lang) {
				echo "<table style='width:660px; border:0px; vertical-align:top; margin-top:1em; ' summary='"
						._T('adminlang:tableau_ajouter_raccourcis')."'>\r";
			
				print "<tr class='verdana3' style='background-color:".$couleur_foncee."; color:white; padding: 3px; font-weight:bold;'>\r";
				print "<th colspan='2' style='text-align:center;'>"._T('adminlang:ajouter')."</th>";
				print "</tr>";
			
				print "<tr class='verdana2' style='background-color:".$couleur_foncee."; color:white; padding: 3px; font-weight:bold;'>\r";
				print "<th >"._T('adminlang:aide_saisie_variable')."</th>";
				print "<th>"._T('adminlang:aide_saisie_texte')."</th>";
				print "</tr>";
			 
				for ($i = 0; $i < $addition_volume; $i++) {
					//'#eeeeee','white'
					$bgcolor = ($switch_color) ? $normal_color : $altern_color; 
					print "<tr style='background-color:".$bgcolor."'>\r";
							
					print "
						<td class='verdana2'><input type='text' name=\"new_value[$i]\" value='' size='40'>
						</td>";	
				
					print "<td>\r";
					print "<textarea name=\"new_trad[$i]\" cols='45' rows='1' wrap='soft'></textarea>
							</td>\r"; 
					$switch_color = !$switch_color;
					print "</tr>";
				} 
		
				print "<tr style='background-color: ".$couleur_foncee."; '>
						<td colspan='2' style='text-align:right' >";
		
				print "<input type='hidden' name='module' value='".$module."' >\n";
				print "<input type='hidden' name='mode' value='work'>\n";
				print "<input type='hidden' name='target_lang' value='".$target_lang."'>\n";
				print "<input type='hidden' name='master_lang' value='".$master_lang."'>\n";
				print  "<input id='save_lang_file' type='submit' name='submit' value="._T('adminlang:validation').">";	
				//print  "<a href=\"javascript:window.close();\" class=\"boutonBOform\">Fermer cette fen&ecirc;tre</a>";
				print "<!-- what are you doing here? -->";
				print "</td></tr></table>";
//			}
		}

		print "</form>\r";

	fin_page();


}

?>
