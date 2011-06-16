<?php


function balise_CSS($p) {
	$_css = interprete_argument_balise(1,$p);
	$p->code = "timestamp(direction_css(zless_select_css($_css)))";
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
function zless_select_css($css_file){
	if (function_exists('less_css')
	  AND substr($css_file,-4)==".css"){
		$less_file = substr($css_file,0,-4).".less";
		$less_or_css = find_less_or_css_in_path($less_file, $css_file);
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
function find_less_or_css_in_path($less_file, $css_file){
	if ($l = find_in_path($less_file)){
		// ok il y a un less,
		// voyons si il y a aussi un css
		if ($c = find_in_path($css_file)){
			// on a un less et un css en concurence
			// prioriser en fonction de leur position dans le path
			$path = creer_chemin();
			foreach($path as $dir) {
				// css prioritaire
				if ($c == $dir . $css_file) return $c;
				if ($l == $dir . $less_file) return $l;
			}
			spip_log('Resolution chemin less/css impossible',_LOG_ERREUR);
			die('paf ?');
		}
		else
			return $l;
	}
	else
		return find_in_path($css_file);
}