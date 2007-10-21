<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', 'lib/');


/*
 * Balise #JQUERY_PLUGIN{x1, x2...}
 * 
 * Ecrit le code html appelant le script jQuery UI
 * indiqué par x
 * 
 * Exemples : 
 * - #JQUERY_PLUGIN{ui.tabs}
 * - #JQUERY_PLUGIN{ui.droppable, ui.mouse}
 */
function balise_JQUERY_PLUGIN($p){

	if (!is_array($p->param))
		$p->param=array();
	
	$i = 0;
	$liste_plugins = jqueryp_liste_plugins_dispo();
	$p->code = "''";
	while ($plug = interprete_argument_balise(++$i, $p)){
		if  ($plug == "''") 
			continue;
		$plug = str_replace("'", "", $plug);
		if (isset($liste_plugins[$plug])) {
			jqueryp_add_script($p, _DIR_LIB . $liste_plugins[$plug]);
		}	
	}
	$p->interdire_scripts = false;
	
	return $p;
}

/* Balise #JQUERY_PLUGIN{x, y1, y2...}
 *  
 * x : le nom du squelette css a interpreter
 * ou le nom d'un theme connu (light, dark, flora...)
 * 
 * Optionnellement le nom des plugins qui ont un theme specifique
 * comme flora.tabs.css 
 * #JQUERY_PLUGIN{flora} // theme de ui
 * #JQUERY_PLUGIN{flora, tabs} ajoute flora.css et flora.tabs.css
 * #JQUERY_PLUGIN{monquelette.css} ajoute un lien vers un squelette compile (monsquelette.css.html)
 */
function balise_JQUERY_PLUGIN_THEME($p){
	if (!is_array($p->param))
		$p->param=array();
		
	$p->code = "''";
	
	$theme = interprete_argument_balise(1, $p);
	if ($theme = str_replace("'", "", $theme)){

		// squelette css
		if (!jqueryp_add_link($p, $theme, true)){
			// ou theme comme jquery.ui flora
			$liste_themes = jqueryp_liste_themes_dispo();
			if (isset($liste_themes[$theme])) {
				jqueryp_add_link($p, _DIR_LIB . $liste_themes[$theme] . '/' . $theme . '.css');
				// extensions des themes flora.tabs
				$i = 1;
				while ($plug = interprete_argument_balise(++$i, $p)){
					if  ($plug == "''") 
						continue;
					$plug = str_replace("'", "", $plug);
					jqueryp_add_link($p, _DIR_LIB . $liste_themes[$theme] . '/' . $theme . '.' . $plug . '.css');
				}				
			}
		}
	}

	$p->interdire_scripts = false;
	return $p;
}
	
/* ajoute le code html <script ... /> */
function jqueryp_add_script(&$p, $adresse){
	if ($f = find_in_path($adresse))
		return $p->code .= '. "\n<script type=\"text/javascript\" src=\"'
				. $f . '\"></script>"';	
						
	return false;
}
	
/* ajoute le code html <link ... /> */
function jqueryp_add_link(&$p, $adresse, $generer_url=false){
	$_adresse = (($generer_url) ? $adresse . '.html' : $adresse);
	if ($f = find_in_path($_adresse))
		return $p->code .= '. "\n<link rel=\"stylesheet\" href=\"' 
				. (($generer_url)?generer_url_public($adresse):$f)
				. '\" type=\"text/css\" media=\"screen\" />"';	
	
	return false;
}


/* fourni un tableau 'nom' => 'adresse' des plugins possibles */
function jqueryp_liste_plugins_dispo(){
	global $jquery_plugins;
	
	$liste_plugins = array();
	foreach ($jquery_plugins as $nom_ext=>$extension) {
		foreach ($extension['files'] as $nom=>$fichier){
			$liste_plugins[$nom] = $extension['dir'] . '/' . $fichier;
		}
	}
	return $liste_plugins;
}

/* fourni un tableau 'nom' => 'adresse' des themes possibles */
function jqueryp_liste_themes_dispo(){
	global $jquery_plugins_themes;
	
	$liste_themes = array();
	foreach ($jquery_plugins_themes as $nom_ext=>$extension) {
		foreach ($extension['themes'] as $nom=>$dossier){
			$liste_themes[$nom] = $extension['dir'] . '/' . $dossier;
		}
	}
	return $liste_themes;
}
?>
