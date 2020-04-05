<?php
/**
 * Test unitaire de la fonction bible_test_livre_seul
 * du fichier ../plugins/spip-bible/bible_fonctions.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-03 19:21
 */

	$test = 'bible_test_livre_seul';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/bible_fonctions.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('bible_test_livre_seul', essais_bible_test_livre_seul());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_bible_test_livre_seul(){
		$essais = array (
  0 => 
  array (
    0 => 'non',
    1 => 'Gn1',
  ),
  1 => 
  array (
    0 => 'oui',
    1 => 'Gn',
  ),
);
		return $essais;
	}



?>