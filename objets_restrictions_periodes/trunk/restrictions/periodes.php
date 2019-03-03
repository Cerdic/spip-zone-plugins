<?php
if (!defined("_ECRIRE_INC_VERSION"))
	return;

// Définition des champs pour le détail du formulaire restriction du plugin restrictions (https://github.com/abelass/restrictions)
function restrictions_periodes_dist($flux) {
	// Les jours de la semaine
	$jours_semaines = array();
	for ($i = 1; $i < 8; $i++) {
		$jour = $i + 1;
		if ($i == 7) {
			$jour = 1;
		}

		$jours_semaines[$i] = _T('spip:date_jour_' . $jour);
	}


	$return = [
		'nom' => _T('periodes:periodes_titre'),
		'verifier' => [
			'type' => 'dates',
			'champs' => ['date_debut', 'date_fin'],
		],
		'saisies' => [
			[
				'saisie' => 'periodes',
				'options' => [
					'nom' => 'periode',
					'label' => _T('periode:champ_periode_label'),
				]
			],
			[
				'saisie' => 'selection',
				'options' => [
					'nom' => 'type',
					'label' => _T('objets_restrictions_periodes:champ_type_label'),
					'data' => [
						'duree' => _T('objets_restrictions_periodes:choix_duree'),
						'jours' => _T('objets_restrictions_periodes:choix_jours')
					],
					'obligatoire' => 'oui'
				]
			],
			[
				'saisie' => 'input',
				'options' => [
					'nom' => 'duree',
					'label' => _T('objets_restrictions_periodes:champ_duree_label'),

					'afficher_si' => '@type@=="duree"'
				]
			],
			/*[
				'saisie' => 'oui_non',
				'options' => [
					'nom' => 'duree_minimale',
					'label' => _T('objets_restrictions_periodes:champ_duree_minimale_label'),
					'defaut' => 'on',
					'afficher_si' => '@type@=="duree"'
				]
			],*/
			[
				'saisie' => 'selection',
				'options' => [
					'nom' => 'jour_debut',
					'label' => _T('objets_restrictions_periodes:champ_jour_debut_label'),
					'data' => $jours_semaines,
					'afficher_si' => '@type@=="jours"'
				]
			],
			[
				'saisie' => 'selection',
				'options' => [
					'nom' => 'jour_fin',
					'label' => _T('objets_restrictions_periodes:champ_jour_fin_label'),
					'data' => $jours_semaines,
					'afficher_si' => '@type@=="jours"'
				]
			],
		]

	];

	return $return;
}

