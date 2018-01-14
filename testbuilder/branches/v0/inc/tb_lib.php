<?php
/*
 * Plugin TestBuilder
 * (c) 2010 Cedric MORIN Yterium
 * Distribue sous licence GPL
 *
 */


/**
 * Analyse un fichier source php
 * et ressort la liste des fonctions nommes et pour chacune la liste de ses arguments
 *
 * @param string $filename
 * @return array
 */
function tb_liste_fonctions($filename, $clear = false){
	static $funcs=array();
	$filename = find_in_path($filename);
	if (!$filename) return array();

	if ($clear){
		unset($funcs[$filename]);
	}

	if (isset($funcs[$filename]))
		return $funcs[$filename];

	// cache file ?
	$cache_func = sous_repertoire(_DIR_CACHE,"functions")."f".md5($filename).".txt";
	if ($clear) {
		spip_unlink($cache_func);
		return array();
	}

	if (file_exists($cache_func)
		AND @filemtime($cache_func)>@filemtime($filename)
		AND lire_fichier($cache_func, $cache)
		AND $cache = unserialize($cache))
		return $funcs[$filename] = $cache;


	lire_fichier($filename,$content);
	if (!trim($content)) return $funcs[$filename] = array();

	$tokens = token_get_all($content);
	$funcs = array();
	$previous_token_line = 0;
	$func_name = "";
	while (count($tokens)){
		$t = array_shift($tokens);
		if (is_string($t) AND $t=='}')
			$previous_token_line=0;
		if (!is_string($t) AND $previous_token_line==0)
			$previous_token_line = $t[2];
		if (!is_string($t) AND in_array($t[0],array(T_INCLUDE,T_INCLUDE_ONCE,T_STRING,T_ECHO)))
			$previous_token_line=0;

		#if (!is_string($t)) echo token_name($t[0]).":".$t[1].":".$t[2]."<br />";
		if ($t[0]==T_FUNCTION){
			#die();
			// si on avait trouve une fonction auparavant, lui affecter la ligne de fin
			if ($func_name AND $funcs[$filename][$func_name])
				$funcs[$filename][$func_name][3] = $previous_token_line-1;

			while (count($tokens) AND $t[0]!==T_STRING) $t = array_shift($tokens);
			$func_line = $t[2];
			$func_name = $t[1];
			$func_args = array();
			$open_token = 0;
			while (count($tokens) AND $t!=='(') $t = array_shift($tokens);
			$open_token++;
			while (count($tokens) AND $open_token){
				$t = array_shift($tokens);
				if ($t==')') $open_token--;
				elseif ($t=='(') $open_token++;
				else if ($t[0]==T_VARIABLE){
					$arg = $arg_aff = $t[1];
					$func_args[] = $arg;
				}
			}
			$funcs[$filename][$func_name] = array($func_args,$func_line,min($previous_token_line+1,$func_line),$func_line+10);
		}
	}
	if ($func_name AND $funcs[$filename][$func_name])
		$funcs[$filename][$func_name][3] = $previous_token_line;

	ecrire_fichier($cache_func, serialize($funcs[$filename]));
	return $funcs[$filename];
}

/**
 * Lister les repertoires de test dispo
 * 
 * @return array()
 */
function tb_liste_dirs_tests(){
	$bases = array(_DIR_RACINE . 'tests/unit/');
	foreach (creer_chemin() as $d) {
		if ($d && @is_dir("${d}tests"))
			$bases[] = "${d}tests/";
	}
	return $bases;
}

/**
 * Lister les tests dispos
 *
 * @return array()
 */
