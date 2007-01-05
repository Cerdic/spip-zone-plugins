<?php
/* etend la balise #CONFIG 
 *
 *  cfg plugin for spip (c) toggg 2007 -- licence LGPL
 */

//
// #CONFIG etendue interpretant les /
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx etant une table serialisee dans spip_meta
//
// La balise appelle celle de la dist si pas de /
//
function balise_CONFIG($p) {
	$arg = interprete_argument_balise(1,$p);
	if (!$arg || strpos($arg, '/') === false) {
		return balise_CONFIG_dist($p);
	}
	$arg = explode('/', $arg);
	$fin = $deb = '';
	for ($i = 0; $i < count($arg); ++$i) {
		if ($i == count($arg) - 1) {
			$arg[$i] = substr($arg[$i], 0, -1);
		}
		if (!$i) {
			$arg[$i] = substr($arg[$i], 1);
		}
		if (!is_numeric($arg[$i])) {
			$arg[$i] = "'" . $arg[$i] . "'";
		}
	}
	$p->code = "((\$cfgarr=unserialize(\$GLOBALS['meta'][{$arg[0]}]))? \$cfgarr";
	for ($i = 1; $i < count($arg); ++$i) {
		$p->code .= '[' . $arg[$i] . ']';
	}
	$p->code .= ": '')";
	return $p;
}

?>
