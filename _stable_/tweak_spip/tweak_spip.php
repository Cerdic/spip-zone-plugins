<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

/*****************/
/* COMPATIBILITE */
/*****************/

global $spip_version_code;
if (!defined('_DIR_PLUGIN_TWEAK_SPIP')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	$p=_DIR_PLUGINS.end($p); if ($p[strlen($p)-1]!='/') $p.='/';
	define('_DIR_PLUGIN_TWEAK_SPIP', $p);
}
if ($spip_version_code<1.92) { 
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

// installation de $tweaks_metas_pipes
// $type ici est egal a 'options' ou 'fonctions'
function set_tweaks_metas_pipes_fichier($tweaks_pipelines, $type) {
	global $tweaks_metas_pipes;
	$code = '';
	if (isset($tweaks_pipelines['inc_'.$type]))
		foreach ($tweaks_pipelines['inc_'.$type] as $inc) $code .= "include_spip('tweaks/$inc');\n";
	if (isset($tweaks_pipelines['code_'.$type]))
		foreach ($tweaks_pipelines['code_'.$type] as $inline) $code .= $inline."\n";
	$tweaks_metas_pipes[$type] = $code;
tweak_log("set_tweaks_metas_pipes_fichier($type) : strlen=".strlen($code));
	$fichier_dest = sous_repertoire(_DIR_TMP, "tweak-spip") . "mes_$type.php";
	ecrire_fichier($fichier_dest, "<?php\n// Code de controle pour le plugin Tweak-SPIP\n$code?".'>');
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
	ecrire_fichier($fichier_dest, "<?php\n// Code de contr�le pour le plugin Tweak-SPIP\n$code?".'>');
}

// retourne les css ou js utilises (en vue d'un insertion en head)
function tweak_insert_header($type) {
	include_spip('inc/filtres');
	global $tweaks_metas_pipes;
	$head = '';
	if (isset($tweaks_metas_pipes[$type])) 
	  foreach	($tweaks_metas_pipes[$type] as $inc) {
	  	$f = find_in_path('tweaks/'.$inc);
	  	if ($type='css') $f = direction_css($f);
		$head .= '<link rel="stylesheet" href="'.$f.'" type="text/css" media="projection, screen" />';
	  }
	return $head;
}

// est-ce que $pipe est un pipeline ?
function is_tweak_pipeline($pipe, &$set_pipe) {
	if ($ok=preg_match(',^\s*pipeline\s*:(.*)$,',$pipe,$t)) $set_pipe = trim($t[1]);
	return $ok;
}

// cree un tableau $tweaks_pipelines et initialise $tweaks_metas_pipes
function tweak_initialise_includes() {
  global $tweaks, $tweaks_metas_pipes;
  $tweaks_pipelines = array();
  // liste des pipelines utilises
  $pipelines_utilises = array();
  // parcours de tous les tweaks
  foreach ($tweaks as $i=>$tweak) {
	// stockage de la liste des fonctions par pipeline, si le tweak est actif...
	if ($tweak['actif']) {
		$inc = $tweak['id']; $pipe2 = '';
		foreach ($tweak as $pipe=>$fonc) if (is_tweak_pipeline($pipe, $pipe2)) {
			// module a inclure
			$tweaks_pipelines[$pipe2]['inclure'][] = $inc;
			// fonction a appeler
			$tweaks_pipelines[$pipe2]['fonction'][] = $fonc;
			// liste des pipelines utilises
			if (!in_array($pipe2, $pipelines_utilises)) $pipelines_utilises[] = $pipe2;
		}
		// recherche d'un fichier .css et/ou .js eventuellement present dans tweaks/
		if (find_in_path('tweaks/'.$inc.'.css')) $tweaks_metas_pipes['css'][] = $inc.'.css';
		if (find_in_path('tweaks/'.$inc.'.js')) $tweaks_metas_pipes['js'][] = $inc.'.js';
		// recherche d'un code inline eventuellement propose
		if (isset($tweak['code'])) { $inc = $tweak['code']; $prefixe = 'code_'; }
			else $prefixe = 'inc_';
		if ($tweak['options']) $tweaks_pipelines[$prefixe.'options'][] = $inc;
		if ($tweak['fonctions']) $tweaks_pipelines[$prefixe.'fonctions'][] = $inc;
	}
  }
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
// $code est le code inline livre par tweak_spip_config
function tweak_parse_code($code) {
	global $metas_vars;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)(/[ds])?(/[^%]+)?%%,', $code, $matches)) {
		$rempl = '""';	
		// si le meta est present on garde la valeur du meta, sinon la valeur par defaut si elle existe
		if (isset($metas_vars[$matches[1]])) {
				$rempl = $metas_vars[$matches[1]];
				if (preg_match(',^"(.*)"$,', trim($rempl), $matches2)) $rempl = str_replace('\"','"',$matches2[1]);
			} else { 
				$rempl = isset($matches[3])?substr($matches[3],1):'""';
				if($matches[2]=='/d') $rempl = 'intval('.$rempl.')';
					elseif($matches[2]=='/s') $rempl = 'strval('.$rempl.')';
				eval('$rempl='.$rempl.';');
			}
		if($matches[2]!='/d' && $rempl[0]!='"') $rempl = '"'.str_replace('"','\"',$rempl).'"';
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
	global $tweaks, $metas_vars; 
//tweak_log(" -- tweak_parse_description({$tweaks[$tweak]['id']})");
	$tweaks[$tweak]['nb_variables'] = 0;
	$tweaks[$tweak]['description'] = $tweaks[$tweak]['description'];
	$t = preg_split(',%([a-zA-Z_][a-zA-Z0-9_]*)%,', $tweaks[$tweak]['description'], -1, PREG_SPLIT_DELIM_CAPTURE);
	$descrip = '';
	for($i=0;$i<count($t);$i+=2) if (($var=trim($t[$i+1]))!='') {
		// si le meta est present on remplace
		if (isset($metas_vars[$var]))
				$descrip .= $tweak_input(
					$tweaks[$tweak]['basic']+(++$tweaks[$tweak]['nb_variables']), 
					$var, 
					$metas_vars[$var],
					$t[$i],
					$tweaks[$tweak]['actif'], 
					'tweak_spip_admin');
			else $descrip .= $t[$i]."[$var?]";
	} else $descrip .= $t[$i];
	$tweaks[$tweak]['description'] = $descrip;
}

// decommenter pour debug...
function tweak_log($s) { 
	spip_log('TWEAKS. '.$s);
}	

// lance la fonction d'installation de chaque tweak actif, si elle existe.
function tweak_installe_tweaks() {
	global $tweaks; 
	foreach($temp = $tweaks as $tweak) if ($tweak['actif']) {
		if (function_exists($f = $tweak['id'].'_installe')) {
			$f();
tweak_log(" -- $f() : install� !");
		}
	}
}

// lit ecrit les metas et initialise $tweaks_metas_pipes
function tweak_initialisation($forcer=false) {
	global $tweaks, $tweaks_metas_pipes, $metas_vars;
	global $spip_version_code;
	$rand = rand();
	// au premier passage, on force l'installation si le calcul ou le recalcul est demande
	static $deja_passe_ici;
	if (!intval($deja_passe_ici)) {
tweak_log("#### 1er PASSAGE ####################################### - \$rand = $rand - \$forcer = ".intval($forcer));
		$forcer |= ($GLOBALS['var_mode'] == 'recalcul') || ($GLOBALS['var_mode']=='calcul');
	}
	$deja_passe_ici++;
	// si les metas ne sont pas lus, on les lit
tweak_log("tweak_initialisation($forcer) : Entr�e #$deja_passe_ici - \$rand = $rand");
	if (!isset($GLOBALS['meta']['tweaks_actifs']) || $forcer) {
tweak_log(" -- lecture metas - \$rand = $rand");
		include_spip('inc/meta');
		lire_metas();
	}
	if (isset($GLOBALS['meta']['tweaks_pipelines'])) {
		$tweaks_metas_pipes = unserialize($GLOBALS['meta']['tweaks_pipelines']);
tweak_log(" -- tweaks_metas_pipes = ".join(', ',array_keys($tweaks_metas_pipes)));
tweak_log(" -- tweaks actifs :  = ".join(', ',array_keys(unserialize($GLOBALS['meta']['tweaks_actifs']))));
tweak_log($forcer?"\$forcer = true":"tweak_initialisation($forcer) : Sortie car les metas sont pr�sents - \$rand = $rand");
		// Les pipelines sont en meta, tout va bien on peut partir d'ici.
		if (!$forcer) return;
	}
	include_spip('tweak_spip_config');
	$metas_tweaks = unserialize($GLOBALS['meta']['tweaks_actifs']);
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_variables']);
	// au cas ou un tweak a besoin d'input
	$tweak_input = charger_fonction('tweak_input', 'inc');
	// completer les variables manquantes et incorporer l'activite lue dans les metas
tweak_log(" -- foreach(\$tweaks) : tweak_parse_code, tweak_parse_description... - \$rand = $rand");
	foreach($temp = $tweaks as $i=>$tweak) {
		if (!isset($tweak['id'])) { $tweaks[$i]['id']='erreur'; $tweaks[$i]['nom'] = _T('tweak:erreur_id');	}
		if (!isset($tweak['categorie'])) $tweaks[$i]['categorie'] = 'divers';//_T('tweak:divers');
//			else $tweaks[$i]['categorie'] = _T('tweak:'.$tweaks[$i]['categorie']);
		if (!isset($tweak['nom'])) $tweaks[$i]['nom'] = _T('tweak:'.$tweak['id'].':nom');
		if (!isset($tweak['description'])) $tweaks[$i]['description'] = _T('tweak:'.$tweak['id'].':description');
		$tweaks[$i]['actif'] = isset($metas_tweaks[$tweaks[$i]['id']])?$metas_tweaks[$tweaks[$i]['id']]['actif']:0;
		// Si Spip est trop ancien...
		if (isset($tweak['version']) && $spip_version_code<$tweak['version']) $tweaks[$i]['actif'] = 0;
		// au cas ou des variables sont presentes dans le code
		$tweaks[$i]['basic'] = $i*10; $tweaks[$i]['nb_variables'] = 0;
		// cette ligne peut initialiser des variables dans $metas_vars
		if (isset($tweak['code'])) $tweaks[$i]['code'] = tweak_parse_code($tweaks[$i]['code']);
		// cette ligne peut utiliser des variables dans $metas_vars
		tweak_parse_description($i, $tweak_input);
	}
	// installer $tweaks_metas_pipes
	$tweaks_metas_pipes = array();
tweak_log(" -- tweak_initialise_includes()... - \$rand = $rand");
	tweak_initialise_includes();
tweak_log(" -- tweak_installe_tweaks... - \$rand = $rand");
	tweak_installe_tweaks();
	// tweaks actifs
tweak_log(" -- ecriture metas - \$rand = $rand");
	ecrire_meta('tweaks_actifs', serialize($metas_tweaks));
	// variables de tweaks
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	// code inline pour les pipelines, mes_options et mes_fonctions;
	ecrire_meta('tweaks_pipelines', serialize($tweaks_metas_pipes));
	ecrire_metas();
tweak_log("tweak_initialisation($forcer) : Sortie - \$rand = $rand");
}

// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
function tweak_exclure_balises($balises, $fonction, $texte){
	global $spip_version_code;
	if (!function_exists($fonction)) {
		spip_log("Erreur - tweak_exclure_balises() : $fonction() non definie !");
		return $texte;
	}
	$balises = strlen($balises)?',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS':'';
	if ($spip_version_code<1.92 && $balises=='') $balises = ',<(html|code|cadre|frame|script)>(.*)</\1>,UimsS';
	$texte = echappe_retour($fonction(echappe_html($texte, 'TWEAKS', true, $balises)), 'TWEAKS');
	return $texte;
}

// transforme un chemin d'image relatif en chemin html
function tweak_htmlpath($relative_path) {
   $realpath=str_replace("\\", "/", realpath($relative_path));
   $htmlpathURL=str_replace($_SERVER['DOCUMENT_ROOT'],'',$realpath);
   return $htmlpathURL;
}

/*****************/
/* DEBUT DU CODE */
/*****************/

// les globales :
// $tweaks est un tableau ultra complet avec tout ce qu'il faut savoir sur chaque tweak
// $tweaks_metas_pipes ne sert qu'a l'execution et ne comporte que :
//	- les fichiers .js 
//	- les fichiers .css 
//	- le code pour les options.php
//	- le code pour les fonction.php
//	- le code pour les pipelines utilises
global $tweaks, $tweaks_metas_pipes;
$tweaks = $tweaks_metas_pipes = array();

// lancer l'initialisation
tweak_initialisation();

?>