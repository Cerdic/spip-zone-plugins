<?php
/*
 * Plugin Scss
 * Distribue sous licence MIT
 *
 */
use Leafo\ScssPhp\Compiler;

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Compiler des styles inline SCSS en CSS
 *
 * @param string $style
 *   contenu du .scss
 * @param array $contexte
 *   file : chemin du fichier compile
 *          utilise en cas de message d'erreur, et pour le repertoire de reference des @import
 * @return string
 */
function scss_compile($style, $contexte = array()) {
	require_once 'scssphp/scss.inc.php';

	// le compilateur Leafo\ScssPhp\Compiler compile le contenu
	$scss = new Compiler();
	// lui transmettre le path qu'il utilise pour les @import
	$scss->setImportPaths(_chemin());
	
	// Si definie on affiche les commentaires des sources et numero de ligne dans le fichier css genere
	// **Les sourcemaps ne sont supportée completement que dans la version CLI de scssphp**
	// on utilise donc que LINE_COMMENTS, permettant un debugage plus facile avec une extension comme
	// https://addons.mozilla.org/fr/firefox/addon/firecompass-for-firebug/
	if (defined('_SCSS_LINE_COMMENTS') AND '_SCSS_LINE_COMMENTS' == true) {
        $scss->setLineNumberStyle(Compiler::LINE_COMMENTS);
	}
	
	try {
		$out = $scss->compile($style);
		return $out;
	}
	// en cas d'erreur, on retourne du vide...
	catch (exception $ex) {
		spip_log('SCSS Compiler fatal error:'.$ex->getMessage(),'scss'._LOG_ERREUR);
		erreur_squelette(
			"SCSS : Echec compilation"
			. (isset($contexte['file'])?" fichier ".$contexte['file']:"")
		  . "<br />".$ex->getMessage()
		);
		return '';
	}
}

/**
 * Transformer du SCSS en CSS
 * Peut prendre en entree
 * - un fichier .css ou .scss :
 *   [(#CHEMIN{messtyles.scss.css}|scss_css)]
 *   la sortie est un chemin vers un fichier CSS
 * - des styles inline,
 *   pour appliquer dans une feulle scss calculee :
 *   #FILTRE{scss_css}
 *   la sortie est du style inline
 *
 * @param string $source
 * @return string
 */
function scss_css($source){
	static $chemin = null;

	// Si on n'importe pas, est-ce un fichier ?
	if (
		!preg_match(',[\s{}],', $source)
		and preg_match(',\.(scss|css)$,i', $source, $r)
		and file_exists($source)
	) {
		static $done = array();
		// ne pas essayer de compiler deux fois le meme fichier dans le meme hit
		// si on a echoue une fois, on echouera pareil
		if (isset($done[$source])) {
			return $done[$source];
		}

		if (is_null($chemin)) {
			$chemin = _chemin();
			$chemin = md5(serialize($chemin));
		}
		
		$f = basename($source,$r[0]);
		$f = sous_repertoire (_DIR_VAR, 'cache-scss')
		. preg_replace(",(.*?)(_rtl|_ltr)?$,",
				"\\1-cssify-" . substr(md5("$source-scss-$chemin"), 0,7) . "\\2",
				$f, 1)
		. '.css';

		// si la feuille compilee est plus recente que la feuille source
		// l'utiliser sans rien faire, sauf si recalcul explicite
		$changed = false;
		if (@filemtime($f) < @filemtime($source))
			$changed = true;

		if (
			!$changed
			and (!($mode=_request('var_mode')) or $mode != 'css')
		) {
			return $f;
		}

		if (!lire_fichier($source, $contenu)) {
			return $source;
		}

		// compiler le SCSS si besoin (ne pas generer une erreur si source vide
		if (!$contenu){
			$contenu = "/* Source $source : vide */\n";
		}
		else {
			$contenu = scss_compile($contenu, array('file'=>$source));
		}
		
		// si erreur de compilation on renvoit un commentaire, et il y a deja eu un log
		if (!$contenu){
			$contenu = "/* Compilation $source : vide */\n";
		}

		// passer la css en url absolue (on ne peut pas le faire avant, car c'est du SCSS, pas des CSS)
		$contenu = urls_absolues_css($contenu, $source);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		// on ecrit sur un fichier
		if (ecrire_fichier($f.".last", $contenu, true)) {
			if ($changed OR md5_file($f)!=md5_file($f.".last")) {
				@copy($f.".last",$f);
				// eviter que PHP ne reserve le vieux timestamp
				if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
					clearstatcache(true,$f);
				}
				else {
					clearstatcache();
				}
			}
			
			return $done[$source] = $f;
		}
		else {
			return $done[$source] = $source;
		}
	}
	
	$source = scss_compile($source);
	
	if (!$source) {
		return "/* Erreur compilation SCSS : cf scss.log */";
	}
	else {
		return $source;
	}
}


