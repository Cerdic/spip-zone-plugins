<?php
/**
 * Test unitaire de la fonction saisies_afficher_si_verifier_syntaxe
 * du fichier ../plugins/saisies/inc/saisies_afficher_si_commun.php
 *
 * genere automatiquement par TestBuilder
 * le 2018-12-20 23:52
 */

	$test = 'saisies_evaluer_afficher_si';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/saisies/inc/saisies_afficher_si_commun.php",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='saisies_afficher_si_verifier_syntaxe')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}


	//
	// hop ! on y va
	//
	$err = tester_fun($f, essais_saisies_afficher_si_verifier_syntaxe());

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";

	// On va tester essentiellement sur des set_request, le cas $env étant normalement identique

	function essais_saisies_afficher_si_verifier_syntaxe(){
		$essais = array (
			'simple_marche' =>
			array (
				0 => true,
				1 => "@a@ == 'a'"
			),
			'simple_marche_pas' =>
			array (
				0 => false,
				1 => "@a@ == 'a"
			),
			'simple_marche_pas_2' =>
			array (
				0 => false,
				1 => "@a@ === 'a'"
			),
			'simple_marche_pas_3' =>
			array (
				0 => false,
				1 => "@a == 'a'"
			),
			'double_marche' =>
			array (
				0 => true,
				1 => "@a@ == 'a' && @b@ == 'b'"
			),
			'double_marche_pas' =>
			array (
				0 => false,
				1 => "@a@ == 'a' && @b@ == 'b' &&"
			),
			'double_marche_pas_2' =>
			array (
				0 => false,
				1 => "@a@ == 'a' && @b@ == 'b' ||"
			),
			'double_marche_pas_3' =>
			array (
				0 => false,
				1 => "&& @a@ == 'a' && @b@ == 'b'"
			),
			'double_marche_pas_4' =>
			array (
				0 => false,
				1 => "|| @a@ == 'a' && @b@ == 'b'"
			),
			'double_marche_pas_5' =>
			array (
				0 => false,
				1 => "@a@ == 'a' &&  && @b@ == 'b'"
			),
			'parenthese_marche_pas_5' =>
			array (
				0 => false,
				1 => "(@a@ == 'a'"
			),
			'false' =>
			array (
				0 => true,
				1 => "false"
			),
			'true' =>
			array (
				0 => true,
				1 => "true"
			),
			'total' =>
			array(
				0 => true,
				1 => '@checkbox_1@:TOTAL > 1'
			)
		);
		foreach ($essais as $nom=>$param) {
			$essais[$nom][2] = saisies_parser_condition_afficher_si($param[1]);
		}
		return $essais;
	}


?>
