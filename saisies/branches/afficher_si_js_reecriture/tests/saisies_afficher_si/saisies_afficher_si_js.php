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

	// les saisies à passer
	$saisies_form = array();
	foreach (array('case_1','radio_1','checkbox_1','input_1') as $saisie) {
			$saisies_form[] = array(
				'saisie' => str_replace('_1', '', $saisie),
				'options' => array('nom' => $saisie)
			);
	}
	// hop ! on y va
	//
	$err = tester_fun($f, essais_saisies_afficher_si_js($saisies_form));
	effacer_config('tests_saisies_config');

	// si le tableau $err est pas vide ca va pas
	if ($err) {
		die ('<dl>' . join('', $err) . '</dl>');
	}

	echo "OK";

	// On va tester essentiellement sur des set_request, le cas $env étant normalement identique

	function essais_saisies_afficher_si_js($saisies_form){
		$essais = array (
			'input_egalite' => array(
				0 => '$(form).find(\'[name=&quot;input_1&quot;]\').val() == \'toto\'',
				1 => '@input_1@ == \'toto\'',
				2 => $saisies_form
			),
			'input_egalite_double_quote' => array(
				0 => '$(form).find(\'[name=&quot;input_1&quot;]\').val() == &quot;toto&quot;',
				1 => '@input_1@ == "toto"',
				2 => $saisies_form
			),
			'input_egalite_nb' => array(
				0 => '$(form).find(\'[name=&quot;input_1&quot;]\').val() == 23',
				1 => '@input_1@ == 23',
				2 => $saisies_form
			),
			'input_inegalite' => array(
				0 => '$(form).find(\'[name=&quot;input_1&quot;]\').val() != \'toto\'',
				1 => '@input_1@ != \'toto\'',
				2 => $saisies_form
			),
			'input_egalite_nie' => array(
				0 => '!$(form).find(\'[name=&quot;input_1&quot;]\').val() == \'toto\'',
				1 => '!@input_1@ == \'toto\'',
				2 => $saisies_form
			),
			'input_inegalite_nie' => array(
				0 => '!$(form).find(\'[name=&quot;input_1&quot;]\').val() != \'toto\'',
				1 => '!@input_1@ != \'toto\'',
				2 => $saisies_form
			),
			'checkbox_egalite' => array(
				0 => '($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '@checkbox_1@ == \'toto\'',
				2 => $saisies_form
			),
			'checkbox_inegalite' => array(
				0 => '!($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '@checkbox_1@ != \'toto\'',
				2 => $saisies_form
			),
			'checkbox_IN' => array(
				0 => '($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '@checkbox_1@ IN \'toto\'',
				2 => $saisies_form
			),
			'checkbox_NOT_IN' => array(
				0 => '!($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '@checkbox_1@ !IN \'toto\'',
				2 => $saisies_form
			),
			'checkbox_IN_nie' => array(
				0 => '!($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '@checkbox_1@ !IN \'toto\'',
				2 => $saisies_form
			),
			'checkbox_NOT_IN_nie' => array(//cas sans doute rare, mais sait-on jamais
				0 => '($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\'))',
				1 => '!@checkbox_1@ !IN \'toto\'',
				2 => $saisies_form
			),
			'checkbox_IN_MULTIPLE' => array(
				0 => '($(form).find(checkbox[name=checkbox_1[]][value=\'toto\']).is(\':checked\') || $(form).find(checkbox[name=checkbox_1[]][value=\'tata\']).is(\':checked\'))',
				1 => '@checkbox_1@ IN \'toto,tata\'',
				2 => $saisies_form
			),
			'radio_egal' => array(
				0 => '$(form).find(&quot;[name=\'radio_1\']:checked&quot;).val() == \'toto\'',
				1 => '@radio_1@ == \'toto\'',
				2 => $saisies_form
			),
			'radio_inegal' => array(
				0 => '$(form).find(&quot;[name=\'radio_1\']:checked&quot;).val() != \'toto\'',
				1 => '@radio_1@ != \'toto\'',
				2 => $saisies_form
			),
			'case' => array(
				0 => '$(form).find(&quot;.checkbox[name=\'case_1\']&quot;).is(\':checked\') ? $(form).find(&quot;.checkbox[name=\'case_1\']&quot;).val() : \'\'',
				1 => '@case_1@',
				2 => $saisies_form
			),
			'false' => array(
				0 => 'false',
				1 => 'false'
			),
			'true' => array(
				0 => 'true',
				1 => 'true'
			),
			'anti_false' => array(
				0 => '!false',
				1 => '!false'
			),
			'anti_true' => array(
				0 => '!true',
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
			'premier_niveau_nie' => array(
				0 => 'false',
				1 => '!@config:tests_saisies_config:a@==\'a\'',
			),
			'second_niveau_nie' => array(
				0 => 'true',
				1 => '!@config:tests_saisies_config:sous:b@==\'c\'',
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
			'plugin_actif_nie' => array(
				0 =>  'false',
				1 => '!@plugin:saisies@'
			),
			'plugin_inactif_nie' => array(
				0 => 'true',
				1 => '!@plugin:tartempion_de_test@' // en espérant que personne ne nomme un plugin tartempion_de_test
			),
		);
		return $essais;
	}


?>
