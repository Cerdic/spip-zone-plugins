<?php
/**
 * Test unitaire de la fonction tb_export
 * du fichier ../plugins/testbuilder/inc/tb_lib.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-02-27 13:55
 */

	$test = 'tb_export';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/testbuilder/inc/tb_lib.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('tb_export', essais_tb_export());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_tb_export(){
		$essais = array (
);
		return $essais;
	}









?>