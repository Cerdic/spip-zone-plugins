<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  la configuration du plugin                         #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

cs_log("chargement de cout_utils.php");
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
define('_format_CHAINE', 10);
define('_format_NOMBRE', 20);

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
	effacer_meta('cs_decoupe');
	if(defined('_SPIP19300')) {
		if($metas_vars['radio_desactive_cache3']==1) $metas_vars['radio_desactive_cache4']=-1;
		cs_suppr_metas_var('radio_desactive_cache3');
	}
}

/*************/
/* FONCTIONS */
/*************/

// ajoute un outil a $outils;
function add_outil($tableau) {
	global $outils;
	static $id; $id = isset($id)?$id + 10:0;
	if (!isset($tableau['id'])) { $tableau['id']='erreur'.count($outils); $tableau['nom'] = _T('couteauprive:erreur_id');	}
	$tableau['index'] = $id;
	$outils[$tableau['id']] = $tableau;
}

// ajoute une variable a $cs_variables et fabrique une liste des chaines et des nombres
function add_variable($tableau) {
	global $cs_variables;
	$nom = $tableau['nom'];
	// code '%s' par defaut si aucun code n'est defini
	$test=false; 
	foreach(array_keys($tableau) as $key) if($test=preg_match(',^code(:(.*))?$,', $key)) break;
	if(!$test) $tableau['code'] = '%s';
	// enregistrement
	$cs_variables[$nom] = $tableau;
	// on fabrique ici une liste des chaines et une liste des nombres
	if($tableau['format']==_format_NOMBRE) $cs_variables['_nombres'][] = $nom;
		elseif($tableau['format']==_format_CHAINE) $cs_variables['_chaines'][] = $nom;

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
	if($variable['format']==_format_NOMBRE) $defaut = "intval($defaut)";
		elseif($variable['format']==_format_CHAINE) $defaut = "strval($defaut)";
//cs_log("cs_get_defaut() - \$defaut[{$variable['nom']}] = $defaut");
	eval("\$defaut=$defaut;");
	$defaut2 = cs_php_format($defaut, $variable['format']!=_format_NOMBRE);
//cs_log(" -- cs_get_defaut() - \$defaut[{$variable['nom']}] est devenu : $defaut2");
	return $defaut2;
}

// $type ici est egal a 'spip_options', 'options' ou 'fonctions'
function ecrire_fichier_en_tmp(&$infos_fichiers, $type) {
	$code = '';
	if (isset($infos_fichiers['inc_'.$type]))
		foreach ($infos_fichiers['inc_'.$type] as $inc) $code .= "include_spip('outils/$inc');\n";
	if (isset($infos_fichiers['code_'.$type]))
		foreach ($infos_fichiers['code_'.$type] as $inline) $code .= $inline."\n";
	// on optimise avant...
	$code = str_replace(array('intval("")',"intval('')"), '0', $code);
	$code = str_replace("\n".'if(strlen($foo="")) ',"\n\$foo=''; //", $code);
	// ... en avant le code !
	$fichier_dest = _DIR_CS_TMP . "mes_$type.php";
cs_log("ecrire_fichier_en_tmp($type) : lgr=".strlen($code))." pour $fichier_dest";
	if(!ecrire_fichier($fichier_dest, '<'."?php\n// Code d'inclusion pour le plugin 'Couteau Suisse'\n++\$GLOBALS['cs_$type'];\n$code?".'>', true))
		cs_log("ERREUR ECRITURE : $fichier_dest");
}

