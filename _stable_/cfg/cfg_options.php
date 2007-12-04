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
	$params = cfg_analyse_param($cfg);
	return cfg_lire_config($params['chemin'], $params['donnees'], $def, $serialize);
}

/*
 * 
 * ecrire_config($chemin, $valeur) 
 * permet d'enregistrer une configuration
 * 
 * Si valeur == null : suppression.
 * 
 * $serialise = true : serialise les donnees (choix par defaut)
 */
function ecrire_config($cfg='', $valeur=null, $serialize=true){
	$params = cfg_analyse_param($cfg);
	
// 1) lecture
	// on recupere toutes les informations depuis
	// la racine de la meta ou du champ de table (extra par defaut)
	$chemin = explode('/', $params['chemin']);
	$champ 	= $chemin[count($chemin)-1];
	$racine = $chemin[0];

	switch ($params['storage']) {
		case 'auteur':
			$base = lire_config('~' . implode('', $params['table']['id']));
			break; 
			
		case 'table':
			$base = lire_config($params['table']['nom'] . ':' . implode(':', $params['table']['id']));
			break; 
			
		case 'meta':
		default:
			$base = lire_config($racine);
			break;	
	}
	
	
// 2) modifications
	// on modifie le tableau recupere pour prendre en compte
	// les changements (modifs ou suppressions)
	$ici = &$base;
	$supprimer = ($valeur === null);

	switch ($params['storage']) {
		case 'auteur':
		case 'table':
			// champs compose : 'chose/truc', 'chose/bidule/truc', ...
			if (count($chemin)>1){
				array_pop($chemin);		
				$ici = &cfg_monte_arbre($ici, $chemin);

				if ($supprimer) unset($ici[$champ]);
				else $ici[$champ] = $valeur;
				
			// champs simples : 'truc'
			} else {
				// si pas de champ (ie. '~duchmol/' ou '~duchmol' ou 'auteur:3')
				// modifier tout le contenu
				if (empty($champ)) {
					if ($supprimer) unset($base);
					else $base = $valeur;	
				
				// si un champ 	(ie. '~duchmol/champ')	
				// ne modifier que celui ci		
				} else {
					if (!is_array($ici)) $ici = array();
					
					if ($supprimer) unset($ici[$champ]);
					else $ici[$champ] = $valeur;	
				}
			}			
			break; 
			
		case 'meta':
		default:
			// champs compose : 'chose/truc', 'chose/bidule/truc', ...
			if (count($chemin)>1){	
				array_pop($chemin);
				array_shift($chemin);
				$ici = &cfg_monte_arbre($ici, $chemin);
	
				if ($supprimer) unset($ici[$champ]);
				else $ici[$champ] = $valeur;
				
			// champs simples : 'truc'
			} else {
				if ($supprimer) unset($base);
				else $base = $valeur;
			}
			break;	
	}			

		
	
// 3) ecriture
	// on sauvegarde les changements dans la meta
	// ou le champ de table (extra par defaut)
	switch ($params['storage']) {
		case 'table':
		case 'auteur':
			// une requete sql pour mettre a jour
			// where
			$where = array();
			foreach ($params['table']['id'] as $nom => $val)
				$where[] = "$nom = '$val'";
			// contenu
			$c = (($base == null) ? '' : (($serialize) ? serialize($base) : $base));
			
			sql_updateq(
				$params['table']['nom'],
				array($params['table']['champ'] => $c ),
				$where);
			break;	
			
		case 'meta':
		default:	
			if (!$base) effacer_meta($racine);
			else ecrire_meta($racine, (($serialize) ? serialize($base) : $base) );
				
			break;	
	}

}

/*
 * effacer_config($chemin) 
 * permet de supprimer une config 
 */
function effacer_config($cfg=''){
	ecrire_config($cfg);		
}


/*
 *  se positionner dans le tableau arborescent
 */
