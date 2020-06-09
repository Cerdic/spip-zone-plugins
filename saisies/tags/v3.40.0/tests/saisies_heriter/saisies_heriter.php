<?php
/**
 * Test unitaire de la fonction saisies_charger_infos, avec heritage
 * du fichier ../plugins/saisies/inc/saisies_lister.php
 *
 */

$test = 'saisies_heriter';
$remonte = "../";
while (!is_dir($remonte."ecrire"))
	$remonte = "../$remonte";
require $remonte.'tests/test.inc';
find_in_path("../plugins/saisies/inc/saisies.php",'',true);

// chercher la fonction si elle n'existe pas
function tester_saisies_charger_infos($saisie, $chemin) {
	if (!function_exists($f='saisies_charger_infos')){
		find_in_path("inc/filtres.php",'',true);
		$f = chercher_filtre($f);
	}
	return saisies_supprimer_identifiants($f($saisie, $chemin));
}
$g =  'tester_saisies_charger_infos';

$err = tester_fun($g, essais_saisies_charger_infos());

// si le tableau $err est pas vide ca va pas
if ($err) {
	die ('<dl>' . join('', $err) . '</dl>');
}

echo "OK";

// On va tester essentiellement sur des set_request, le cas $env étant normalement identique

function essais_saisies_charger_infos(){
	$essais = array (
		'0' => array(
			0 => array (
				'titre' => 'toto',
				'description' => 'toto_description',
				'icone' => 'plugins/saisies/images/saisies_input.png',
				'options' =>
				array (
					0 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'description',
							'label' => '<:saisies:option_groupe_description:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_au_debut',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'label',
									'label' => '<:saisies:option_label_label:>',
									'explication' => '<:saisies:option_label_explication:>',
									'size' => 50,
								),
							),
							2 =>
							array (
								'options' =>
								array (
									'nom' => 'defaut',
									'label' => 'toto',
									'size' => 50,
								),
								'verifier' =>
								array (
									'type' => 'toto_verifier',
									'options' =>
									array (
										'normaliser' => true,
									),
								),
								'saisie' => 'toto',
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'explication',
									'label' => '<:saisies:option_explication_label:>',
									'explication' => '<:saisies:option_explication_explication:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_a_la_fin',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
						),
					),
					1 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'utilisation',
							'label' => '<:saisies:option_groupe_utilisation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'maxlength',
									'label' => '<:saisies:option_maxlength_label:>',
									'explication' => '<:saisies:option_maxlength_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable',
									'label_case' => '<:saisies:option_disable_label:>',
									'explication' => '<:saisies:option_disable_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable_avec_post',
									'label_case' => '<:saisies:option_disable_avec_post_label:>',
									'explication' => '<:saisies:option_disable_avec_post_explication:>',
								),
							),
							3 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'readonly',
									'label_case' => '<:saisies:option_readonly_label:>',
									'explication' => '<:saisies:option_readonly_explication:>',
								),
							),
						),
					),
					2 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'affichage',
							'label' => '<:saisies:option_groupe_affichage:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'textarea',
								'options' =>
								array (
									'nom' => 'afficher_si',
									'label' => '<:saisies:option_afficher_si_label:>',
									'explication' => '<:saisies:option_afficher_si_explication:>',
									'rows' => 5,
								),
								'verifier' =>
								array (
									'type' => 'afficher_si',
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'afficher_si_remplissage_uniquement',
									'label' => '<:saisies:option_afficher_si_remplissage_uniquement_label:>',
									'label_case' => '<:saisies:option_afficher_si_remplissage_uniquement_label_case:>',
									'explication' => '<:saisies:option_afficher_si_remplissage_uniquement_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'attention',
									'label' => '<:saisies:option_attention_label:>',
									'explication' => '<:saisies:option_attention_explication:>',
									'size' => 50,
								),
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'class',
									'label' => '<:saisies:option_class_label:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'conteneur_class',
									'label' => '<:saisies:option_conteneur_class_label:>',
									'size' => 50,
								),
							),
							5 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'size',
									'label' => '<:saisies:option_size_label:>',
									'explication' => '<:saisies:option_size_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							6 =>
							array (
								'saisie' => 'radio',
								'options' =>
								array (
									'nom' => 'autocomplete',
									'label' => '<:saisies:option_autocomplete_label:>',
									'explication' => '<:saisies:option_autocomplete_explication:>',
									'datas' =>
									array (
										'defaut' => '<:saisies:option_autocomplete_defaut:>',
										'on' => '<:saisies:option_autocomplete_on:>',
										'off' => '<:saisies:option_autocomplete_off:>',
									),
									'defaut' => 'defaut',
								),
							),
						),
					),
					3 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'validation',
							'label' => '<:saisies:option_groupe_validation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'obligatoire',
									'label_case' => '<:saisies:option_obligatoire_label:>',
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'info_obligatoire',
									'label' => '<:saisies:option_info_obligatoire_label:>',
									'explication' => '<:saisies:option_info_obligatoire_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'erreur_obligatoire',
									'label' => '<:saisies:option_erreur_obligatoire_label:>',
									'explication' => '<:saisies:option_erreur_obligatoire_explication:>',
								),
							),
						),
					),
				),
				'defaut' =>
				array (
					'options' =>
					array (
						'label' => 'toto',
						'size' => 40,
						'sql' => 'BIGINT (255)',
						'readonly' => 'on',
					),
					'verifier' =>
					array (
						'type' => 'toto',
						'options' =>
						array (
							'normaliser' => true,
						),
					),
				),
			),
			1 => 'toto',
			2 => 'tests/saisies/'
		),
		'1' => array(
			0 => array (
				'titre' => 'titi',
				'description' => 'titi_description',
				'icone' => 'plugins/saisies/images/saisies_input.png',
				'options' =>
				array (
					0 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'description',
							'label' => '<:saisies:option_groupe_description:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_au_debut',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'label',
									'label' => '<:saisies:option_label_label:>',
									'explication' => '<:saisies:option_label_explication:>',
									'size' => 50,
								),
							),
							2 =>
							array (
								'options' =>
								array (
									'nom' => 'defaut',
									'label' => 'toto',
									'size' => 50,
								),
								'verifier' =>
								array (
									'type' => 'toto_verifier',
									'options' =>
									array (
										'normaliser' => true,
									),
								),
								'saisie' => 'toto',
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'explication',
									'label' => '<:saisies:option_explication_label:>',
									'explication' => '<:saisies:option_explication_explication:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_a_la_fin',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
						),
					),
					1 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'utilisation',
							'label' => '<:saisies:option_groupe_utilisation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'maxlength',
									'label' => '<:saisies:option_maxlength_label:>',
									'explication' => '<:saisies:option_maxlength_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable',
									'label_case' => '<:saisies:option_disable_label:>',
									'explication' => '<:saisies:option_disable_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable_avec_post',
									'label_case' => '<:saisies:option_disable_avec_post_label:>',
									'explication' => '<:saisies:option_disable_avec_post_explication:>',
								),
							),
							3 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'readonly',
									'label_case' => '<:saisies:option_readonly_label:>',
									'explication' => '<:saisies:option_readonly_explication:>',
								),
							),
						),
					),
					2 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'affichage',
							'label' => '<:saisies:option_groupe_affichage:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'textarea',
								'options' =>
								array (
									'nom' => 'afficher_si',
									'label' => '<:saisies:option_afficher_si_label:>',
									'explication' => '<:saisies:option_afficher_si_explication:>',
									'rows' => 5,
								),
								'verifier' =>
								array (
									'type' => 'afficher_si',
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'afficher_si_remplissage_uniquement',
									'label' => '<:saisies:option_afficher_si_remplissage_uniquement_label:>',
									'label_case' => '<:saisies:option_afficher_si_remplissage_uniquement_label_case:>',
									'explication' => '<:saisies:option_afficher_si_remplissage_uniquement_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'attention',
									'label' => '<:saisies:option_attention_label:>',
									'explication' => '<:saisies:option_attention_explication:>',
									'size' => 50,
								),
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'class',
									'label' => '<:saisies:option_class_label:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'conteneur_class',
									'label' => '<:saisies:option_conteneur_class_label:>',
									'size' => 50,
								),
							),
							5 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'size',
									'label' => '<:saisies:option_size_label:>',
									'explication' => '<:saisies:option_size_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							6 =>
							array (
								'saisie' => 'radio',
								'options' =>
								array (
									'nom' => 'autocomplete',
									'label' => '<:saisies:option_autocomplete_label:>',
									'explication' => '<:saisies:option_autocomplete_explication:>',
									'datas' =>
									array (
										'defaut' => '<:saisies:option_autocomplete_defaut:>',
										'on' => '<:saisies:option_autocomplete_on:>',
										'off' => '<:saisies:option_autocomplete_off:>',
									),
									'defaut' => 'defaut',
								),
							),
						),
					),
					3 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'validation',
							'label' => '<:saisies:option_groupe_validation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'obligatoire',
									'label_case' => '<:saisies:option_obligatoire_label:>',
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'info_obligatoire',
									'label' => '<:saisies:option_info_obligatoire_label:>',
									'explication' => '<:saisies:option_info_obligatoire_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'erreur_obligatoire',
									'label' => '<:saisies:option_erreur_obligatoire_label:>',
									'explication' => '<:saisies:option_erreur_obligatoire_explication:>',
								),
							),
						),
					),
				),
				'defaut' =>
				array (
					'options' =>
					array (
						'label' => 'titi',
						'size' => 40,
						'sql' => 'BIGINT (255)',
						'readonly' => 'on',
					),
					'verifier' =>
					array (
						'type' => 'toto',
						'options' =>
						array (
							'normaliser' => false,
						),
					),
				),
			),
			1 => 'titi',
			2 => 'tests/saisies/'
		),
		'3' => array(
			0 => array (
				'titre' => 'tata',
				'description' => 'tata_description',
				'icone' => 'plugins/saisies/images/saisies_input.png',
				'options' =>
				array (
					0 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'description',
							'label' => '<:saisies:option_groupe_description:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_au_debut',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'label',
									'label' => '<:saisies:option_label_label:>',
									'explication' => '<:saisies:option_label_explication:>',
									'size' => 50,
								),
							),
							2 =>
							array (
								'saisie' => 'tata',
								'options' =>
								array (
									'nom' => 'defaut',
									'label' => 'tata',
									'size' => 50,
									'label' => '<:saisies:option_defaut_label:>',
								),
								'verifier' =>
								array (
									'type' => 'tata',
									'options' =>
									array (
										'normaliser' => true,
									),
								),
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'explication',
									'label' => '<:saisies:option_explication_label:>',
									'explication' => '<:saisies:option_explication_explication:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'une_option_en_plus_dans_la_description_tout_a_la_fin',
									'label' => 'une_option_en_plus',
									'size' => 50,
								),
							),
						),
					),
					1 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'utilisation',
							'label' => '<:saisies:option_groupe_utilisation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'maxlength',
									'label' => '<:saisies:option_maxlength_label:>',
									'explication' => '<:saisies:option_maxlength_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable',
									'label_case' => '<:saisies:option_disable_label:>',
									'explication' => '<:saisies:option_disable_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'disable_avec_post',
									'label_case' => '<:saisies:option_disable_avec_post_label:>',
									'explication' => '<:saisies:option_disable_avec_post_explication:>',
								),
							),
							3 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'readonly',
									'label_case' => '<:saisies:option_readonly_label:>',
									'explication' => '<:saisies:option_readonly_explication:>',
								),
							),
						),
					),
					2 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'affichage',
							'label' => '<:saisies:option_groupe_affichage:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'textarea',
								'options' =>
								array (
									'nom' => 'afficher_si',
									'label' => '<:saisies:option_afficher_si_label:>',
									'explication' => '<:saisies:option_afficher_si_explication:>',
									'rows' => 5,
								),
								'verifier' =>
								array (
									'type' => 'afficher_si',
								),
							),
							1 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'afficher_si_remplissage_uniquement',
									'label' => '<:saisies:option_afficher_si_remplissage_uniquement_label:>',
									'label_case' => '<:saisies:option_afficher_si_remplissage_uniquement_label_case:>',
									'explication' => '<:saisies:option_afficher_si_remplissage_uniquement_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'attention',
									'label' => '<:saisies:option_attention_label:>',
									'explication' => '<:saisies:option_attention_explication:>',
									'size' => 50,
								),
							),
							3 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'class',
									'label' => '<:saisies:option_class_label:>',
									'size' => 50,
								),
							),
							4 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'conteneur_class',
									'label' => '<:saisies:option_conteneur_class_label:>',
									'size' => 50,
								),
							),
							5 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'size',
									'label' => '<:saisies:option_size_label:>',
									'explication' => '<:saisies:option_size_explication:>',
								),
								'verifier' =>
								array (
									'type' => 'entier',
									'options' =>
									array (
										'min' => 1,
									),
								),
							),
							6 =>
							array (
								'saisie' => 'radio',
								'options' =>
								array (
									'nom' => 'autocomplete',
									'label' => '<:saisies:option_autocomplete_label:>',
									'explication' => '<:saisies:option_autocomplete_explication:>',
									'datas' =>
									array (
										'defaut' => '<:saisies:option_autocomplete_defaut:>',
										'on' => '<:saisies:option_autocomplete_on:>',
										'off' => '<:saisies:option_autocomplete_off:>',
									),
									'defaut' => 'defaut',
								),
							),
						),
					),
					3 =>
					array (
						'saisie' => 'fieldset',
						'options' =>
						array (
							'nom' => 'validation',
							'label' => '<:saisies:option_groupe_validation:>',
						),
						'saisies' =>
						array (
							0 =>
							array (
								'saisie' => 'case',
								'options' =>
								array (
									'nom' => 'obligatoire',
									'label_case' => '<:saisies:option_obligatoire_label:>',
								),
							),
							1 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'info_obligatoire',
									'label' => '<:saisies:option_info_obligatoire_label:>',
									'explication' => '<:saisies:option_info_obligatoire_explication:>',
								),
							),
							2 =>
							array (
								'saisie' => 'input',
								'options' =>
								array (
									'nom' => 'erreur_obligatoire',
									'label' => '<:saisies:option_erreur_obligatoire_label:>',
									'explication' => '<:saisies:option_erreur_obligatoire_explication:>',
								),
							),
						),
					),
				),
				'defaut' =>
				array (
					'options' =>
					array (
						'label' => 'tata',
						'size' => 40,
						'sql' => 'BIGINT (255)',
						'readonly' => 'on',
					),
					'verifier' =>
					array (
						'type' => 'tata',
						'options' =>
						array (
							'normaliser' => true,
						),
					),
				),
			),
			1 => 'tata',
			2 => 'tests/saisies/'
		),
	);
	return $essais;
}


?>
