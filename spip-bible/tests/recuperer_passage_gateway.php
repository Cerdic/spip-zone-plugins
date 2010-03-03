<?php
/**
 * Test unitaire de la fonction recuperer_passage_gateway
 * du fichier ../plugins/spip-bible/traduction/gateway.php
 *
 * genere automatiquement par TestBuilder
 * le 2010-03-04 00:17
 */

	$test = 'recuperer_passage_gateway';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/spip-bible/traduction/gateway.php",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('recuperer_passage_gateway', essais_recuperer_passage_gateway());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function essais_recuperer_passage_gateway(){
		$essais = array (
  0 => 
  array (
    0 => '<strong>1</strong><sup>1 </sup><strong>2</strong><sup>1 </sup>',
    1 => 'Gn',
    2 => 1,
    3 => 1,
    4 => 2,
    5 => 8,
    6 => 
    array (
      0 => 2,
      1 => 'LSG',
    ),
    7 => 'fr',
  ),
);
		return $essais;
	}


?>