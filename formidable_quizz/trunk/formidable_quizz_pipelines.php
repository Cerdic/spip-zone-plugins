<?php

/**
 * Utilisation de pipelines
 *
 * @package SPIP\Formidable\Quizz\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/saisies');

function formidable_quizz_declarer_tables_objets_sql($tables) {
	$tables['spip_formulaires_reponses']['field']['quizz_score'] = 'int(12) not null default 0';
	$tables['spip_formulaires_reponses']['field']['quizz_total'] = 'int(12) not null default 0';
	
	return $tables;
}

function formidable_quizz_declarer_tables_principales($tables) {
	$tables['spip_formulaires_reponses_champs']['field']['quizz_score'] = 'int(12) not null default 0';
	
	return $tables;
}

function formidable_quizz_formulaire_verifier($flux) {
	if (
		$flux['args']['form'] == 'construire_formulaire'
		and strpos($flux['args']['args'][0], 'formidable_') === 0
		and $nom_ou_id = _request('configurer_saisie')
	) {
		// On ajoute le préfixe devant l'identifiant
		$identifiant = 'constructeur_formulaire_'.$flux['args']['args'][0];
		// On récupère le formulaire à son état actuel
		$formulaire_actuel = session_get($identifiant);
		
		if ($nom_ou_id[0] == '@') {
			$saisies_actuelles = saisies_lister_par_identifiant($formulaire_actuel);
			$name = $saisies_actuelles[$nom_ou_id]['options']['nom'];
		} else {
			$saisies_actuelles = saisies_lister_par_nom($formulaire_actuel);
			$name = $nom_ou_id;
		}
		$config = 'configurer_' . $name;
		
		// saisie inexistante => on sort
		if (!isset($saisies_actuelles[$nom_ou_id])) {
			return $flux;
		}
		
		// On ajoute un fieldset pour les quizz
		$flux['data'][$config] = saisies_inserer($flux['data'][$config], array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => "saisie_modifiee_${name}[options][quizz]",
				'label' => _T('formidable_quizz:config_quizz_label'),
			),
			'saisies' => array(
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => "saisie_modifiee_${name}[options][bareme]",
						'label' => _T('formidable_quizz:config_bareme_label'),
						'explication' => _T('formidable_quizz:config_bareme_explication'),
						'rows' => 5,
					),
				),
			),
		));
	}
	
	 return $flux;
}

function formidable_quizz_formulaire_traiter($flux) {
	// Si on est dans un Formidable et qu'il y a des barèmes
	if (
		$flux['args']['form'] == 'formidable'
		and $id_formulaire = formidable_id_formulaire($flux['args']['args'][0])
		and $id_formulaires_reponse = $flux['data']['id_formulaires_reponse']
		and $saisies = sql_getfetsel('saisies', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)
		and $saisies = unserialize($saisies)
		and $saisies_quizz = saisies_lister_avec_option('bareme', $saisies, 'identifiant')
	) {
		// Pour chaque champ ayant un barème, on va additionner pour chercher le total des scores maximum
		$total = 0;
		$score_reponse = 0;
		
		foreach ($saisies_quizz as $identifiant => $saisie) {
			if ($bareme = saisies_chaine2tableau($saisie['options']['bareme']) and is_array($bareme)) {
				// On cherche le score maximum du barème
				$bareme = array_map('intval', $bareme);
				$score_max = max($bareme);
				$total += $score_max;
				
				// On cherche les points de la personne pour ce champ
				$reponse = _request($saisie['options']['nom']);
				$score_champ = 0; // score par défaut
				if (isset($bareme[$reponse])) {
					$score_champ = $bareme[$reponse];
				}
				// On l'ajoute au score total de la réponse
				$score_reponse += $score_champ;
				
				// On met à jour l'enregistrement de réponse à ce champ pour garder le score
				sql_updateq(
					'spip_formulaires_reponses_champs',
					array('quizz_score' => $score_champ),
					array(
						'id_formulaires_reponse = '.$id_formulaires_reponse,
						'nom = '.sql_quote($saisie['options']['nom'])
					)
				);
			}
		}
		
		// On met à jour l'enregistrement de la réponse pour garder le score et le total
		sql_updateq(
			'spip_formulaires_reponses',
			array('quizz_score' => $score_reponse, 'quizz_total' => $total),
			'id_formulaires_reponse = '.$id_formulaires_reponse
		);
		
		// On ajoute l'affichage possible du score DANS le message de retour
		$pourcent = $total ? round(100*$score_reponse/$total, 1) : 0;
		$flux['data']['message_ok'] = _L(
			$flux['data']['message_ok'],
			array('score'=>$score_reponse, 'score_total'=>$total, 'score_pourcent'=>$pourcent)
		);
	}
	
	return $flux;
}

function formidable_quizz_formidable_affiche_resume_reponse($flux) {
	// On ne refait pas le test pour chaque réponse…
	static $test_bareme = array();
	
	// Si le formulaire a au moins un champ avec barème
	if (
		$id_formulaire = intval($flux['args']['id_formulaire'])
		and $id_formulaires_reponse = intval($flux['args']['id_formulaires_reponse'])
		and formidable_quizz_tester_bareme($id_formulaire)
	) {
		$reponse = sql_fetsel('quizz_score, quizz_total', 'spip_formulaires_reponses', 'id_formulaires_reponse = '.$id_formulaires_reponse);
		$quizz_score = intval($reponse['quizz_score']);
		$quizz_total = intval($reponse['quizz_total']);
		$pourcent = $quizz_total ? round(100*$quizz_score/$quizz_total, 1) : 0;
		$affichage = array($flux['data'], "<strong>${quizz_score}/${quizz_total} (${pourcent}%)</strong>");
		$flux['data'] = join('<br/>', $affichage);
	}
	
	return $flux;
}

function formidable_quizz_recuperer_fond($flux) {
	if ($flux['args']['fond'] == 'prive/objets/contenu/formulaires_reponse') {
		$reponse = sql_fetsel('*', 'spip_formulaires_reponses', 'id_formulaires_reponse = '.$flux['args']['contexte']['id_formulaires_reponse']);
		
		if (formidable_quizz_tester_bareme($reponse['id_formulaire'])) {
			$quizz_score = intval($reponse['quizz_score']);
			$quizz_total = intval($reponse['quizz_total']);
			$pourcent = $quizz_total ? round(100*$quizz_score/$quizz_total, 1) : 0;
			
			$flux['data']['texte'] .= '<div class="champ contenu_date">';
			$flux['data']['texte'] .= '	<div class="label">' . _T('formidable_quizz:resultats_score_label') . '</div>';
			$flux['data']['texte'] .= "	<strong>${quizz_score}/${quizz_total} (${pourcent}%)</strong>";
			$flux['data']['texte'] .= '</div>';
		}
	}
	
	return $flux;
}

function formidable_quizz_formidable_exporter_formulaire_reponses_titres($flux) {
	if (formidable_quizz_tester_bareme($flux['args']['id_formulaire'])) {
		$flux['data'][] = _T('formidable_quizz:resultats_score_label');
		$flux['data'][] = _T('formidable_quizz:resultats_total_label');
	}
	
	return $flux;
}

function formidable_quizz_formidable_exporter_formulaire_reponses_reponse($flux) {
	if (formidable_quizz_tester_bareme($flux['args']['id_formulaire'])) {
		$flux['data'][] = $flux['args']['reponse']['quizz_score'];
		$flux['data'][] = $flux['args']['reponse']['quizz_total'];
	}
	
	return $flux;
}

/**
 * Tester si un formulaire contient au moins un barème
 * 
 * Ne teste le formulaire qu'une fois par hit
 * 
 * @param int $id_formulaire
 * 		Identifiant du formulaire à tester
 * @return bool
 * 		Retourne true si c'est le cas, false sinon
 **/
function formidable_quizz_tester_bareme($id_formulaire) {
	static $test_bareme = array();
	
	if (!isset($test_bareme[$id_formulaire])) {
		$test_bareme[$id_formulaire] = (
			$saisies = sql_getfetsel('saisies', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)
			and $saisies = unserialize($saisies)
			and saisies_lister_avec_option('bareme', $saisies)
		);
	}
	
	return $test_bareme[$id_formulaire];
}
