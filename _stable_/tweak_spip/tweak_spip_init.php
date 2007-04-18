<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  l'execution du plugin                              #
#  Seulement s'il y a lieu, tweak_spip_init.php       #
#  va inclure tweak_spip.php                          #
#-----------------------------------------------------#

// si le tweak 'log_tweaks' est actif, on logue pour Tweak-Spip
function tweak_log($variable, $prefixe='') {
 if(!$GLOBALS['log_tweaks'] || !strlen($variable)) return;
 if (!is_string($variable)) $variable = var_export($variable, true);
 spip_log('TWEAKS. '.$prefixe.$variable);
}

// compatibilite avec tweak-spip de version anterieure a 1.7.0.0
function tweak_choix($s) { if ($p = strpos($s, '(')) return substr($s, 0, $p); return ''; }

function tweak_initialisation_d_un_tweak($tweak0, $tweak_input) {
	global $tweaks, $metas_tweaks;
	$tweak = &$tweaks[$tweak0];
	if (!isset($tweak['categorie'])) $tweak['categorie'] = 'divers';
	if (!isset($tweak['nom'])) $tweak['nom'] = _T('tweak:'.$tweak['id'].':nom');
	if (!isset($tweak['description'])) $tweak['description'] = _T('tweak:'.$tweak['id'].':description');
	$tweak['actif'] = isset($metas_tweaks[$tweak['id']])?$metas_tweaks[$tweak['id']]['actif']:0;
	// Si Spip est trop ancien ou trop recent...
	if ((isset($tweak['version-min']) && $GLOBALS['spip_version']<$tweak['version-min'])
		|| (isset($tweak['version-max']) && $GLOBALS['spip_version']>$tweak['version-max']))
			$tweak['actif'] = 0;
	// au cas ou des variables sont presentes dans le code
	$tweak['variables'] = array(); $tweak['nb_variables'] = 0;
	// ces 2 lignes peuvent initialiser des variables dans $metas_vars ou $metas_vars_code
	if (isset($tweak['code:options'])) $tweak['code:options'] = tweak_parse_code_php($tweak['code:options']);
	if (isset($tweak['code:fonctions'])) $tweak['code:fonctions'] = tweak_parse_code_php($tweak['code:fonctions']);
	// cette ligne peut utiliser des variables dans $metas_vars ou $metas_vars_code
	$tweak['description'] = $tweak_input($tweak0, 'tweak_spip_admin');
}

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
		$actifs=unserialize($GLOBALS['meta']['tweaks_actifs']);
tweak_log("[#$rand]  -- ".(is_array($actifs)?count($actifs):0).' tweak(s) actif(s)'.(is_array($actifs)?" = ".join(', ',array_keys($actifs)):''));
tweak_log("[#$rand] ".($forcer?"\$forcer = true":"tweak_initialisation($forcer) : Sortie car les metas sont présents"));
		// Les pipelines sont en meta, tout va bien on peut partir d'ici.
		if (!$forcer) return;
	}

	// ici on commence l'initialisation de tous les tweaks
	global $tweaks, $metas_vars, $metas_tweaks;
	include_spip('tweak_spip');
	// charger les metas
	$metas_tweaks = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();
	// remplir $tweaks (et aussi $tweak_variables qu'on n'utilise pas ici);
	include_spip('tweak_spip_config');
	// nettoyage des versions anterieures
	unset($metas_vars['radio_set_options']);
	unset($metas_vars['radio_type_urls2']);
	unset($metas_vars['radio_filtrer_javascript2']);
	unset($metas_vars['radio_suivi_forums']);
	unset($metas_vars['desactive_cache']);
	unset($metas_vars['target_blank']);
	unset($metas_vars['']);
	// stocker les types de variables declarees
	global $tweak_variables;
	$metas_vars['_chaines'] = $tweak_variables['_chaines'];
	$metas_vars['_nombres'] = $tweak_variables['_nombres'];
	// au cas ou un tweak manipule des variables
	$tweak_input = charger_fonction('tweak_input', 'inc');
	// completer les variables manquantes et incorporer l'activite lue dans les metas
tweak_log("[#$rand]  -- foreach(\$tweaks) : tweak_initialisation_d_un_tweak()");

	// initialiser chaque tweak
	$id = 0;
	foreach($temp = $tweaks as $tweak) $id = tweak_initialisation_d_un_tweak($tweak['id'], $tweak_input);
	// installer $tweaks_metas_pipes
	$tweaks_metas_pipes = array();
tweak_log("[#$rand]  -- tweak_initialise_includes()...");
	// initialiser les includes et creer les fichiers de controle
	tweak_initialise_includes();
	// sauver la configuration
	tweak_sauve_configuration();
tweak_log("[#$rand]  -- tweak_installe_tweaks...");
	// lancer la procedure d'installation pour chaque tweak
	tweak_installe_tweaks();
	// en metas : tweaks actifs
tweak_log("[#$rand]  -- ecriture metas");
	ecrire_meta('tweaks_actifs', serialize($metas_tweaks));
	// en metas : variables de tweaks
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	// en metas : code inline pour les pipelines, mes_options et mes_fonctions;
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
// cette fonction est utilisable par les tweaks eux-meme durant l'execution du plugin
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

tweak_log("Début de tweak_spip_init.php");

global $tweaks_metas_pipes;
$tweaks_metas_pipes = array();
// lancer l'initialisation
tweak_initialisation();

?>
