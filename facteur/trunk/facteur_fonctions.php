<?php
/*
 * Plugin Facteur 2
 * (c) 2009-2011 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * facteur_addstyle
 * @author Eric Dols
 *
 * @param $matches
 * @return string
 */
function facteur_addstyle($matches) {

	// $matches[1]=tag, $matches[2]=tag attributes (if any), $matches[3]=xhtml closing (if any)

	// variables values set in calling function
	global $styledefinition, $styletag, $styleclass;

	// convert the style definition to a one-liner
	$styledefinition = preg_replace ("!\s+!mi", " ", $styledefinition );
	// convert all double-quotes to single-quotes
	$styledefinition = preg_replace ('/"/','\'', $styledefinition );

	if (preg_match ("/style\=/i", $matches[2])) {
			// add styles to existing style attribute if any already in the tag
			$pattern = "!(.* style\=)[\"]([^\"]*)[\"](.*)!mi";
			$replacement = "\$1".'"'."\$2 ".$styledefinition.'"'."\$3";
			$attributes = preg_replace ($pattern, $replacement , $matches[2]);
	} else {
			// otherwise add new style attribute to tag (none was present)
			$attributes = $matches[2].' style="'.$styledefinition.'"';
	}

	if ($styleclass!="") {
		// if we were injecting a class style, remove the now useless class attribute from the html tag

		// Single class in tag case (class="classname"): remove class attribute altogether
		$pattern = "!(.*) class\=['\"]".$styleclass."['\"](.*)!mi";
		$replacement = "\$1\$2";
		$attributes = preg_replace ( $pattern, $replacement, $attributes);

		// Multiple classes in tag case (class="classname anotherclass..."): remove class name from class attribute.
		// classes are injected inline and removed by order of appearance in <head> stylesheet
		// exact same behavior as where last declared class attributes in <style> take over (IE6 tested only)
		$pattern = "!(.* class\=['\"][^\"]*)(".$styleclass." | ".$styleclass.")([^\"]*['\"].*)!mi";
		$replacement = "\$1\$3";
		$attributes = preg_replace ( $pattern, $replacement, $attributes);

	}

	return "<".$matches[1].$attributes.$matches[3].">";
}

/**
 * Un filtre pour transformer les retour ligne texte en br si besoin (si pas autobr actif)
 *
 * @param string $texte
 * @return string
 */
function facteur_nl2br_si_pas_autobr($texte){
	return (_AUTOBR?$texte:nl2br($texte));
}

?>