function set_cs_metas_pipelines(&$infos_pipelines) {
	global $cs_metas_pipelines;
	$controle='';
	foreach($infos_pipelines as $pipe=>$infos) {
		$code = "\n# Copie du code utilise en eval() pour le pipeline '$pipe(\$flux)'\n";
		// compilation des differentes facon d'utiliser un pipeline
		if(is_array($infos['inclure'])) foreach ($infos['inclure'] as $inc) $code .= "include_spip('outils/$inc');\n";
		if(is_array($infos['inline'])) foreach ($infos['inline'] as $inc) $code .= "$inc\n";
		if(is_array($infos['fonction'])) foreach ($infos['fonction'] as $fonc) $code .= "if (function_exists('$fonc')) \$flux=$fonc(\$flux);\n\telse spip_log('Erreur - $fonc(\$flux) non definie !');\n";
		$controle .= $cs_metas_pipelines[$pipe] = $code;
	}
	$nb = count($infos_pipelines);
cs_log("$nb pipeline(s) actif(s) : strlen=".strlen($controle));
	ecrire_fichier(_DIR_CS_TMP . "pipelines.php", 
		'<'."?php\n// Code de controle pour le plugin 'Couteau Suisse' : $nb pipeline(s) actif(s)\n{$controle}?".'>');
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
			elseif (!preg_match(',:aide$,', _T("couteauprive:$id:aide") ))
				$aide[] = '<li style="margin: 0.7em 0 0 0;">&bull; ' .  _T("couteauprive:$id:aide") . '</li>';
		}
	}
	if(!count($aide)) return '';
	// remplacement des constantes de forme @_CS_XXXX@
	$aide = preg_replace_callback(',@(_CS_[a-zA-Z0-9_]+)@,', 
		create_function('$matches','return defined($matches[1])?constant($matches[1]):"";'), join("\n", $aide));
	return '<p><b>' . _T('couteauprive:raccourcis') . '</b></p><ul class="cs_raccourcis">' . $aide . '</ul>';
}

// retourne une aide concernant les pipelines utilises par l'outil
function cs_aide_pipelines() {
	global $cs_metas_pipelines, $outils, $metas_outils;
	$aide = array();
	foreach (array_keys($cs_metas_pipelines) as $pipe) {
		// stockage de la liste des pipelines et du nombre d'outils actifs concernes
		$nb=0; foreach($outils as $outil) if($outil['actif'] && (isset($outil['pipeline:'.$pipe]) || isset($outil['pipelinecode:'.$pipe]))) $nb++;
		if ($nb) $aide[] = _T('couteauprive:outil_nb'.($nb>1?'s':''), array('pipe'=>$pipe, 'nb'=>$nb));
	}
	// nombre d'outils actifs
	$nb=0; foreach($metas_outils as $o) if($o['actif']) $nb++;
	// nombre d'outils caches
	$ca = isset($GLOBALS['meta']['tweaks_caches'])?count(unserialize($GLOBALS['meta']['tweaks_caches'])):0;
	return '<p><b>' . _T('couteauprive:pipelines') . '</b> '.count($aide).'</p><p style="margin-left:1em;">' . join("<br/>", $aide) . '</p>'
		. '<p><b>' . _T('couteauprive:outils_actifs') . "</b> $nb</p>"
		. '<p><b>' . _T('couteauprive:outils_caches') . "</b> $ca</p>";
}

// met en forme le fichier $f en vue d'un insertion en head
function cs_insert_header($f, $type) {
	if ($type=='css') {
		include_spip('inc/filtres');
		return '<link rel="stylesheet" href="'.url_absolue(direction_css($f)).'" type="text/css" media="projection, screen" />';
	} elseif ($type=='js')
		return '<script type="text/javascript" src="'.url_absolue($f).'"></script>';
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

include_spip('inc/charset');
$sauve .= $temp = "\n######## "._T('couteauprive:pack_actuel_titre')." #########\n\n// "
	. unicode2charset(html2unicode(_T('couteauprive:pack_actuel_avert')."\n\n"
		. "\$GLOBALS['cs_installer']['"._T('couteauprive:pack_actuel', array('date'=>cs_date()))."'] = array(\n\n\t// "._T('couteauprive:pack_outils_defaut')."\n"
		. "\t'outils' =>\n\t\t'".join(",\n\t\t", $actifs)."',\n"
		. "\n\t// "._T('couteauprive:pack_variables_defaut')."\n"))
	. "\t'variables' => array(\n\t" . join(",\n\t", $metas_actifs) . "\n\t)\n);\n";

	ecrire_fichier(_DIR_CS_TMP.'config.php', '<'."?php\n// Configuration de controle pour le plugin 'Couteau Suisse'\n\n$sauve?".'>');
	if($_GET['cmd']=='pack') $GLOBALS['cs_pack_actuel'] = $temp;
}

