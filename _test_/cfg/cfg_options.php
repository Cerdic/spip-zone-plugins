<?php
/* etend la balise #CONFIG 
 *
 *  cfg plugin for spip (c) toggg 2007 -- licence LGPL
 */

//
// #CONFIG etendue dynamique interpretant les /
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx etant un tableau serialise dans spip_meta comme avec exec=cfg&cfg=montruc
// Le 2eme argument de la balise est la valeur defaut comme pour la dist
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
// $cfg: la config, lire_config('montruc') est un tableau
// lire_config('montruc/sub') est l'element "sub" de cette config
// $def: un defaut optionnel
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
	} else {
		// on peut aussi comme cfg_extrapack donner directement table et le reste
		list($table, $colid, $id, $cfg) = $cfg;
		if (!$colid) {
			list($table, $colid) = get_table_id($table);
		}
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
		$extra = spip_query($extra);
		$extra = spip_fetch_array($extra);
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
	if ($try = table_objet($table)) {
		return array('spip_' . $try, array(id_table_objet($table)));
	}
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
	return array(false, false);
}
?>
