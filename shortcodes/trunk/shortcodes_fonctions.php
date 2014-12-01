<?php
/**
 * Fonctions utiles au plugin Shortcodes
 *
 * @plugin     Shortcodes
 * @copyright  2014
 * @author     Cédric
 * @licence    GNU/GPL
 * @package    SPIP\Shortcodes\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function shortcodes_pre_propre($texte){

	$texte = shortcodes_traiter($texte);
	return $texte;
}

/**
 * Ajouter la prise en charge d'un [shortcode param='xxx']WP style[/shortcode]
 * add_shortcode( 'myshortcode', 'my_shortcode_handler' );
 *
 * my_shortcode_handler est une fonction du type
 * function my_shortcode_handler($args, $content) {
 *   ....
 *   return $texte;
 * }
 *
 * @param $tagname
 * @param $handler
 * @return mixed
 */
function add_shortcode($tagname,$handler){
	static $shortcodes = array();
	if ($tagname=="-") return $shortcodes;
	$shortcodes[strtolower($tagname)] = $handler;
}

/**
 * Trouver les shortcodes et appeller les fonctions spip_shortcode_xxx pour chaque shortcode [xxx][/xxx]
 * @param $texte
 * @return mixed
 */
function shortcodes_traiter($texte){
	if (strpos($texte,"[")!==false
		AND strpos($texte,"]")!==false
	  AND count($shortcodes = add_shortcode("-","-"))){

		$tags = array_keys($shortcodes);
		$tags = implode("|",$tags);

		// les tags doubles ?
		if (strpos($texte,"[/")!==false){
			while(preg_match(",\[($tags)\b([^\]]*)\](.*)\[/\\1\],Uims",$texte,$match)){
				$tag = $match[1];
				$arg_string = $match[2];
				$content = shortcodes_traiter($match[3]); // traiter les shortcodes inclus
				if (function_exists($f=$shortcodes[strtolower($tag)])){
					$args = shortcodes_argumenter($arg_string);
					$replace = $f($args,$content);
				}
				else {
					erreur_squelette("No function <tt>$f</tt> for shortcode [$tag]");
					$replace = "&#91;$tag$arg_string&#93;$content&#91;/$tag&#93;";
				}
				$texte = str_replace($match[0],$replace,$texte);
			}
		}

		// les tags simples restants, non fermes
		while(preg_match(",\[($tags)\b([^\]]*)\],Uims",$texte,$match)){
			$tag = $match[1];
			$arg_string = $match[2];
			$content = null;
			if (function_exists($f=$shortcodes[strtolower($tag)])){
				$args = shortcodes_argumenter($arg_string);
				$replace = $f($args,$content);
			}
			else {
				erreur_squelette("No function <tt>$f</tt> for shortcode [$tag]");
				$replace = "&#91;$tag$arg_string&#93;$content&#91;/$tag&#93;";
			}
			$texte = str_replace($match[0],$replace,$texte);
		}

	}
	return $texte;
}

/**
 * Transformer la chaine d'arguments en tableau a passer a la fonction shortcode
 * @param $arg_string
 * @return array
 */
function shortcodes_argumenter($arg_string){
	$arg_string.=" ";
	// defaire le joli apostrophe sur la chaine d'arguments
	$arg_string = str_replace("&#8217;","'",$arg_string);
	$args = array();
	preg_match_all(",\b(\w+)=(['\"])(.*)(\\2)\s,Uims",$arg_string,$matches,PREG_SET_ORDER);
	foreach($matches as $match){
		$args[$match[1]]=$match[3];
	}
	return $args;
}