function &cfg_monte_arbre(&$base, $chemin){
	if (!$chemin) {
		return $base;
	}
	
	if (!is_array($chemin)) {
		$chemin = explode('/', $chemin);
	}	

	if (!is_array($base)) {
		$base = array();
	}

	foreach ($chemin as $chunk) {
		if (!isset($base[$chunk])) {
			$base[$chunk] = array();
		}
		$base = &$base[$chunk];
	}
	
	if (!is_array($base)) $base = array();
	
	return $base;
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
 * 
 * Analyse la chaine ou le tableau passe a CFG
 * et retourne un tableau renseignant le type
 * de donnee a chercher (meta ou table)
 * ainsi que le chemin de la donnee
 * 
 */
function cfg_analyse_param($cfg){
	
	$params = array(
		'storage' => 'meta',
		'chemin' => '',
		'donnees' => '',
		'table' => array(
			'nom' => '',
			'id' => array(), // $nom => $valeur
			'champ' => ''
		)
	);
	
	/*
	 * On peut passer une chaine
	 * ou directement un tableau array($table, $nom_colonne_id, $id, $chemin_cfg)
	 */
	if (is_string($cfg)) {
		/*
		 * Cas de la recherche par auteur
		 * dans l'extra de la table auteur
		 * 
		 * '~login/monchamp'
		 */
		if ($cfg[0] == '~') {
			$cfg = explode('/', $cfg);
			$params['storage'] = 'auteur';
			$params['table']['nom'] = 'spip_auteurs';
			// ~duchmol/ ou ~32/ , on veut un auteur par son login ou id
			if (strlen($cfg[0]) > 1) {
				$id = substr(array_shift($cfg),1);
				$colid = is_numeric($id) ? 'id_auteur' : 'login';
			// ~/
			// dans l'extra de l'auteur connecte, ne marche que si cache nul
			} else {
				array_shift($cfg);
				$id = $GLOBALS['auteur_session'] ? $GLOBALS['auteur_session']['id_auteur'] : '';
				$colid = 'id_auteur';
			}
			$params['table']['id'][$colid] = $id;
			$params['table']['champ'] = 'extra';
			$params['chemin'] = implode('/', $cfg);
			
		/*
		 * si ca commence par table:id:id.../ , 
		 * on veut une table
		 * 
		 * 'rubrique:3/monchamp'
		 */
		} elseif (strpos($cfg, ':')) {
			$cfg = explode('/', $cfg);
			$id = explode(':', array_shift($cfg));
			list($table, $colid) = get_table_id(array_shift($id));
			$params['storage'] = 'table';
			$params['table']['nom'] = $table;
			foreach ($colid as $n=>$c) {
				$params['table']['id'][$c] = $id[$n];
			}
			$params['table']['champ'] = 'extra';
			$params['chemin'] = implode('/', $cfg);
		/*
		 * Sinon, c'est une meta
		 */
		} else {
			$params['storage'] = 'meta';
			$params['chemin'] = $cfg;			
		}
	/*
	 * Sinon, on a passe a cfg directement un tableau
	 */
	} elseif ($cfg) {
		// on peut aussi comme cfg_extrapack donner directement table et le reste
		list($table, $colid, $id, $cfg) = array_pad($cfg, '', 4);
		if ($table && !$colid) {
			list($table, $colid) = get_table_id($table);
		}
		if (!is_array($cfg)) {
			$cfg = array($cfg);
		}
		if (!is_array($colid)){
			$colid = array($colid);
			$id = array($id);
		}		
		$params['storage'] = 'table';
		$params['table']['nom'] = $table;
		foreach ($colid as $n=>$c) {
			$params['table']['id'][$c] = $id[$n];
		}
		$params['table']['champ'] = 'extra';
		$params['chemin'] = implode('/', $cfg);		
	} 
	
	/*
	 * On recupere les donnees (racine du chemin) 
	 * qui serviront a trouver la valeur du chemin demande
	 */
	$params['donnees'] = cfg_recuperer_donnees($params);
	return $params;
}


/*
 * Recuperer les donnees en fonction du storage
 */
function cfg_recuperer_donnees($params){
	switch ($params['storage']) {
		case 'table':
		case 'auteur':
			// recuperer la valeur du champ de la table sql
			$where = array();
			foreach ($params['table']['id'] as $nom => $id) {
				$where[] = $nom . '=' . (is_numeric($id) ? intval($id) : sql_quote($id));
			}

			$res = sql_select($champ = $params['table']['champ'], $params['table']['nom'], $where);
			$res = sql_fetch($res);
			$donnees = isset($res[$champ]) && $res[$champ] ?
						$res[$champ] :  array();		
			break;
			
		case 'meta':
		default:
			$donnees = $GLOBALS['meta'];
			break;	
	}	
	
	return $donnees;
}


/*
 * Lire une entree generale
 */
function cfg_lire_config($chemin, $config, $def=null, $serialize=false){

	$cfg = explode('/',$chemin);

	while ($x = array_shift($cfg)) {
		if (is_string($config) && is_array($c = @unserialize($config))) {
			$config = $c[$x];
		} else {
			if (is_string($config)) {
				$config = null;
			} else {
				$config = $config[$x];
			}
		}
	}

	// transcodage vers le mode serialize
	if ($serialize && is_array($config)) {
		$ret = serialize($config);
	} elseif (!$serialize && is_null($config) && !$def 
			&& $serialize === '') // hack affreux pour le |in_array...
	{
		// pas de serialize requis et config vide, c'est qu'on veut un array()
		// un truc de toggg que je ne sais pas a quoi ca sert.
		// bon, ca sert si on fait un |in_array{#CONFIG{chose,'',''}}
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
