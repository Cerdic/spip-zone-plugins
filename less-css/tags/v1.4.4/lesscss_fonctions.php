<?php
/*
 * Plugin LessCSS
 * Distribue sous licence MIT
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function lesscss_compile_cache_get($less_parser, $file_path, $cache_key) {
	//spip_log("lesscss_compile_cache_get $file_path, $cache_key", 'less');
	if ($store = cache_get("lesscss:$cache_key")
	  and isset($store['path'])
	  and isset($store['value'])
		and $store['path'] === $file_path) {
		//spip_log("lesscss_compile_cache_get OK $file_path, $cache_key", 'less');
		return $store['value'];
	}
	//spip_log("lesscss_compile_cache_get FAIL $file_path, $cache_key", 'less');
	return null;
}

function lesscss_compile_cache_set($less_parser, $file_path, $cache_key, $value) {
	//spip_log("lesscss_compile_cache_set $file_path, $cache_key", 'less');
	cache_set("lesscss:$cache_key", array('path' => $file_path, 'value' => $value));
}

function lesscss_import_dirs() {
	static $import_dirs = null;
	if (is_null($import_dirs)){
		$path = _chemin();
		$import_dirs = array();
		foreach($path as $p){
			$import_dirs[$p] = protocole_implicite(url_absolue($p?$p:"./"));
		}
	}
	return $import_dirs;
}

function lesscss_cache_dir() {
	static $cache_dir;
	if (is_null($cache_dir)) {
		$cache_dir = sous_repertoire (_DIR_VAR, 'cache-less');
		$cache_dir = sous_repertoire($cache_dir, 'compile');
	}
	return $cache_dir;
}

/**
 * Compiler des styles inline LESS en CSS
 *
 * @param string $style
 *   contenu du .less
 * @param array $contexte
 *   file : chemin du fichier compile
 *          utilise en cas de message d'erreur, et pour le repertoire de reference des @import
 * @return string
 */
