<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
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

// retourne les css ou js utilises (en vue d'un insertion en head)
function tweak_insert_header($type) {
	include_spip('inc/filtres');
	global $tweaks_metas_pipes;
	$head = '';
	if (isset($tweaks_metas_pipes[$type])) 
	  foreach	($tweaks_metas_pipes[$type] as $inc) {
	  	$f = find_in_path('tweaks/'.$inc);
	  	if ($type=='css') 
			$head .= '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />'."\n";
	  	elseif ($type=='js') 
			$head .= "<script type=\"text/javascript\" src=\"$f\"></script>\n";
			
	  }
	return $head."\n";
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
			if (function_exists($f=$tweak['id']._raccourcis)) $aide[] = '<li style="margin-top: 0.7em;">' . $f() . '</li>';
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
	$nb = count(unserialize($GLOBALS['meta']['tweaks_actifs']));
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
	include_spip('inc/getdocument');
	effacer_repertoire_temporaire(_DIR_TMP."tweak-spip");
	// installation de $tweaks_metas_pipes
	set_tweaks_metas_pipes_fichier($tweaks_pipelines, 'options');
	set_tweaks_metas_pipes_fichier($tweaks_pipelines, 'fonctions');
	foreach($pipelines_utilises as $pipe) set_tweaks_metas_pipes_pipeline($tweaks_pipelines, $pipe);
}

