<?php

/* Renvoie sur la balise env pour les balise_FOREACH */
function jqueryp_env($p, $nom){
	if (function_exists('balise_ENV'))
		return balise_ENV($p, $nom);
	else
		return balise_ENV_dist($p, $nom);	
}

function balise_JQUERY_PLUGINS_DISPO_dist($p) {
	return jqueryp_env($p, 'jqueryp_liste_plugins_groupe()');
}

function balise_JQUERY_PLUGINS_DISPO_GROUPE_dist($p) {
	return jqueryp_env($p, 'jqueryp_liste_plugins_dispo_groupe()');
} 

function balise_JQUERY_PLUGINS_TELECHARGEMENT_dist($p) {
	return jqueryp_env($p, 'jqueryp_liste_plugins(\'telechargeables\')');
} 


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
	$liste_plugins = jqueryp_liste_fichiers_dispo();
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
	
	// liste des themes
	$themes = jqueryp_liste_themes_dispo();
	
	$i = 0;
	while ($theme = interprete_argument_balise(++$i, $p)){
		$theme = str_replace("'", "", $theme);
		
		// squelette css
		if (jqueryp_add_link($p, $theme, true)){
			continue;
		// c'est le nom d'un theme
		} else {
			// si 'theme.extension', ne garder que 'theme'
			$t = explode('.',$theme);
			$t = array_shift($t);
			// si le theme existe
			if (isset($themes[$t])) {
				jqueryp_add_link($p, $themes[$t] . '/' . $theme . '.css');
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
				. $f . '\"></script>\n"';	
						
	return false;
}
	
/* ajoute le code html <link ... /> */
function jqueryp_add_link(&$p, $adresse, $generer_url=false){
	$_adresse = (($generer_url) ? $adresse . '.html' : $adresse);
	if ($f = find_in_path($_adresse))
		return $p->code .= '. "\n<link rel=\"stylesheet\" href=\"' 
				. (($generer_url)?generer_url_public($adresse):$f)
				. '\" type=\"text/css\" media=\"screen\" />\n"';	
	
	return false;
}

/* 
 * Retourne le contenu des fichiers js des plugin jquery dont les id 
 * sont envoyes.
 * 
 * - renvoie un array() avec le nom des fichiers à inserer
 *   pour le pipeline insert_jquery_plugins
 * jqueryp_add_plugins('ui.tabs', $flux);
 * jqueryp_add_plugins('ui.tabs','ui.dimensions', $flux);
 * 
 * - renvoie <script language='javascript'>...</script>
 *   pour le pipeline insert_head 
 * jqueryp_add_plugins('ui.tabs');
 * jqueryp_add_plugins(array('ui.tabs','ui.dimensions'));
 * 
 */
function jqueryp_add_plugins($plugins, $flux=null){
	static $lpda; // liste plugins deja actifs (même nom OU meme adresse)
		
	if (empty($lpda)) $lpda = array('nom' => array(), 'adresse' => array());
	if (!is_array($plugins)) $plugins = array($plugins);
	
	$lpa = jqueryp_liste_fichiers_dispo();
			
	$res = '';
	foreach ($plugins as $nom){
		if ($lpda['nom'][$nom] OR $lpda['adresse'][$lpa[$nom]])
			continue;
			
		if ($c = find_in_path($lpa[$nom])) {
			if (isset($flux)){
				if ($flux['type']=='fichier')
					$flux['data'][$nom] = substr($lpa[$nom],0,-3); // enlever '.js'
				elseif ($flux['type']=='inline')
					$flux['data'][$nom] =  spip_file_get_contents($c);
			} else {
				// inline dans insert_head (compat 1.9.2)
				$res .=  "\n\n" . spip_file_get_contents($c) . "\n\n";	
			}
			// pas deux fois la meme chose...
			$lpda['nom'][$nom] = $lpda['adresse'][$lpa[$nom]] = true;
		} else {
			spip_log("Adresse introuvable ($lpa[$nom]) sur $nom",'jquery_plugins');
		}
	}

	if (isset($flux))
		return $flux;
	else
		return "<script language='javascript'>" . $res . "</script>";
}



/* affiche un bouton pour telecharger ou mettre a jour
 * un plugin jquery
 * 
 * Soit on a un zip a recuperer,
 * soit un array de fichiers js
 */
function jqueryp_bouton_telechargement($id_jquery_plugin){
	global $jquery_plugins;
	$j = $jquery_plugins[$id_jquery_plugin];

	// deja present ?  texte du bouton :  "mettre a jour", sinon "telecharger"
	if (is_dir(_DIR_RACINE . _DIR_LIB . $j['dir'])){
		$quoi='update';
	} else {
		$quoi='install';
	}
			
	// zip -> chargeur plugin/lib de spip
	if (!is_array($j['install'])){
		$action = 'charger_plugin';
		$args = 'lib';
		$input = "<input type='hidden' name='url_zip_plugin' value='$j[install]' />";
	// js  -> recuperations des fichiers distants
	} else {
		$action = 'jqueryp_charger_lib';
		$args = '';
		$input = "<input type='hidden' name='id_jquery_plugin' value='$id_jquery_plugin' />"
				. "<input type='hidden' name='retour' value='".$_SERVER['QUERY_STRING']."' />";
	}
	
	include_spip('inc/actions');
	return redirige_action_auteur(
		$action, $args, '', '',
			$input
			."<input type='submit' class='fondo' name='ok' value='"
			. (($quoi=='update')?_T('jqueryp:bouton_mettre_a_jour'):_T('bouton_telecharger'))
			."' />","\nmethod='post'");

}


/*
 * Multiples utilisations du tableau jquery_plugins
 * 
 * Creation de listes d'elements
 * 
 * Pour la cause, j'ai appele un 'groupe'
 * le nom du plugin (ui, yav...)
 */
function jqueryp_liste_plugins($type, $groupe=''){
	global $jquery_plugins;

	// soit tous les plugins
	// soit juste un plugin (groupe)
	if (!$groupe){
		$plugs = $jquery_plugins;
	} else {
		$plugs = array($groupe=>$jquery_plugins[$groupe]);
	}
			
				
	$type = strtolower($type);	
	switch($type){
		
		case 'telechargeables':
			return $plugs;
			break;	
		
		
		case 'actifs':
			// retourne nom->adresse des plugins actifs
			// liste plugins (nom->adresse)
			$lpa = lire_config('jqueryp/plugins_actifs');
			$lpd  = jqueryp_liste_fichiers_dispo();
			
			$lp = array();
			if (is_array($lpa)){
				foreach ($lpa as $p){
					$lp[$p] = $lpd[$p];	
				}
			}
			
			return $lp;		
			break;
		
		
		case 'disponibles':
			$liste_plugins = array();
			$exclus = array('.pack','.min','.js.compresed');
			
			foreach ($plugs as $nom=>$extension){
				// eliminer les plugins non installes
				if (!is_dir($dir = _DIR_RACINE . _DIR_LIB . $extension['dir'])){
					unset($plugs[$nom]);
				// trouver les fichiers js et creer leurs alias	
				} else {
					include_spip('inc/flock');
					// tous les fichiers .js (adresse complete)
					$files = preg_files($dir.'/','.*\.js$',10000,false);
					// juste le nom du fichier
					$files = preg_replace(',^.*([^/]*)$,U','$1',$files);
					// aliaser
					$plugs[$nom]['files'] = array();
					foreach ($files as $f){
						// le nom peut contenir ui. ou jquery. et .js (parfois .pack.js)
						// on enleve tout cela
						if (preg_match(",^((jquery|$nom)\.)?([^.]*)(.*)\.js$,i",$f, $g)){
							// g4 : .pack .min .ext ...
							if (!in_array($g[4],$exclus)){
								$plugs[$nom]['files'][$nom . '.' . $g[3] . $g[4]] 
									= _DIR_LIB . $extension['dir'] . '/' . $f;
							}
						}
					}
				}
			}
			return $plugs;
			break;
			

	}	
}



/*
 * 
 * Creation de listes de themes
 * 
 * Pour la cause, j'ai appele un 'groupe'
 * le nom du plugin (ui, yav...)
 */
function jqueryp_liste_themes($type, $groupe=''){
	global $jquery_plugins;

	// soit tous les plugins
	// soit juste un plugin (groupe)
	if (!$groupe){
		$plugs = $jquery_plugins;
	} else {
		$plugs = array($groupe=>$jquery_plugins[$groupe]);
	}
			
				
	$type = strtolower($type);	
	switch($type){
		case 'disponibles':
			$liste_plugins = array();
			
			foreach ($plugs as $nom=>$extension){
				// eliminer les plugins non installes
				// eliminer les plugins dans themes
				if ((!is_dir($dir = _DIR_RACINE . _DIR_LIB . $extension['dir']))
					OR (!isset($extension['themes']))) {
						unset($plugs[$nom]);
				} else {
					// mettre la bonne adresse
					foreach ($extension['themes'] as $t=>$dir) {
						$plugs[$nom]['themes'][$t] = _DIR_LIB . $extension['dir'] . '/' . $dir;
					}
				}
			}
			return $plugs;
			break;
			

	}	
}



/*
 * Liste des fichiers js disponibles
 */
function jqueryp_liste_fichiers_dispo($groupe=''){
	$plugs = jqueryp_liste_plugins('disponibles', $groupe);
	
	// tous les alias dans un meme tableau
	$files = array();
	foreach ($plugs as $groupe=>$p){
		$files = array_merge($files, $p['files']);
	}
	return $files;
}


/*
 * Liste des fichiers css disponibles
 */
function jqueryp_liste_themes_dispo($groupe=''){
	$plugs = jqueryp_liste_themes('disponibles', $groupe);
	
	// tous les alias dans un meme tableau
	$files = array();
	foreach ($plugs as $groupe=>$p){
		$files = array_merge($files, $p['themes']);
	}
	return $files;
}


/*
 * Connaitre les groupes qui ont des plugins actifs
 * On les signale par actif=true
 */
function jqueryp_liste_plugins_groupe(){
	
	$actifs = array_keys(jqueryp_liste_plugins('actifs'));
	$groupes = jqueryp_liste_plugins('disponibles');
	
	foreach ($groupes as $nom_ext=>$valeurs) {
		$groupes[$nom_ext]['actif'] = false;
		// pour depliage auto de la liste
		// si un des plugins est coche
		foreach ($valeurs['files'] as $nom=>$url){
			if (in_array($nom, $actifs)){
				$groupes[$nom_ext]['actif'] = true;
				break;
			}
		}
	}

	return $groupes;
}

/* 
 * fonction pour balise foreach qui ne peut passer directement des arguments
 * enfin, du moins, je n'ai pas trouve comment
 */
function jqueryp_liste_plugins_dispo_groupe($groupe = ''){
	static $_plugins = array();
	static $_groupe;
	
	// on stocke tous les plugins commencant pas '$groupe'
	if (!empty($groupe)){
		$_plugins = jqueryp_liste_plugins('disponibles', $groupe);
		$_groupe = $groupe;
		return true;
	}
	return $_plugins[$_groupe]['files'];
}
?>
