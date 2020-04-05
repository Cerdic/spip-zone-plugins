<?php
#ini_set('display_errors','1'); error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
if (!defined("_ECRIRE_INC_VERSION")) return;
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#

include_spip('base/jeux_tables');
include_spip('jeux_utils');
// tableau de parametres exploitables par les plugins
global $jeux_config;

// filtre retournant un lien cliquable si $nb!=0, sinon un simple tiret
function jeux_lien_jeu($nb='0', $exec='', $id_jeu=0) {
	$lien = generer_url_ecrire($exec,'id_jeu='.$id_jeu);
	return $nb=='0'?'-':"<a href='$lien'>$nb</a>";
}
// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <div title='PLUGIN-DEBUT'></div> et <div title='PLUGIN-FIN'></div>
// ou : <div title='PLUGIN-DEBUT-#xxxx'></div> et <div title='PLUGIN-FIN-#xxxx'></div>
//	 ou xxxx est le numero d'identification du plugin.
if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
 	$texte = preg_replace(",<div[^<]+['\"]JEUX-HEAD-#[0-9]+[^>]+></div>,", '', $texte);
	return preg_replace(",<div[^<]+['\"]PLUGIN-DEBUT(-#[0-9]*)?.*<[^<]+['\"]PLUGIN-FIN\\1?[^>]+></div>,UmsS", '', $texte);
 }
}

