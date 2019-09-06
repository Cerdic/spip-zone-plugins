<?php
/**
 * Test unitaire de la fonction saisies_afficher_si_js
 * du fichier ../plugins/saisies/inc/saisies_afficher_si_js.php
 *
 * genere automatiquement par TestBuilder
 * le 2018-12-20 23:52
 */

	$test = 'saisies_afficher_si_js';
	$remonte = "../";
	while (!is_dir($remonte."ecrire"))
		$remonte = "../$remonte";
	require $remonte.'tests/test.inc';
	find_in_path("../plugins/saisies/inc/saisies_afficher_si_js.php",'',true);

	// chercher la fonction si elle n'existe pas
	if (!function_exists($f='saisies_afficher_si_js')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}

	// Préparer les pseudo config
	include_spip("inc/config");
	ecrire_config("tests_saisies_config", array("a" => "a", "sous" => array("b" => "b", "c" => "c")));
	//
	// hop ! on y va
	//
	$err = tester_fun($f, essais_saisies_evaluer_afficher_si());
	effacer_config('tests_saisies_config');

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
			),
			'nombre_superieur_vrai' => array(
				0 => true,
				1 => "@nombre@ > 10"
			),
			'nombre_superieur_faux' => array(
				0 => false,
				1 => "@nombre@ > 100"
			),
			'nombre_superieur_egal_vrai' => array(
				0 => true,
				1 => "@nombre@ >= 20"
			),
			'nombre_superieur_egal_faux' => array(
				0 => false,
				1 => "@nombre@ >= 100"
			),
			'nombre_inferieur_vrai' => array(
				0 => true,
				1 => "@nombre@ < 100"
			),
			'nombre_inferieur_faux' => array(
				0 => false,
				1 => "@nombre@ < 10"
			),
			'nombre_inferieur_egal_vrai' => array(
				0 => true,
				1 => "@nombre@ <= 20"
			),
			'nombre_inferieur_egal_faux' => array(
				0 => false,
				1 => "@nombre@ <= 10"
			),
			'false' => array(
				0 => false,
				1 => 'false'
			),
			'true' => array(
				0 => true,
				1 => 'true'
			),
			'anti_false' => array(
				0 => true,
				1 => '!false'
			),
			'anti_true' => array(
				0 => false,
				1 => '!true'
			),
			'premier_niveau' => array(
				0 => 'true',
				1 => '@config:tests_saisies_config:a@==\'a\'',
			),
			'second_niveau' => array(
				0 => 'false',
				1 => '@config:tests_saisies_config:sous:b@==\'c\'',
			),
			'second_niveau_bis' => array(
				0 => 'true',
				1 => '@config:tests_saisies_config:sous:c@==\'c\'',
			),
			'second_niveau_et' => array(
				0 => 'true && false',
				1 => '@config:tests_saisies_config:sous:c@==\'c\' && @config:tests_saisies_config:sous:b@==\'c\'',
			),
			'second_niveau_ou' => array(
				0 => 'true || false',
				1 => '@config:tests_saisies_config:sous:c@==\'c\' || @config:tests_saisies_config:sous:b@==\'c\'',
			),
			'plugin_actif' => array(
				0 =>  'true',
				1 => '@plugin:saisies@'
			),
			'plugin_inactif' => array(
				0 => 'false',
				1 => '@plugin:tartempion_de_test@' // en espérant que personne ne nomme un plugin tartempion_de_test
			),
		);
		return $essais;
	}


?>
