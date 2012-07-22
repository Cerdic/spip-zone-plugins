<?php

	while(basename(getcwd())!=='plugins')
		chdir('../');
	chdir('../tests/');
	@define('_SPIP_TEST_CHDIR','../');
	$test = 'table_objet_jeu';
	require 'test.inc';

	include_spip('base/connect_sql');

// Des tests
$essais['table_objet'] = array(
array('jeux','jeu'),
);

$essais['table_objet_sql'] = array(
array('spip_jeux','jeu'),
);

$essais['id_table_objet'] = array(
array('id_jeu','jeu'),
);


$essais['objet_type'] = array(
array('jeu','jeux'),
);

	// hop ! on y va
	$err = array();
	foreach($essais as $f=>$essai)
		$err = array_merge(tester_fun($f, $essai),$err);
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		echo ('<dl>' . join('', $err) . '</dl>');
	} else {
		echo "OK";
	}

?>
