<?php
/**
 * Test unitaire de la fonction @funcname@
 * du fichier @filename@
 *
 * genere automatiquement par TestBuilder
 * le @date@
 */

	$test = '@funcname@';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("@filename@",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='@funcname@')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}

	//
	// hop ! on y va
	//
	$err = tester_fun($f, @essais_funcname@());
	
	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";
	

	function @essais_funcname@(){
		$essais = array();
		return $essais;
	}

?>