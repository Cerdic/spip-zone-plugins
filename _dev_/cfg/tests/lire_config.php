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
	array(array(), $GLOBALS['meta'], $sermeta),
	array('' , $GLOBALS['meta'], $sermeta),
	array('/' , $GLOBALS['meta'], $sermeta),
	array('//' , $GLOBALS['meta'], $sermeta),
	array('chaine' , 'une chaine'),
	array('chaine/' , 'une chaine'),
	array('chaine//' , 'une chaine'),
	array('assoc' , $assoc, $serassoc),
	array('assoc/two' , 'element 2'),
	array('serie' , $assoc, $serassoc),
	array('serie/two' , 'element 2'),
// pas la
	array('assoc/pasla' , null),
	array('serie/pasla' , null),
	array('la/testid/' , null),
	array('pasla' , null),
	array('la/pasla' , null),
// pas la avec defaut	
	array(array('assoc/pasla', 'defaut'), 'defaut'),
	array(array('serie/pasla', 'defaut'), 'defaut'),
	array(array('la/testid/', 'defaut'), 'defaut'),
	array(array('pasla', 'defaut'), 'defaut'),
	array(array('la/pasla', 'defaut'), 'defaut')
);
$ok = true;
$rsans = array();
$err = array();
$fun = 'lire_config';
foreach ($essais as $i => $spec) {
	if (!is_array($spec[0])) {
		$spec[0] = array($spec[0]);
	}
	switch (count($spec[0])) {
		case 0:
			$rsans[$i] = $fun();
			$ravec[$i] = $fun(null, null, true);
		break;
		case 1:
			$rsans[$i] = $fun($spec[0][0]);
			$ravec[$i] = $fun($spec[0][0], null, true);
		break;
		case 2:
			$rsans[$i] = $fun($spec[0][0], $spec[0][1]);
			$ravec[$i] = $fun($spec[0][0], $spec[0][1], true);
		break;
	}
	if ($rsans[$i] !== $spec[1]) {
		$err[] = $i . ' sans (' . print_r($rsans[$i], true) .
			') attendu (' . print_r($spec[1], true) . ')';
	}
	if (isset($spec[2])) {
		$spec[1] = $spec[2];
	}
	if ($ravec[$i] !== $spec[1]) {
		$err[] = $i . ' avec (' . print_r($ravec[$i], true) .
			') attendu (' . print_r($spec[1], true) . ')';
	}
}
echo $err ? 'Echec:<ul><li>' . join('</li><li>', $err) . '</li></ul>' : 'OK';
if ($_GET['dump']) {
	echo "<div>\n" . print_r($rsans, true) . "</div>\n";
}
