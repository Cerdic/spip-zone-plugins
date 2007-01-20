<?php

$test = 'cfg:lire_config';
require '../../../tests/test.inc';

include_spip('cfg_options');

$assoc = array('one' => 'element 1', 'two' => 'element 2');
$serassoc = serialize($assoc);
$GLOBALS['meta'] = array(
	'chaine' => 'une chaine',
	'assoc' => $assoc,
	'serie' => serialize($assoc)
);
$sermeta = serialize($GLOBALS['meta']);

$essais = array(
// presents
	array(array(), $GLOBALS['meta'], $GLOBALS['meta'], $sermeta),
	array('' , $GLOBALS['meta'], $GLOBALS['meta'], $sermeta),
	array('/' , $GLOBALS['meta'], $GLOBALS['meta'], $sermeta),
	array('//' , $GLOBALS['meta'], $GLOBALS['meta'], $sermeta),
	array('chaine' , 'une chaine'),
	array('chaine/' , 'une chaine'),
	array('chaine//' , 'une chaine'),
	array('assoc' , $assoc, $assoc, $serassoc),
	array('assoc/two' , 'element 2'),
	array('serie' , $assoc, $assoc, $serassoc),
	array('serie/two' , 'element 2'),
// pas la
	array('assoc/pasla', null, 'defaut'),
	array('serie/pasla', null, 'defaut'),
	array('la/testid/', null, 'defaut'),
	array('pasla', null, 'defaut'),
	array('la/pasla', null, 'defaut')
);
$ok = true;
$r = array(null, array(), array(), array());
$s = array(1, '', '');
$err = array();
$fun = 'lire_config';
$bal = '#CONFIG{';

foreach ($essais as $i => $spec) {
	if (!is_array($spec[0])) {
		$spec[0] = array($spec[0]);
	}
	switch (count($spec[0])) {
		case 0:
			$r[1][$i] = $fun();
			$r[2][$i] = $fun(null, 'defaut');
			$r[3][$i] = $fun(null, null, true);
			$s[1] .= '(((' . $i . ')))' . $bal . "}\n";
			$s[2] .= '(((' . $i . ')))' . $bal . "'',defaut" . "}\n";
		break;
		case 1:
			$r[1][$i] = $fun($spec[0][0]);
			$r[2][$i] = $fun($spec[0][0], 'defaut');
			$r[3][$i] = $fun($spec[0][0], null, true);
			$s[1] .= '(((' . $i . ')))' . $bal . $spec[0][0] . "}\n";
			$s[2] .= '(((' . $i . ')))' . $bal . $spec[0][0] . ',defaut' . "}\n";
		break;
/*		case 2:
			$r[1][$i] = $fun($spec[0][0], $spec[0][1]);
			$r[2][$i] = $fun($spec[0][0], $spec[0][1]);
			$r[3][$i] = $fun($spec[0][0], $spec[0][1], true);
		break;
*/
	}
	for ($nbarg = 1; $nbarg < 4; ++$nbarg) {
		if ($r[$nbarg][$i] !== ($res = isset($spec[$nbarg]) ? $spec[$nbarg] : $spec[1])) {
			$err[] = $i . "({$essais[$i][0]}) $nbarg (" . print_r($r[$nbarg][$i], true) .
				') attendu (' . print_r($res, true) . ')';
		}
	}
}

function get_fond($contexte = array())
{
    include_spip('public/assembler');
    return recuperer_fond('local/cache-tests/cfg-test', $contexte);
}
//echo get_fond();

echo $err ? 'Echec:<ul><li>' . join('</li><li>', $err) . '</li></ul>' : 'OK';
if ($_GET['dump']) {
	echo "<div>\n" . print_r($r[1], true) . "</div>\n";
	echo "<div>\n" . print_r($r[2], true) . "</div>\n";
	echo "<div>\n" . print_r($r[3], true) . "</div>\n";
	echo "<div>\n" . print_r($s[1], true) . "</div>\n";
	echo "<div>\n" . print_r($s[2], true) . "</div>\n";
}
