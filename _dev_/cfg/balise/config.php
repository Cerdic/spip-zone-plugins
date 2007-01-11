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
// La balise fait comme celle de la dist si pas de /
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
	return calculer_balise_dynamique($p,'CONFIG', array());
}
function balise_CONFIG_stat($args, $filtres)
{
	return array($args[0], $args[1]);
}
function balise_CONFIG_dyn($cfg, $sinon)
{
	return cfg_meta($cfg, $sinon);
}
?>
