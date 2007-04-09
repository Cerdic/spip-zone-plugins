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
}


/*************/
/* FONCTIONS */
/*************/

// ajoute un tweak a $tweaks;
function add_tweak($tableau) {
	global $tweaks;
	$tweaks[] = $tableau;
}

// ajoute une variable à $tweak_variables
function add_variable($tableau) {
	global $tweak_variables;
	$tweak_variables[] = $tableau;
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
			if (find_in_path('tweaks/'.$inc.'.css')) $tweaks_metas_pipes['css'][] = $inc.'.css';
			if (find_in_path('tweaks/'.$inc.'.js')) $tweaks_metas_pipes['js'][] = $inc.'.js';
			// recherche d'un code inline eventuellement propose
			if (isset($tweak['code:options'])) $tweaks_pipelines['code_options'][] = $tweak['code:options'];
			if (isset($tweak['code:fonctions'])) $tweaks_pipelines['code_fonctions'][] = $tweak['code:fonctions'];
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

// remplace les valeurs marquees comme %%toto%% par la valeur reelle de $metas_vars['toto']
// attention : la description du tweak (trouvee dans lang/tweak_xx.php) doit 
// obligatoirement conporter la demande de valeur : %toto%
// %%toto/d%% oblige un nombre et %%toto/s%% oblige une chaine
// %%toto/valeurpardefaut%% renvoie valeurpardefaut si le meta n'existe pas encore
// syntaxe generale : %%toto/d/valeurpardefaut%% ou %%toto/s/valeurpardefaut%% 
// /s est une chaine, /d est un nombre
// pour les boutons radio, il faut utiliser deux variables. Par ex : set_options.
// $code est le code inline livre par tweak_spip_config
function tweak_parse_code($code) {
	global $metas_vars;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)(/[ds]|/r\(.*?\))?(/[^%]+)?%%,', $code, $matches)) {
		$rempl = '""';	
		// si le meta est present on garde la valeur du meta, sinon la valeur par defaut si elle existe
		if (isset($metas_vars[$matches[1]])) {
			$rempl = $metas_vars[$matches[1]];
			if (preg_match(',^"(.*?)"$,', trim($rempl), $matches2)) $rempl = str_replace('\"','"',$matches2[1]);
		} else { 
			$cmd = substr($matches[2], 1, 1);
			// une valeur par defaut est-elle specifiee ?
			$rempl = strlen($matches[3])>1?substr($matches[3],1):'""';
			// une commande d ou s est-elle specifiee ?
			if($cmd=='d') $rempl = 'intval('.$rempl.')';
				elseif($cmd=='s') $rempl = 'strval('.$rempl.')';
			eval('$rempl='.$rempl.';');
		}
		// si on ne veut pas de nombre, on met des guillemets !
		if($cmd!='d' && $rempl[0]!='"') $rempl = '"'.str_replace('"','\"',$rempl).'"';
		// placement de la variable
		$code = str_replace($matches[0], $rempl, $code);
		// on conserve le resultat dans $metas_vars
		$metas_vars[$matches[1]] = $rempl;
//print_r($metas_vars);
//print_r($matches); echo "rempl=$rempl\ncode=$code\n\n";
	}
	return $code;
}

// parse la description et renseigne le nombre de variables
function tweak_parse_description($tweak, $tweak_input) {
	global $tweaks, $tweak_variables, $metas_vars; 
//tweak_log(" -- tweak_parse_description({$tweaks[$tweak]['id']})");
	$tweaks[$tweak]['nb_variables'] = 0;
//	$tweaks[$tweak]['description'] = $tweaks[$tweak]['description'];
	$t = preg_split(',%([a-zA-Z_][a-zA-Z0-9_]*)%,', $tweaks[$tweak]['description'], -1, PREG_SPLIT_DELIM_CAPTURE);
	$descrip = '';
	$index = $tweaks[$tweak]['basic'];
	for($i=0;$i<count($t);$i+=2) if (($var=trim($t[$i+1]))!='') {
		// si le meta est present on remplace
		if (isset($metas_vars[$var]))
				$descrip .= $tweak_input(
					$index = $tweaks[$tweak]['basic']+(++$tweaks[$tweak]['nb_variables']), 
					$var, 
					$metas_vars[$var],
					$t[$i],
					$tweaks[$tweak]['actif'], 
					'tweak_spip_admin');
			else $descrip .= $t[$i]."[$var?]";
	} else $descrip .= $t[$i];
	if (count($t)==1) $descrip = "<p>$descrip</p>";
	$tweaks[$tweak]['description'] = "<div id='tweak_input-$index'>$descrip</div>";
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
//  - ces deux tableaus ne sont remplis qu'une seule fois, lors d'une initialisation totale
//    les hits ordinaires ne se servent que des metas, non des fichiers.
//  - l'initialisation totale insere en premier lieu tweak_spip_config.php
//

global $tweaks, $tweak_variables;
$tweaks = array();

// lancer l'initialisation
tweak_initialisation();

?>