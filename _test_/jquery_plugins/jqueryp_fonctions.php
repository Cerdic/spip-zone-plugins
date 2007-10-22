<?php

/*
 * Balise #JQUERY_PLUGIN{x1, x2...}
 * 
 * Ecrit le code html appelant le script jQuery UI
 * indique par x
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
			jqueryp_add_script($p, $liste_plugins[$plug]);
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
				jqueryp_add_link($p, $liste_themes[$theme] . '/' . $theme . '.css');
				// extensions des themes flora.tabs
				$i = 1;
				while ($plug = interprete_argument_balise(++$i, $p)){
					if  ($plug == "''") 
						continue;
					$plug = str_replace("'", "", $plug);
					jqueryp_add_link($p, $liste_themes[$theme] . '/' . $theme . '.' . $plug . '.css');
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

/* 
 * Retourne le contenu des fichiers js des plugin jquery dont les id 
 * sont envoyes. En profite pour les compacter 
 * 
 * jqueryp_add_plugins('ui.tabs');
 * jqueryp_add_plugins(array('ui.tabs','ui.dimensions'));
 */
function jqueryp_add_plugins($plugins){
	static $lpda; // liste plugins deja actifs (même nom OU meme adresse)
	if (empty($lpda)) $lpda = array('nom' => array(), 'adresse' => array());
	if (!is_array($plugins)) $plugins = array($plugins);
	
	$lpa = jqueryp_liste_plugins_dispo();	
	$res = '';
	foreach ($plugins as $nom){
		if ($lpda['nom'][$nom] OR $lpda['adresse'][$lpa[$nom]])
			continue;
			
		if ($c = find_in_path($lpa[$nom])) {
			$res .=  "\n\n" . compacte_js(spip_file_get_contents($c)) . "\n\n";
			$lpda['nom'][$nom] = $lpda['adresse'][$lpa[$nom]] = true;
		} else {
			spip_log("Adresse introuvable ($lpa[$nom]) sur $nom",'jquery_plugins');
		}
	}
	
	return $res;
}

/* fourni un tableau 'nom' => 'adresse' des plugins possibles */
function jqueryp_liste_plugins_dispo(){
	$l = jqueryp_liste_dispo();
	return $l['plugins'];
}

/* fourni un tableau 'nom' => 'adresse' des themes possibles */
function jqueryp_liste_themes_dispo(){
	$l = jqueryp_liste_dispo();
	return $l['themes'];
}

function jqueryp_liste_dispo($theme = false){
	global $jquery_plugins;
	
	$liste_plugins = array();
	$liste_themes = array();
	foreach ($jquery_plugins as $nom_ext=>$extension) {
		if(isset($extension['files'])){
			foreach ($extension['files'] as $nom=>$fichier){
				$liste_plugins[$nom] = _DIR_LIB . $extension['dir'] . '/' . $fichier;
			}
		}
		if(isset($extension['themes'])){
			foreach ($extension['themes'] as $nom=>$dossier){
				$liste_themes[$nom] = _DIR_LIB . $extension['dir'] . '/' . $extension['dir_theme'] . '/' . $dossier;
			}
		}
	}
	
	return array('plugins' => $liste_plugins, 'themes'  => $liste_themes);
}

function jqueryp_liste_plugins_actifs(){
	$l = jqueryp_liste_actifs();
	return $l['plugins'];
}

function jqueryp_liste_actifs(){
	// liste plugins (nom->adresse)
	$lpa = lire_config('jqueryp/plugins_actifs');
	$lpd  = jqueryp_liste_plugins_dispo();

	foreach ($lpa as $p){
		$lp[$p] = $lpd[$p];	
	}
	
	return 
		array(
			'plugins' => $lp
		);
}

function balise_JQUERY_PLUGINS_DISPO_dist($p) {
	if(function_exists('balise_ENV'))
		return balise_ENV($p, 'jqueryp_liste_plugins_dispo()');
	else
		return balise_ENV_dist($p, 'jqueryp_liste_plugins_dispo()');
	return $p;
}
?>
