<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions utilisees pendant  #
#  l'execution du plugin.                             #
#  Seulement s'il y a lieu, on va inclure ici         #
#  cout_utils.php et compiler les outils.             #
#-----------------------------------------------------#

cs_log("Chargement de cout_lancement.php pour initialisation");

// compatibilite avec les plugins de version anterieure a 1.7.0.0
function tweak_choix($s) { if ($p = strpos($s, '(')) return substr($s, 0, $p); return ''; }

// si l'outil 'log_couteau_suisse' est actif, on logue dans tmp/spip.log
function cs_log($variable, $prefixe='') {
 if(!defined('_LOG_CS') || !strlen($variable)) return;
 if (!is_string($variable)) $variable = var_export($variable, true);
 spip_log('COUTEAU-SUISSE. '.$prefixe.$variable);
}

// Echapper les elements perilleux en les passant en base64
// Creer un bloc base64 correspondant a $rempl ; au besoin en marquant
// une $source differente ; optimisation du code spip !
// echappe_retour() permet de revenir en arriere
function cs_code_echappement($rempl, $source='') {
	// Convertir en base64
	$base64 = base64_encode($rempl);
	// guillemets simple dans la balise pour simplifier l'outil 'guillemets'
	return "<span class='base64$source' title='$base64'></span>";
}

// lit ecrit les metas et initialise $cs_metas_pipelines
// cette fonction est appellee par cout_options a chaque hit de la page
function cs_initialisation($forcer=false) {
	global $cs_metas_pipelines;
	$rand = rand();
	// au premier passage, on force l'installation si var_mode est defini
	static $deja_passe_ici;
	if (!intval($deja_passe_ici)) {
cs_log("#### 1er PASSAGE [#$rand] ################################# - \$forcer = ".intval($forcer));
cs_log("[#$rand] Version PHP courante : ".phpversion()." - Versions SPIP (base/code) : {$GLOBALS['spip_version']}/{$GLOBALS['spip_version_code']}");
		$forcer |= (_request('var_mode')!=NULL);
	}
	$deja_passe_ici++;
	// si les metas ne sont pas lus, on les lit
cs_log("[#$rand] cs_initialisation($forcer) : Entrée #$deja_passe_ici");
	if (!isset($GLOBALS['meta']['tweaks_actifs']) || $forcer) {
cs_log("[#$rand]  -- lecture metas");
		include_spip('inc/meta');
		lire_metas();
	}
	if (isset($GLOBALS['meta']['tweaks_pipelines'])) {
		$cs_metas_pipelines = unserialize($GLOBALS['meta']['tweaks_pipelines']);
cs_log("[#$rand]  -- cs_metas_pipelines = ".(is_array($cs_metas_pipelines)?join(', ',array_keys($cs_metas_pipelines)):''));
		$actifs = unserialize($GLOBALS['meta']['tweaks_actifs']);
		// compatibilite : SPIP_cache => spip_cache
		if (isset($actifs['SPIP_cache'])) { 
			$actifs['spip_cache'] = $actifs['SPIP_cache']; unset($actifs['SPIP_cache']);
			ecrire_meta('tweaks_actifs', serialize($actifs));
			ecrire_metas();
		}
		// definition des constantes attestant qu'un outil est bien actif : define('_CS_monoutil', 'oui');
		foreach($actifs as $nom=>$actif) if($actif['actif']) @define('_CS_'.$nom, 'oui');
cs_log("[#$rand]  -- ".(is_array($actifs)?count($actifs):0).' outil(s) actif(s)'.(is_array($actifs)?" = ".join(', ',array_keys($actifs)):''));
cs_log("[#$rand] ".($forcer?"\$forcer = true":"cs_initialisation($forcer) : Sortie car les metas sont présents"));
		// Les pipelines sont en meta, tout va bien on peut partir d'ici.
		if (!$forcer) return;
	}

	// ici on commence l'initialisation de tous les outils
	global $outils, $metas_vars, $metas_outils;
	include_spip('cout_utils');
	// charger les metas
	$metas_outils = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();
	// remplir $outils (et aussi $cs_variables qu'on n'utilise pas ici);
	include_spip('config_outils');
	// nettoyage des versions anterieures
	cs_compatibilite_ascendante();
	// stocker les types de variables declarees
	global $cs_variables;
	$metas_vars['_chaines'] = $cs_variables['_chaines'];
	$metas_vars['_nombres'] = $cs_variables['_nombres'];
	// au cas ou un outil manipule des variables
	$description_outil = charger_fonction('description_outil', 'inc');
	// completer les variables manquantes et incorporer l'activite lue dans les metas
cs_log("[#$rand]  -- foreach(\$outils) : cs_initialisation_d_un_outil()");

	// initialiser chaque outil et construire la liste des contribs
	$contribs = array();
	include_spip('inc/cs_outils');
	foreach($outils as $outil) {
		cs_initialisation_d_un_outil($id = $outil['id'], $description_outil, false);
		if(isset($outil['contrib']) && isset($metas_outils[$id]['actif']))
			$contribs[] = '<br/> &bull; [@@cout:'.$outil['id'].':nom@@->http://www.spip-contrib.net/?article'.$outil['contrib'].']';
	}
	// installer $cs_metas_pipelines
	$cs_metas_pipelines = array();
cs_log("[#$rand]  -- cs_initialise_includes()... cout_fonctions.php sera probablement inclus.");
	// initialiser les includes et creer les fichiers de controle
	cs_initialise_includes();
	// sauver la configuration
	cs_sauve_configuration();
	// en metas : outils actifs
cs_log("[#$rand]  -- ecriture metas");
	ecrire_meta('tweaks_actifs', serialize($metas_outils));
	// en metas : variables d'outils
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	// en metas : code inline pour les pipelines, mes_options et mes_fonctions;
	ecrire_meta('tweaks_pipelines', serialize($cs_metas_pipelines));
	// en metas : les liens sur spip-contrib
	ecrire_meta('tweaks_contribs', serialize($contribs));
	ecrire_metas();
cs_log("[#$rand] cs_initialisation($forcer) : Sortie");
}

