<?php
/**
 * Test unitaire de la fonction bible_analyser_ref
 * du fichier ../plugins/spip-bible/bible_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-04-06 20:34
 */

	$test = 'bible_analyser_ref';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('bible_analyser_ref', essais_bible_analyser_ref());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_bible_analyser_ref(){
		$essais = array (
  0 => 
  array (
    0 => 
    array (
      0 => 'Gn',
      1 => '1',
      2 => '',
      3 => '2',
      4 => '',
    ),
    1 => 'Gn1-2',
    2 => 'lsg',
  ),
  1 => 
  array (
    0 => 
    array (
      0 => 'Gn',
      1 => '1',
      2 => '3',
      3 => '2',
      4 => '3',
    ),
    1 => 'Gn1,3-2,3',
    2 => 'nbs',
  ),
);
		return $essais;
	}



?>