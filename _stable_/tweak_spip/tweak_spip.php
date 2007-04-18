<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  la configuration du plugin                         #
#-----------------------------------------------------#

tweak_log("Chargement de tweak_spip.php...");

/*****************/
/* COMPATIBILITE */
/*****************/

if (!defined('_DIR_PLUGIN_TWEAK_SPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$p=_DIR_PLUGINS.end($p); if ($p[strlen($p)-1]!='/') $p.='/';
	define('_DIR_PLUGIN_TWEAK_SPIP', $p);
}
if ($GLOBALS['spip_version_code']<1.92) {
	if (!function_exists('stripos')) {
		function stripos($botte, $aiguille) {
			if (preg_match('@^(.*)' . preg_quote($aiguille, '@') . '@isU', $botte, $regs)) return strlen($regs[1]);
			return false;
		}
	}
	if (!function_exists('ajax_action_greffe')) {
		function ajax_action_greffe($idom, $corps)	{
			return _request('var_ajaxcharset') ? $corps	: "\n<div id='$idom'>$corps\n</div>\n";
		}
	}
}

function tweak_suppr_metas_var($meta, $new = false) {
 global $metas_vars;
 if (!isset($metas_vars[$meta])) return;
 if ($new) {
 	if (preg_match(',([0-9A-Za-z_-]*)\(('.'[0-9A-Za-z_-]*=[A-Za-z_:-]+\|[0-9A-Za-z_:=>|-]+'.')\),', $metas_vars[$meta], $reg)) $metas_vars[$new] = $reg[1];
	else $metas_vars[$new] = $metas_vars[$meta];
 }
 unset($metas_vars[$meta]);
}

// on actualise/supprime de vieilles variables creees par les version anterieures de tweak-spip
function tweak_compatibilite_ascendante() {
	tweak_suppr_metas_var('set_options');
	tweak_suppr_metas_var('radio_set_options3');
	tweak_suppr_metas_var('radio_set_options', 'radio_set_options4');
	tweak_suppr_metas_var('radio_type_urls', 'radio_type_urls3');
	tweak_suppr_metas_var('radio_type_urls2', 'radio_type_urls3');
	tweak_suppr_metas_var('radio_filtrer_javascript', 'radio_filtrer_javascript3');
	tweak_suppr_metas_var('radio_filtrer_javascript2', 'radio_filtrer_javascript3');
	tweak_suppr_metas_var('radio_suivi_forums', 'radio_suivi_forums3');
	tweak_suppr_metas_var('desactive_cache');
	tweak_suppr_metas_var('radio_desactive_cache', 'radio_desactive_cache3');
	tweak_suppr_metas_var('target_blank');
	tweak_suppr_metas_var('');
}

/*************/
/* FONCTIONS */
/*************/

// ajoute un tweak a $tweaks;
function add_tweak($tableau) {
	global $tweaks;
	static $id; $id = isset($id)?$id + 10:0;
	if (!isset($tableau['id'])) { $tableau['id']='erreur'.count($tweaks); $tableau['nom'] = _T('tweak:erreur_id');	}
	$tableau['index'] = $id;
	$tweaks[$tableau['id']] = $tableau;
}

// ajoute une variable à $tweak_variables et fabrique une liste des chaines et des nombres
function add_variable($tableau) {
	global $tweak_variables;
	$nom = $tableau['nom'];
	$tweak_variables[$nom] = $tableau;
	// on fabrique ici une liste des chaines et une liste des nombres
	if($tableau['format']=='nombre') $tweak_variables['_nombres'][] = $nom;
		elseif($tableau['format']=='chaine') $tweak_variables['_chaines'][] = $nom;
}

// retourne la valeur 'defaut' (format php) de la variable apres compilation du code
// le resultat comporte des guillemets si c'est une chaine
function tweak_get_defaut($variable) {
	global $tweak_variables;
	// si la variable n'est pas declaree, serieux pb dans tweak_spip_config !
	if (!isset($tweak_variables[$variable])) {
		spip_log("Erreur - variable '$variable' non déclarée dans tweak_spip_config.php !");
		return false;
	}
	$variable = &$tweak_variables[$variable];
	$defaut = $variable['defaut'];
	if($variable['format']=='nombre') $defaut = "intval($defaut)";
		elseif($variable['format']=='chaine') $defaut = "strval($defaut)";
//tweak_log("tweak_get_defaut() - \$defaut[{$variable['nom']}] = $defaut");	
	eval("\$defaut=$defaut;");
	$defaut2 = tweak_php_format($defaut, $variable['format']!='nombre');
tweak_log(" -- tweak_get_defaut() - \$defaut[{$variable['nom']}] est devenu : $defaut2");	
	return $defaut2;
}
// installation de $tweaks_metas_pipes
// $type ici est egal a 'options' ou 'fonctions'
function set_tweaks_metas_pipes_fichier($tweaks_pipelines, $type) {
	global $tweaks_metas_pipes;
	$code = '';
	if (isset($tweaks_pipelines['inc_'.$type]))
		foreach ($tweaks_pipelines['inc_'.$type] as $inc) $code .= "include_spip('tweaks/$inc');\n";
	if (isset($tweaks_pipelines['code_'.$type]))
		foreach ($tweaks_pipelines['code_'.$type] as $inline) $code .= $inline."\n";
	// on optimise avant...
	$code = str_replace('intval("")', '0', $code);
	$code = str_replace("\n".'if(strlen($foo="")) ',"\n\$foo=''; //", $code);
	// ... en avant le code !
	$tweaks_metas_pipes[$type] = $code;
tweak_log("set_tweaks_metas_pipes_fichier($type) : strlen=".strlen($code));
	$fichier_dest = sous_repertoire(_DIR_TMP, "tweak-spip") . "mes_$type.php";
tweak_log(" -- fichier_dest = $fichier_dest");
	ecrire_fichier($fichier_dest, '<'."?php\n// Code de controle pour le plugin Tweak-SPIP\n++\$GLOBALS['tweak_$type'];\n$code?".'>');
}

// installation de $tweaks_metas_pipes
function set_tweaks_metas_pipes_pipeline($tweaks_pipelines, $pipeline) {
	global $tweaks_metas_pipes;
	$code = '';
	if (isset($tweaks_pipelines[$pipeline])) {
		foreach ($tweaks_pipelines[$pipeline]['inclure'] as $inc) $code .= "include_spip('tweaks/$inc');\n";
		foreach ($tweaks_pipelines[$pipeline]['fonction'] as $fonc) $code .= "if (function_exists('$fonc')) \$flux = $fonc(\$flux);\n\telse spip_log('Erreur - $fonc(\$flux) non definie !');\n";
	}
	$tweaks_metas_pipes[$pipeline] = $code;
tweak_log("set_tweaks_metas_pipes_pipeline($pipeline) : strlen=".strlen($code));
	$fichier_dest = sous_repertoire(_DIR_TMP, "tweak-spip") . "$pipeline.php";
tweak_log(" -- fichier_dest = $fichier_dest");
	ecrire_fichier($fichier_dest, '<'."?php\n// Code de contrôle pour le plugin Tweak-SPIP\n$code?".'>');
}

// est-ce que $pipe est un pipeline ?
function is_tweak_pipeline($pipe, &$set_pipe) {
	if ($ok = preg_match(',^pipeline:(.*?)$,', $pipe, $t)) $set_pipe = trim($t[1]);
	return $ok;
}

// est-ce que $traitement est un traitement ?
function is_tweak_traitements($traitement, $fonction, &$set_traitements_utilises) {
	if ($ok = preg_match(',^traitement:([A-Z]+):(pre|post)_([a-zA-Z0-9_-]+)$,', $traitement, $t))
		$set_traitements_utilises[$t[1]][$t[3]][$t[2]][] = $fonction;
	return $ok;
}

// lire un fichier php et retirer si possible les balises ?php
function tweak_lire_fichier_php($file) {
	$file=find_in_path($file);
	if ($file && lire_fichier($file, $php)) {
		if (preg_match(',^<\?php(.*)?\?>$,msi', trim($php), $regs)) return trim($regs[1]);
		return "\n"."?>\n".trim($php)."\n<"."?php\n";
	}
	return false;
}

// retourne une aide concernant les raccourcis ajoutes par le tweak
function tweak_aide_raccourcis() {
	global $tweaks;
	$aide = array();
	foreach ($tweaks as $tweak) {
		// stockage de la liste des fonctions par pipeline, si le tweak est actif...
		if ($tweak['actif']) {
			if (function_exists($f=$tweak['id'].'_raccourcis')) $aide[] = '<li style="margin-top: 0.7em;">' . $f() . '</li>';
			elseif (!preg_match(',:aide$,', _T("tweak:{$tweak['id']}:aide") ))
				$aide[] = '<li style="margin-top: 0.7em;">' .  _T("tweak:{$tweak['id']}:aide") . '</li>';
		}
	}
	if(!count($aide)) return '';
	return '<p><strong>' . _T('tweak:raccourcis') . '</strong></p><ul style="margin: 0 0 0 0.7em; padding-left: 0.7em; list-style-image: none; list-style-position: outside; ">' . join("\n", $aide) . '</ul>';
}

// retourne une aide concernant les pipelines utilises par le tweak
function tweak_aide_pipelines() {
	global $tweaks_metas_pipes, $tweaks;
	$aide = array();
	foreach (array_keys($tweaks_metas_pipes) as $pipe) {
		// stockage de la liste des pipelines et du nombre de tweaks actifs concernes
		$nb=0; foreach($tweaks as $tweak) if($tweak['actif'] && isset($tweak['pipeline:'.$pipe])) $nb++;
		if ($nb) $aide[] = '<li style="margin-top: 0.7em;">' .  _T('tweak:nbtweak'.($nb>1?'s':''), array('pipe'=>$pipe, 'nb'=>$nb)) . '</li>';
	}
	// nombre de tweaks actifs
	$nb = isset($GLOBALS['meta']['tweaks_actifs'])?count(unserialize($GLOBALS['meta']['tweaks_actifs'])):0;
	return '<p><strong>' . _T('tweak:pipelines') . '</strong> '.count($aide).'</p><ul style="margin: 0 0 0 0.7em; padding-left: 0.7em; list-style-image: none; list-style-position: outside; ">' . join("\n", $aide) . '</ul>'
		. '<p><strong>' . _T('tweak:actifs') . "</strong> $nb</p>";
}

// met en forme le fichier $f en vue d'un insertion en head
function tweak_insert_header($f, $type) {
	if ($type=='css') {
		include_spip('inc/filtres');
		return "<link rel=\"stylesheet\" href=\"".tweak_htmlpath(direction_css($f))."\" type=\"text/css\" media=\"projection, screen\" />\n";
	} elseif ($type=='js') 
		return "<script type=\"text/javascript\" src=\"".tweak_htmlpath($f)."\"></script>\n";
}
// sauve la configuration dans un fichier tmp/tweak-spip/config.php
function tweak_sauve_configuration() {
	global $tweaks, $metas_vars;
	$metas = $variables = $actifs = array();
	foreach($tweaks as $t) if($t['actif']) {
		$actifs[] = $t['id'];
		$variables = array_merge($variables, $t['variables']);
	}
	foreach($metas_vars as $i => $v) 
		if($i!='_chaines' && $i!='_nombres') $metas[] = "'$i' => '$v'";
	$sauve = "// Tweaks actifs\n\$tweaks = array('" . join("', '", $actifs) . "');\n";
	$sauve .= "// Variables actives\n\$variables = array('" . join("', '", $variables) . "');\n";
	$sauve .= "// Valeurs validees en metas\n\$valeurs = array(" . join(', ', $metas) . ");\n";
	$fichier_dest = sous_repertoire(_DIR_TMP, "tweak-spip") . "config.php";
	ecrire_fichier($fichier_dest, '<'."?php\n// Configuration de controle pour le plugin Tweak-SPIP\n\n$sauve?".'>');
}

// cree un tableau $tweaks_pipelines et initialise $tweaks_metas_pipes
function tweak_initialise_includes() {
	global $tweaks, $tweaks_metas_pipes;
	// toutes les infos sur les pipelines
	$tweaks_pipelines = array();
	// liste des pipelines utilises
	$pipelines_utilises = array();
	// liste des pipelines utilises
	$traitements_utilises = array();
	// parcours de tous les tweaks
	foreach ($tweaks as $i=>$tweak) {
		// stockage de la liste des fonctions par pipeline, si le tweak est actif...
		if ($tweak['actif']) {
			$inc = $tweak['id']; $pipe2 = '';
			foreach ($tweak as $pipe=>$fonc) {
				if (is_tweak_pipeline($pipe, $pipe2)) {
					// module a inclure
					$tweaks_pipelines[$pipe2]['inclure'][] = $inc;
					// fonction a appeler
					$tweaks_pipelines[$pipe2]['fonction'][] = $fonc;
					// liste des pipelines utilises
					if (!in_array($pipe2, $pipelines_utilises)) $pipelines_utilises[] = $pipe2;
				} elseif (is_tweak_traitements($pipe, $fonc, $traitements_utilises)) {
					// bah rien a faire du coup... $traitements_utilises est deja rempli
				}
			}
			// recherche d'un fichier .css et/ou .js eventuellement present dans tweaks/
			if ($f=find_in_path('tweaks/'.$inc.'.css')) $tweaks_metas_pipes['header'][] = tweak_insert_header($f, 'css');
			if ($f=find_in_path('tweaks/'.$inc.'.js')) $tweaks_metas_pipes['header'][] = tweak_insert_header($f, 'js');
			// recherche d'un code inline eventuellement propose
			if (isset($tweak['code:options'])) $tweaks_pipelines['code_options'][] = $tweak['code:options'];
			if (isset($tweak['code:fonctions'])) $tweaks_pipelines['code_fonctions'][] = $tweak['code:fonctions'];
			if (isset($tweak['code:css'])) $tweaks_pipelines['header'][] = "<style type=\"text/css\">\n"
				.tweak_parse_code_js($tweak['code:css'])."\n</style>";
			if (isset($tweak['code:js'])) $tweaks_pipelines['header'][] = "<script type=\"text/javascript\"><!--\n"
				.tweak_parse_code_js($tweak['code:js'])."\n// --></script>";
			// recherche d'un fichier montweak_options.php ou montweak_fonctions.php pour l'inserer dans le code
			if ($temp=tweak_lire_fichier_php('tweaks/'.$inc.'_options.php')) $tweaks_pipelines['code_options'][] = $temp;
			if ($temp=tweak_lire_fichier_php('tweaks/'.$inc.'_fonctions.php')) $tweaks_pipelines['code_fonctions'][] = $temp;
		}
	}
	// mise en code des traitements trouves
	foreach($traitements_utilises as $b=>$balise){
		foreach($balise as $f=>$fonction) {
			$pre = isset($fonction['pre'])?join('(', $fonction['pre']).'(':'';
			$post = isset($fonction['post'])?join('(', $fonction['post']).'(':'';
			$traitements_utilises[$b][$f] = $post.$f.'('.$pre;
		}
		$temp = "\$GLOBALS['table_des_traitements']['$b'][]='" . join('(', $traitements_utilises[$b]).'%s';
		$traitements_utilises[$b] = $temp . str_repeat(')', substr_count($temp, '(')) . "';";
	}
	$tweaks_pipelines['code_options'][] = "// Table des traitements\n" . join("\n", $traitements_utilises);
	// effacement du repertoire temporaire de controle
	if (@file_exists($f=sous_repertoire(_DIR_TMP, "tweak-spip"))) {
		include_spip('inc/getdocument');
		effacer_repertoire_temporaire($f);
	} else spip_log("Erreur - tweak_initialise_includes() : $f introuvable !");
	// installation de $tweaks_metas_pipes
	set_tweaks_metas_pipes_fichier($tweaks_pipelines, 'options');
	set_tweaks_metas_pipes_fichier($tweaks_pipelines, 'fonctions');
	foreach($pipelines_utilises as $pipe) set_tweaks_metas_pipes_pipeline($tweaks_pipelines, $pipe);
}

// retire les guillemets extremes s'il y en a
function tweak_retire_guillemets($valeur) {
	$valeur = trim($valeur);
	if (preg_match(',^"(.*)"$,', trim($valeur), $matches)) $valeur = str_replace('\"','"',$matches[1]);
	elseif (preg_match(',^\'(.*)\'$,', trim($valeur), $matches)) $valeur = str_replace("\'","'",$matches[1]);
	return $valeur;
}

// met en forme une valeur dans le stype php
function tweak_php_format($valeur, $is_chaine) {
	$valeur = tweak_retire_guillemets($valeur);
	return $is_chaine?'"'.str_replace('"', '\"', $valeur).'"':$valeur;
}

// retourne le code compile d'une variable en fonction de sa valeur
function tweak_get_code_variable($variable, $valeur) {
	global $tweak_variables;
	// si la variable n'a pas ete declaree
	if(!isset($tweak_variables[$variable])) return _L("// Variable '$variable' inconnue !");
	$tweak_variable = &$tweak_variables[$variable];
	// mise en forme php de $valeur
	if(!strlen($valeur)) { 
		if($tweak_variable['format']=='nombre') $valeur='0'; else $valeur='""'; 
	} else 
		$valeur = tweak_php_format($valeur, $tweak_variable['format']!='nombre');
	$code = '';
	foreach($tweak_variable as $type=>$param) if (preg_match(',^code(:?(.*))?$,', $type, $regs)) {
		$eval = '$test = ' . (strlen($regs[2])?str_replace('%s', $valeur, $regs[2]):'true') . ';';
		$test = false;
		eval($eval);
		if($test) return str_replace('%s', $valeur, $param);
	}
}

// remplace les valeurs marquees comme %%toto%% par le code reel prevu par $tweak_variables['toto']['code:condition']
// attention de bien declarer les variables a l'aide de add_variable()
function tweak_parse_code_php($code) {
	global $metas_vars, $tweak_variables;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)%%,U', $code, $matches)) {
		$nom = $matches[1];
		// la valeur de la variable n'est stockee dans les metas qu'au premier post
		if (isset($metas_vars[$nom])) {
			$rempl = tweak_get_code_variable($nom, $metas_vars[$nom]);
		} else { 
			// tant que le webmestre n'a pas poste, on prend la valeur (dynamique) par defaut
			$defaut = tweak_get_defaut($nom);
			$rempl = tweak_get_code_variable($nom, $defaut);
			$code = "/* Valeur par defaut : {$nom} = $defaut */\n" . $code;
		}
		$code = str_replace($matches[0], $rempl, $code);
//echo "\nRETURN CODE = $code";

	}
	return $code;
}