/*
function qui determine si $texte est de type Block (true) ou Inline (false)
_BALISES_BLOCS est defini dans texte.php :
define('_BALISES_BLOCS',
	'div|pre|ul|ol|li|blockquote|h[1-6r]|'
	.'t(able|[rdh]|body|foot|extarea)|'
	.'form|object|center|marquee|address|'
	.'d[ltd]|script|noscript|map|button|fieldset');
*/
function cs_block($texte) {
	return strpos($texte, "\n\n")
		|| preg_match(',</?(p|'._BALISES_BLOCS.')[>[:space:]],iS', $texte);
}

// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
function cs_echappe_balises($balises, $fonction, $texte, $arg=NULL){
	if(!strlen($texte)) return '';
	if (!function_exists($fonction)) {
		spip_log("Erreur - cs_echappe_balises() : $fonction() non definie !");
		return $texte;
	}
	if(!strlen($balises)) $balises = 'html|code|cadre|frame|script';
	$balises = ',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS';
	include_spip('inc/texte');
	$texte = echappe_html($texte, 'CS', true, $balises);
	$texte = echappe_retour($arg==NULL?$fonction($texte):$fonction($texte, $arg), 'CS');
	return $texte;
}

// transforme un chemin d'image relatif en chemin html absolu
// cette fonction est utilisable par les outils eux-memes durant l'execution du plugin
function cs_htmlpath($relative_path) {
	$realpath = str_replace('\\', '/', realpath($relative_path));
	$root = preg_replace(',/$,', '', $_SERVER['DOCUMENT_ROOT']);
	if (strlen($root) && strpos($realpath, $root)===0)
		return substr($realpath, strlen($root));
	$dir = dirname(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
			(!empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] :
			(!empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : str_replace('\\','/',__FILE__)
		)));
	return cs_canonicalize($dir.'/'.$relative_path);
}

// retourne un chemin canonique a partir d'un chemin contenant des ../
function cs_canonicalize($address) {
	$address = str_replace('\\', '/', str_replace('//', '/', $address));
	$address = explode('/', $address);
	$keys = array_keys($address, '..');
	foreach($keys as $keypos => $key) array_splice($address, $key - ($keypos * 2 + 1), 2);
	$address = implode('/', $address);
	return preg_replace(',([^.])\./,', '\1', $address);
}

?>
