<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');

// inclure les fonctions lire_config(), ecrire_config() et effacer_config()
include_spip('inc/cfg_config');


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


/*
 * cfg_charger_classe(), sur le meme code que charger_fonction()
 *
 * charge un fichier perso ou, a defaut, standard
 * et retourne si elle existe le nom de la fonction class homonyme ($nom),
 * ou de suffixe _dist
 */
function cfg_charger_classe($nom, $dossier='inc', $continue=false) {

	if (substr($dossier,-1) != '/') $dossier .= '/';

	if (class_exists($f = $nom))
		return $f;
	if (class_exists($g = $f . '_dist'))
		return $g;

	// Sinon charger le fichier de declaration si plausible
	if (!preg_match(',^\w+$,', $f))
		die(htmlspecialchars($nom)." pas autorise");

	// passer en minuscules (cf les balises de formulaires)
	$inc = include_spip($d = ($dossier . strtolower($nom)));

	if (class_exists($f)) return $f;
	if (class_exists($g)) return $g;
	if ($continue) return false;

	// Echec : message d'erreur
	spip_log("class $nom ($f ou $g) indisponible" .
		($inc ? "" : " (fichier $d absent)"));

	include_spip('inc/minipres');
	echo minipres(_T('forum_titre_erreur'),
		 _T('fichier_introuvable', array('fichier'=> '<b>'.htmlentities($d).'</b>')));
	exit;
}

// Inclure les balises sinon SPIP ne voit pas les fonctions calculer_x()... meuh !
include_spip('balise/formulaire_cfg');
include_spip('balise/cfg_vue');
include_spip('balise/cfg_arbo');
include_spip('balise/cfg_traiter');

// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
?>
