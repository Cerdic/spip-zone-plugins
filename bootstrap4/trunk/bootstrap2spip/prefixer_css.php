<?php

/**
 * Prefixer du css pour ameliorer la compat
 * Peut prendre en entree
 * - un fichier .css:
 *   la sortie est un chemin vers un fichier CSS
 * - des styles inline,
 *   pour appliquer dans une feulle scss calculee :
 *   #FILTRE{prefixer_css}
 *   la sortie est du style inline
 *
 * @param string $source
 * @return string
 */
function prefixer_css($source) {
	static $chemin = null;

	// Si on n'importe pas, est-ce un fichier ?
	if (
		!preg_match(',[\s{}],', $source)
		and preg_match(',\.css$,i', $source, $r)
		and file_exists($source)
	) {

		$f = basename($source, $r[0]);
		$f = sous_repertoire(_DIR_VAR, 'cache-prefixer')
		. preg_replace(
			',(.*?)(_rtl|_ltr)?$,',
			"\\1-prefixer-" . substr(md5("$source-prefixer"), 0, 7) . "\\2",
			$f,
			1
		)
		. '.css';

		# si la feuille prefixee est plus recente que la feuille source
		# l'utiliser sans rien faire, sauf si il y a un var_mode
		$changed = false;
		if (@filemtime($f) < @filemtime($source)){
			$changed = true;
		}

		// si pas change ET pas de var_mode du tout, rien a faire (performance)
		if (!$changed
			AND !defined('_VAR_MODE'))
			return $f;

		$contenu = false;
		if (!lire_fichier($source, $contenu)) {
			return $source;
		}

		// prefixer le CSS
		$contenu = prefixer_css_inline($contenu);

		// passer la css en url absolue
		$contenu = urls_absolues_css($contenu, $source);

		// ecrire le fichier destination, en cas d'echec renvoyer la source
		// on ecrit sur un fichier
		if (ecrire_fichier($f.'.last', $contenu, true)) {
			if ($changed or md5_file($f) != md5_file($f.'.last')) {
				@copy($f.'.last', $f);
				// eviter que PHP ne reserve le vieux timestamp
				if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
					clearstatcache(true, $f);
				} else {
					clearstatcache();
				}
			}

			return $f;
		} else {
			return $source;
		}
	}

	$source = prefixer_css_inline($source);
	return $source;
}

function prefixer_css_inline($css) {

	// les proprietes qu'on veut prefixer par une ou plusieurs variantes
	$list = [
		"align-items" => ["-ms-flex-align"],
		"align-content" => ["-ms-flex-line-pack"],
		"align-self" => ["-ms-flex-item-align"],
		"animation" => ["-webkit-animation"],
		"appearance" => ["-webkit-appearance", "-moz-appearance"],

		"backdrop-filter" => ["-webkit-backdrop-filter"],
		"backface-visibility" => ["-webkit-backface-visibility"],

		"column-count" => ["-webkit-column-count", "-moz-column-count"],
    "column-gap" => ["-webkit-column-gap", "-moz-column-gap"],

		"flex-basis" => ["-ms-flex-preferred-size"],
		"flex-grow" => ["-ms-flex-positive"],
		"flex-wrap" => ["-ms-flex-wrap"],
		"flex-flow" => ["-ms-flex-flow"],
		"flex-shrink" => ["-ms-flex-negative"],
		"flex-direction" => ["-ms-flex-direction"],
		"flex" => ["-ms-flex"],

		"justify-content" => ["-ms-flex-pack"],

		"order" => ["-ms-flex-order"],

		"user-select" => ["-webkit-user-select", "-moz-user-select", "-ms-user-select"],

		"text-decoration" => ["-webkit-text-decoration"],
    "text-decoration-skip-ink" => ["-webkit-text-decoration-skip-ink"],
		"touch-action" => ["-ms-touch-action"],
    "transform" => ["-webkit-transform"],

		// cas particuliers : la propriete ne change pas, mais on passe par le remplacement de valeurs qui fait le job de prefixage
		"display" => ["display"],
		"position" => ["position"],
	];

	// les renommages de valeur sur les proprietes remplacees
	$values_replacement = [
		"-ms-flex-align" => [
			"flex-start" => "start",
			"flex-end" => "end",
		],
		"-ms-flex-item-align" => [
			"flex-start" => "start",
			"flex-end" => "end",
		],
		"-ms-flex-line-pack" => [
			"flex-start" => "start",
			"flex-end" => "end",
			"space-between" => "justify",
			"space-around" => "distribute",
		],
		"-ms-flex-pack" => [
			"flex-start" => "start",
			"flex-end" => "end",
			"space-between" => "justify",
			"space-around" => "distribute",
		],
		"display" => [
			"flex" => "-ms-flexbox",
			"inline-flex" => "-ms-inline-flexbox",
		],
		"position" => [
			"sticky" => "-webkit-sticky",
		],
	];

	// les eventuelles evaluation de condition avant remplacement
	$prefix_conditions = [
		"-webkit-text-decoration" => "prefixer_is_value_multiple",
	];

	// et on lance
	foreach ($list as $property=>$prefixed_list) {
			$css = prefixer_prefix_property($css, $property, $prefixed_list, $prefix_conditions, $values_replacement);
	}
	return $css;
}

