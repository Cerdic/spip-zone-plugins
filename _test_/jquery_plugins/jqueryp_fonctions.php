<?php

define('_DIR_JQUERY_UI', 'lib/jquery.ui-1.0');
define('_DIR_JQUERY_UI_THEMES', _DIR_JQUERY_UI . '/themes');

/*
 * Balise #JQUERY_UI{x, y?}
 * 
 * Ecrit le code html appelant le script jQuery UI
 * indiqué par x
 * Et optionnellement le theme css 
 * ou le nom du squelette css a interpreter
 * indique par y
 * 
 * Exemples : 
 * - #JQUERY_UI{tabs}
 * - #JQUERY_UI{tabs, light} // theme de jquery.ui
 * - #JQUERY_UI{tabs, jqueryui.tabs.css} // squelette jqueryui.tabs.css.html
 * 
 */
function balise_JQUERY_UI($p){

	if (!is_array($p->param))
		$p->param=array();
	
	$nom 	= interprete_argument_balise(1, $p);
	$nom	= str_replace("'", "", $nom);

	if ($fichier_js = find_in_path(_DIR_JQUERY_UI . '/ui.' . $nom . '.js')) {
		$p->code = '"<script type=\"text/javascript\" src=\"'
			. $fichier_js
			. '\"></script>"';
		$p->interdire_scripts = false;
		
		// theme
		$theme 	= interprete_argument_balise(2, $p);
		if ($theme	= str_replace("'", "", $theme)){
			// squelette css
			if (!jqueryui_stylesheets_link($p, $theme, true)){
				// ou theme comme jquery.ui
				jqueryui_stylesheets_link($p, _DIR_JQUERY_UI_THEMES . '/' . $theme . '/' . $theme . '.css');
				jqueryui_stylesheets_link($p, _DIR_JQUERY_UI_THEMES . '/' . $theme . '/' . $theme . '.' . $nom . '.css');
			}
		}									
	} else {
		$p->code = "''";
	}
	return $p;
}

/* ajoute le code html <link ... /> */
function jqueryui_stylesheets_link(&$p, $adresse, $generer_url=false){
	$_adresse = (($generer_url) ? $adresse . '.html' : $adresse);
	if ($f = find_in_path($_adresse))
		return $p->code .= '. "\n<link rel=\"stylesheet\" href=\"' 
				. (($generer_url)?generer_url_public($adresse):$f)
				. '\" type=\"text/css\" media=\"screen\" />"';	
	
	return false;
}
?>
