<?php
/**
 * Test unitaire de la fonction tb_files
 * du fichier ../plugins/testbuilder/prive/exec/testbuilder_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 
 */

	$test = 'tb_files';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/testbuilder/prive/exec/testbuilder_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('tb_files', essais_tb_files());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_tb_files(){
		$essais = array (
  1 => 
  array (
    0 => 
    array (
      'tb_essais_type.php' => 'plugins/testbuilder/inc/tb_essais_type.php',
      'tb_lib.php' => 'plugins/testbuilder/inc/tb_lib.php',
    ),
    1 => _DIR_PLUGIN_TB.'inc/',
  ),
);
		return $essais;
	}













?>