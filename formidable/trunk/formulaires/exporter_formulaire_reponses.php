<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable');
include_spip('inc/formidable_fichiers');
include_spip('inc/config');

function formulaires_exporter_formulaire_reponses_charger($id_formulaire = 0) {
	$contexte                  = array();
	$contexte['id_formulaire'] = intval($id_formulaire);

	return $contexte;
}

function formulaires_exporter_formulaire_reponses_verifier($id_formulaire = 0) {
	$erreurs = array();

	if (_request('date_debut') && _request('date_fin')) {
		// Vérifions que la date debut soit < date de fin
		if (strtotime(str_replace('/', '-', _request('date_debut'))) > strtotime(str_replace('/', '-', _request('date_fin')))) {
			$erreurs['message_erreur'] = _T('formidable:exporter_formulaire_date_erreur');
		}
	}

	return $erreurs;
}

function formulaires_exporter_formulaire_reponses_traiter($id_formulaire = 0) {
	$retours         = array();
	$statut_reponses = _request('statut_reponses');
	// Normaliser la date
	$verifier = charger_fonction('verifier', 'inc/');
	$date_debut = _request('date_debut') ? $verifier(_request('date_debut'), 'date', array('normaliser' => 'datetime')) : false;
	$date_fin = _request('date_fin') ? $verifier(_request('date_fin'), 'date', array('normaliser' => 'datetime')) : false;

	if (_request('type_export') == 'csv') {
		$ok = exporter_formulaires_reponses($id_formulaire, ',', $statut_reponses, $date_debut, $date_fin);
	} elseif (_request('type_export') == 'xls') {
		$ok = exporter_formulaires_reponses($id_formulaire, 'TAB', $statut_reponses, $date_debut, $date_fin);
	}

	if (!$ok) {
		$retours['editable']       = 1;
		$retours['message_erreur'] = _T('formidable:info_aucune_reponse');
	}

	return $retours;
}

/*
 * Exporter toutes les réponses d'un formulaire (anciennement action/exporter_formulaire_reponses)
 * @param integer $id_formulaire
 * @return unknown_type
 */
