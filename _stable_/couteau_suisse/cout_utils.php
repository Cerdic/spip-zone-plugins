<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  la configuration du plugin                         #
#-----------------------------------------------------#

cs_log("chargement de cout_utils.php et lancement de cs_initialisation...");
$GLOBALS['cs_utils']++;

// $outils : tableau ultra complet avec tout ce qu'il faut savoir sur chaque outil
// $cs_variables : tableau de toutes les variables que les outils peuvent utiliser et manipuler
//  - ces deux tableaux ne sont remplis qu'une seule fois, lors d'une initialisation totale
//    les hits ordinaires ne se servent que des metas, non des fichiers.
//  - l'initialisation totale insere en premier lieu config_outils.php
global $outils, $cs_variables;
$cs_variables = $outils = array();
// liste des types de variable
$cs_variables['_chaines'] = $cs_variables['_nombres'] = array();

/*****************/
/* COMPATIBILITE */
/*****************/

if (!defined('_DIR_PLUGIN_COUTEAU_SUISSE')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$p=_DIR_PLUGINS.end($p); if ($p[strlen($p)-1]!='/') $p.='/';
	define('_DIR_PLUGIN_COUTEAU_SUISSE', $p);
}
if(defined('_SPIP19100')) {
	function compacte_css($texte) { return $texte; }
	function compacte_js($texte) { return $texte; }
}

// SPIP 1.93 a change cette fonction. donc, en attendant mieux...
function cs_ajax_action_greffe($idom, $corps, $br='<br />')	{
	return _request('var_ajaxcharset') ? "$br$corps"	: "\n<div id='$idom'>$corps\n</div>\n";
}

function cs_suppr_metas_var($meta, $new = false) {
 global $metas_vars;
 if (!isset($metas_vars[$meta])) return;
 if ($new) {
 	if (preg_match(',([0-9A-Za-z_-]*)\(('.'[0-9A-Za-z_-]*=[A-Za-z_:-]+\|[0-9A-Za-z_:=>|-]+'.')\),', $metas_vars[$meta], $reg)) $metas_vars[$new] = $reg[1];
	else $metas_vars[$new] = $metas_vars[$meta];
 }
 unset($metas_vars[$meta]);
}

// on actualise/supprime de vieilles variables creees par les version anterieures du Couteau Suisse
function cs_compatibilite_ascendante() {
	cs_suppr_metas_var('set_options');
	cs_suppr_metas_var('radio_set_options3');
	cs_suppr_metas_var('radio_set_options', 'radio_set_options4');
	cs_suppr_metas_var('radio_type_urls', 'radio_type_urls3');
	cs_suppr_metas_var('radio_type_urls2', 'radio_type_urls3');
	cs_suppr_metas_var('radio_filtrer_javascript', 'radio_filtrer_javascript3');
	cs_suppr_metas_var('radio_filtrer_javascript2', 'radio_filtrer_javascript3');
	cs_suppr_metas_var('radio_suivi_forums', 'radio_suivi_forums3');
	cs_suppr_metas_var('desactive_cache');
	cs_suppr_metas_var('radio_desactive_cache', 'radio_desactive_cache3');
	cs_suppr_metas_var('target_blank');
	cs_suppr_metas_var('url_glossaire_externe', 'url_glossaire_externe2');
	cs_suppr_metas_var('');
}

/*************/
/* FONCTIONS */
/*************/

// ajoute un outil a $outils;
function add_outil($tableau) {
	global $outils;
	static $id; $id = isset($id)?$id + 10:0;
	if (!isset($tableau['id'])) { $tableau['id']='erreur'.count($outils); $tableau['nom'] = _T('desc:erreur_id');	}
	$tableau['index'] = $id;
	$outils[$tableau['id']] = $tableau;
}

// ajoute une variable a $cs_variables et fabrique une liste des chaines et des nombres
function add_variable($tableau) {
	global $cs_variables;
	$nom = $tableau['nom'];
	$cs_variables[$nom] = $tableau;
	// on fabrique ici une liste des chaines et une liste des nombres
	if($tableau['format']=='nombre') $cs_variables['_nombres'][] = $nom;
		elseif($tableau['format']=='chaine') $cs_variables['_chaines'][] = $nom;
}