function lesscss_compile($style, $contexte = array()){
	static $parser_options = null;
	static $chemin = null;

	spip_timer('lesscss_compile');
	if (!class_exists('Less_Autoloader')){
		include_spip('lib/Less/Autoloader');
		Less_Autoloader::register();
	}
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}

	if (is_null($parser_options)) {
		$parser_options = array();
		if (lire_config('lesscss/activer_sourcemaps', false) == "on") {
			$parser_options['sourceMap'] = true;
		}
		$parser_options['cache_dir'] = lesscss_cache_dir();
		if (defined('_MEMOIZE_CACHE_LESS')
		  and isset($GLOBALS['Memoization'])
			and in_array($GLOBALS['Memoization']->methode, array('apc','apcu','xcache'))) {
			$parser_options['cache_method'] = 'callback';
			$parser_options['cache_callback_get'] = 'lesscss_compile_cache_get';
			$parser_options['cache_callback_set'] = 'lesscss_compile_cache_set';
		}
		$parser_options['import_dirs'] = lesscss_import_dirs();
		// il faut prefixer avec une empreinte du import_dirs qui inclue les URLs absolues correspondantes
		// car cela change le contenu et n'est pas pris en compte par la gestion du cache less, on a donc un risque d'empoisonement
		// du cache d'un domaine par un autre domaine ou entre 2 variantes de chemin SPIP
		$parser_options['prefix'] = 'lessphp_'. substr(md5(json_encode($parser_options['import_dirs'])),0,4) . '_';

		if (defined('_VAR_MODE') and in_array(_VAR_MODE, array('css', 'recalcul'))) {
			$parser_options['use_cache'] = false;
		}
	}

	$url_absolue = (!empty($contexte['file'])?protocole_implicite(url_absolue($contexte['file'])):null);
	if (!empty($parser_options['sourceMap'])) {
		if (!empty($contexte['dest'])) {
			$parser_options['sourceMapWriteTo'] = $contexte['dest'] . '.map';
			$parser_options['sourceMapURL'] = protocole_implicite(url_absolue($parser_options['sourceMapWriteTo']));
		}
		else {
			unset($parser_options['sourceMapWriteTo']);
			unset($parser_options['sourceMapURL']);
		}
		$parser_options['sourceMapBasepath'] = realpath(_DIR_RACINE);
		$url_base = url_absolue(_DIR_RACINE?_DIR_RACINE:'./');
		$parts = parse_url($url_base);
		$path = rtrim($parts['path'],'/');
		if (strlen($path)) {
			$url_base = explode($path, $url_base);
			$url_base = reset($url_base).'/';
			$parser_options['sourceMapBasepath'] = substr($parser_options['sourceMapBasepath'], 0, -strlen($path));
		}
		$url_base = protocole_implicite($url_base);
		if ($url_absolue) {
			$url_absolue = '/' . substr($url_absolue, strlen($url_base));
		}
		$parser_options['sourceMapRootpath'] = $url_base;
	}

	$parser = new Less_Parser($parser_options);
	$parser->relativeUrls = true;

	try {
		if (!$style and isset($contexte['file']) and $contexte['file'])  {
			$internal_cache_file = $parser_options['cache_dir'] . Less_Cache::Get(array( $contexte['file'] => $url_absolue ), $parser_options);
			$out = file_get_contents($internal_cache_file);
			spip_log('lesscss_compile Compile CACHED ' . $contexte['file'] . ' :: '.spip_timer('lesscss_compile'), 'less');
		}
		else {
			$parser->parse($style,$url_absolue);
			spip_log('lesscss_compile parse '.(isset($contexte['file'])?$contexte['file']:substr($style,0,100)).' :: '.spip_timer('lesscss_compile'), 'less');
			spip_timer('lesscss_compile');
			$out = $parser->getCss();
			spip_log('lesscss_compile getCSS '.(isset($contexte['file'])?$contexte['file']:substr($style,0,100)).' :: '.spip_timer('lesscss_compile'), 'less');
		}

	}
	// en cas d'erreur, on retourne du vide...
	catch (exception $ex) {
		spip_log($e = 'LESS Echec compilation :'.$ex->getMessage(),'less'._LOG_ERREUR);
		$out = "/* LESS Echec compilation *"."/\n";
		erreur_squelette(
			"LESS : Echec compilation"
			. (isset($contexte['file'])?" fichier ".$contexte['file']:"")
		  . "<br />".$ex->getMessage()
		);
	}

	// si on a rien parse parce que fichier en cache, indiquer au moins le fichier source concerne dans l'en-tete
	$files = Less_Parser::AllParsedFiles();
	if (!$files and !empty($contexte['file'])) {
		$files = array($contexte['file']);
	}
	if ($files AND count($files)){

		$l = strlen(_DIR_RACINE);
		foreach($files as $k=>$file){
			if (strncmp($file,_DIR_RACINE,$l)==0){
				$files[$k] = substr($file,$l);
			}
		}
		$out = "/*\n#@".implode("\n#@",$files)."\n*"."/\n" . $out;
	}

	return $out;
}

/**
 * Transformer du LESS en CSS
 * Peut prendre en entree
 * - un fichier .css ou .less :
 *   [(#CHEMIN{messtyles.less.css}|less_css)]
 *   la sortie est un chemin vers un fichier CSS
 * - des styles inline,
 *   pour appliquer dans une feulle less calculee :
 *   #FILTRE{less_css}
 *   la sortie est du style inline
 *
 * @param string $source
 * @return string
 */
