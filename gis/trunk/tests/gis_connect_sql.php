<?php

	// attention avant de lancer ce test !
	// le dossier du plugin ne doit pas être un lien symbolique dans /plugins
	// sous peine de générer un timeout...

	$test = 'gis_connect_sql';

	$remonte = '../';
	while (!is_dir($remonte.'ecrire')) {
		$remonte = "../$remonte";
	}
	require $remonte.'tests/test.inc';
	find_in_path('./base/connect_sql.php', '', true);


	// Les tests
	$essais['table_objet'] = array(
		array('gis','gis'),
	);

	$essais['table_objet_sql'] = array(
		array('spip_gis','gis'),
	);

	$essais['id_table_objet'] = array(
		array('id_gis','gis'),
	);


	$essais['objet_type'] = array(
		array('gis','gis'),
	);

	// hop ! on y va
	$err = array();
	foreach ($essais as $f => $essai) {
		$err = array_merge(tester_fun($f, $essai), $err);
	}

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		echo ('<dl>' . join('', $err) . '</dl>');
	} else {
		echo 'OK';
	}
