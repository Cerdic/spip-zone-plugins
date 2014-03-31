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

function exec_creer_fichier_lang() {
	global $connect_statut, $couleur_foncee, $spip_lang_right; 
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	
	parametres_admin_lang();	

	$fichier_lang = urldecode(_request('fichier_lang'));
	$err = '';
	if($fichier_lang and $fichier_lang != '') {
		if (!is_dir(dirname($fichier_lang)))
			$err = _T('admin:lang:dossier_existe_pas', array('dossier', dirname($fichier_lang)));
		if (last_folder_name($fichier_lang) != 'lang')
			$err = _T('dossier_non_lang', array('dossier', dirname($fichier_lang)));
		if ($_POST) 
			if($_POST['creer']) {
				$fd = fopen("$fichier_lang","a");	
				fclose($fd);
				redirige_par_entete(parametre_url(parametre_url(self(),'exec','admin_lang','&'),'fichier_lang', '', '&'));
			} else {
				redirige_par_entete(parametre_url(parametre_url(self(),'exec','admin_lang','&'),'module','','&'));
			}
	}

//	debut_page(_T('adminlang:creer_master_module'),"administration", "langues");
	$commencer_page = charger_fonction('commencer_page', 'inc');
   echo $commencer_page(_T('adminlang:creer_master_module'),"administration", "langues", "");

	echo '<br /><br />';
	echo gros_titre(_T('adminlang:creer_fichier_lang'), "",false); 
	echo '<br />';
	
	if ($err=='') {
//		echo "<form name='creer_fichier' method='post' action='",parametre_url(self(),'creer','oui','&'),"'>\n";
		echo "<form name='creer_fichier' method='post' action=''>\n";
		echo	'<label>', _T('adminlang:fichier_existe_pas', array('fichier_lang' => $fichier_lang, 'dir_lang' => $dir_lang)), '<br />';
		echo _T('adminlang:question_creer_fichier'), '<br /></label>';
		echo "<input type='submit' name='creer' value='"._T('adminlang:creer_fichier')."'>\n";
		echo "<input type='submit' name='annuler' value='"._T('adminlang:annuler')."'>\n";
		echo "</form>\n";
	} else {
		echo $err,'<br /><br />';
		echo '<a href="',generer_url_ecrire('admin_lang'),'">Volver a la administración de traducciones</a>';
	}
	fin_page();
}

?>
