<?php
/**
 * Test unitaire de la fonction @funcname@
 * du fichier @filename@
 *
 * genere automatiquement par TestBuilder
 * le @date@
 */
    global $spip_lang;
    $spip_lang = 'fr';
	$test = '@funcname@';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
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