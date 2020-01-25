<?php
/**
 * Test unitaire de la fonction saisies_verifier_securite_afficher_si
 * du fichier ../plugins/saisies/inc/saisies_afficher.php
 *
 * genere automatiquement par TestBuilder
 * le 2018-12-18 23:50
 */

	$test = 'saisies_verifier_securite_afficher_si';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/saisies/inc/saisies_afficher_si.php",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='saisies_verifier_securite_afficher_si')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}

	//
	// hop ! on y va
	//
	$err = tester_fun($f, essais_saisies_verifier_securite_afficher_si());

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";


	function essais_saisies_verifier_securite_afficher_si(){
		$essais = array (
			0 =>
			array (
				0 => false,
				1 => 's',
			),
			1 =>
			array (
				0 => false,
				1 => ';',
			),
			array (
				0 => true,
				1 => '@champ@ == "a"',
			),
			array (
				0 => true,
				1 => "@champ@ == ';'"
			),
			array (
				0 => true,
				1 => '@champ@ == ";"'
			),
			array (
				0 => true,
				1 => "@champ@ == 'oui;non'"
			),
			array (
				0 => true,
				1 => '@champ@ == "oui;non"'
			),
			array (
				0 => false,
				1 => '@champ@ == "";'
			),
			array (
				0 => false,
				1 => '@champ1@ == "1"; @champ2@ =="2"'
			),
			array (
				0 => true,
				1 => '@sql_insert@ == "1"'
			),
			array (
				0 => false,
				1 => 'sql_insert() == "1"'
			),
			array (
				0 => true,
				1 => '@champ_1@ == "a" && @champ_2@ == "b"'
			),
			array (
				0 => true,
				1 => "@champ_1@ == 'a' || @champ_2@ == 'b'"
			),
			array (
				0 => true,
				1 => '(@checkbox_1@ IN "vendredi" && @checkbox_1@ !IN "samedi") || (@checkbox_1@ !IN "vendredi" && @checkbox_1@ IN "samedi")'
			),
			array (
				0 => false,
				1 => "@champ_1@ == ''\r\n == spip_log()"
			)
		);
		return $essais;
	}



?>