// cree les tableaux $infos_pipelines et $infos_fichiers, puis initialise $cs_metas_pipelines
function cs_initialise_includes($count_metas_outils) {
	global $outils, $cs_metas_pipelines;
	// toutes les infos sur les fichiers mes_options/mes_fonctions et sur les pipelines;
	$infos_pipelines = $infos_fichiers =
	// liste des traitements utilises
	$traitements_utilises =
	// variables temporaires
	$temp_html = $temp_css = $temp_js = $temp_jq = $temp_jq_init = $temp_filtre_imprimer = array();
	// inclure d'office outils/cout_fonctions.php
	if ($temp=cs_lire_fichier_php("outils/cout_fonctions.php"))
		$infos_fichiers['code_fonctions'][] = $temp;
	// variable de verification
	$infos_fichiers['code_options'][] = "\$GLOBALS['cs_verif']=$count_metas_outils;";
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
				} elseif (is_pipeline_outil_inline($pipe, $pipe2)) {
					// code inline
					$infos_pipelines[$pipe2]['inline'][] = cs_optimise_if(cs_parse_code_js($fonc));
				} elseif (is_traitements_outil($pipe, $fonc, $traitements_utilises)) {
					// rien a faire : $traitements_utilises est rempli par is_traitements_outil()
				}
			}
			// recherche d'un fichier .css, .css.html et/ou .js eventuellement present dans outils/
			if ($f=find_in_path($_css = "outils/$inc.css")) $cs_metas_pipelines['header'][] = cs_insert_header($f, 'css');
			if ($f=find_in_path("outils/$inc.js")) $cs_metas_pipelines['header'][] = cs_insert_header($f, 'js');
			 // en fait on peut pas compiler ici car les balises vont devoir etre traitees et les traitements ne sont pas encore dispo !
			if ($f=find_in_path("outils/$inc.css.html")) {
				// le code est mis de cote. il sera compile plus tard au moment du pipeline grace a cs_compile_header()
				lire_fichier($f, $ff);
				$temp_html[] = $ff;
			}
			// recherche d'un code inline eventuellement propose
			if (isset($outil['code:spip_options'])) $infos_fichiers['code_spip_options'][] = $outil['code:spip_options'];
			if (isset($outil['code:options'])) $infos_fichiers['code_options'][] = $outil['code:options'];
			if (isset($outil['code:fonctions'])) $infos_fichiers['code_fonctions'][] = $outil['code:fonctions'];
			if (isset($outil['code:css'])) $temp_css[] = cs_optimise_if(cs_parse_code_js($outil['code:css']));
			if (isset($outil['code:js'])) $temp_js[] = cs_optimise_if(cs_parse_code_js($outil['code:js']));
			if (isset($outil['code:jq_init'])) $temp_jq_init[] = cs_optimise_if(cs_parse_code_js($outil['code:jq_init']));
			if (isset($outil['code:jq'])) $temp_jq[] = cs_optimise_if(cs_parse_code_js($outil['code:jq']));
			// recherche d'un fichier monoutil_options.php ou monoutil_fonctions.php pour l'inserer dans le code
			if ($temp=cs_lire_fichier_php("outils/{$inc}_options.php")) 
				$infos_fichiers['code_options'][] = $temp;
			if ($temp=cs_lire_fichier_php("outils/{$inc}_fonctions.php"))
				$infos_fichiers['code_fonctions'][] = $temp;
		}
	}
	// insertion du css pour la BarreTypo
	if(isset($infos_pipelines['bt_toolbox']))
		$temp_css[] = 'span.cs_BT {background-color:#FFDDAA; font-weight:bold; border:1px outset #CCCC99; padding:0.2em 0.3em;}
