<?
// ---------------------------------------------
//  Plugin admin_lang
//	admin_lang_fonctions.php
//  spip addition to manage language files
//  alm@elastick.net
//  simeray@tektonika.com
// ---------------------------------------------


//retourne le dernier dossier d'un chemin
function last_folder_name($path){ 
	   if (!$path){$path=$_SERVER['PHP_SELF'];}
	   $current_directory = dirname($path);
	   $current_directory = str_replace('\\','/',$current_directory);
	   $current_directory = explode('/',$current_directory);
	   $current_directory = end($current_directory);
	   return $current_directory;
	}	


//renvoie l'arborescence complète d'un chemin
function up_arbo_complet($path,$racine_arbo){
	/*if (count(explode("..", $path)) > 1){ //on remonte l'arbo
	$path = str_replace('..', '', $path); 
	$path = $racine_arbo."".$path; 
	} */
	return $path;
	}
	
	
// retourne le chemin d'un fichier sinon tous les chemins possibles
// à partir de find_in_path() : chercher un fichier nomme x selon le chemin rep1:rep2:rep3

function admin_lang_creer_chemin () {
	static $path_a = array();
	static $c = '';

	// on calcule le chemin si le nombre de plugins a change
	if ($c != count($GLOBALS['plugins']).$GLOBALS['dossier_squelettes']) {
		$c = count($GLOBALS['plugins']).$GLOBALS['dossier_squelettes'];

		// retirer Chemin standard depuis l'espace public
		 
		/*
		$path = defined('_SPIP_PATH') ? _SPIP_PATH : 
			_DIR_RACINE.':'.
			_DIR_RACINE.'dist/:'.
			_DIR_RACINE.'formulaires/:'.
			_DIR_RESTREINT;
		*/
			 

		// Ajouter les repertoires des plugins
		if ($GLOBALS['plugins'])
			$path = _DIR_PLUGINS
				. join(':'._DIR_PLUGINS, $GLOBALS['plugins'])
				. ':' . $path;

		// Ajouter squelettes/
		if (@is_dir(_DIR_RACINE.'squelettes'))
			$path = _DIR_RACINE.'squelettes/:' . $path;

		// Et le(s) dossier(s) des squelettes nommes
		if ($GLOBALS['dossier_squelettes'])
			foreach (array_reverse(explode(':', $GLOBALS['dossier_squelettes'])) as $d)
				$path = 
					($d[0] == '/' ? '' : _DIR_RACINE) . $d . '/:' . $path;

		// nettoyer les / du path
		$path_a = array();
		foreach (explode(':', $path) as $dir) {
			if (strlen($dir) AND substr($dir,-1) != '/')
				$dir .= "/";
			$path_a[] = $dir;
		}
	} 
	return $path_a;
}
 
function admin_lang_find_in_path ($filename) {
	foreach (admin_lang_creer_chemin () as $dir) {
			 //$dir = up_arbo_complet($dir,$racine_arbo); 
			 $listdir[] = $dir;
			 
				if (@is_readable($f = "$dir$filename")) {
				//$f = up_arbo_complet($f,$racine_arbo);
		# spip_log("find_in_path trouve $f");
					return $f;
				} 
	}



# spip_log("find_in_path n'a pas vu '$filename' dans " . join(':',$path_a));
//alors on recup l'array des dir possibles!
	return $listdir;
	//return false;
}	


function dossier($url)
{
$cheminjoli = explode("/", $url); 
return $cheminjoli; 
}



///ajouts microtime inutile...
 
// ---------------------------------------------
//	get time in microseconds, and format in seconds
// ---------------------------------------------
function getmicrotime(){
	// get micro time and format in second
	list($sec, $usec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

// ---------------------------------------------
//	calc duration from 2 timestamps
// ---------------------------------------------
function exetime($t_start,$t_end) {
	// evaluate exe time in second
	$e_time = $t_end - $t_start;
	$e_time = sprintf("%01.4f",$e_time);
	return $e_time;
}

// ---------------------------------------------
//	combine both previous to deliver a formated result
// ---------------------------------------------
function display_exe_time ($time_start, $comment) {
	$time_end = getmicrotime();
	$duration = exetime($time_start,$time_end);
	print "$comment : $duration sec</i><br /><br />";
}


///ajouts nocturnes...inutiles..

//
// Charger un fichier langue
//
function admin_lang_chercher_module_lang($module, $lang = '') {
	if ($lang)
		$lang = '_'.$lang;

	// 1) dans un repertoire nomme lang/ se trouvant sur le chemin
	if ($f = include_spip('lang/'.$module.$lang, false))
		return $f;

	// 2) directement dans le chemin (old style)
	return include_spip($module.$lang, false);
}

function admin_lang_charger_langue($lang, $module = 'spip') {
	if ($fichier_lang = admin_lang_chercher_module_lang($module, $lang)) {
		$GLOBALS['idx_lang']='i18n_'.$module.'_'.$lang;
		include_once($fichier_lang);
	} else {
		// si le fichier de langue du module n'existe pas, on se rabat sur
		// la langue par defaut du site -- et au pire sur le francais, qui
		// *par definition* doit exister, et on copie le tableau dans la
		// var liee a la langue
		$l = $GLOBALS['meta']['langue_site'];
		if (!$fichier_lang = admin_lang_chercher_module_lang($module, $l))
			$fichier_lang = admin_lang_chercher_module_lang($module, 'fr');

		if ($fichier_lang) {
			$GLOBALS['idx_lang']='i18n_'.$module.'_' .$l;
			include($fichier_lang);
			$GLOBALS['i18n_'.$module.'_'.$lang]
				= &$GLOBALS['i18n_'.$module.'_'.$l];
			#spip_log("module de langue : ${module}_$l.php");
		}
	}
}

function admin_lang_afficher_raccourcis($module="public") {
	global $spip_lang;
	global $couleur_foncee;
	
	admin_lang_charger_langue($spip_lang, $module);

	$tableau = $GLOBALS['i18n_' . $module . '_' . $spip_lang];
	ksort($tableau);

	if ($module != "public" AND $module != "local")
		$aff_nom_module = "$module:";

	echo "<div class='arial2'>"._T('module_texte_explicatif')."</div>";
	echo "<div>&nbsp;</div>";

	foreach (preg_files(repertoire_lang().$module.'_[a-z_]+\.php[3]?$') as $f)
		if (ereg("^".$module."\_([a-z_]+)\.php[3]?$", $f, $regs))
				$langue_module[$regs[1]] = traduire_nom_langue($regs[1]);

	if ($langue_module) {
		ksort($langue_module);
		echo "<div class='arial2'>"._T('module_texte_traduction',
			array('module' => $module));
		echo " ".join(", ", $langue_module).".";
		echo "</div><div>&nbsp;</div>";
	}

	echo "<table cellpadding='3' cellspacing='1' border='0'>";
	echo "<tr bgcolor='$couleur_foncee' style='color:white;'><td class='verdana1'><b>"._T('module_raccourci')."</b></td><td class='verdana2'><b>"._T('module_texte_affiche')."</b></td></tr>\n";

	foreach ($tableau as $raccourci => $val) {
		$bgcolor = alterner($i++, '#eeeeee','white');
		echo "<tr bgcolor='$bgcolor'>
		<td class='verdana2'>
		<b><:$aff_nom_module$raccourci:></b>
		</td><td class='arial2'>".$val."</td></tr>\n";
	}

	echo "</table>";
}
?>
