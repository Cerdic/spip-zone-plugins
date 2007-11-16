<?php

// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_cfg');
	
/* etend la balise #CONFIG 
 *
 *  cfg plugin for spip (c) toggg 2007 -- licence LGPL
 */

//
// #CONFIG etendue interpretant les /, ~ et table:
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx est un tableau serialise dans spip_meta comme avec exec=cfg&cfg=xxx
// si xxx demarre par ~ on utilise extra de spip_auteurs
// ~ tout court veut sire l'auteur connecte,
// ~duchmol celui de login "duchmol", ~123 celui d'id 123
// table:123 l'extra de l'enregistrement id 123 de "table"
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

// lire_config() permet de recuperer une config depuis le php
// memes arguments que la balise (forcement)
// $cfg: la config, lire_config('montruc') est un tableau
// lire_config('montruc/sub') est l'element "sub" de cette config
// comme la balise pour ~, ~duchmol ou table:id
// on peut aussi passer un tableau array($table, $colid, $id, $cfg) cf. inc/cfg_extrapack.php
// $def: un defaut optionnel
// $serialize: defaut false contrairement a la balise
// (en php on veut plutot un tableau, en squellette, du texte)
function lire_config($cfg='', $def=null, $serialize=false) {
	$table = false;
	if (is_string($cfg)) {
		$cfg = explode('/', $cfg);
		// si ca commence par ~duchmol/ , on veut un auteur par son login ou id
		if ($cfg[0][0] == '~') {
			$table = 'spip_auteurs';
			// si c'est ~duchmol/ , on veut un auteur par son login ou id
			if (strlen($cfg[0]) > 1) {
				$id = array(substr(array_shift($cfg), 1));
				$colid = array(is_numeric($id[0]) ? 'id_auteur' : 'login');
			// dans l'extra de l'auteur connecte, ne marche que si cache nul
			} else {;
				array_shift($cfg);
				$id = $GLOBALS['auteur_session'] ? $GLOBALS['auteur_session']['id_auteur'] : '';
				$colid = array('id_auteur');
			}
		// si ca commence par table:id:id.../ , on veut une table
		} elseif (strpos($cfg[0], ':')) {
			$id = explode(':', array_shift($cfg));
			list($table, $colid) = get_table_id(array_shift($id));
		}
	} elseif ($cfg) {
		// on peut aussi comme cfg_extrapack donner directement table et le reste
		list($table, $colid, $id, $cfg) = array_pad($cfg, '', 4);
		if ($table && !$colid) {
			list($table, $colid) = get_table_id($table);
		}
		if (!is_array($cfg)) {
			$cfg = explode('/', $cfg);
		}
	} else {
		$cfg = array();
	}
	// dans une table
	if ($table) {
		$extra = 'SELECT extra FROM ' . $table;
		$and = ' WHERE ';
		foreach ($colid as $i => $name) {
			$extra .= $and . $name . '=' . 
				(is_numeric($id[$i]) ? intval($id[$i]) : _q($id[$i]));
			$and = ' AND ';
	    }
		$extra = sql_query($extra);
		$extra = sql_fetch($extra);
		$config = isset($extra['extra']) && $extra['extra'] ?
					$extra['extra'] :  array();
	// sinon classiquement de meta
	} else {
		$config = $GLOBALS['meta'];
	}

	while ($x = array_shift($cfg)) {
		if (is_string($config) && is_array($c = @unserialize($config))) {
			$config = $c[$x];
		} else {
			$config = $config[$x];
		}
	}

	// transcodage vers le mode serialize
	if ($serialize && is_array($config)) {
		$ret = serialize($config);
	} elseif (!$serialize && is_null($config) && !$def) {
	// pas de serialize requis et config vide, c'est qu'on veut une array()
		$ret = array();
	} elseif (!$serialize && ($c = @unserialize($config))) {
	// transcodage vers le mode non serialize
		$ret = $c;
	} else {
	// pas de transcodage
		$ret = $config;
	}
	return is_null($ret) && $def ? $def : $ret;
}