function tb_liste_tests(){
	$liste_tests = array();
	$bases = tb_liste_dirs_tests();
	foreach ($bases as $base) {
		// regarder tous les tests
		$tests = preg_files($base, '/\w+/.*\.(php|html)$');

		foreach ($tests as $test) {
			//ignorer le contenu du jeu de squelettes dédié aux tests
			if (stristr($test,'squelettes'))
				continue;

			//ignorer les fichiers lanceurs pour simpleTests aux tests
			if (stristr($test,'lanceur_spip.php'))
				continue;
			if (stristr($test,'all_tests.php'))
				continue;

			if (substr(basename($test),0,7) != 'inclus_' &&
				substr(basename($test),-14) != '_fonctions.php'){

				$joli = basename($test);
				$liste_tests[$joli] = $test;
			}
		}
	}
	return $liste_tests;
}

/**
 * Trouver si une fonction a un test
 * et retourne le chemin du test
 *
 * @staticvar array $tests
 * @param string $funcname
 * @return tring
 */
function tb_hastest($funcname, $force=false){
	static $tests = null;
	if (is_null($tests) OR $force)
		$tests = tb_liste_tests();
	if (isset($tests["$funcname.php"]))
		return $tests["$funcname.php"];
	/*if (isset($tests["$funcname.html"]))
		return $tests["$funcname.html"];*/

	return '';
}

/**
 * Url du test
 * 
 * @param <type> $testfun
 * @param <type> $lien
 * @return <type>
 */
function tb_url_test($testfun, $lien=false){
	if (!$testfun) return "";
	if (preg_match(',\.php$,', $testfun))
		$url = $testfun .'?mode=test_general';
	else{
		$testfun = substr($testfun,strlen(_DIR_RACINE."tests/"));
		$url = _DIR_RACINE."tests/squel.php?test=$testfun&amp;var_mode=recalcul";
	}
	if (!$lien)
		return $url;
	return "<a href='$url'>$lien</a>";
}

/**
 * Extraire une fonction php d'un script
 *
 * @param string $filename
 * @param strinf $funcname
 * @return string
 */
function tb_function_extract($filename,$funcname){
	$liste = tb_liste_fonctions($filename);
	if (!isset($liste[$funcname]))
		return "";
	$func = $liste[$funcname];
	$start = $func[2];
	$length = $func[3]-$start+1;

	lire_fichier(find_in_path($filename),$content);
	$content = explode("\n",$content);
	$content = array_slice($content, $start,$length);
	return trim(implode("\n",$content));
}


/**
 * Generer un nouveau test vierge
 * pour la fonction $funcname, du fichier $filename
 * 
 * @param <type> $filename
 * @param <type> $funcname
 * @param <type> $essais 
 */
function tb_generate_new_blank_test($filename,$funcname){
	lire_fichier(find_in_path("templates/function.php"),$template);

	$template = str_replace(
					array('@funcname@','@essais_funcname@','@filename@','@date@'),
					array($funcname,"essais_$funcname",$filename,date('Y-m-d H:i')),
					$template
					);
	$d="";
	$plugins_dist = defined('_DIR_PLUGINS_DIST')?_DIR_PLUGINS_DIST:_DIR_EXTENSIONS;
	if (strcmp($filename,_DIR_PLUGINS)==0
		OR strcmp($filename,$plugins_dist)==0){
		// trouver le repertoire du plugin en remontant jusqu'a retrouver plugin.xml
		$d = dirname($filename);
		while(!file_exists("$d/plugin.xml") AND strlen($d)>strlen(_DIR_PLUGINS))
			$d = dirname($d);
		if (strlen($d)>strlen(_DIR_PLUGINS))
			$d = sous_repertoire("$d/","tests");
		else
			$d="";
	}
	if (!$d)
		$d = sous_repertoire(_DIR_RACINE."tests/unit/",basename($filename,'.php'));
	ecrire_fichier($f="$d/$funcname.php",$template);
	return $f;
}

/**
 * Lit un fichier de test existant et recupere le jeu d'essai qu'il contient
 * si un nouveau jeu d'essai est fourni, il remplace l'ancien
 * et le fichier est mis a jour
 *
 * @param string $filetest
 * @param array $essais_new
 */
