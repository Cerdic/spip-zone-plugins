<?php
/**
 * Test unitaire de la fonction @funcname@
 * du fichier @filename@
 *
 * genere automatiquement par TestBuilder
 * le @date@
 */

	$test = '@funcname@';
	require '../test.inc';
	find_in_path("@filename@",'',true);

	//
	// hop ! on y va
	//
	$err = tester_fun('@funcname@', @essais_funcname@());
	
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