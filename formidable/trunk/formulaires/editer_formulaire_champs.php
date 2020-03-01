<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_editer_formulaire_champs_charger($id_formulaire) {
	$contexte = array();
	$id_formulaire = intval($id_formulaire);

	// On teste si le formulaire existe
	if ($id_formulaire
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)
		and autoriser('editer', 'formulaire', $id_formulaire)
	) {
		$saisies = unserialize($formulaire['saisies']);

		// Est-ce qu'on restaure une révision ?
		if ($id_version = _request('id_version')) {
			include_spip('inc/revisions');
			$old = recuperer_version($id_formulaire, 'formulaire', $id_version);
			$saisies = unserialize($old['saisies']);
		}
		if (!is_array($saisies)) {
			$saisies = array();
		}
		$contexte['_saisies'] = $saisies;
		$contexte['id'] = $id_formulaire;
		$contexte['saisie_id'] = "formidable_$id_formulaire";

		// Les options globales que l'on permet de configurer pour le contexte de Formidables
		$contexte['_options_globales'] = array(
			array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'texte_submit',
					'label' => _T('formidable:editer_globales_texte_submit_label'),
				),
			),
			array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'etapes_activer',
					'label_case' => _T('formidable:editer_globales_etapes_activer_label_case'),
					'explication' => _T('formidable:editer_globales_etapes_activer_explication'),
				),
			),
			array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'etapes_suivant',
					'label' => _T('formidable:editer_globales_etapes_suivant_label'),
				),
			),
			array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'etapes_precedent',
					'label' => _T('formidable:editer_globales_etapes_precedent_label'),
				),
			),
			array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'verifier_valeurs_acceptables',
					'label' => _T('saisies:verifier_valeurs_acceptables_label'),
					'explication' => _T('saisies:verifier_valeurs_acceptables_explication'),
				)
			)
		);
	}

	return $contexte;
}

function formulaires_editer_formulaire_champs_verifier($id_formulaire) {
	include_spip('inc/saisies');
	$erreurs = array();

	// Si c'est pas une confirmation ni une annulation
	if (!_request('enregistrer_confirmation')
		and !($annulation = _request('annulation'))) {
		// On récupère le formulaire dans la session
		$saisies_nouvelles = session_get("constructeur_formulaire_formidable_$id_formulaire");
		$md5_precedent_formulaire_initial = session_get("constructeur_formulaire_formidable_$id_formulaire".'_md5_formulaire_initial');

		// On récupère les anciennes saisies
		$saisies_anciennes = sql_getfetsel(
			'saisies',
			'spip_formulaires',
			'id_formulaire = '.$id_formulaire
		);
		if (!$saisies_anciennes) {
			return $erreurs;
		}
		// On vérifie que les saisies en bases n'ont pas été modifiés depuis le début de la modification du formulaire
		// Si tel est le cas, on demande de recommencer la modif du formulaire, avec la saisie en base
		// Ne pas le faire si on est en train de restaurer une vieille version, puisque dans ce cas ce qui compte sera bien sur la veille version qu'on veut restaurer, et pas la version plus récente en base:)
		$md5_saisies_anciennes = md5($saisies_anciennes);
		$saisies_anciennes = unserialize($saisies_anciennes);
		if ($md5_precedent_formulaire_initial and $md5_precedent_formulaire_initial != $md5_saisies_anciennes and !_request('id_version')) {
			session_set("constructeur_formulaire_formidable_$id_formulaire", $saisies_anciennes);
			session_set("constructeur_formulaire_formidable_$id_formulaire".'_md5_formulaire_initial', $md5_saisies_anciennes);
			$erreurs['message_erreur'] = _T('formidable:erreur_saisies_modifiees_parallele');
			$erreurs['saisies_modifiees_parallele'] = _T('formidable:erreur_saisies_modifiees_parallele');
			return $erreurs;
		}

		// On compare les anciennes saisies aux nouvelles
		$comparaison = saisies_comparer($saisies_anciennes, $saisies_nouvelles);

		// S'il y a des suppressions, on demande confirmation avec attention
		if ($comparaison['supprimees']) {
			$erreurs['message_erreur'] = _T('saisies:construire_attention_supprime');
		}
	} elseif (isset($annulation) and $annulation) {
		// Si on annule on génère une erreur bidon juste pour réafficher le formulaire
		$erreurs['pouetpouet'] = true;
	}

	return $erreurs;
}

function formulaires_editer_formulaire_champs_traiter($id_formulaire) {
	include_spip('inc/saisies');
	$retours = array();
	$id_formulaire = intval($id_formulaire);

	if (_request('revert')) {
		session_set("constructeur_formulaire_formidable_$id_formulaire");
		$retours = array('editable'=>true);
	}

	if (_request('enregistrer') or _request('enregistrer_confirmation')) {
		// On récupère le formulaire dans la session
		$saisies_nouvelles = session_get("constructeur_formulaire_formidable_$id_formulaire");

		// On récupère les anciennes saisies
		$saisies_anciennes = unserialize(sql_getfetsel(
			'saisies',
			'spip_formulaires',
			'id_formulaire = '.$id_formulaire
		));

		// On envoie les nouvelles dans la table
		include_spip('action/editer_objet');
		$err = objet_modifier('formulaire', $id_formulaire, array('saisies' => serialize($saisies_nouvelles)));

		// Si c'est bon on appelle d'éventuelles fonctions d'update des traitements
		// puis on renvoie vers la config des traitements
		if (!$err) {
			// On va chercher les traitements
			$traitements = unserialize(sql_getfetsel(
				'traitements',
				'spip_formulaires',
				'id_formulaire = '.$id_formulaire
			));

			// Pour chaque traitements on regarde s'i y a une fonction d'update
			if (is_array($traitements)) {
				foreach ($traitements as $type_traitement => $traitement) {
					if ($update = charger_fonction('update', "traiter/$type_traitement", true)) {
						$update($id_formulaire, $traitement, $saisies_anciennes, $saisies_nouvelles);
					}
				}
			}
			// On redirige vers la config suivante
			$retours['redirect'] = parametre_url(
				parametre_url(
					parametre_url(
						generer_url_ecrire('formulaire_edit'),
						'id_formulaire',
						$id_formulaire
					),
					'configurer',
					'traitements'
				),
				'avertissement',
				'oui'
			);
			if ($id_version = _request('id_version')) {
				$retours['redirect'] = parametre_url($retours['redirect'], 'id_version', $id_version);
			}
		}
	}

	return $retours;
}