// retourne la valeur 'defaut' (format php) de la variable apres compilation du code
// le resultat comporte des guillemets si c'est une chaine
function cs_get_defaut($variable) {
	global $cs_variables;
	// si la variable n'est pas declaree, serieux pb dans config_outils !
	if (!isset($cs_variables[$variable])) {
		spip_log("Erreur - variable '$variable' non declaree dans config_outils.php !");
		return false;
	}
	$variable = &$cs_variables[$variable];
	$defaut = $variable['defaut'];
	if(!strlen($defaut)) $defaut = "''";
	if($variable['format']=='nombre') $defaut = "intval($defaut)";
		elseif($variable['format']=='chaine') $defaut = "strval($defaut)";
//cs_log("cs_get_defaut() - \$defaut[{$variable['nom']}] = $defaut");
	eval("\$defaut=$defaut;");
	$defaut2 = cs_php_format($defaut, $variable['format']!='nombre');
//cs_log(" -- cs_get_defaut() - \$defaut[{$variable['nom']}] est devenu : $defaut2");
	return $defaut2;
}

// $type ici est egal a 'options' ou 'fonctions'
function ecrire_fichier_en_tmp($infos_pipelines, $type) {
	global $cs_metas_pipelines;
	$code = '';
	if (isset($infos_pipelines['inc_'.$type]))
		foreach ($infos_pipelines['inc_'.$type] as $inc) $code .= "include_spip('outils/$inc');\n";
	if (isset($infos_pipelines['code_'.$type]))
		foreach ($infos_pipelines['code_'.$type] as $inline) $code .= $inline."\n";
	// on optimise avant...
	$code = str_replace(array('intval("")',"intval('')"), '0', $code);
	$code = str_replace("\n".'if(strlen($foo="")) ',"\n\$foo=''; //", $code);
	// ... en avant le code !
	$fichier_dest = _DIR_CS_TMP . "mes_$type.php";
cs_log("ecrire_fichier_en_tmp($type) : lgr=".strlen($code))." pour $fichier_dest";
	if(!ecrire_fichier($fichier_dest, '<'."?php\n// Code d'inclusion pour le plugin 'Couteau Suisse'\n++\$GLOBALS['cs_$type'];\n$code?".'>', true))
		cs_log("ERREUR ECRITURE : $fichier_dest");
}

// installation de $cs_metas_pipelines
function set_cs_metas_pipelines_pipeline($infos_pipelines, $pipeline) {
	global $cs_metas_pipelines;
	// precaution utile ?
	$code = '';//"@include(_COUT_FONCTIONS_PHP);\n";
	if (isset($infos_pipelines[$pipeline])) {
		$a = &$infos_pipelines[$pipeline];
		if(is_array($a['inline'])) foreach ($a['inline'] as $inc) $code .= "$inc\n";
		if(is_array($a['inclure'])) foreach ($a['inclure'] as $inc) $code .= "include_spip('outils/$inc');\n";
		if(is_array($a['fonction'])) foreach ($a['fonction'] as $fonc) $code .= "if (function_exists('$fonc')) \$flux=$fonc(\$flux);\n\telse spip_log('Erreur - $fonc(\$flux) non definie !');\n";
	}
	$cs_metas_pipelines[$pipeline] = $code;
cs_log("set_cs_metas_pipelines_pipeline($pipeline) : strlen=".strlen($code));
	return "# Copie du code code utilise en eval() pour le pipeline '$pipeline(\$flux)'\n$code";
}

// est-ce que $pipe est un pipeline ?
function is_pipeline_outil($pipe, &$set_pipe) {
	if ($ok = preg_match(',^pipeline:(.*?)$,', $pipe, $t)) $set_pipe = trim($t[1]);
	return $ok;
}
// est-ce que $pipe est un pipeline inline?
function is_pipeline_outil_inline($pipe, &$set_pipe) {
	if ($ok = preg_match(',^pipelinecode:(.*?)$,', $pipe, $t)) $set_pipe = trim($t[1]);
	return $ok;
}

// est-ce que $traitement est un traitement ?
function is_traitements_outil($traitement, $fonction, &$set_traitements_utilises) {
	if ($ok = preg_match(',^traitement:([A-Z_]+)/?([a-z]+)?:(pre|post)_([a-zA-Z0-9_-]+)$,', $traitement, $t)) {
		if(!strlen($t[2])) $t[2] = 0;
		$set_traitements_utilises[$t[1]][$t[2]][$t[4]][$t[3]][] = $fonction;
	} elseif ($ok = preg_match(',^traitement:([A-Z]+)$,', $traitement, $t))
		$set_traitements_utilises[$t[1]][0][0][] = $fonction;
	return $ok;
}

