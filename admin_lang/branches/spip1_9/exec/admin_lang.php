<?php
// ---------------------------------------------
//  Plugin admin_lang
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
//  dani@rezo.net
// ---------------------------------------------

include_spip('inc/presentation');
include_spip('inc/lang');
include_spip('inc/utils');
include_spip('inc/headers');
include_spip('inc/lang_trad'); 



if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_admin_lang() { 
	global $connect_statut, $couleur_foncee, $spip_lang_right; 
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	
	parametres_admin_lang();
	
	if ($display_debug) echo "Module ", $module, '<br />';
	 
	if ($module and $module != '') {
		if ($display_debug) echo "le module est la.<br />";
		if(!@file_exists($master_file_full)) 
			redirige_par_entete(parametre_url(parametre_url(self(),'exec','creer_fichier_lang','&'), 'fichier_lang', $master_file_full, '&' ));
		if($master_lang == $target_lang) {
			if ($display_debug) echo "et les langues sont les memes <br />";
			redirige_par_entete(parametre_url(self(),'exec','gerer_master','&'));
		}
		if(!@file_exists($target_file_full)) 
			redirige_par_entete(parametre_url(parametre_url(self(),'exec','creer_fichier_lang','&'), 'fichier_lang', $target_file_full, '&' ));
		else 
			redirige_par_entete(parametre_url(self(),'exec','traduire_module','&'));
	}
		


	##############""//////////// DEBUT PAGE ///////////////#######################"
	debut_page(_T('adminlang:module_fichier_langue'),"administration", "langues");
	
	if ($display_debug) {
		echo $master_lang, $target_lang, '<br />';
		echo $dir_lang;
	}

	// ---------------------------------------------
	//		display bloc gauche (module)
	// ---------------------------------------------
	echo gros_titre(_T('adminlang:module_fichier_langue')); 

	debut_gauche();
		echo debut_cadre_relief();	
	
		echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>",
						_T('adminlang:module_fichiers_langues'),":</b></div><br>\n";

		echo _T('adminlang:on_traduit_le_module'),'<br />';
		echo '<b>',$module,'</b><br />';
		echo _T('adminlang:on_traduit_de');
	$menu_langues_master = menu_langues_trad('master_lang',$master_lang);  //  ,'','',generer_url_ecrire($redir_nompage));
	echo  "<div>",$menu_langues_master,"</div>\n";

//			echo "<span style='color:red; font-size:1.5em;'>", traduire_nom_langue($target_lang), "</span>";

	echo _T('adminlang:on_traduit_a');
	$menu_langues_target = menu_langues_trad('target_lang',$target_lang);  //  ,'','',generer_url_ecrire($redir_nompage));
	echo  "<div>",$menu_langues_target,"</div>\n";


	echo fin_cadre_relief();

	debut_droite();

	// choisir le module de langue: 
	echo _T('adminlang:choisir_module'),'<br />';
//	$url_module = .generer_url_ecrire("ad"&target_lang=$target_lang&mode=work);
	foreach ($modules as $nom_module) {
// 		if ($nom_module == $module) echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><b>$nom_module</b></div>";
		if ($nom_module != $module)  echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'>
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
			

				 
	fin_page();
}

?>
