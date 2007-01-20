<?php

	$test = 'cfg:lire_config';
	require '../../../tests/test.inc';

include_spip('cfg_options');

$assoc = array('one' => 'element 1', 'two' => 'element 2');
$GLOBALS['meta'] = array(
	'chaine' => 'une chaine',
	'assoc' => $assoc,
	'serie' => serialize($assoc)
);

$sans = array(
	'' => $GLOBALS['meta'],
	'/' => $GLOBALS['meta'],
	'//' => $GLOBALS['meta'],
	'chaine' => 'une chaine',
	'chaine/' => 'une chaine',
	'chaine//' => 'une chaine',
	'assoc' => $assoc,
	'assoc/two' => 'element 2',
	'assoc/pasla' => null,
	'serie' => $assoc,
	'serie/two' => 'element 2',
	'serie/pasla' => null,
	'la/testid/' => null,
	'pasla' => null,
	'la/pasla' => null
);
$ok = true;
$rsans = array();
$err = array();
foreach ($sans as $arg => $res) {
	$rsans[$arg] = lire_config($arg);
	if ($rsans[$arg] === $res) {
		continue;
	}
	$err[] = print_r($rsans[$arg], true) . ' attendu: ' . print_r($res, true);
}
echo $err ? 'Echec:<br />' . join('<br />', $err) : 'OK';
if ($_GET['dump']) {
	print_r($rsans);
	echo "<br />\n";
}