// lire un fichier php et retirer si possible les balises ?php
function cs_lire_fichier_php($file) {
	$file=find_in_path($file);
	if ($file && lire_fichier($file, $php)) {
		if (preg_match(',^<\?php(.*)?\?>$,msi', trim($php), $regs)) return trim($regs[1]);
		return "\n"."?>\n".trim($php)."\n<"."?php\n";
	}
	return false;
}

// retourne une aide concernant les raccourcis ajoutes par l'outil
function cs_aide_raccourcis() {
	global $outils;
	$aide = array();
	foreach ($outils as $outil) {
		// stockage de la liste des fonctions par pipeline, si l'outil est actif...
		if ($outil['actif']) {
			$id = $outil['id'];
			include_spip('outils/'.$id);	
			if (function_exists($f = $id.'_raccourcis')) $aide[] = '<li style="margin: 0.7em 0 0 0;">&bull; ' . $f() . '</li>';
			elseif (!preg_match(',:aide$,', _T("desc:$id:aide") ))
				$aide[] = '<li style="margin: 0.7em 0 0 0;">&bull; ' .  _T("desc:$id:aide") . '</li>';
		}
	}
	if(!count($aide)) return '';
	return '<p><b>' . _T('desc:raccourcis') . '</b></p><ul style="list-style-type:none; padding:0; margin: 0; list-style-image: none; list-style-position: outside; ">' . join("\n", $aide) . '</ul>';
}

// retourne une aide concernant les pipelines utilises par l'outil
function cs_aide_pipelines() {
	global $cs_metas_pipelines, $outils, $metas_outils;
	$aide = array();
	foreach (array_keys($cs_metas_pipelines) as $pipe) {
		// stockage de la liste des pipelines et du nombre d'outils actifs concernes
		$nb=0; foreach($outils as $outil) if($outil['actif'] && isset($outil['pipeline:'.$pipe])) $nb++;
		if ($nb) $aide[] = _T('desc:nb_outil'.($nb>1?'s':''), array('pipe'=>$pipe, 'nb'=>$nb));
	}
	// nombre d'outils actifs
	$nb=0; foreach($metas_outils as $o) if($o['actif']) $nb++;
	// nombre d'outils caches
	$ca = isset($GLOBALS['meta']['tweaks_caches'])?count(unserialize($GLOBALS['meta']['tweaks_caches'])):0;
	return '<p><b>' . _T('desc:pipelines') . '</b> '.count($aide).'</p><p style="margin-left:1em;">' . join("<br/>", $aide) . '</p>'
		. '<p><b>' . _T('desc:actifs') . "</b> $nb</p>"
		. '<p><b>' . _T('desc:caches') . "</b> $ca</p>";
}

// met en forme le fichier $f en vue d'un insertion en head
function cs_insert_header($f, $type) {
	if ($type=='css') {
		include_spip('inc/filtres');
		return "<link rel=\"stylesheet\" href=\"".url_absolue(direction_css($f))."\" type=\"text/css\" media=\"projection, screen\" />\n";
	} elseif ($type=='js')
		return "<script type=\"text/javascript\" src=\"".url_absolue($f)."\"></script>\n";
}
// sauve la configuration dans un fichier tmp/couteau-suisse/config.php
function cs_sauve_configuration() {
	global $outils, $metas_vars;
	$metas = $metas_actifs = $variables = $lesoutils = $actifs = array();
	foreach($outils as $t) {
		$lesoutils[] = "\t// ".$t['nom']."\n\t'".$t['id']."' => '".join('|', $t['variables']) . "'";
		if($t['actif']) {
			$actifs[] = $t['id'];
			$variables = array_merge($variables, $t['variables']);
		}
	}
	foreach($metas_vars as $i => $v) if($i!='_chaines' && $i!='_nombres') {
		$metas[] = $temp = "\t'$i' => " . cs_php_format($v, in_array($i, $metas_vars['_chaines']));
		if(in_array($i, $variables)) $metas_actifs[] = $temp;
	}
	$sauve = "// Tous les outils et leurs variables\n\$liste_outils = array(\n" . join(",\n", $lesoutils) . "\n);\n"
		. "\n// Outils actifs\n\$outils_actifs =\n\t'" . join('|', $actifs) . "';\n"
		. "\n// Variables actives\n\$variables_actives =\n\t'" . join('|', $variables) . "';\n"
		. "\n// Valeurs validees en metas\n\$valeurs_validees = array(\n" . join(",\n", $metas) . "\n);\n";

$sauve .= $temp = "\n######## PACK ACTUEL DE CONFIGURATION DU COUTEAU SUISSE #########\n"
	. "\n// Attention, les surcharges sur les define() ou les globales ne sont pas specifiees ici\n"
	. "\$GLOBALS['cs_installer']['"._T('desc:pack')."'] = array(\n\n\t// Installation des outils par defaut\n"
	. "\t'outils' =>\n\t\t'".join('|', $actifs)."',\n"
	. "\n\t// Installation des variables par defaut\n"
	. "\t'variables' => array(\n\t" . join(",\n\t", $metas_actifs) . "\n\t)\n);\n";

	ecrire_fichier(_DIR_CS_TMP.'config.php', '<'."?php\n// Configuration de controle pour le plugin 'Couteau Suisse'\n\n$sauve?".'>');
	if($_GET['cmd']=='pack') $GLOBALS['cs_pack_actuel'] = $temp;
}