function get_table_id($table) {
	static $catab = array(
		'tables_principales' => 'base/serial',
		'tables_auxiliaires' => 'base/auxiliaires',
	);
	$try = array($table, 'spip_' . $table);
	foreach ($catab as $categ => $catinc) {
		include_spip($catinc);
		foreach ($try as $nom) {
			if (isset($GLOBALS[$categ][$nom])) {
				return array($nom,
					preg_split('/\s*,\s*/', $GLOBALS[$categ][$nom]['key']['PRIMARY KEY']));
			}
		}
	}
	if ($try = table_objet($table)) {
		return array('spip_' . $try, array(id_table_objet($table)));
	}
	return array(false, false);
}




/*
 * Affiche une arborescence du contenu d'un #CONFIG
 * 
 * #CFG_ARBO, #CFG_ARBO{ma_meta}, #CFG_ARBO{~toto}, #CFG_ARBO{ma_meta/mon_casier}
 * 
 */
function balise_CFG_ARBO($p) {
	if (!$arg = interprete_argument_balise(1,$p)) {
		$arg = "''";
	}
	$p->code = 'affiche_arborescence(' . $arg . ')';
	return $p;
}

function affiche_arborescence($cfg='') {
	static $present = false;
	$sortie = '';
	
	// integration du css
	if (!$present)
		$sortie .= "<style type='text/css'>\n"
				.  ".cfg_arbo{}\n"
				.  ".cfg_arbo h5{padding:0.2em 0.2em; margin:0.2em 0;}\n"
				.  ".cfg_arbo ul{border:1px solid #ccc; margin:0; padding:0.2em 0.5em; list-style-type:none;}\n"
				.  "</style>\n";
	// integration du js	
	if (!$present)
		$sortie .= "<script type='text/javascript'>
					$(document).ready(function(){
						jQuery('.cfg_arbo ul').hide();
						jQuery('.cfg_arbo h5')
						.prepend('<b>[+] </b>')
						.toggle(
						  function () {
							$(this).children('b').text('[-] ');
							$(this).next('ul').show();
						  },
						  function () {
							$(this).children('b').text('[+] ');
							$(this).next('ul').hide();
						  })
					});
					</script>\n";

	$present = true;	
	
	$tableau = lire_config($cfg);
	if (empty($cfg)) $cfg = 'spip_meta';
	// parcours des donnees
	$sortie .= 
		"<div class='cfg_arbo'>\n" .
		affiche_sous_arborescence($cfg, $tableau) .
		"\n</div>\n";


	return $sortie;
}

function affiche_sous_arborescence($nom, $tableau){
	$sortie = "\n<h5>$nom</h5>\n";
	$sortie .= "\n<ul>";
	if (is_array($tableau)){
		ksort($tableau);
		foreach ($tableau as $tab=>$val){
			if (is_array($val)) 
				$sortie .= affiche_sous_arborescence($tab, $val);
			elseif (false !== $v = @unserialize($val))
				$sortie .= affiche_sous_arborescence($tab, $v);
			else
				$sortie .= "<li>$tab = " . htmlentities($val) ."</li>\n";
			
		}
	} else {
		$sortie .= "<li>$nom = " . htmlentities($tableau) . "</li>";
	}
	$sortie .= "</ul>\n";
	return $sortie;	
}




/*
 * Affiche le formulaire CFG de la vue (fond) demandee
 */

function balise_VUE_CFG($p){
	//return calculer_balise_dynamique($p, 'VUE_CFG', array());
	
	$vue = 			sinon(interprete_argument_balise(1,$p), "''"); // indispensable neanmmoins
	$id = 			sinon(interprete_argument_balise(2,$p), "''");
	$aff_titre = 	sinon(interprete_argument_balise(3,$p), "''");
	//include_spip('balise/vue_cfg');
	$p->code = "calculer_VUE_CFG($vue, $id, $aff_titre)";
	return $p;
}

function calculer_VUE_CFG($fond, $id, $afficher_titre){
	include_spip('inc/cfg');
	$cfg = cfg_charger_classe('cfg');
	$config = & new $cfg($fond, $fond, $id); 

	$config->traiter();
	
	$sortie = ($afficher_titre)
		? "<h2>$config->titre</h2>\n"
		: "";
		
	return $sortie
		   . $config->formulaire();	
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


// signaler le pipeline de notification
$GLOBALS['spip_pipeline']['cfg_post_edition'] = "";
?>
