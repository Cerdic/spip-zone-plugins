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
		($sinon && $sinon != "''" ? ($sinon == "'#ARRAY'" ? 'array()' : $sinon) : 'null') . ',' . 
		($serialize ? $serialize : 'true') . ')';
	return $p;
}

// lire_cfg() permet de recuperer une config depuis le php
// $cfg: la config, lire_cfg('montruc') est un tableau
// lire_cfg('montruc/sub') est l'element "sub" de cette config
// $def: un defaut optionnel
function lire_config($cfg='', $def=null, $serialize=false) {
	$config = $GLOBALS['meta'];
	$cfg = explode('/', $cfg);

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
	} elseif (!$serialize && ($c = @unserialize($config))) {
	// transcodage vers le mode non serialize
		$ret = $c;
	} else {
	// pas de transcodage
		$ret = $config;
	}
	return is_null($ret) && !is_null($def) ? $def : $ret;
}

?>