// cree un tableau $infos_pipelines et initialise $cs_metas_pipelines
function cs_initialise_includes() {
	global $outils, $cs_metas_pipelines;
	// toutes les infos sur les pipelines
	$infos_pipelines = array();
	// liste des pipelines utilises
	$pipelines_utilises = array();
	// liste des pipelines utilises
	$traitements_utilises = array();
	// variables temporaires
	$temp_css = $temp_js = $temp_jq = $temp_filtre_imprimer = array();
	// pour la fonction inclure_page()
	include_spip('public/assembler');
	// inclure d'office outils/cout_fonctions.php
	if ($temp=cs_lire_fichier_php("outils/cout_fonctions.php"))
		$infos_pipelines['code_fonctions'][] = $temp;
	// parcours de tous les outils
	foreach ($outils as $i=>$outil) {
		// stockage de la liste des fonctions par pipeline, si l'outil est actif...
		if ($outil['actif']) {
			$inc = $outil['id']; $pipe2 = '';
			foreach ($outil as $pipe=>$fonc) {
				if (is_pipeline_outil($pipe, $pipe2)) {
					// module a inclure
					$infos_pipelines[$pipe2]['inclure'][] = $inc;
					// fonction a appeler
					$infos_pipelines[$pipe2]['fonction'][] = $fonc;
					// liste des pipelines utilises
					if (!in_array($pipe2, $pipelines_utilises)) $pipelines_utilises[] = $pipe2;
				} elseif (is_pipeline_outil_inline($pipe, $pipe2)) {
					// code inline
					$infos_pipelines[$pipe2]['inline'][] = $fonc;
					if (!in_array($pipe2, $pipelines_utilises)) $pipelines_utilises[] = $pipe2;
				} elseif (is_traitements_outil($pipe, $fonc, $traitements_utilises)) {
					// rien a faire : $traitements_utilises est rempli par is_traitements_outil()
				}
			}
			// recherche d'un fichier .css, .css.html et/ou .js eventuellement present dans outils/
			if ($f=find_in_path($_css = "outils/$inc.css")) $cs_metas_pipelines['header'][] = cs_insert_header($f, 'css');
			if ($f=find_in_path("outils/$inc.js")) $cs_metas_pipelines['header'][] = cs_insert_header($f, 'js');
			 // en fait on peut pas car les balises vont devoir etre traitees et les traitements ne sont pas encore dispo !
			if ($f=find_in_path("outils/$inc.css.html")) { 
				// ici, cout_fonction.php va etre appele pour traiter les balises du css
				$GLOBALS['cs_options']++;
				$f = inclure_page($_css, array('fond'=>$_css));
				$GLOBALS['cs_options']--;
				$temp_css[] = $f['texte']; 
			}
			// recherche d'un code inline eventuellement propose
			if (isset($outil['code:options'])) $infos_pipelines['code_options'][] = $outil['code:options'];
			if (isset($outil['code:fonctions'])) $infos_pipelines['code_fonctions'][] = $outil['code:fonctions'];
			if (isset($outil['code:css'])) $temp_css[] = cs_parse_code_js($outil['code:css']);
			if (isset($outil['code:js'])) $temp_js[] = cs_parse_code_js($outil['code:js']);
			if (isset($outil['code:jq'])) $temp_jq[] = cs_parse_code_js($outil['code:jq']);
			// recherche d'un fichier monoutil_options.php ou monoutil_fonctions.php pour l'inserer dans le code
			if ($temp=cs_lire_fichier_php("outils/{$inc}_options.php")) 
				$infos_pipelines['code_options'][] = $temp;
			if ($temp=cs_lire_fichier_php("outils/{$inc}_fonctions.php"))
				$infos_pipelines['code_fonctions'][] = $temp;
		}
	}
	// insertion du css pour la BarreTypo
	if(in_array('BT_toolbox', $pipelines_utilises))
		$temp_css[] = 'span.cs_BT {background-color:#FFDDAA; font-weight:bold; border:1px outset #CCCC99; padding:0.2em 0.3em;}
span.cs_BTg {font-size:140%; padding:0 0.3em;}';
	// concatenation des css inline, js inline et filtres trouves
	if (count($temp_css))
		$cs_metas_pipelines['header'][] = "<style type=\"text/css\">\n"
			.compacte_css(join("\n", $temp_css))."\n</style>";
	if (count($temp_jq))
		$temp_js[] = "if (window.jQuery) jQuery(document).ready(function(){\n".join("\n", $temp_jq)."\n});";
	if (count($temp_js))
		$cs_metas_pipelines['header'][] = "<script type=\"text/javascript\"><!--\n"
			.compacte_js(join("\n", $temp_js))."\n// --></script>";
	// mise en code des traitements trouves
	foreach($traitements_utilises as $b=>$balise) {
		foreach($balise as $p=>$precision) {
			// ici, on fait attention de ne pas melanger propre et typo
			if(array_key_exists('typo', $precision) && array_key_exists('propre', $precision)) die(_T('desc:erreur:traitements'));
			foreach($precision as $f=>$fonction)  {
				if ($f===0)	$traitements_utilises[$b][$p][$f] = join("(", array_reverse($fonction)).'(';
				else {
					$pre = isset($fonction['pre'])?join('(', $fonction['pre']).'(':'';
					$post = isset($fonction['post'])?join('(', $fonction['post']).'(':'';
					$traitements_utilises[$b][$p][$f] = $post.$f.'('.$pre;
				}
			}
			$temp = "\$GLOBALS['table_des_traitements']['$b'][" . ($p=='0'?'':"'$p'") . "]='" . join('(', $traitements_utilises[$b][$p]).'%s';
			$traitements_utilises[$b][$p] = $temp . str_repeat(')', substr_count($temp, '(')) . "';";
		}
		$traitements_utilises[$b] = join("\n", $traitements_utilises[$b]);		
	}
	if(count($traitements_utilises))
		$infos_pipelines['code_options'][] = "// Table des traitements\n" . join("\n", $traitements_utilises);
	// effacement du repertoire temporaire de controle
	if (@file_exists(_DIR_CS_TMP) && ($handle = @opendir(_DIR_CS_TMP))) {
		while (($fichier = @readdir($handle)) !== false)
			if ($fichier[0] != '.')	supprimer_fichier(_DIR_CS_TMP.$fichier);
		closedir($handle);
	} else spip_log('Erreur - cs_initialise_includes() : '._DIR_CS_TMP.' introuvable !');
	// installation de $cs_metas_pipelines
	ecrire_fichier_en_tmp($infos_pipelines, 'options');
	ecrire_fichier_en_tmp($infos_pipelines, 'fonctions');
	$code = array();
	foreach($pipelines_utilises as $pipe) $code[] = set_cs_metas_pipelines_pipeline($infos_pipelines, $pipe);
	if($nb=count($code))
		ecrire_fichier(_DIR_CS_TMP . "pipelines.php", 
			'<'."?php\n// Code de controle pour le plugin 'Couteau Suisse' : $nb pipeline(s) actif(s)\n\n".join("\n", $code).'?'.'>');
}

