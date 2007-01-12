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
// La balise appelle celle de la dist si pas de /
//

function cfg_meta($cfg = '', $def = NULL)
{
	include_spip('inc/meta');
	lire_metas();
	global $meta;
	$ret = '';
	if (!$cfg) {
		$ret = serialize($meta);
	} elseif (strpos($cfg, '/') === false) {
		$ret = $meta[$cfg];
	} else {
		$cfg = explode('/', $cfg);
		$ret = $cfg[0] ? unserialize($meta[$cfg[0]]) : $meta;
		for ($i = 1; $ret && $i < count($cfg) && $cfg[$i] !== ''; ++$i) {
			$ret = is_array($ret) ? (is_numeric($cfg[$i]) ? $ret[0 + $cfg[$i]] : $ret[$cfg[$i]]) : '';
		}
	}
	return !$ret && $def ? $def : $ret;
}
function balise_CONFIG($p)
{
	$arg = interprete_argument_balise(1,$p);
	$sinon = interprete_argument_balise(2,$p);
	$p->code = 'cfg_meta(' . $arg . ($sinon ? ",$sinon)" : ')');
	return $p;
}

// lire_cfg() permet de recuperer une config depuis le php
// $cfg: la config, lire_cfg('montruc') est un tableau
// lire_cfg('montruc/sub') est l'element "sub" de cette config
// $def: un defaut optionnel

function lire_cfg($cfg = '', $def = NULL)
{
	return cfg_meta($cfg . '/', $def);
}
?>