function tb_test_essais($funcname,$filetest,$essais_new=null){
	$function = tb_function_extract($filetest,"essais_$funcname");

	if (is_array($essais_new)
	  AND $l=lire_fichier($filetest, $contenu)){
		// renumeroter
		$re = array();
		while(count($essais_new))
			$re[] = array_shift($essais_new);
		$new_func = "function essais_$funcname(){
		\$essais = ".var_export($re,true).";
		return \$essais;
	}
";
		if (strlen($function))
			$contenu = str_replace($function, $new_func, $contenu);
		else
			$contenu = str_replace("?>", "$new_func\n?>", $contenu);
		ecrire_fichier($filetest, $contenu);
		// purger la liste de fonctions de ce fichier
		tb_liste_fonctions($filetest, true);
		return $essais_new;
		$function = $new_func;
	}
	if (!$function) return array();
	$tst = "essais"; $i=0;

	while (function_exists("$tst$i")) $i++;

	$function = str_replace("function essais_$funcname"."(","function $tst$i"."(",$function);
	$function .= " return $tst$i();";
	return eval($function);
}

/**
 * Recalculer le resultat des tests
 * pour cela on passe par un hit public car les tests
 * se deroulent dans le public
 * 
 * @param  $filename
 * @param  $funcname
 * @param  $filetest
 * @return void
 */
function tb_refresh_test($filename,$funcname,$filetest){
	include_spip('inc/securiser_action');
	$arg = "$filename|$funcname|".substr($filetest,strlen(_DIR_RACINE));
	$hash = calculer_cle_action($arg);
	$url = generer_url_action("tb_set_test_output", "arg=$arg&hash=$hash",true,true);
	#var_dump($url);
	include_spip("inc/distant");
	if (recuperer_page($url)) return true;
	return false;
}

function tb_error_handler($output)
{
    $error = error_get_last();
    $output = "";
    foreach ($error as $info => $string)
        $output .= "{$info}: {$string}<br />";
    return $output;
}

/**
 * Mapping de var_export pour utiliser dans array_map()
 *
 * @param string $var
 * @return mixed
 */
function tb_export($var){
	return var_export($var,true);
}

/**
 * Filtrer un jeu d'essais en enlevant les doublons (meme arg, meme resultat)
 * 
 * @param array $essais
 */
function tb_filter_essais(&$essais){
	$t = array();
	foreach($essais as $k=>$v){
		$s = md5(var_export($v,true));
		if (isset($t[$s]))
			unset($essais[$k]);
		else
			$t[$s] = true;
	}
}

function tb_try_essai($filename,$funcname,$essai,&$output_test){
	ob_start('tb_error_handler');
	try {
		find_in_path($filename,'',true);
		$appel = "$funcname(".implode(", ",array_map('tb_export',$essai)).")";
		#var_dump($appel);
		$res = call_user_func_array($funcname, $essai);
	}
	catch (Exception $e) {
		$res = "Erreur : ".$e->getMessage();
	}
	$output_test = ob_get_contents();
	ob_end_clean();
	$call = "$appel = ".var_export($res,true);
	$call = str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$call);
	$output_test .= ($output_test?"<br />":"")."<tt>$call</tt>";
	return $res;
}


/**
 * Construire un jeu d'essai complet combinatoire
 * Pour chaque entree, on teste chaque valeur unitaire candidate
 * combinatoirement avec les autres entrees
 *
 * @param array $types
 */
function tb_essai_combinatoire($types){
	static $tb_essais_type=null;
	$essais = array();
	if (!count($types))
		return $essais;

	if (is_null($tb_essais_type))
		$tb_essais_type = charger_fonction('tb_essais_type','inc');

	$type = array_shift($types);
	$es = tb_essai_combinatoire($types);
	$samples = $tb_essais_type($type);
	if (!count($samples)){
		// ce n'est pas un type, c'est un argument fixe
		$samples = eval("return array($type);");
	}
	foreach($samples as $s){
		if (count($es)){
			foreach($es as $e) {
				array_unshift($e,$s);
				$essais[] = $e;
			}
		}
		else
			$essais[] = array($s);
	}
	return $essais;
}
?>