function exporter_formulaires_reponses($id_formulaire, $delim = ',', $statut_reponses = 'publie', $date_debut = false, $date_fin = false) {
	include_spip('inc/puce_statut');
	// on ne fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	if ($id_formulaire > 0
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire)
		and $reponses = sql_allfetsel(
			'*',
			'spip_formulaires_reponses',
			'id_formulaire = ' . intval($id_formulaire) . ($statut_reponses == 'publie' ? ' and statut = "publie"' : '')
			. ($date_debut ? ' and date >= "'. $date_debut. '"' : '')
			. ($date_fin ? ' and date <= "'.$date_fin.'"' : '')
		)) {
		include_spip('inc/saisies');
		include_spip('facteur_fonctions');
		include_spip('inc/filtres');
		$reponses_completes = array();

		// La première ligne des titres
		$titres = array(
			_T('formidable:id_formulaires_reponse'),
			_T('public:date'),
			_T('formidable:reponses_auteur'),
			_T('formidable:reponses_ip'),
		);
		if ($statut_reponses != 'publie') {
			$titres[] = _T('formidable:reponse_statut');
		}
		$saisies = saisies_lister_par_nom(unserialize($formulaire['saisies']), false);
		foreach ($saisies as $nom => $saisie) {
			if ($saisie['saisie'] != 'explication') {    // on exporte tous les champs sauf explications
				$options  = $saisie['options'];
				$titres[] = sinon(
					isset($options['label_case']) ? $options['label_case'] : '',
					sinon(
						isset($options['label']) ? $options['label'] : '',
						$nom
					)
				);
			}
		}

		// On passe la ligne des titres de colonnes dans un pipeline
		$titres               = pipeline(
			'formidable_exporter_formulaire_reponses_titres',
			array(
				'args' => array('id_formulaire' => $id_formulaire, 'formulaire' => $formulaire),
				'data' => $titres,
			)
		);
		$reponses_completes[] = $titres;
		$saisies_fichiers     = array();

		// On parcourt chaque réponse
		foreach ($reponses as $reponse) {
			// Est-ce qu'il y a un auteur avec un nom
			$nom_auteur = '';
			if ($id_auteur = intval($reponse['id_auteur'])) {
				$nom_auteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur = ' . $id_auteur);
			}
			if (!$nom_auteur) {
				$nom_auteur = '';
			}

			// Le début de la réponse avec les infos (date, auteur, etc)
			$reponse_complete = array(
				$reponse['id_formulaires_reponse'],
				$reponse['date'],
				$nom_auteur,
				$reponse['ip'],
			);
			if ($statut_reponses != 'publie') {
				$reponse_complete[] = statut_texte_instituer('formulaires_reponse', $reponse['statut']);
			}

			// Ensuite tous les champs
			$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');
			foreach ($saisies as $nom => $saisie) {
				if ($saisie['saisie'] != 'explication') {
					$valeur = sql_getfetsel(
						'valeur',
						'spip_formulaires_reponses_champs',
						'id_formulaires_reponse = ' . intval($reponse['id_formulaires_reponse']) . ' and nom = ' . sql_quote($nom)
					);
					$valeur = $tenter_unserialize($valeur);

					// Saisie de type fichier ?
					if ($saisie['saisie'] == 'fichiers' && is_array($valeur)) {//tester s'il y a des saisies parmi les fichiers
						$chemin         = _DIR_FICHIERS_FORMIDABLE . 'formulaire_' . $id_formulaire . '/reponse_' . $reponse['id_formulaires_reponse'];
						foreach ($valeur as $v) {
							$chemin_fichier = $chemin . '/' . $saisie['options']['nom'] . '/' . $v['nom'];
							if (file_exists($chemin_fichier)) {
								$saisies_fichiers[] = $chemin_fichier;
							}
						}
					}

					$reponse_complete[] = facteur_mail_html2text(
						recuperer_fond(
							'saisies-vues/_base',
							array_merge(
								array(
									'valeur_uniquement' => 'oui',
									'type_saisie'       => $saisie['saisie'],
									'valeur'            => $valeur,
								),
								$saisie['options']
							)
						)
					);
				}
			}

			// On passe la ligne de réponse dans un pipeline
			$reponse_complete = pipeline(
				'formidable_exporter_formulaire_reponses_reponse',
				array(
					'args' => array(
						'id_formulaire' => $id_formulaire,
						'formulaire'    => $formulaire,
						'reponse'       => $reponse,
					),
					'data' => $reponse_complete,
				)
			);

			// On ajoute la ligne à l'ensemble des réponses
			$reponses_completes[] = $reponse_complete;
		}

		if (!count($saisies_fichiers)) {// si pas de saisie fichiers, on envoie directement le csv
			if ($reponses_completes and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)) {
				$exporter_csv('reponses-formulaire-' . $formulaire['identifiant'], $reponses_completes, $delim);
				exit();
			}
		} else {
			if ($reponses_completes and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)) {
				$fichier_csv = $exporter_csv('reponses-formulaire-' . $formulaire['identifiant'], $reponses_completes, $delim, null, false);
				$fichier_zip = sous_repertoire(_DIR_CACHE, 'export') . 'reponses-formulaire-' . $formulaire['identifiant'] . '.zip';
				include_spip('inc/formidable_fichiers');
				$fichier_zip = formidable_zipper_reponses_formulaire($formulaire['id_formulaire'], $fichier_zip, $fichier_csv, $saisies_fichiers);
				if (!$fichier_zip) {// si erreur lors du zippage
					return false;
				} else {
					formidable_retourner_fichier($fichier_zip, basename($fichier_zip));
				}
			}
		}
	} else {
		return false;
	}
}