// remplace les valeurs marquees comme %%toto%% par la valeur reelle de $metas_vars['toto']
// + quelques optimisations du code
// si cette valeur n'existe pas encore, la valeur utilisee sera $tweak_variables['toto']['defaut']
// attention de bien declarer les variables a l'aide de add_variable()
function tweak_parse_code_js($code) {
	global $metas_vars, $tweak_variables;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)%%,U', $code, $matches)) {
		// la valeur de la variable n'est stockee dans les metas qu'au premier post
		if (isset($metas_vars[$matches[1]])) {
			$rempl = $metas_vars[$matches[1]];
		} else { 
			// tant que le webmestre n'a pas poste, on prend la valeur (dynamique) par defaut
			$rempl = tweak_get_defaut($matches[1]);
		}
		$code = str_replace($matches[0], $rempl, $code);
	}
	return tweak_optimise_js($code);
}

// attention : optimisation tres sommaire, pour codes simples !
// -> optimise les if(0), if(1), if(false), if(true)
function tweak_optimise_js($code) {
	$code = preg_replace(',if\s*\(\s*([^)]*\s*)\)\s*{\s*,imsS', 'if(\\1){', $code);
	$code = str_replace('if(false){', 'if(0){', $code);
	$code = str_replace('if(true){', 'if(1){', $code);
	if (preg_match(',if\(([0-9])\){(.*)$,msS', $code, $regs)) {
		$temp = $regs[2]; $ouvre = $ferme = -1; $nbouvre = 1;
		do {
			if ($ouvre===false) $min = $ferme + 1; else $min = min($ouvre, $ferme) + 1;
			$ouvre=strpos($temp, '{', $min);
			$ferme=strpos($temp, '}', $min);
			if($ferme!==false) { if($ouvre!==false && $ouvre<$ferme) $nbouvre++; else $nbouvre--; }
//echo "<:$min,$ouvre,$ferme,$nbouvre:>";
		} while($ferme!==false && $nbouvre>0);
		if($ferme===false) return "/* Erreur sur les accolades : \{$regs[2] */";
		$temp = substr($temp, 0, $ferme);
		$rempl = "if($regs[1])\{$temp}";
		if(intval($regs[1])) $code = str_replace($rempl, "/* optimisation 'if($regs[1])'*/ $temp", $code);
			else $code = str_replace($rempl, "/* optimisation '\{$temp}'*/", $code);
	}
	return $code;
}


