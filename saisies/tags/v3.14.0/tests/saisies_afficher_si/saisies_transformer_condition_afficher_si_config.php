<?php
/**
 * Test unitaire de la fonction saisies_tester_condition_afficher_si
 * du fichier ../plugins/saisies/inc/saisies_afficher.php
 *
 * genere automatiquement par TestBuilder
 * le 2018-12-20 19:31
 */

	$test = 'saisies_transformer_condition_afficher_si_config';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/saisies/inc/saisies_afficher_si.php",'',true);


	// On va plutot test le resultat des évaluations
	function condition_eval($condition) {
		$condition = saisies_transformer_condition_afficher_si_config($condition);
		return saisies_evaluer_afficher_si($condition);
	};

	// hop ! on y va
	//
	// des configs bidons, pour les tests
	include_spip("inc/config");
	ecrire_config("tests_saisies_config", array("a" => "a", "sous" => array("b" => "b", "c" => "c")));

	$err = tester_fun("condition_eval", essais_saisies_transformer_condition_afficher_si_config());
	effacer_config("tests_saisies_config");

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";


	function essais_saisies_transformer_condition_afficher_si_config(){
		$essais = array (
			"premier_niveau" => array(
				0 => true,
				1 => "@config:tests_saisies_config:a@=='a'",
			),
			"second_niveau" => array(
				0 => false,
				1 => "@config:tests_saisies_config:sous:b@=='c'",
			),
			"second_niveau_bis" => array(
				0 => true,
				1 => "@config:tests_saisies_config:sous:c@=='c'",
			),
			"second_niveau_et" => array(
				0 => false,
				1 => "@config:tests_saisies_config:sous:c@=='c' && @config:tests_saisies_config:sous:b@=='c'",
			),
			"second_niveau_ou" => array(
				0 => true,
				1 => "@config:tests_saisies_config:sous:c@=='c' || @config:tests_saisies_config:sous:b@=='c'",
			)
		);
		return $essais;
	}


?>
