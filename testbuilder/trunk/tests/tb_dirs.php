<?php
/**
 * Test unitaire de la fonction tb_dirs
 * du fichier ../plugins/testbuilder/prive/exec/testbuilder_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 
 */

	$test = 'tb_dirs';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/testbuilder/prive/exec/testbuilder_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('tb_dirs', essais_tb_dirs());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_tb_dirs(){
		$essais = array (
  0 => 
  array (
    0 => 
    array (
      'ecrire' => 'ecrire/',
      'plugins' => 'plugins/',
      'plugins-dist' => 'plugins-dist/',
      'prive' => 'prive/',
    ),
    1 => '/',
  ),
  1 => 
  array (
    0 => 
    array (
      'ecrire' => 'ecrire/',
      'plugins' => 'plugins/',
      'plugins-dist' => 'plugins-dist/',
      'prive' => 'prive/',
    ),
    1 => '/etc/',
  ),
  2 => 
  array (
    0 => 
    array (
      'ecrire' => 'ecrire/',
      'plugins' => 'plugins/',
      'plugins-dist' => 'plugins-dist/',
      'prive' => 'prive/',
    ),
    1 => '../../',
  ),
  3 => 
  array (
    0 => 
    array (
      'ecrire' => 'ecrire/',
      'plugins' => 'plugins/',
      'plugins-dist' => 'plugins-dist/',
      'prive' => 'prive/',
    ),
    1 => '../',
  ),
);
		return $essais;
	}









?>