// lance la fonction d'installation de chaque tweak actif, si elle existe.
function tweak_installe_tweaks() {
	global $tweaks;
	foreach($temp = $tweaks as $tweak) if ($tweak['actif']) {
		include_spip('tweaks/'.$tweak['id']);
		if (function_exists($f = $tweak['id'].'_installe')) {
			$f();
tweak_log(" -- $f() : installé !");
		}
	}
}

// on force la reinstallation complete des tweaks et des plugins
function tweak_initialisation_totale() {
	// on force la reinstallation complete des tweaks
	tweak_initialisation(true);
	// reinitialisation des pipelines, par precaution
	// if (file_exists($f = _DIR_TMP."charger_pipelines.php")) @unlink($f);
}

/*****************/
/* DEBUT DU CODE */
/*****************/

// les globales :
//
// $tweaks : tableau ultra complet avec tout ce qu'il faut savoir sur chaque tweak
// $tweak_variables : tableau de toutes les variables que les tweaks peuvent utiliser et manipuler
//  - ces deux tableaux ne sont remplis qu'une seule fois, lors d'une initialisation totale
//    les hits ordinaires ne se servent que des metas, non des fichiers.
//  - l'initialisation totale insere en premier lieu tweak_spip_config.php
//

global $tweaks, $tweak_variables;
$tweak_variables = $tweaks = array();

// liste des types de variable
$tweak_variables['_chaines'] = $tweak_variables['_nombres'] = array();

// lancer l'initialisation
tweak_initialisation();

//print_r(unserialize($GLOBALS['meta']['tweaks_variables']));
?>
