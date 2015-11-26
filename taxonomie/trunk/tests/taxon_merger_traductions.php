<?php
/**
 * Test unitaire de la fonction taxon_merger_traductions
 * du fichier ../plugins/taxonomie/inc/taxonomer.php
 *
 * genere automatiquement par TestBuilder
 * le 2015-11-22 22:06
 */

	$test = 'taxon_merger_traductions';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/taxonomie/inc/taxonomer.php",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='taxon_merger_traductions')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}

	//
	// hop ! on y va
	//
	$err = tester_fun($f, essais_taxon_merger_traductions());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_taxon_merger_traductions(){
		$essais = array (
  0 => 
  array (
    0 => '',
    1 => '',
    2 => '',
  ),
  1 => 
  array (
    0 => '',
    1 => '',
    2 => '<multi></multi>',
  ),
  2 => 
  array (
    0 => '',
    1 => '',
    2 => '<multi> </multi>',
  ),
  3 => 
  array (
    0 => '<multi>[fr]en français</multi>',
    1 => '',
    2 => '<multi>[fr]en français</multi>',
  ),
  4 => 
  array (
    0 => '<multi>[fr]en français</multi>',
    1 => '',
    2 => '[fr]en français',
  ),
  5 => 
  array (
    0 => '',
    1 => '<multi></multi>',
    2 => '',
  ),
  6 => 
  array (
    0 => '',
    1 => '<multi> </multi>',
    2 => '',
  ),
  7 => 
  array (
    0 => '<multi>[fr]en français</multi>',
    1 => '<multi>[fr]en français</multi>',
    2 => '',
  ),
  8 => 
  array (
    0 => '<multi>[fr]en français</multi>',
    1 => '[fr]en français',
    2 => '',
  ),
  9 => 
  array (
    0 => '',
    1 => '<multi></multi>',
    2 => '<multi></multi>',
  ),
  10 => 
  array (
    0 => '<multi>[en]in English[fr]en français</multi>',
    1 => '<multi>[fr]en français</multi>',
    2 => '<multi>[en]in English</multi>',
  ),
  11 => 
  array (
    0 => '<multi>[en]in English[fr]en français 1</multi>',
    1 => '<multi>[fr]en français 1</multi>',
    2 => '<multi>[fr]en français 2[en]in English</multi>',
  ),
  12 => 
  array (
    0 => '<multi>par defaut[fr]en français</multi>',
    1 => '<multi>[fr]en français</multi>',
    2 => '<multi>par defaut</multi>',
  ),
  13 => 
  array (
    0 => '<multi>par defaut[fr]en français</multi>',
    1 => '<multi>par defaut </multi>',
    2 => '<multi>[fr]en français </multi>',
  ),
);
		return $essais;
	}



















?>