span.cs_BTg {font-size:140%; padding:0 0.3em;}';
	// prise en compte des css.html qu'il faudra compiler plus tard
	if (count($temp_html))
		$temp_css[] = '<cs_html>'.join("\n", $temp_html).'</cs_html>';
	// concatenation des css inline, js inline et filtres trouves
	if (count($temp_css)) {
		$temp = array("<style type=\"text/css\">\n".compacte_css(join("\n", $temp_css))."\n</style>");
		$cs_metas_pipelines['header'] = is_array($cs_metas_pipelines['header'])?array_merge($temp, $cs_metas_pipelines['header']):$temp;
	}
	if (count($temp_jq_init)) {
		$temp_js[] = "var cs_init = function() {\n\t".join("\n\t", $temp_jq_init)."\n}\nif(typeof onAjaxLoad=='function') onAjaxLoad(cs_init);";
		$temp_jq[] = "cs_init.apply(document);";
	}
	if (count($temp_jq))
		$temp_js[] = "if (window.jQuery) jQuery(document).ready(function(){\n\t".join("\n\t", $temp_jq)."\n});";
	if (count($temp_js)) {
		$temp = array("<script type=\"text/javascript\"><!--\nvar cs_prive=window.location.pathname.match(/\\/ecrire\\/\$/)!=null;\n"
			.compacte_js(join("\n", $temp_js))."\n// --></script>\n");
		$cs_metas_pipelines['header'] = is_array($cs_metas_pipelines['header'])?array_merge($temp, $cs_metas_pipelines['header']):$temp;
	}
	// join final...
	$cs_metas_pipelines['header'] = join("\n", $cs_metas_pipelines['header']);
	// mise en code des traitements trouves
	foreach($traitements_utilises as $b=>$balise) {
		foreach($balise as $p=>$precision) {
			// ici, on fait attention de ne pas melanger propre et typo
			if(array_key_exists('typo', $precision) && array_key_exists('propre', $precision)) die(_T('couteauprive:erreur:traitements'));
			foreach($precision as $f=>$fonction)  {
				// pas d'objet precis
				if ($f===0)	$traitements_utilises[$b][$p][$f] = join("(", array_reverse($fonction)).'(';
				// un objet precis
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
	// mes_options.php : ajout des traitements
	if(count($traitements_utilises))
		$infos_fichiers['code_options'][] = "// Table des traitements\n" . join("\n", $traitements_utilises);
	// effacement du repertoire temporaire de controle
	if (@file_exists(_DIR_CS_TMP) && ($handle = @opendir(_DIR_CS_TMP))) {
		while (($fichier = @readdir($handle)) !== false)
			if ($fichier[0] != '.')	supprimer_fichier(_DIR_CS_TMP.$fichier);
		closedir($handle);
	} else spip_log('Erreur - cs_initialise_includes() : '._DIR_CS_TMP.' introuvable !');
	// ecriture des fichiers mes_options et mes_fonctions
	ecrire_fichier_en_tmp($infos_fichiers, 'spip_options');
	ecrire_fichier_en_tmp($infos_fichiers, 'options');
	ecrire_fichier_en_tmp($infos_fichiers, 'fonctions');
	// installation de cs_metas_pipelines[] et ecriture du fichier de controle
	set_cs_metas_pipelines($infos_pipelines);
}

define('_CS_SPIP_OPTIONS_A', "// Partie reservee au Couteau Suisse. Ne pas modifier, merci");
define('_CS_SPIP_OPTIONS_B', "// Fin du code. Ne pas modifier ces lignes, merci");

// verifier le fichier d'options _FILE_OPTIONS (ecrire/mes_options.php ou config/mes_options.php)
function cs_verif_FILE_OPTIONS($activer=false, $ecriture = false) {
	$include = str_replace('\\','/',realpath(_DIR_CS_TMP.'mes_spip_options.php'));
	$include = "@include_once \"$include\";\nif(\$GLOBALS['cs_spip_options']) define('_CS_SPIP_OPTIONS_OK',1);";
	$inclusion = _CS_SPIP_OPTIONS_A."\n// Please don�t modify; this code is auto-generated\n$include\n"._CS_SPIP_OPTIONS_B;
cs_log("cs_verif_FILE_OPTIONS($activer, $ecriture) : le code d'appel est $include");
	$fo = strlen(_FILE_OPTIONS)? _FILE_OPTIONS:false;
	if ($fo) {
		if (lire_fichier($fo, $t)) {
			// verification du contenu inclu
			$ok = preg_match('`\s*('.preg_quote(_CS_SPIP_OPTIONS_A,'`').'.*'.preg_quote(_CS_SPIP_OPTIONS_B,'`').')\s*`ms', $t, $regs);
			// s'il faut une inclusion
			if ($activer) {
				// pas besoin de reecrire si le contenu est identique a l'inclusion
				if (($regs[1]==$inclusion)) $ecriture = false;
				$t2 = $ok?str_replace($regs[0], "\n$inclusion\n\n", $t):preg_replace(',<\?(?:php)?\s*,', '<?'."php\n$inclusion\n\n", $t);
			} else {
				$t2 = $ok?str_replace($regs[0], "\n", $t):$t;
			}
cs_log(" -- fichier $fo present. Inclusion " . ($ok?" trouvee".($ecriture?" et remplacee":""):"absente".(($ecriture && $activer)?" mais ajoutee":"")));
			if($ecriture) if($t2<>$t) {
				$ok = ecrire_fichier($fo, $t2);
				if(!$ok) cs_log("ERREUR : l'ecriture du fichier $fo a echoue !");
			}
			return;
		} else cs_log(" -- fichier $fo illisible. Inclusion non permise");
	} else 
		$fo = defined('_SPIP19100')?_DIR_RESTREINT.'mes_options.php':_DIR_RACINE._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php';
	// creation
	if($activer) {
		if($ecriture) $ok=ecrire_fichier($fo, '<?'."php\n".$inclusion."\n\n?".'>');
cs_log(" -- fichier $fo absent. Fichier '$fo' et inclusion ".((!$ecriture || !$ok)?"non ":"")."crees");
	}
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
		if($cs_variable['format']==_format_NOMBRE) $valeur='0'; else $valeur='""';
	} else
		$valeur = cs_php_format($valeur, $cs_variable['format']!=_format_NOMBRE);
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
	return $code;
}

// attention : optimisation tres sommaire, pour codes simples !
// -> optimise les if(0), if(1), if(false), if(true)
function cs_optimise_if($code, $root=true) {
	if($root) {
		$code = preg_replace(',if\s*\(\s*([^)]*\s*)\)\s*{\s*,imsS', 'if(\\1){', $code);
		$code = str_replace('if(false){', 'if(0){', $code);
		$code = str_replace('if(true){', 'if(1){', $code);
	}
	if (preg_match_all(',if\(([0-9])+\){(.*)$,msS', $code, $regs, PREG_SET_ORDER))
	foreach($regs as $r) {
		$temp = $r[2]; $ouvre = $ferme = -1; $nbouvre = 1;
		do {
			if ($ouvre===false) $min = $ferme + 1; else $min = min($ouvre, $ferme) + 1;
			$ouvre=strpos($temp, '{', $min);
			$ferme=strpos($temp, '}', $min);
			if($ferme!==false) { if($ouvre!==false && $ouvre<$ferme) $nbouvre++; else $nbouvre--; }
		} while($ferme!==false && $nbouvre>0);
		if($ferme===false) return "/* Erreur sur les accolades : \{$r[2] */";
		$temp2 = cs_optimise_if($temp3=substr($temp, $ferme+1), false);
		$temp = substr($temp, 0, $ferme);
		$rempl = "if($r[1]){".$temp."}$temp3";
		if(intval($r[1])) $code = str_replace($rempl, "/* optimisation : 'IF($r[1])' */ {$temp}{$temp2}", $code);
			else $code = str_replace($rempl, "/* optimisation : 'IF($r[1]) \{$temp\}' */{$temp2}", $code);
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