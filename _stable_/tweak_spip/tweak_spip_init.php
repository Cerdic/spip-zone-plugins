<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  l'execution du plugin                              #
#-----------------------------------------------------#

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

// si le tweak 'log_tweaks' est activé, on logue pour Tweak-Spip
function tweak_log($s) {
 if($GLOBALS['log_tweaks'] && strlen($s)) spip_log('TWEAKS. '.$s);
}

// retourne le tableau $reg si le code propose est un code de boutons radio
//  forme : choixX(choixY=traductionY|choixX=traductionX|etc)
function tweak_is_radio($code, &$reg) {
	return preg_match(',([0-9A-Za-z_-]*)\(('.'[0-9A-Za-z_-]*=[A-Za-z_:-]+\|[0-9A-Za-z_:=>|-]+'.')\),', $code, $reg);
}

// obtenir la valeur d'un choix radio
// forme de $s : choixX(choixY=traductionY|choixX=traductionX|etc)
// resultat : choixX
function tweak_choix($s) { if ($p = strpos($s, '(')) return substr($s, 0, $p); return ''; }

// lit ecrit les metas et initialise $tweaks_metas_pipes
function tweak_initialisation($forcer=false) {
	global $tweaks_metas_pipes;
	$rand = rand();
	// au premier passage, on force l'installation si var_mode est defini
	static $deja_passe_ici;
	if (!intval($deja_passe_ici)) {
tweak_log("#### 1er PASSAGE [#$rand] ###################################### - \$forcer = ".intval($forcer));
tweak_log("[#$rand] Version PHP courante : ".phpversion()." - Versions SPIP (base/code) : {$GLOBALS['spip_version']}/{$GLOBALS['spip_version_code']}");
		$forcer |= isset($GLOBALS['var_mode']);
	}
	$deja_passe_ici++;
	// si les metas ne sont pas lus, on les lit
tweak_log("[#$rand] tweak_initialisation($forcer) : Entrée #$deja_passe_ici");
	if (!isset($GLOBALS['meta']['tweaks_actifs']) || $forcer) {
tweak_log("[#$rand]  -- lecture metas");
		include_spip('inc/meta');
		lire_metas();
	}
	if (isset($GLOBALS['meta']['tweaks_pipelines'])) {
		$tweaks_metas_pipes = unserialize($GLOBALS['meta']['tweaks_pipelines']);
tweak_log("[#$rand]  -- tweaks_metas_pipes = ".join(', ',array_keys($tweaks_metas_pipes)));
tweak_log("[#$rand]"); $actifs=unserialize($GLOBALS['meta']['tweaks_actifs']);
tweak_log("[#$rand]  -- ".(is_array($actifs)?count($actifs):0).' tweak(s) actif(s)'.(is_array($actifs)?" = ".join(', ',array_keys($actifs)):''));
tweak_log("[#$rand] ".($forcer?"\$forcer = true":"tweak_initialisation($forcer) : Sortie car les metas sont présents"));
		// Les pipelines sont en meta, tout va bien on peut partir d'ici.
		if (!$forcer) return;
	}

	// ici on commence l'initialisation de tous les tweaks
	global $tweaks, $metas_vars;
	include_spip('tweak_spip');
	include_spip('tweak_spip_config');
	$metas_tweaks = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();
	// au cas ou un tweak a besoin d'input
	$tweak_input = charger_fonction('tweak_input', 'inc');
	// completer les variables manquantes et incorporer l'activite lue dans les metas
tweak_log("[#$rand]  -- foreach(\$tweaks) : tweak_parse_code, tweak_parse_description...");
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
tweak_log("[#$rand]  -- tweak_initialise_includes()...");
	tweak_initialise_includes();
tweak_log("[#$rand]  -- tweak_installe_tweaks...");
	tweak_installe_tweaks();
	// tweaks actifs
tweak_log("[#$rand]  -- ecriture metas");
	ecrire_meta('tweaks_actifs', serialize($metas_tweaks));
	// variables de tweaks
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	// code inline pour les pipelines, mes_options et mes_fonctions;
	ecrire_meta('tweaks_pipelines', serialize($tweaks_metas_pipes));
	ecrire_metas();
tweak_log("[#$rand] tweak_initialisation($forcer) : Sortie");
}

// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
function tweak_echappe_balises($balises, $fonction, $texte){
	if(!strlen($texte)) return '';
	if (!function_exists($fonction)) {
		spip_log("Erreur - tweak_echappe_balises() : $fonction() non definie !");
		return $texte;
	}
	if(!strlen($balises)) $balises = 'html|code|cadre|frame|script';
	$balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
	include_spip('inc/texte');
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

// $tweaks_metas_pipes ne sert ici qu'a l'execution et ne comporte que :
//	- le code pour <head></head>
//	- le code pour les options.php
//	- le code pour les fonction.php
//	- le code pour les pipelines utilises
global $tweaks_metas_pipes;
$tweaks_metas_pipes = array();

// lancer l'initialisation
tweak_initialisation();

?>