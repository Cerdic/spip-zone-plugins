<?php

// nom du test
$test = 'cfg:lire_config';

// chemin vers test.inc, ici plugins/cfg/tests/
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
$s = array(null, '<dl>', '<dl>');
$err = array();
$fun = 'lire_config';
$bal = 'CONFIG';

foreach ($essais as $i => $spec) {
	if (!is_array($spec[0])) {
		$spec[0] = array($spec[0]);
	}
	switch (count($spec[0])) {
		case 0:
			$r[1][$i] = $fun();
			$r[2][$i] = $fun(null, 'defaut');
			$r[3][$i] = $fun(null, null, true);
			$tst1 = $bal;
			$tst2 = $bal . "{'',defaut}";
		break;
		case 1:
			$r[1][$i] = $fun($spec[0][0]);
			$r[2][$i] = $fun($spec[0][0], 'defaut');
			$r[3][$i] = $fun($spec[0][0], null, true);
			$tst1 = $bal . '{' . $spec[0][0] . '}';
			$tst2 = $bal . '{' . ($spec[0][0] ? $spec[0][0] : "''") . ',defaut}';
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
	$s[1] .= '<dt>&#035;' . $tst1 . '</dt><dd>#' . $tst1 . "</dd>\n";
	$s[2] .= '<dt>&#035;' . $tst2 . '</dt><dd>#' . $tst2 . "</dd>\n";
}
$s[1] .= '</dl>';
$s[2] .= '</dl>';

function test_bal($bali, $skel, $contexte = array())
{
	$dossier = sous_repertoire(_DIR_VAR, 'cache-tests');
	$fichier = "$dossier$bali.html";

	if (($handle = fopen($fichier, 'w'))) {
		fwrite($handle, '[(#REM) ' . $bali . " ]\n" . $skel);
		fclose($handle);
	    return recuperer_fond('local/cache-tests/' . $bali, $contexte);
	}
	return '';
}

echo $err ? 'Echec:<ul><li>' . join('</li><li>', $err) . '</li></ul>' : 'OK';
if ($_GET['dump']) {
	echo "<div>\n" . print_r($r[1], true) . "</div>\n";
	echo "<div>\n" . print_r($r[2], true) . "</div>\n";
	echo "<div>\n" . print_r($r[3], true) . "</div>\n";
//	echo "<div>\n" . print_r($s[1], true) . "</div>\n";
//	echo "<div>\n" . print_r($s[2], true) . "</div>\n";

    include_spip('public/assembler');
    recuperer_fond('local/cache-tests/cfg-test', array());
	$GLOBALS['meta']['chaine'] = 'une chaine';
	$GLOBALS['meta']['assoc'] = $GLOBALS['assoc'];
	$GLOBALS['meta']['serie'] = serialize($GLOBALS['assoc']);

	for ($i = 1; $i < 3; ++$i) {
		echo test_bal($bal . $i, $s[$i]);
	}
}