function less_css($source){
	static $chemin = null;

	// Si on n'importe pas, est-ce un fichier ?
	if (!preg_match(',[\s{}],', $source)
	  AND preg_match(',\.(less|css)$,i', $source, $r)
	  AND file_exists($source)) {
		static $done = array();
		// ne pas essayer de compiler deux fois le meme fichier dans le meme hit
		// si on a echoue une fois, on echouera pareil
		if (isset($done[$source])) return $done[$source];

		if (is_null($chemin)){
			$chemin = _chemin();
			$chemin = md5(serialize($chemin));
		}
		// url de base de la source
		// qui se trouvera dans la css car url absolue des images
		// il faut que la css generee en depende
		$url_base_source = protocole_implicite(url_absolue($source));

		$f = basename($source,$r[0]);
		$f = sous_repertoire (_DIR_VAR, 'cache-less')
		. preg_replace(",(.*?)(_rtl|_ltr)?$,",
				"\\1-cssify-" . substr(md5("$url_base_source-lesscss-$chemin"), 0,7) . "\\2",
				$f, 1)
		. '.css';

		# si la feuille compilee est plus recente que la feuille source
		# l'utiliser sans rien faire, sauf si il y a un var_mode
		# dans ca cas on passe par la compilation qui utilise un cache et est donc rapide si rien de change
		$changed = false;
		if (@filemtime($f) < @filemtime($source)){
			$changed = true;
		}

		// si pas change ET pas de var_mode du tout, rien a faire (performance)
		if (!$changed
			AND !defined('_VAR_MODE'))
			return $f;

		# compiler le LESS si besoin (ne pas generer une erreur si source vide
		if (!filesize($source)){
			$contenu = "/* Source $source : vide */\n";
		}
		else {
			$contenu = lesscss_compile('', array('file'=>$source, 'dest'=>$f));
		}
		// si erreur de compilation on renvoit un commentaire, et il y a deja eu un log
		if (!$contenu){
			$contenu = "/* Compilation $source : vide */\n";
		}

		# passer la css en url absolue
		# plus la peine : le parser CSS resoud les ULRs absolues des images en meme temps qu'il les cherche dans le path
		# $contenu = urls_absolues_css($contenu, $url_base_source);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		// on ecrit sur un fichier
		if (ecrire_fichier($f.".last", $contenu, true)){
			if ($changed OR md5_file($f)!=md5_file($f.".last")){
				@copy($f.".last",$f);
				// eviter que PHP ne reserve le vieux timestamp
				if (version_compare(PHP_VERSION, '5.3.0') >= 0)
					clearstatcache(true,$f);
				else
					clearstatcache();
			}
			return $done[$source] = $f;
		}
		else
			return $done[$source] = $source;
	}
	$source = lesscss_compile($source);
	if (!$source)
		return "/* Erreur compilation LESS : cf less.log */";
	else
		return $source;
}


/**
 * injecter l'appel au compresseur sous la forme de filtre
 * pour intervenir sur l'ensemble du head
 * du squelette public
 *
 * @param string $flux
 * @return string
 */
function lesscss_insert_head($flux){
	$flux .= '<'
		.'?php header("X-Spip-Filtre: '
		.'lesscss_cssify_head'
		.'"); ?'.'>';
	return $flux;
}


/**
 * Attraper automatiquement toutes les .less ou .less.css du head
 * les compiler, et les remplacer par leur css compilee
 *
 * @param string $head
 * @return string
 */
function lesscss_cssify_head($head){
	$url_base = url_de_base();
	$balises = extraire_balises($head,'link');
	$files = array();
	foreach ($balises as $s){
		if (extraire_attribut($s, 'rel') === 'stylesheet'
			AND (!($type = extraire_attribut($s, 'type')) OR $type == 'text/css')
			AND $src = extraire_attribut($s, 'href')
			// format .less.css ou .less avec un eventuel timestamp ?123456
			AND preg_match(",\.(less\.css|less)(\?\d+)?$,",$src)
			AND $src = preg_replace(",\?\d+$,","",$src)
			AND $src = preg_replace(",^$url_base,",_DIR_RACINE,$src)
			AND file_exists($src))
			$files[$s] = $src;
	}

	if (!count($files))
		return $head;

	foreach($files as $s=>$lessfile){
		$cssfile = less_css($lessfile);
		$m = @filemtime($cssfile);
		$s2 = inserer_attribut($s,"href","$cssfile?$m");
		$head = str_replace($s, $s2, $head);
	}

	return $head;
}

