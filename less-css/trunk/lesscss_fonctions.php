<?php

/**
 * Compiler des styles inline LESS en CSS
 * @param string $style
 * @return string
 */
function less_compile($style){
	require 'lessphp/lessc.inc.php';

	// le compilateur lessc compile le contenu
	$less = new lessc();
	try {
		$out = $less->parse($style);
		return $out;
	}
	// en cas d'erreur, on retourne du vide...
	catch (exception $ex) {
		spip_log('lessc fatal error:'.$ex->getMessage(),'less'._LOG_ERREUR);
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
	// Si on n'importe pas, est-ce un fichier ?
	if (!preg_match(',[\s{}],', $source)
	  AND preg_match(',\.(less|css)$,i', $source, $r)
	  AND file_exists($source)) {

		$f = basename($source,$r[0]);
		$f = sous_repertoire (_DIR_VAR, 'cache-less')
		. preg_replace(",(.*?)(_rtl|_ltr)?$,","\\1-cssify-"
		. substr(md5("$source-lesscss"), 0,4) . "\\2", $f, 1)
		. '.css';

		# si la feuille compilee est plus recente que la feuille source
		# l'utiliser sans rien faire, sauf si recalcul explicite
		if ((@filemtime($f) > @filemtime($source))
		  AND (!defined('_VAR_MODE') OR _VAR_MODE != 'recalcul'))
			return $f;

		if (!lire_fichier($source, $contenu))
			return $source;

		# compiler le LESS
		$contenu = less_compile($contenu);
		// si erreur de compilation on renvoit la source, et il y a deja eu un log
		if (!$contenu)
			return $source;
		# passer la css en url absolue (on ne peut pas le faire avant, car c'est du LESS, pas des CSS)
		$contenu = urls_absolues_css($contenu, $source);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		if (ecrire_fichier($f, $contenu, true))
			return $f;
		else
			return $source;
	}
	$source = less_compile($source);
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
		.'cssify_head'
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
function cssify_head($head){
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
?>