// filtre qui retire le code source des jeux du texte original
function pas_de_balise_jeux($texte) {
	if(strpos($texte, _JEUX_DEBUT)===false) return $texte;
	return preg_replace(','.preg_quote(_JEUX_DEBUT).'.*?'.preg_quote(_JEUX_FIN).',UimsS', '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'pas_de_balise_jeux';

// ajoute l'id_jeu du jeu a sa config interne et traite le jeu grace a propre()
// ce filtre doit agir sur #TEXTE* sans balises <jeux/>
// dans le cas d'un formulaire CVT, il faut egalement $jeuCVT='oui' et $indexJeux 
function traite_contenu_jeu($texte, $id_jeu, $jeuCVT='non', $indexJeux=0) { 
	return propre(str_replace(_JEUX_FIN, "[config]jeu_cvt=$jeuCVT\nindex_jeux=$indexJeux\nid_jeu=$id_jeu"._JEUX_FIN, $texte));
}

// complete la config d'un jeu brut non encore decode
function ajoute_config_jeu($texte, $config=array()) {
	// separateurs inutiles ici, le texte est celui d'un seul jeu
	$texte = str_replace(array(_JEUX_DEBUT, _JEUX_FIN), '', $texte);
	if(!is_array($config)) return $texte . "\n["._JEUX_CONFIG.']' . $config;
	array_walk($config, create_function('&$v,&$k','$v = trim($k)."=".trim($v);'));
	return $texte . '['._JEUX_CONFIG.']' . join("\n", $config);
}

// fonction de traitement appelant directement une fonction de pipeline
function jeux_traitement_pre_propre($texte, $texte_brut=false) {
	include_spip('jeux_pipelines');
	return jeux_pre_propre($texte, $texte_brut);
}

// renvoie le titre public du jeu que l'on peut trouver grace au separateur [titre]
function titre_jeu($texte) {
	include_spip('jeux_utils');
	return jeux_trouver_titre_public($texte);
}

// renvoie le type du jeu
function type_jeu($texte) {
	include_spip('jeux_utils');
	return jeux_trouver_nom($texte);
}

/* Quelques balises "raccourcis" */

// extraction du titre public (table 'jeux' seulement)
// #TITRE_PUBLIC equivalent a : #CONTENU*|titre_jeu
function balise_TITRE_PUBLIC_dist($p) {
	if($p->id_boucle) {
		$id = $p->boucles[$p->id_boucle]->id_table;
		if($id == 'jeux') {
			$contenu = champ_sql('contenu', $p);
			$p->code = "titre_jeu($contenu)";
			return $p;
		}
		$p->code = champ_sql('titre_public', $p);
		return $p; 
	}
	// verifier s'il existe deja un champ titre_public dans la table utilisee
	$original = champ_sql('titre_public', $p);
	$contenu = champ_sql('contenu', $p);
	$p->code = "(strlen($original)?$original:titre_jeu($contenu))";
	return $p;
}

// interpretation du texte d'un jeu present en base apres avoir complete sa config interne
// utilisation : #TEXTE_JEU{key1, value1, key2, value2,...}
// qui creera avant l'interpretation du jeu une nouvelle section [config]key1=value1, key2=value2, etc.
// parametre necessaire aux jeux en base : id_jeu
// parametre necessaires aux jeux affiches en CVT : jeu_cvt='oui' et index_jeux
function balise_TEXTE_JEU_dist($p) {
	$id_jeu = champ_sql('id_jeu', $p);
	$texte = champ_sql('texte', $p);
	$args = ''; $n = 1;
	while($k = interprete_argument_balise($n++, $p))
		if($v = interprete_argument_balise($n++, $p)) $args .= " . \"\\n\" . $k . '=' . $v";
	// ajout des parametres trouves en argument a la config interne du jeu
	// puis lancement du decodage du jeu, c'est mieux de le faire au plus tot.
	$p->code = "jeux_traitement_pre_propre(ajoute_config_jeu($texte, 'id_jeu = ' . intval($id_jeu)$args), true)";
	// le traitement de la balise #TEXTE_JEU fera le reste.
	return $p;
}

// renvoie la configuration interne d'un jeu
function balise_CONFIG_INTERNE_dist($p) {
	$texte = champ_sql('texte', $p);
	$param = interprete_argument_balise(1, $p);
	$p->code = "jeux_trouver_configuration_interne($texte, ".($param?$param:"''").')';
	return $p;
}

// traduction longue du type de resultat
function balise_TYPE_RESULTAT_LONG_dist($p) {
	$type = champ_sql('type_resultat', $p);
	$p->code = "_T('jeux:resultat2_'.$type)";
	return $p;
}

// traduction courte du type de resultat
function balise_TYPE_RESULTAT_COURT_dist($p) {
	$type = champ_sql('type_resultat', $p);
	$p->code = "_T('jeux:resultat_'.$type)";
	return $p;
}

function balise_NETTOYER_URI_dist($p) {
	$p->code = 'nettoyer_uri()';
	$p->interdire_scripts = false;
	return $p;
}

function table_jeux_caracteristiques() {
	global $jeux_caracteristiques;
	$res = _T('jeu:explication_modules')
		. "\n\n| {{"._T('jeux:jeux').'}} | {{'._T('public:signatures_petition').'}} | {{'._T('jeu:label_options').'}} | {{'._T('spip:icone_configuration_site').'}} |';
	foreach($jeux_caracteristiques['TYPES'] as $j=>$t) {
		include_spip('jeux/'.$j);
		$config = function_exists($f='jeux_'.$j.'_init')?trim($f()):'';
		$res .= "\n|$t|&#91;" 
			. join("]<br />&#91;", $jeux_caracteristiques['SIGNATURES'][$j]) . ']|['
			. join("]<br />&#91;", array_diff($jeux_caracteristiques['SEPARATEURS'][$j], $jeux_caracteristiques['SIGNATURES'][$j])) . ']|'
			. preg_replace(array(',//.*,', ',[\n\r]+,'), array('', '<br />'), $config) . '|';
	}
	return propre($res);
}

/**
 * Construit un lien horizontal 
 *
 * @example
 *     <:jeux:chaine|jeux_icone_horizontale{#URL_ECRIRE{xx},#CHEMIN_IMAGE{jeu-16.png}}:>
 * 
 * @param string $texte Le texte du lien
 * @param string $lien L'url du lien
 * @param string $image Le chemin vers l'image (16px) à afficher
 * @return string Code HTML du lien
**/
function jeux_icone_horizontale($texte, $lien, $image){
	return icone_base($lien, $texte, $image, "", "horizontale", "");
}

/*
 filtre interpretant le resultat long d'un jeu multiple
 exemples d'utilisation : 
 	[(#RESULTAT_LONG|resultat_intermediaire{nb})]
 	[(#RESULTAT_LONG|resultat_intermediaire{score,1})]
 $index doit commencer a 1
 valeurs reconnues pour $code : 
 	score => renvoie le score intermediaire
	total => renvoie le total intermediaire
	detail => renvoie le resultat long intermediaire
	nb => renvoie le nombre de sous-jeux
*/
function filtre_resultat_intermediaire($texte, $code='score', $index=0) {
	include_spip('jeux/multi_jeux');
	$nb = count($t = explode(_SEP_BASE_MULTI_JEUX, $texte));
	if($nb<2) return '';
	if($code=='nb') return $nb-1;
	if($index<1 || $index>$nb) return 'ERR';
	switch($code) {
		case 'score': case 'total':
			$t = array_pop($t);
			$t = explode('/', $t, 2);
			$t = explode(',', $t[$code=='score'?0:1]);
			return trim($t[$index-1]);
		case 'detail': return $t[$index-1];
	}
}

?>
