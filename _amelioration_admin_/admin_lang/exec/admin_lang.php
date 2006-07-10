<?php
// ---------------------------------------------
//  Plugin admin_lang
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
// ---------------------------------------------

#Accès interface privé de spip, configuration->gestion des langues->gestion des fichiers de langues

#Ce plugin permet de gérer les traductions par ecriture d'un fichier de langue
#et si ce fichier n'existe pas, de le créer dans un répertoire lang et uniquement dans des chemins proposés

$time_start = getmicrotime();

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/config'); 
include_spip('inc/lang');
include_spip('inc/plugin'); //?
 

function exec_admin_lang() { 
 global $connect_statut, $connect_toutes_rubriques, $couleur_foncee, $spip_lang_right, $changer_config;

lire_metas(); 
//détermine les filtres d'affichage et d'enregistrement!
$nompage="admin_lang";
$charset = $GLOBALS['meta']["charset"];
$master_lang =  $GLOBALS['meta']['langue_site'];
$target_lang = _request('target_lang');
$module = _request('module');

	if (!isset($module)){
	$default = 'local'; 
	$module = $default;
	}
	
$mode = _request('mode');
		if(!isset($mode))
		$mode="work";
$submit = _request('submit');
$langues = $GLOBALS['codes_langues'];
$lang_file_root = '/lang'; 

 
// ---- user options -------------
$addition_volume 	= 5;		// nb field for add
$file_backup_option = true;		// to backup init lang files

// ---- dev options --------------
$display_debug 		= false;	// to debug
$display_debug_full = false;	// to debug deeper
$writedb 			= false;	// to write file or not
 
// ---------------------------------------------
	//		get possible lang
	// ---------------------------------------------
	//init_codes_langues(); 
	$lang_install = explode(',', $GLOBALS['all_langs']);
	$lang_author = explode(',', lire_meta('langues_multilingue')); 
	/*if ($display_debug) {
		 print "<br /><br />INSTALLED<br />";
		 print_r($lang_install);
		
		 print "<br /><br />AUTHORIZED<br />";
		 print_r($lang_author);
		
		 print count($lang_author); 
		
	}  */
	//if ((!in_array($target_lang, $lang_author))OR($target_lang ==''))//pourquoi interdire une langue?
	if ($target_lang =='')
	$target_lang="$master_lang"; //on prend la langue principale du site
 
	/* on ne veut pas des fichiers spip*/
	##### a revoir : des repertoires lang, il peut y en avoir plusieurs
	$modules = array();
	$fichiers = preg_files(repertoire_lang().'[a-z_]+\.php[3]?$');
	foreach ($fichiers as $fichier) {
		if (preg_match(',/([a-z]+)_([a-z_]+)\.php[3]?$,', $fichier, $r))
			$modules[$r[1]] ++;
	} 
	$modules = array_keys($modules); 
	//print_r ($modules);
	
	
	$module = _request('module');
	if (!isset($module)){
	$default = 'local'; //public ou autre?
	$module = $default;
	}	
//il faudrait aussi le charset de la langue!
//$charset_lang =  $GLOBALS['meta']['langue_site']["charset"];
 
 
//recup arguments
$file_module_lang = "".$module."_".$target_lang.".php";
 
//
// recherche des arguments
//
	//recup arguments 
	$target_file =  $module . "_" . "$target_lang.php";	//arguments url module et target_lang 
	$master_file =  $module . "_" . "$master_lang.php";
	//on cherche le fichier
	$filefirstsearch = admin_lang_find_in_path("lang/$target_file",$racine_arbo);
	
##############""//////////// DEBUT PAGE ///////////////#######################"
debut_page(_T('module_fichier_langue').": $module","administration", "langues");

if ($display_debug) { 	
echo "<div style='font-size:0.6em;'>";

echo "Mode = $mode<br />module = $module<br />";
echo "target_lang= $target_lang <br />";
echo "master_lang = $master_lang <br />";
echo "folder_root= $folder_root<br>"; //e:/mes_sites/spip9-clean//ecrire
//on recup le dossier courant
echo "last_folder_name= " .$foldername."<br />"; //ecrire 
echo "lang_file_root = $lang_file_root<br />"; //e:/mes_sites/spip9-clean//ecrire/lang
echo "ENTRY<br />module = $module / target lang = $target_lang<br /><br />";
if ($submit) {echo "FORM SUBMITED<br />";}
else {echo "FORM not submited<br />";}

//fin affichage debug en petite police
echo "</div>\n";
}


// ------------ON TEST LE FICHIER ------
	//echo "$target_file_full et $file_module_lang $lang_file_root";
	if(@file_exists($target_file_full)){
	$affichedossier = last_folder_name($target_file_full);
	}else{
	$affichedossier = '';
	}
	
	echo "<br /><br />";
	gros_titre(_T('module_fichier_langue').": $affichedossier"."/"."$file_module_lang"); 
	barre_onglets("config_lang", "admin_lang");

//cherche les fichiers
/*
if ($module and $target_lang) { 
	$target_file 			 =  $module . "_" . "$target_lang.php";
	$master_file 			 =  $module . "_" . "$master_lang.php"; 
	$target_file_full 		 = "$lang_file_root/$target_file";
	$target_file_full_backup = $target_file_full . '_bak';
	$master_file_full 		 = "$lang_file_root/$master_file"; 
	$new_file_full 			 = $target_file_full; // could be != if needed 
	
}*/
echo "<div style='font-size:0.6em; color:green;'>";
echo "le charset du site est $charset et la langue choisie est $target_lang<br>";  
if (!in_array($target_lang, $lang_author))
echo "<br>attention! cette langue n'est pas installe!<br>"; //texte

//si pas dans array  des path ça veut dire qu'il est trouvé!, sinon proposer des chemins /lang pour le creer
if (!is_array($filefirstsearch)){
$target_file_full=$filefirstsearch; 
echo "voici le chemin trouve pour $target_file<br>";
print "$target_file_full<br>"; //chemin complet du fichier
$lang_file_root = dirname($filefirstsearch);//retourne le chemin des dossiers pour ce fichier
//echo"lang_file_root=$lang_file_root<br>"; 
}  

if ($display_debug) {  
//cherche les fichiers<br>module=$module<br>target_lang=$target_lang 
echo "
<br>racine_arbo $racine_arbo
<br>lang_file_root=$lang_file_root
<br>file_module_lang=$target_file
<br>target_file_full = $target_file_full
";
}

//	----------------------------------------- 
//    On test file_exists si fichier trouvé
//	----------------------------------------- 
 
print "<div style='color:red;'>";

if ($module and $target_lang) {
	$target_file_full_backup = $target_file_full . '_bak';
	$master_file_full =  $lang_file_root . "/$master_file"; 

 
	if((file_exists($target_file_full))&&(!file_exists($master_file_full))){
	echo"<br>master_file_full n'existe pas !<br>$master_file_full<br>";
	$master_file_full =  $target_file_full;
	$master_lang = $target_lang;
	$master_file = $target_file; 
	echo"donc on prend comme master<br> $master_file_full";
	}

print "</div>";
print "</div>";

  
	$target_file_full_backup = $target_file_full . '_bak'; 
	$new_file_full 			 = $target_file_full; // could be != if needed 
 
			if(@file_exists($target_file_full) and !@is_writable($target_file_full)) {
				print _T('admin_lang:pas_droit_ecriture');
				exit;
			}
	
			if(@file_exists($master_file_full)) {
				if  (!@is_readable($master_file_full)) {
					print _T('admin_lang:pas_droit_lecture');
					exit;
				}
			}
	else {
		if (($master_lang != $target_lang)||(!file_exists($master_file_full))) { 
		//echo"ça bloque ici? master_lang !=  target_lang $master_lang != $target_lang";
			$link_redo .= "<a href='".generer_url_ecrire("$nompage&target_lang=$target_lang&mode=create","module=$module")."'>Create file => $target_file</a> "; 
			//($mode == 'create') 
		echo "<div style='font-size:0.6em; padding:1em; margin:1em;'>"; 
			print "<strong>$target_file </strong><br />";
 			print "<span style='color:red;'>". _T('admin_lang:fichier_non_existant')."!</span>";	
			 
			print "<div style='font-size:1.4em; font-weight:bold;'>";
			print "Pour cr&eacute;er ce fichier copiez votre choix parmi les chemins propos&eacute;s";
			print "</div>";		
			
		print "<div style='color:purple; text-align:left; width:80%'>";
		if (is_array($filefirstsearch)){
		arsort($filefirstsearch); 
		//on prépare un chemin pour enregistrer le fichier
		foreach ($filefirstsearch as $chemin) {
				if ($chemin!='')
				$chemin_s_possible[] = $chemin."lang/$target_file"; //obligation de /lang/fichier
				}
		} 
		foreach ($chemin_s_possible as $chemin) {
		echo " $chemin<br> ";
					//présentation en dossiers
		$cheminblocs[] = dossier($chemin); //on fait un explode du chemin
		}	
/* on laisse tomber pour l'instant
		//présentation en dossiers suite, on compare chaque arete avec la suivante

		echo "<br> cheminblocs est un array qui contient des arêtes arf";
		print_r ($cheminblocs) ;
		echo"</br>";
		//mainteannt il faut comparer ses arrays
		for ($i = 0; $i < count($cheminblocs); $i++) {  
		$cheminbik = array_keys($cheminblocs); 
			echo "<br>array_keys de cheminsbloc".$cheminbik[$i]; 	
				
			 $result = array_diff($cheminblocs[0], $cheminblocs[1]++);
			echo"<br>test";
			
			print_r ($result); 
			} 
*/
			 

			 
			
		if (isset($_POST['nouvellepage']))
		{
		$post=$_POST['nouvellepage'];  
		$verifphp = substr($post,-4); 
		if(in_array($post, $chemin_s_possible)){ //si on est dans les fichiers autorisés
		if($verifphp==".php"){ //si l'extension est bien .php
		
		$post=stripslashes($post);   
		//
		//on test si le directory de lang existe bien sinon on le crée
		//
  		   $dircherche = dirname($post); 
		   $diracreer = last_folder_name($post);
		  
		   if (last_folder_name($post) == lang)// on verifie que l'on va bien creer lang
		   if (!is_dir($dircherche)) {  
				   mkdir($dircherche,0777); //si dossier lang n'existe pas on le crée
				   echo "<br><a href='#'>Le dossier $diracreer a dut etre cree!</a>";
		   }
		 
		 $fd = fopen("$post","a"); 
		 fclose($fd);
		 echo "<br /><a href='".generer_url_ecrire("$nompage&target_lang=$target_lang_code&mode=work","module=$module")."'> Fichier cree = $post <br />Cliquez ici pour voir votre fichier</a>"; 
		//sinon on reload la page?
		//header("Location:$selfpage&target_lang=$target_lang&mode=work&module=$module"); 
		}
		}
		else {echo"<br><strong style='color:red;'>impossible de cr&eacute;er ce fichier!</strong>"; };
		}
		 
		print "</div>";
		
			print"<form name='create' method='post' action=''> 
			<label><br />  Chemin de votre fichier</label><br />  
			<input type='text'  name='nouvellepage' value='' size='100'>
			<input type='submit' id='create_lang_file' name='submit' value='"._T('adminlang:create_lang_file')."'>
			</form>"; 
			echo"</div>";
			exit;
		}
	}
  }	
 

if ($display_debug) {
print "FILE TO READ = $target_file_full <br />FILE TO WRITE = $new_file_full<br /><br />";
echo "target_file_full $target_file_full<br>";
}

// ---------------------------------------------
//		TREAT
// ---------------------------------------------
if ($submit) {
	$target_table = $_POST['target_table'];
	
	if ($display_debug) {
	print "<H1>TREAT : Enregistrement du fichier langue : $target_file</H1><br />";
	}
	
	if ($mode == 'work'){
		// ---------------------------------------------
		//		backup init spip files if needed
		// ---------------------------------------------
		if(@!is_readable($target_file_full_backup) and $file_backup_option) {
			copy ($target_file_full, $target_file_full_backup);
			print "<p class='arial2'>" . _T('admin_lang:sauvegarde'). " $target_file" . "_bak</p>";	
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
		 		 if ($charset == 'utf-8'){  
				 $trad = unicode2charset(utf_8_to_unicode($trad), 'iso-8859-1'); 
				 } 
		 		 $trans = get_html_translation_table(HTML_ENTITIES);
				 $trad = strtr($trad, $trans);
				 $trad = addslashes($trad); 
				
			$any_line[$ctr] = "'$item' => '$trad $target_to_change',";
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
} // ================ end treatment ==========================





// ---------------------------------------------
//		FOR MODULE
// ---------------------------------------------

if ($module) {
	// ---------------------------------------------
	//		get existing lang files name
	// ---------------------------------------------
	$dir_lang = opendir($lang_file_root);
	$ctr = 0;
	while (($lang_file = readdir($dir_lang)) != '') {
		// Eviter ".", "..", ".htaccess", etc.
		if ($lang_file[0] == '.') continue;
		if ($lang_file == 'CVS') continue;
	
		$lang_file_name = "$lang_file_root/$lang_file";
		if (is_file($lang_file_name)) {
			$lang_file_table[$ctr] = $lang_file;
			$ctr++;
		}
	}
	closedir($dir_lang);
	
	//if ($display_debug) {print_r($lang_file_table);}
	
	// ---------------------------------------------
	//		get possible lang
	// ---------------------------------------------
	//init_codes_langues();
	$langues = $GLOBALS['codes_langues'];
	
	
	$lang_install = explode(',', $GLOBALS['all_langs']);
	$lang_author = explode(',', lire_meta('langues_multilingue'));
	
	if ($display_debug) {
		 /*print "<br /><br />INSTALLED<br />";
		 print_r($lang_install);
		
		 print "<br /><br />AUTHORIZED<br />";
		 print_r($lang_author);
		
		 print count($lang_author);
		 */
	}
	// ---------------------------------------------
	//		prepare interface
	// ---------------------------------------------
	$available_file = array();
	$file_to_create = array();
	
	$ctrok = $ctrnotok = 0;
	for ($i = 0; $i < count($lang_author); $i++) {
		$str_to_search = $module . '_' . $lang_author[$i] . '.php';
		
		if (in_array($str_to_search, $lang_file_table)) {
			$available_file[$ctrok] = $lang_author[$i];
			$ctrok++;
		}
		else {
			$file_to_create[$ctrnotok] = $lang_author[$i];
			$ctrnotok++;
		}
	}
	
	// ---------------------------------------------
	//		show resulting choice
	// ---------------------------------------------
	for ($i = 0; $i < count($available_file); $i++) {
		$any_lang_lib 	= $langues[$available_file[$i]];
		$any_lang_code 	= $available_file[$i];
		
		// bold master lang
		if ($any_lang_code == $master_lang) {$any_lang_lib = "<strong>$any_lang_lib</strong>";} 
		$bloc_lang_work .= "<a href='".generer_url_ecrire("$nompage&target_lang=$any_lang_code&mode=work","module=$module")."'>$any_lang_lib</a>, "; 
	}
	

	for ($i = 0; $i < count($file_to_create); $i++) {
		$any_lang_lib 	= $langues[$file_to_create[$i]];
		$any_lang_code 	= $file_to_create[$i];
		
		// bold master lang
		if ($any_lang_code == $master_lang) {$any_lang_lib = "<strong>$any_lang_lib</strong>";} 
		$bloc_lang_create .= "<a href='".generer_url_ecrire("$nompage&target_lang=$any_lang_code&mode=create","module=$module")."'>$any_lang_lib</a>, ";
	}
	
	// remove last , and add .
	$bloc_lang_work = substr(trim($bloc_lang_work), 0, -1) . '.';
	$bloc_lang_create = substr(trim($bloc_lang_create), 0, -1) . '.';
}


if ($display_debug) { 
	print "Target lang: $target_lang<br />";
	print "DIR TO WRITE = $lang_file_root<br />";
	print "FILE TO READ = $target_file_full <br />FILE TO WRITE = $new_file_full<br /><br />";
}

 

// ---------------------------------------------
//		display bloc gauche (module)
// ---------------------------------------------
debut_gauche();


if ($target_lang) {
if (count($modules) > 1) {
echo debut_cadre_relief();			

$target_lang_lib = $langues[$target_lang]; 
		echo "<div class='verdana3' style='background-color: $couleur_foncee; color: white; padding: 3px;'><b>"._T('module_fichiers_langues').":</b></div><br>\n";
print "<span style='color:red; font-size:1.5em;'>$target_lang_lib</span>";

		if ($module == local) echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><b>local</b></div>";
			else echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><a href='".generer_url_ecrire("$nompage&target_lang=$target_lang&mode=work","module=local")."'>local</a></div>\n";
			
print "<form name='newmodule' method='post' action='".generer_url_ecrire("$nompage")."'>\n";
print "<input type='hidden' name='mode' value='work'>\n";
print "<input type='hidden' name='target_lang' value='$target_lang'>\n";
print "<input type='text' size='8' name='module'>\n";
print "<input type='submit' name='creer' value='Autre module'>\n";
print "</form>\n";
			
//faut-il vraiment permettre de toucher à ces fichiers? je pense à retirer ça!			
		foreach ($modules as $nom_module) {
			if ($nom_module == $module) echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><b>$nom_module</b></div>";
			else echo "<div style='padding-$spip_lang_left: 10px;' class='verdana3'><a href='".generer_url_ecrire("$nompage&target_lang=$target_lang&mode=work","module=$nom_module")."'>$nom_module</a></div>";
		}

} 
 
	
echo fin_cadre_relief();
}

 

// ---------------------------------------------
//		FORM, FOR ANY MODULE AND LANG
// ---------------------------------------------
debut_droite();
//echo "<div style='background-color:pink;'>";
	if ($module) {
		print "<span class='arial2'><strong>"._T('adminlang:fichier_modifiables')."</strong> : $bloc_lang_work</span>";
		print "<p class='arial2'><strong>"._T('adminlang:fichier_a_creer')."</strong> : $bloc_lang_create<br />";
		print "<em>"._T('adminlang:langue_possible')." 
		<a href=\"?exec=config_multilang\">"._T('adminlang:panneau_multilingue')."</a></em></p>";
		if ($master_lang == $target_lang) {
			print "<p class='arial2'>"._T('adminlang:pour_effacer')."</p>";
		}
	}
	else {
		print "<p class='arial2'>"._T('adminlang:choix_module')."</p>";
	}

if ($module and $target_lang) {

	// ---------------------------------------------
	//		get initial data
	//      include file => current lang table = $GLOBALS[$GLOBALS['idx_lang']]
	//      copy $GLOBALS[$GLOBALS['idx_lang']] in new table to keep it active
	// ---------------------------------------------
	 
	$protect_idx_lang = $GLOBALS['idx_lang'];
	$protect_main_lang_table = $GLOBALS[$GLOBALS['idx_lang']]; 
	$GLOBALS['idx_lang'] = 'i18n' . '_' . $module . '_' . $master_lang; 
	include("$lang_file_root/$master_file");
	$master_table = $GLOBALS[$GLOBALS['idx_lang']];
	if(isset($master_table)){ 
	ksort($master_table);
	}
	
	if ($mode == 'work') { 
	if ($display_debug) {
		echo "master_file =$master_lang+ $master_file / target_file =$target_lang+ $target_file<br /><br />"; 
	}
		$GLOBALS['idx_lang'] = 'i18n' . '_' . $module . '_' . $target_lang;
		include("$lang_file_root/$target_file");
		$target_table = $GLOBALS[$GLOBALS['idx_lang']];
		if(isset($target_table)){ 
		ksort($target_table);
		}
	}
	else {
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
	
	$switch_color= false;
	print "<form name='form1' method='post' action=''>\r";
		
	if (count($master_table) > 0) { // do not display update form if target file is empty
		echo "<table style='border:0px; width:660px;' summary='"._T('adminlang:tableau_des_raccourcis_modifiables')."'>";
					
			  echo "<tr>
					<td class='verdana1' ><strong>"._T('module_raccourci')."</strong></td>
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
					 	  <th style='text-align:right'><a href='#save_lang_file' style='color:white; font-family:verdana;' title='"._T('adminlang:allerbasdepage')."'>. </a></th>
					 	  </tr>
						  ";
					$prev_letter = $first_letter;
				}
			
			$master_to_change = stripslashes($master_to_change);
			$target_to_change = $target_table["$value_to_change"];
			
			if ($target_to_change == '') {$style_master_2 = "color:darkred; font-weight:bold;";}
			else {$style_master_2 = "color:#000000; font-weight:normal;";}
			 
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
				} else {print "<span class='verdana2'>$target_to_change</span>"; }
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
 
	
	// ---------------------------------------------
	//		ADDITION ZONE
	// 		only if master = target ??
	// ---------------------------------------------
if ($connect_statut =='0minirezo'  AND $connect_toutes_rubriques) {
	
	if ($master_lang == $target_lang) {
		echo "<table style='width:660px; border:0px; vertical-align:top; margin-top:1em; ' summary='"._T('adminlang:tableau_ajouter_raccourcis')."'>\r";
		
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
		
		print  "<input id='save_lang_file' type='submit' name='submit' value="._T('adminlang:validation').">";	
		//print  "<a href=\"javascript:window.close();\" class=\"boutonBOform\">Fermer cette fen&ecirc;tre</a>";
		print "<!-- what are you doing here? -->";
		print "</td></tr></table>";
	}
}
	
		
	print "</form>\r";
}
else {
	//print "ici, texte d'explication compl&eacute;mentaire possible...<br />";
} // end if module & target_lang


fin_page();
}
//display_exe_time ($time_start, 'after includes<br />');
?>
