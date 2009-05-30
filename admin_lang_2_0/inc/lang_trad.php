<?php

function parametres_admin_lang() {
	global $display_debug;
	global $all_lang, $charset, $master_lang, $target_lang, $dir_lang;
	global $module, $modules, $mode, $submit;
	global $master_file, $target_file, $target_file_full, $target_file_full_backup, $master_file_full;
	global $addition_volume, $file_backup_option;
	// ---- dev options --------------
	$display_debug 		= false;			// to debug
//	$display_debug 		= true;			// to debug

	// ---- user options -------------
	$addition_volume 		= 5;					// nb field for add
	$file_backup_option	= true;			// to backup init lang files

	$all_lang = explode(',', $GLOBALS['meta']['langues_multilingue']);
	$charset = $GLOBALS['meta']["charset"];

	$mode = _request('mode');
	if(!isset($mode))
		$mode="work";
	$submit = _request('submit');
	
	// soit la langue source est bien définie dans la requête (URL ou POST) 
	$master_lang = _request('master_lang');
	if (!isset($master_lang) or !in_array($master_lang, $all_lang)) 
		// soit on prend la langue principale du site  
		$master_lang =  $GLOBALS['meta']['langue_site'];
			

	// soit la langue destination est bien définie dans la requête (URL ou POST) 
	$target_lang = _request('target_lang');
	if (!isset($target_lang) or !in_array($master_lang, $all_lang)) 
		// soit on prend la langue de l'espace privé  
		// $target_lang = $GLOBALS['auteur_session']['lang'];
		$target_lang = utiliser_langue_visiteur();

	// soit le répertoire de modules de langue est défini dans la requête (URL ou POST) 
	$dir_lang = _request('dir_lang');
	if (!is_dir($dir_lang)) 
		// soit on prend le sous répertoire lang du plugin admin_lang  
		$dir_lang = _DIR_PLUGIN_ADMIN_LANG.'lang/';
	if($display_debug) {
		echo "Dir langue: ".$dir_lang."  ";
		echo "Master lang: ".$master_lang."  ";
		echo "Target lang: ".$target_lang."  ";
		echo "auteur_sess lang: ".$GLOBALS['auteur_session']['lang'];
		echo "   utiliser_langue_visiteur()".utiliser_langue_visiteur();
	}
	// si le modules est défini dans la requête (URL ou POST) on l'extrait 
	$module = _request('module');
	// sinon, on proposera de le choisir ou de le créer. 

	// On extrait un array des modules présents dans le répertoire de langue 
	$modules = array();
	$fichiers = preg_files($dir_lang.'[a-z_]+\.php[3]?$');
	// echo $dir_lang;
	// print_r($fichiers);
	
	foreach ($fichiers as $fichier) {
		if (preg_match(',/([a-z]+)_([a-z_]+)\.php[3]?$,', $fichier, $r))
			$modules[$r[1]] ++;
	} 
	$modules = array_keys($modules); 

	if($display_debug) {
		echo 'Module: ', $module;
		print_r($modules);
	}

	// le fichier de langue (si on a défini un  module) 
	if (isset($module) and $module != '') {
		$master_file =  $module . "_" . "$master_lang.php";
		$target_file =  $module . "_" . "$target_lang.php";	 
		$target_file_full 		 = $dir_lang.$target_file;
		$target_file_full_backup =  $target_file_full . '_bak';
		$master_file_full 		 = $dir_lang.$master_file; 
	}

}

function menu_langues_trad($nom_select = 'target_lang', $default = '', $texte = '', $herit = '', $lien='') {
        global $couleur_foncee, $connect_id_auteur ,$master_lang, $target_lang;

//        $ret = liste_options_langues($nom_select, $default =='' ? $nom_select : $default , $herit);
        $ret = liste_options_langues('changer_lang', $default =='' ? $nom_select : $default , $herit);

        if (!$ret) return  traduire_nom_langue("${$nom_select}");

        if (!$couleur_foncee) $couleur_foncee = '#044476';

        if (!$lien)
                $lien = self();

	$script = _request('exec');
//        $lien = parametre_url($lien, 'exec', 'redir_'.$script);
        $lien = parametre_url($lien, $nom_select, '');
        $lien = parametre_url($lien, 'url', '');
        $cible = '';

	$module = _request('module');
	$mode = _request('mode');
	//$target_lang = _request('target_lang');
	// $master_lang = _request('master_lang');
	$params = "";
	if ($master_lang != "" and $nom_select  != 'master_lang') 
		$params .= "<input type='hidden' name='master_lang' value='".$master_lang."' >\n";
	if ($target_lang != "" and $nom_select  != 'target_lang') 
		$params .= "<input type='hidden' name='target_lang' value='".$target_lang."' >\n";
	if ($mode != "" ) 
		$params .= "<input type='hidden' name='mode' value='".$mode."' >\n";
	if ($module != "" ) 
		$params .= "<input type='hidden' name='module' value='".$module."' >\n";

        // attention, en Ajax ce select doit etre suivi de
        // <span><input type='submit'
        $change = ($lien === 'ajax')
        ? "\nonchange=\"this.nextSibling.firstChild.style.visibility='visible';\""
        : ("\nonchange=\"document.location.href='"
           . parametre_url($lien, 'url', str_replace('&amp;', '&', $cible))
           ."&amp;$nom_select='+this.options[this.selectedIndex].value\"");

        $ret = $texte
          . "<select name='$nom_select' "
          . (_DIR_RESTREINT ?
             ("class='forml' style='vertical-align: top; max-height: 24px; margin-bottom: 5px; width: 120px;'") :
             (($nom_select == 'var_lang_ecrire')  ?
              ("class='verdana1' style='background-color: " . $couleur_foncee
               . "; max-height: 24px; border: 1px solid white; color: white; width: 100px;'") :
              "class='fondl'"))
          . $change
          . ">\n"
          . $ret
          . "</select>";
        if ($lien === 'ajax') return $ret;

        $ret .= "<noscript><div><input type='submit' class='fondo' value='". _T('bouton_changer')."' /></div></noscript>";

        return "\n<form action='$lien' method='post' style='margin:0px; padding:0px;'>\n"
	  . $params
	  . "<div>"
          . (!$cible ? '' : "<input type='hidden' name='url' value='$cible' />")
          . $ret
          . "</div></form>\n";
}
?>