/**
 * injecter l'appel au compresseur sous la forme de filtre
 * pour intervenir sur l'ensemble du head
 * du squelette public
 *
 * @param string $flux
 * @return string
 */
function scss_insert_head($flux) {
	$flux .= '<'
		.'?php header("X-Spip-Filtre: '
		.'scss_cssify_head'
		.'"); ?'.'>';
	return $flux;
}


/**
 * Attraper automatiquement toutes les .scss ou .scss.css du head
 * les compiler, et les remplacer par leur css compilee
 *
 * @param string $head
 * @return void
 */
function scss_cssify_head($head) {
	$url_base = url_de_base();
	$balises = extraire_balises($head,'link');
	$files = array();
	
	foreach ($balises as $s) {
		if (
			extraire_attribut($s, 'rel') === 'stylesheet'
			and (!($type = extraire_attribut($s, 'type')) OR $type == 'text/css')
			and $src = extraire_attribut($s, 'href')
			// format .scss.css ou .scss avec un eventuel timestamp ?123456
			and preg_match(",\.(scss\.css|scss)(\?\d+)?$,",$src)
			and $src = preg_replace(",\?\d+$,","",$src)
			and $src = preg_replace(",^$url_base,",_DIR_RACINE,$src)
			and file_exists($src)
		) {
			$files[$s] = $src;
		}
	}

	if (!count($files)) {
		return $head;
	}

	foreach ($files as $s=>$scssfile) {
		$cssfile = scss_css($scssfile);
		$m = @filemtime($cssfile);
		$s2 = inserer_attribut($s,"href","$cssfile?$m");
		$head = str_replace($s, $s2, $head);
	}
	
	return $head;
}

/*
 * Prise en charge de la balise #CSS{style.css}
 * Regles :
 * - cherche un .css ou un .css.html ou un .scss comme feuille de style
 * - si un seul des 3 trouve dans le chemin il est renvoye (et compile au passage si .scss)
 * - si un .css.html et un .css trouves dans le chemin, c'est le .css.html qui est pris (surcharge d'un statique avec une css calculee)
 * - si un .scss et un (.css ou .css.html) on compare la priorite du chemin des deux trouves :
 *   le plus prioritaire des 2 est choisi
 *   si priorite equivalente on choisi le (.css ou .css.html) qui est le moins couteux a produire
 *   permet d'avoir dans le meme dossier le .scss et sa version compilee .css : cette derniere est utilisee
 *
 * #CSS{style.css} renvoie dans tous les cas un fichier .css qui est soit :
 * - un .scss compile en .css
 * - un .css statique
 * - un .css.html calcule en .css
 */
function balise_CSS($p) {
	$_css = interprete_argument_balise(1,$p);
	$p->code = "timestamp(direction_css(scss_select_css($_css)))";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Selectionner de preference la feuille .scss (en la compilant)
 * et sinon garder la .css classiquement
 *
 * @param string $css_file
 * @return string
 */
function scss_select_css($css_file) {
	if (
		function_exists('scss_css')
		and substr($css_file,-4)==".css"
	) {
		$scss_file = substr($css_file,0,-4).".scss";
		$scss_or_css = scss_find_scss_or_css_in_path($scss_file, $css_file);
		
		if (substr($scss_or_css,-5)==".scss") {
			return scss_css($scss_or_css);
		}
		else {
			return $scss_or_css;
		}
	}
	
	return find_in_path($css_file);
}

/**
 * Faire un find_in_path en cherchant un fichier .scss ou .css
 * et en prenant le plus prioritaire des deux
 * ce qui permet de surcharger un .css avec un .scss ou le contraire
 * Si ils sont dans le meme repertoire, c'est le .css qui est prioritaire,
 * par soucis de rapidite
 *
 * @param string $scss_file
 * @param string $css_file
 * @return string
 */
function scss_find_scss_or_css_in_path($scss_file, $css_file) {
	$l = find_in_path($scss_file);
	$c = $f = trouver_fond($css_file);
	
	if (!$c) {
		$c = find_in_path($css_file);
	}

	if (!$l) {
		return ($f ? produire_fond_statique($css_file,array('format'=>'css')) : $c);
	}
	elseif (!$c) {
		return $l;
	}

	// on a un scss et un css en concurence
	// prioriser en fonction de leur position dans le path
	$path = creer_chemin();
	foreach ($path as $dir) {
		// css prioritaire
		if (strncmp($c,$dir . $css_file,strlen($dir . $css_file))==0) {
			return ($f ? produire_fond_statique($css_file,array('format'=>'css')) : $c);
		}
		if ($l == $dir . $scss_file) {
			return $l;
		}
	}
	
	// on ne doit jamais arriver la !
	spip_log('Resolution chemin scss/css impossible',_LOG_CRITIQUE);
	debug_print_backtrace();
	die('Erreur fatale, je suis perdu');
}

?>