/**
 * Verifie si la valeur de la propriete est multiple (valeurs separees par des espaces)
 * @param $line
 * @return bool
 */
function prefixer_is_value_multiple($line) {
	$value = explode(':', $line,2)[1];
	$value = str_replace("!important", "", $value);
	$value = trim($value,';}');
	$value = trim($value);
	if (count(explode(' ', $value))>1) {
		return true;
	}
	return false;
}

/**
 * Remplacer une valeur par une ou plusieurs autres, chaque remplacement effectif produisant une nouvelle ligne
 * @param string $line
 * @param array $replacement
 * @return string
 */
function prefixer_replace_value($line, $replacement) {
	$out = '';
	list($base_property, $base_value) = explode(':', $line, 2);
	foreach ($replacement as $standard_value => $prefixed_values) {
		if (is_string($prefixed_values)) {
			$prefixed_values = [$prefixed_values];
		}
		foreach($prefixed_values as $prefixed_value) {
			$v = $base_value;
			if (strpos($base_value, $standard_value) !== false) {
					$v = preg_replace(",(^|[\s]){$standard_value}($|\s|;|\))," , "$1$prefixed_value$2", $v);
			}
			if ($v !== $base_value) {
				$out .= $base_property . ':' . $v;
			}
		}
	}
	if (!$out) {
		return $line;
	}
	return $out;
}

/**
 * Le parseur, qui recherche la propriete a prefixer et applique ensuite les remplacements necessaires
 * @param string $css
 * @param string $standard_property
 * @param array $prefixed_list
 * @param array $prefix_conditions
 * @param array $values_replacement
 * @return string
 */
function prefixer_prefix_property($css, $standard_property, $prefixed_list, $prefix_conditions, $values_replacement) {
	$p = 0;
	while (($p = strpos($css, $standard_property . ":", $p)) !== false) {
		// go back to previous space or { or ;
		$pstart = $pend = $p;
		while ($pstart>0 && !in_array($css[$pstart-1], array("\r","\n",'{',';','('))) {
			$pstart--;
			if (!in_array($css[$pstart], array(" ", "\t"))) {
				$p+=strlen($standard_property);
				continue 2;
			}
		}
		while ($p>0 && in_array($css[$pstart-1], array("\r","\n"))) {
			$pstart--;
		}
		$len = strlen($css);
		$endchars = array(';','}');
		if ($css[$pstart-1] == '(') {
				$pstart--;
				$endchars[] = ')';
		}
		while ($pend<$len && !in_array($css[$pend],$endchars)) {
			$pend++;
		}
		$pend++;

		$property_line = substr($css, $pstart, $pend - $pstart);
		if (substr($property_line, -1) == '}') {
				$property_line = substr($property_line, 0, -1) . ';';
		}
		$is_expression = false;
		// si on est entre des () c'est un @support ()
		if (substr($property_line, 0, 1) == '(' && substr($property_line, -1) == ')') {
				$property_line = $property_line . " or ";
				$is_expression = true;
		}
		$insert = "";
		foreach ($prefixed_list as $prefixed) {
			$line = str_replace("$standard_property:","$prefixed:", $property_line);
			if (!isset($prefix_conditions[$prefixed]) or $prefix_conditions[$prefixed]($line)) {
					if (isset($values_replacement[$prefixed])) {
						$line = prefixer_replace_value($line, $values_replacement[$prefixed]);
					}
					if ($line != $property_line) {
							$insert .= $line;
					}
			}
		}

		if ($insert) {
			if (!$is_expression) {
					$css = substr_replace($css, $insert, $pstart, 0);
			}
			else {
					$property = substr($css, $pstart, $pend - $pstart);
					$pstart++;
					$pend--;
					$insert = $insert . $property;
					$css = substr_replace($css, $insert, $pstart, $pend - $pstart);
			}
		}
		$p = $pend + strlen($insert);
	}
	return $css;
}