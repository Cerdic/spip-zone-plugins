<?php
/**
 * Test unitaire de la fonction zcore_echaffaudable
 * du fichier public/styliser_par_z.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 22:02
 */

	$test = 'zcore_echaffaudable';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("public/styliser_par_z.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('test_zcore_echaffaudable', essais_zcore_echaffaudable());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";

	function test_zcore_echaffaudable(){
		$args = func_get_args();
		$res = call_user_func_array('zcore_echaffaudable', $args);
		return is_array($res)?count($res):false;
	}

	function essais_zcore_echaffaudable(){
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