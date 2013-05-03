<?php
/*
 * Plugin LessCSS
 * Distribue sous licence MIT
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
	require_once 'lessphp/lessc.inc.php';

	// le compilateur lessc compile le contenu
	$less = new lessc();
	// lui transmettre le path qu'il utilise pour les @import
	$less->importDir = _chemin();

	try {
		$out = $less->parse($style);
		return $out;
	}
	// en cas d'erreur, on retourne du vide...
	catch (exception $ex) {
		spip_log('lessc fatal error:'.$ex->getMessage(),'less'._LOG_ERREUR);
		erreur_squelette(
			"LESS : Echec compilation"
			. (isset($contexte['file'])?" fichier ".$contexte['file']:"")
		  . "<br />".$ex->getMessage()
		);
		return '';
	}
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
		$f = basename($source,$r[0]);
		$f = sous_repertoire (_DIR_VAR, 'cache-less')
		. preg_replace(",(.*?)(_rtl|_ltr)?$,",
				"\\1-cssify-" . substr(md5("$source-lesscss-$chemin"), 0,7) . "\\2",
				$f, 1)
		. '.css';

		# si la feuille compilee est plus recente que la feuille source
		# l'utiliser sans rien faire, sauf si recalcul explicite
		$changed = false;
		if (@filemtime($f) < @filemtime($source))
			$changed = true;

		if (!$changed
		  AND (!defined('_VAR_MODE') OR _VAR_MODE != 'recalcul'))
			return $f;

		if (!lire_fichier($source, $contenu))
			return $source;

		# compiler le LESS si besoin (ne pas generer une erreur si source vide
		if (!$contenu){
			$contenu = "/* Source $source : vide */\n";
		}
		else {
			$contenu = lesscss_compile($contenu, array('file'=>$source));
		}
		// si erreur de compilation on renvoit un commentaire, et il y a deja eu un log
		if (!$contenu){
			$contenu = "/* Compilation $source : vide */\n";
		}

		# passer la css en url absolue (on ne peut pas le faire avant, car c'est du LESS, pas des CSS)
		$contenu = urls_absolues_css($contenu, $source);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		// on ecrit sur un fichier
		if (ecrire_fichier($f.".last", $contenu, true)){
			if ($changed OR md5_file($f)!=md5_file($f.".last")){
				@copy($f.".last",$f);
				clearstatcache(true,$f); // eviter que PHP ne reserve le vieux timestamp
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
 * @return void
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
function balise_CSS($p) {
	$_css = interprete_argument_balise(1,$p);
	$p->code = "timestamp(direction_css(lesscss_select_css($_css)))";
	$p->interdire_scripts = false;
	return $p;
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

	if (!$l)
		return ($f?produire_fond_statique($css_file,array('format'=>'css')):$c);
	elseif(!$c)
		return $l;

	// on a un less et un css en concurence
	// prioriser en fonction de leur position dans le path
	$path = creer_chemin();
	foreach($path as $dir) {
		// css prioritaire
		if (strncmp($c,$dir . $css_file,strlen($dir . $css_file))==0)
			return ($f?produire_fond_statique($css_file,array('format'=>'css')):$c);;
		if ($l == $dir . $less_file)
			return $l;
	}
	// on ne doit jamais arriver la !
	spip_log('Resolution chemin less/css impossible',_LOG_CRITIQUE);
	debug_print_backtrace();
	die('Erreur fatale, je suis perdu');
}

?>