// retire les guillemets extremes s'il y en a
function cs_retire_guillemets($valeur) {
	if (preg_match(',^\'(.*)\'$,ms', trim($valeur), $matches)) 
		return str_replace("\'", "'", $matches[1]);
	if (preg_match(',^"(.*)"$,ms', trim($valeur), $matches))
		return str_replace('\"', '"', $matches[1]);
	return $valeur;
}

// met en forme une valeur dans le stype php
function cs_php_format($valeur, $is_chaine = true) {
	$valeur = cs_retire_guillemets($valeur);
	if(!strlen($valeur)) return $is_chaine?"''":0;
	if(!$is_chaine) return $valeur;
	$valeur = str_replace("\\", "\\\\", $valeur);
	return "'".str_replace("'", "\\'", $valeur)."'";
}

// retourne le code compile d'une variable en fonction de sa valeur
function cs_get_code_variable($variable, $valeur) {
	global $cs_variables;
	// si la variable n'a pas ete declaree
	if(!isset($cs_variables[$variable])) return _L("// Variable '$variable' inconnue !");
	$cs_variable = &$cs_variables[$variable];
	// mise en forme php de $valeur
	if(!strlen($valeur)) {
		if($cs_variable['format']=='nombre') $valeur='0'; else $valeur='""';
	} else
		$valeur = cs_php_format($valeur, $cs_variable['format']!='nombre');
	$code = '';
	foreach($cs_variable as $type=>$param) if (preg_match(',^code(:(.*))?$,', $type, $regs)) {
		$eval = '$test = ' . (isset($regs[2])?str_replace('%s', $valeur, $regs[2]):'true') . ';';
		$test = false;
		eval($eval);
		if($test) return str_replace('%s', $valeur, $param);
	}
}

