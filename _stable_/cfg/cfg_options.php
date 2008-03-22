<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2007-2008, distribue sous licence GNU/GPL
 * 
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');


// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/cfg_config');
// Inclure la balise #CFG_ARBO
include_spip('balise/cfg_arbo');

// fonction pour effacer les parametres cfg lors le l'inclusion d'un fond
// utile pour les #FORMULAIRE comme formulaires/cfg.html
// [(#INCLURE{fond=fonds/cfg_toto}{env}|effacer_parametres_cfg)]
function effacer_parametres_cfg($texte){
	return preg_replace('/(<!-- ([a-z0-9_]\w+)(\*)?=)(.*?)-->/sim', '', $texte);		
}

//
// #CONFIG etendue interpretant les /, ~ et table:
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx est un tableau serialise dans spip_meta comme avec exec=cfg&cfg=xxx
//
// si xxx demarre par ~ on utilise la colonne 'extra' 
// ('cfg' sera prochainement la colonne par defaut) de spip_auteurs
// cree pour l'occasion. 
//   ~ tout court veut dire l'auteur connecte,
//   ~duchmol celui de login "duchmol", ~123 celui d'id 123

// Pour utiliser une autre colonne que 'cfg', il faut renseigner @colonne
//   ~@extra/champ ou 
//   ~login@prefs/champ
//
// Pour recuperer des valeurs d'une table particuliere,
// il faut utiliser 'table:id/champ' ou 'table@colonne:id/champ'
//   table:123 contenu de la colonne 'cfg' de l'enregistrement id 123 de "table"
//   rubriques@extra:3/qqc  rubrique 3, colonne extra, champ 'qqc'
//
// "table" est un nom de table ou un raccourci comme "article"
// on peut croiser plusieurs id comme spip_auteurs_articles:6:123
// (mais il n'y a pas d'extra dans spip_auteurs_articles ...)
// Le 2eme argument de la balise est la valeur defaut comme pour la dist
// Le 3eme argument permet de controler la serialisation du resultat
//
function balise_CONFIG($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$sinon = interprete_argument_balise(2,$p);
	$serialize = interprete_argument_balise(3,$p);
	$p->code = 'lire_config(' . $arg . ',' . 
		($sinon && $sinon != "''" ? $sinon : 'null') . ',' . 
		($serialize ? $serialize : 'true') . ')';
	return $p;
}

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
?>
