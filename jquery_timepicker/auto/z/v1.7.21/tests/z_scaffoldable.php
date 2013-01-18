<?php
/**
 * Test unitaire de la fonction z_scaffoldable
 * du fichier ../plugins/zpip/z_pipelines.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 22:02
 */

	$test = 'z_scaffoldable';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/zpip/z_pipelines.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('test_z_scaffoldable', essais_z_scaffoldable());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";

	function test_z_scaffoldable(){
		$args = func_get_args();
		$res = call_user_func_array('z_scaffoldable', $args);
		return is_array($res)?count($res):false;
	}

	function essais_z_scaffoldable(){
		$essais = array (
  1 => 
  array (
    0 => false,
    1 => 'articles',
  ),
  2 => 
  array (
    0 => false,
    1 => 'rubriques',
  ),
  3 => 
  array (
    0 => false,
    1 => 'sites',
  ),
  4 => 
  array (
    0 => 3,
    1 => 'article',
  ),
);
		return $essais;
	}









?>