/*
 * Prise en charge de la balise #CSS{style.css}
 * Regles :
 * - cherche un .css ou un .css.html ou un .less comme feuille de style
 * - si un seul des 3 trouve dans le chemin il est renvoye (et compile au passage si .less)
 * - si un .css.html et un .css trouves dans le chemin, c'est le .css.html qui est pris (surcharge d'un statique avec une css calculee)
 * - si un .less et un (.css ou .css.html) on compare la priorite du chemin des deux trouves :
 *   le plus prioritaire des 2 est choisi
 *   si priorite equivalente on choisi le (.css ou .css.html) qui est le moins couteux a produire
 *   permet d'avoir dans le meme dossier le .less et sa version compilee .css : cette derniere est utilisee
 *
 * #CSS{style.css} renvoie dans tous les cas un fichier .css qui est soit :
 * - un .less compile en .css
 * - un .css statique
 * - un .css.html calcule en .css
 */
if (!function_exists('balise_CSS')) {
	function balise_CSS($p) {
		$_css = interprete_argument_balise(1,$p);
		$p->code = "timestamp(direction_css(lesscss_select_css($_css)))";
		$p->interdire_scripts = false;
		return $p;
	}
}

/**
 * Selectionner de preference la feuille .less (en la compilant)
 * et sinon garder la .css classiquement
 *
 * @param string $css_file
 * @return string
 */
function lesscss_select_css($css_file){
	if (function_exists('less_css')
	  AND substr($css_file,-4)==".css"){
		$less_file = substr($css_file,0,-4).".less";
		$less_or_css = lesscss_find_less_or_css_in_path($less_file, $css_file);
		if (substr($less_or_css,-5)==".less")
			return less_css($less_or_css);
		else
			return $less_or_css;
	}
	return find_in_path($css_file);
}

/**
 * Faire un find_in_path en cherchant un fichier .less ou .css
 * et en prenant le plus prioritaire des deux
 * ce qui permet de surcharger un .css avec un .less ou le contraire
 * Si ils sont dans le meme repertoire, c'est le .css qui est prioritaire,
 * par soucis de rapidite
 *
 * @param string $less_file
 * @param string $css_file
 * @return string
 */
function lesscss_find_less_or_css_in_path($less_file, $css_file){
	$l = find_in_path($less_file);
	$c = $f = trouver_fond($css_file);
	if (!$c)
		$c = find_in_path($css_file);

	if (!$l){
		// passer le host en contexte pour differencier les CSS en fonction du HOST car il est inscrit en url absolue
		// dans les chemins d'urls
		return ($f?produire_fond_statique($css_file,array('format'=>'css','host'=>$_SERVER['HTTP_HOST'])):$c);
	}
	elseif(!$c)
		return $l;

	// on a un less et un css en concurence
	// prioriser en fonction de leur position dans le path
	$path = creer_chemin();
	foreach($path as $dir) {
		// css prioritaire
		if (strncmp($c,$dir . $css_file,strlen($dir . $css_file))==0){
			// passer le host en contexte pour differencier les CSS en fonction du HOST car il est inscrit en url absolue
			// dans les chemins d'urls
			return ($f?produire_fond_statique($css_file,array('format'=>'css','host'=>$_SERVER['HTTP_HOST'])):$c);
		}
		if ($l == $dir . $less_file)
			return $l;
	}
	// on ne doit jamais arriver la !
	spip_log('Resolution chemin less/css impossible',_LOG_CRITIQUE);
	debug_print_backtrace();
	die('Erreur fatale, je suis perdu');
}