// remplace les valeurs marquees comme %%toto%% par le code reel prevu par $cs_variables['toto']['code:condition']
// attention de bien declarer les variables a l'aide de add_variable()
function cs_parse_code_php($code) {
	global $metas_vars, $cs_variables;
	while(preg_match(",([']?)%%([a-zA-Z_][a-zA-Z0-9_]*?)%%([']?),", $code, $matches)) {
		$cotes = $matches[1]=="'" && $matches[3]=="'";
		$nom = $matches[2];
		// la valeur de la variable n'est stockee dans les metas qu'au premier post
		if (isset($metas_vars[$nom])) {
			$rempl = cs_get_code_variable($nom, $metas_vars[$nom]);
		} else {
			// tant que le webmestre n'a pas poste, on prend la valeur (dynamique) par defaut
			$defaut = cs_get_defaut($nom);
			$rempl = cs_get_code_variable($nom, $defaut);
			$code = "/* Valeur par defaut : {$nom} = $defaut */\n" . $code;
		}
		if ($cotes) $rempl = str_replace("'", "\'", $rempl);
		$code = str_replace($matches[0], $matches[1].$rempl.$matches[3], $code);
//echo "\nRETURN CODE = $code";
	}
	return $code;
}

// remplace les valeurs marquees comme %%toto%% par la valeur reelle de $metas_vars['toto']
// + quelques optimisations du code
// si cette valeur n'existe pas encore, la valeur utilisee sera $cs_variables['toto']['defaut']
// attention de bien declarer les variables a l'aide de add_variable()
function cs_parse_code_js($code) {
	global $metas_vars, $cs_variables;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)%%,U', $code, $matches)) {
		// la valeur de la variable n'est stockee dans les metas qu'au premier post
		if (isset($metas_vars[$matches[1]])) {
			$rempl = $metas_vars[$matches[1]];
		} else {
			// tant que le webmestre n'a pas poste, on prend la valeur (dynamique) par defaut
			$rempl = cs_get_defaut($matches[1]);
		}
		$code = str_replace($matches[0], $rempl, $code);
	}
	return cs_optimise_js($code);
}

// attention : optimisation tres sommaire, pour codes simples !
// -> optimise les if(0), if(1), if(false), if(true)
function cs_optimise_js($code) {
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
		} while($ferme!==false && $nbouvre>0);
		if($ferme===false) return "/* Erreur sur les accolades : \{$regs[2] */";
		$temp = substr($temp, 0, $ferme);
		$rempl = "if($regs[1])\{$temp}";
		if(intval($regs[1])) $code = str_replace($rempl, "/* optimisation : 'if($regs[1])' */ $temp", $code);
			else $code = str_replace($rempl, "/* optimisation : 'if($regs[1]) {$temp}' */", $code);
	}
	return $code;
}


// lance la fonction d'installation de chaque outil actif, si elle existe.
// la fonction doit etre ecrite sous la forme monoutil_installe() et placee
// dans le fichier outils/monoutil.php
function cs_installe_outils() {
	global $metas_outils;
	foreach($metas_outils as $nom=>$o) if($o['actif']) {
		include_spip('outils/'.$nom);
		if (function_exists($f = $nom.'_installe')) {
			$f();
cs_log(" -- $f() : OK !");
		}
	}
	ecrire_metas();
}

?>