// retourne le tableau $reg si le code propose est un code de boutons radio
//  forme : choixX(choixY=traductionY|choixX=traductionX|etc)
function tweak_is_radio($code, &$reg) {
	return preg_match(',([0-9A-Za-z_-]*)\(('.'[0-9A-Za-z_-]*=[A-Za-z_:-]+\|[0-9A-Za-z_:=>|-]+'.')\),', $code, $reg);
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
	global $tweaks, $metas_vars; 
//tweak_log(" -- tweak_parse_description({$tweaks[$tweak]['id']})");
	$tweaks[$tweak]['nb_variables'] = 0;
	$tweaks[$tweak]['description'] = $tweaks[$tweak]['description'];
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

// si le tweak 'log_tweaks' est activé, on logue pour Tweak-Spip
function tweak_log($s) { 
 if($GLOBALS['log_tweaks'] && strlen($s)) spip_log('TWEAKS. '.$s);
}

// obtenir la valeur d'un choix radio
// forme de $s : choixX(choixY=traductionY|choixX=traductionX|etc)
// resultat : choixX
function tweak_choix($s) { if ($p = strpos($s, '(')) return substr($s, 0, $p); return ''; }

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

// lit ecrit les metas et initialise $tweaks_metas_pipes
function tweak_initialisation($forcer=false) {
	global $tweaks, $tweaks_metas_pipes, $metas_vars;
	$rand = rand();
	// au premier passage, on force l'installation si le calcul ou le recalcul est demande
	static $deja_passe_ici;
	if (!intval($deja_passe_ici)) {
tweak_log("#### 1er PASSAGE ####################################### - \$rand = $rand - \$forcer = ".intval($forcer));
tweak_log("Version PHP courante : " . phpversion() . " - Versions SPIP (base/code) : " . $GLOBALS['spip_version'] . '/' . $GLOBALS['spip_version_code']);
		$forcer |= ($GLOBALS['var_mode'] == 'recalcul') || ($GLOBALS['var_mode']=='calcul');
	}
	$deja_passe_ici++;
	// si les metas ne sont pas lus, on les lit
tweak_log("tweak_initialisation($forcer) : Entrée #$deja_passe_ici - \$rand = $rand");
	if (!isset($GLOBALS['meta']['tweaks_actifs']) || $forcer) {
tweak_log(" -- lecture metas - \$rand = $rand");
		include_spip('inc/meta');
		lire_metas();
	}
	if (isset($GLOBALS['meta']['tweaks_pipelines'])) {
		$tweaks_metas_pipes = unserialize($GLOBALS['meta']['tweaks_pipelines']);
tweak_log(' -- tweaks_metas_pipes = '.join(', ',array_keys($tweaks_metas_pipes)));
tweak_log(''); $actifs=unserialize($GLOBALS['meta']['tweaks_actifs']);
tweak_log(' -- '.(is_array($actifs)?count($actifs):0).' tweak(s) actif(s)'.(is_array($actifs)?" = ".join(', ',array_keys($actifs)):''));
tweak_log($forcer?"\$forcer = true":"tweak_initialisation($forcer) : Sortie car les metas sont présents - \$rand = $rand");
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
		if (!isset($tweak['categorie'])) $tweaks[$i]['categorie'] = 'divers';
		if (!isset($tweak['nom'])) $tweaks[$i]['nom'] = _T('tweak:'.$tweak['id'].':nom');
		if (!isset($tweak['description'])) $tweaks[$i]['description'] = _T('tweak:'.$tweak['id'].':description');
		$tweaks[$i]['actif'] = isset($metas_tweaks[$tweaks[$i]['id']])?$metas_tweaks[$tweaks[$i]['id']]['actif']:0;
		// Si Spip est trop ancien ou trop recent...
		if ((isset($tweak['version-min']) && $GLOBALS['spip_version']<$tweak['version-min']) 
			|| (isset($tweak['version-max']) && $GLOBALS['spip_version']>$tweak['version-max']))
				$tweaks[$i]['actif'] = 0;
		// au cas ou des variables sont presentes dans le code
		$tweaks[$i]['basic'] = $i*10; $tweaks[$i]['nb_variables'] = 0;
		// ces 2 lignes peuvent initialiser des variables dans $metas_vars
		if (isset($tweak['code:options'])) $tweaks[$i]['code:options'] = tweak_parse_code($tweak['code:options']);
		if (isset($tweak['code:fonctions'])) $tweaks[$i]['code:fonctions'] = tweak_parse_code($tweak['code:fonctions']);
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
	if(!strlen($texte)) return '';
	if (!function_exists($fonction)) {
		spip_log("Erreur - tweak_exclure_balises() : $fonction() non definie !");
		return $texte;
	}
	if(!strlen($balises)) $balises = 'html|code|cadre|frame|script';
	$balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
	$texte = echappe_retour($fonction(echappe_html($texte, 'TWEAKS', true, $balises)), 'TWEAKS');
	return $texte;
}

// transforme un chemin d'image relatif en chemin html absolu
function tweak_htmlpath($relative_path) {
	$realpath = str_replace("\\", "/", realpath($relative_path));
	$root = preg_replace(',/$,', '', $_SERVER['DOCUMENT_ROOT']);
	if (strlen($root) && strpos($realpath, $root)===0) 
		return substr($realpath, strlen($root));
	$dir = dirname(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
			(!empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : 
			(!empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : str_replace('\\','/',__FILE__)
		)));
	return tweak_canonicalize($dir.'/'.$relative_path);
}

// retourne un chemin canonique a partir d'un chemin contenant des ../
function tweak_canonicalize($address) {
	$address = str_replace("//", "/", $address);
	$address = explode('/', $address);
	$keys = array_keys($address, '..');
	foreach($keys as $keypos => $key) array_splice($address, $key - ($keypos * 2 + 1), 2);
	$address = implode('/', $address);
	return preg_replace(',([^.])\./,', '\1', $address);
}

/*****************/
/* DEBUT DU CODE */
/*****************/

// les globales :
//
// $tweaks est un tableau ultra complet avec tout ce qu'il faut savoir sur chaque tweak
//  - ce tableau n'est rempli qu'une fois, lors d'une initialisation totale
//    les hits ordinaires ne se servent que des metas, non des fichiers.
//  - l'initialisation totale insere en premier lieu tweak_spip_config.php
//
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