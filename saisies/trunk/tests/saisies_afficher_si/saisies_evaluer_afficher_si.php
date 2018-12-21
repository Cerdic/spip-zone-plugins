<?php
/**
 * Test unitaire de la fonction saisies_verifier_afficher_si
 * du fichier ../plugins/saisies/inc/saisies_afficher_si.php
 *
 * genere automatiquement par TestBuilder
 * le 2018-12-20 23:52
 */

	$test = 'saisies_evaluer_afficher_si';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/saisies/inc/saisies_afficher_si.php",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='saisies_evaluer_afficher_si')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}


	// Preparer les requests
	set_request("case_1", "oui");
	set_request("case_2", "");
	set_request("a", "a");
	set_request("b", "b");
	set_request("c", "c");
	set_request("d", "d");
	set_request("tableau_1", array("a", "b", "c"));
	set_request("tableau_2", array("e", "f", "g"));

	//
	// hop ! on y va
	//
	$err = tester_fun($f, essais_saisies_evaluer_afficher_si());

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";

	// On va tester essentiellement sur des set_request, le cas $env étant normalement identique

	function essais_saisies_evaluer_afficher_si(){
		$essais = array (
			'simple_egalite' =>
			array (
				0 => true,
				1 => "@a@ == 'a'"
			),
			'simple_inegalite' =>
			array (
				0 => false,
				1 => "@a@ == 'b'"
			),
			'double_egalite' =>
			array (
				0 => true,
				1 => "@a@ == 'a' && @b@ == 'b'"
			),
			'double_egalite_fausse' =>
			array (
				0 => false,
				1 => "@a@ == 'a' && @b@ == 'c'"
			),
			'egalite_alternative' =>
			array (
				0 => true,
				1 => "@a@ == 'a' || @b@ == 'c'"
			),
			'egalite_alternative_fausse' =>
			array (
				0 => false,
				1 => "@a@ == 'b' || @b@ == 'c'"
			),
			'presence_tableau_alternative' =>
			array (
				0 => true,
				1 => "@tableau_1@ IN 'b' || @tableau_2@ == 'c'"
			),
			'presence_tableau_cumulative' =>
			array (
				0 => false,
				1 => "@tableau_1@ IN 'b' && @tableau_2@ == 'c'"
			),
			'absence_tableau_cumulative' =>
			array (
				0 => false,
				1 => "@tableau_1@ !IN 'b' && @tableau_2@ !IN 'c'"
			),
			'absence_tableau_alternative' =>
			array (
				0 => true,
				1 => "@tableau_1@ !IN 'b' || @tableau_2@ !IN 'c'"
			),
			'champ_uniquement' => array(
				0 => true,
				1 => "@case_1@"
			),
			'champ_uniquement_faux' => array(
				0 => false,
				1 => "@case_2@"
			),
			'champ_uniquement_negation' => array(
				0 => true,
				1 => "!@case_2@"
			),
			'champ_uniquement_negation_faux' => array(
				0 => false,
				1 => "!@case_1@"
			)
		);
		return $essais;
	}


?>
