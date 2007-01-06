<?php
/* etend la balise #CONFIG 
 *
 *  cfg plugin for spip (c) toggg 2007 -- licence LGPL
 */

//
// #CONFIG etendue interpretant les /
//
// Par exemple #CONFIG{xxx/yyy/zzz} fait comme #CONFIG{xxx}['yyy']['zzz']
// xxx etant un tableau serialise dans spip_meta
// Le 2eme argument de la balise est la valeur defaut comme pour la dist
//
// La balise appelle celle de la dist si pas de /
//
function balise_CONFIG($p) {
	$arg = interprete_argument_balise(1,$p);
	if (!$arg || strpos($arg, '/') === false) {
		return balise_CONFIG_dist($p);
	}
	$sinon = interprete_argument_balise(2,$p);
	$arg = explode('/', $arg);
	$arg[count($arg) - 1] = substr($arg[count($arg) - 1], 0, -1);
	$p->code = "((\$cfgarr=unserialize(\$GLOBALS['meta'][{$arg[0]}']))? \$cfgarr";
	for ($i = 1; $i < count($arg); ++$i) {
		if (!is_numeric($arg[$i])) {
			$arg[$i] = "'" . $arg[$i] . "'";
		}
		$p->code .= '[' . $arg[$i] . ']';
	}
	$p->code .= ": '')";
	if ($sinon) {
		$p->code = 'sinon(' . $p->code . ",$sinon)";
	}
	return $p;
}

?>
