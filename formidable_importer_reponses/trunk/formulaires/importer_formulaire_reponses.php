<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable');
include_spip('inc/config');

function formulaires_importer_formulaire_reponses_charger($id_formulaire = 0) {
	$contexte                  = array();
	$contexte['id_formulaire'] = intval($id_formulaire);

	return $contexte;
}

function formulaires_importer_formulaire_reponses_verifier($id_formulaire = 0) {
	$erreurs = array();
	return $erreurs;
}

function formulaires_importer_formulaire_reponses_traiter($id_formulaire = 0) {
	$retours         = array();
	$statut_reponses = _request('statut_reponses');
	$ok = false;
	if (isset($_FILES) && isset($_FILES['fichier']) && !$_FILES['fichier']['error']) {
		$type_import = _request('type_import');
		$fichier = $_FILES['fichier']['tmp_name'];
		if (_request('type_import') == 'csv') {
			$res = importer_formulaires_reponses($id_formulaire, $fichier, ',', $statut_reponses);
			$retours = $res;
		}
	}

	if (!$res) {
		$retours['editable']       = 1;
		$retours['message_erreur'] = _T('formidable:info_aucune_reponse');
	}

	return $retours;
}

/**
 * Importer toutes les réponses du fichier
 * @param integer $id_formulaire
 * @return unknown_type
 */
function importer_formulaires_reponses($id_formulaire, $fichier, $delim = ',', $statut_reponses = 'publie') {
	// on ne fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	if ($id_formulaire > 0
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire)) {
		include_spip('inc/saisies');
		include_spip('inc/filtres');
		$reponses_completes = array();
		// Charger la fonction inc_importer_csv de bonux
		$importer_csv = charger_fonction('importer_csv', 'inc');
		$reponses = $importer_csv($fichier, true);

		if (is_array($reponses) and count($reponses) >= 1) {
			$saisies = saisies_lister_par_nom(unserialize($formulaire['saisies']), false);
			$saisies_nom = array();
			$saisies_obligatoires = array();
			foreach ($saisies as $nom => $saisie) {
				if ($saisie['saisie'] != 'explication') {    // on exporte tous les champs sauf explications
					$saisies_nom[] = $saisie['options']['nom'];
					if ($saisie['options']['obligatoire'] == 'on') {
						$saisies_obligatoires[] = $saisie['options']['nom'];
					}
				}
			}
			foreach ($saisies_obligatoires as $champ) {
				if (!isset($reponses[0][$champ])) {
					$retours = array(
						'message_erreur' => _T('formidable_importerreponse:message_champ_obligatoire_non_dispo', array('champ' => $champ)),
						'editable' => ' '
					);
					return $retours;
				}
			}

			include_spip('inc/acces');
			// On parcourt chaque réponse
			foreach ($reponses as $reponse) {
				$info_reponse = array(
					'id_formulaire' => $id_formulaire,
					'cookie' => creer_uniqid()
				);
				// Est-ce qu'il y a un auteur avec un nom
				if (isset($reponse['id_auteur'])) {
					$info_reponse['id_auteur'] = intval($reponse['id_auteur']);
					unset($reponse['id_auteur']);
				} else {
					$info_reponse['id_auteur'] = $GLOBALS['visiteur_session']['id_auteur'];
				}
				if (isset($reponse['date'])) {
					$info_reponse['date'] = $reponse['date'];
					unset($reponse['date']);
				} else {
					$info_reponse['date'] = date('Y-m-d H:i:s');
				}
				if (isset($reponse['ip'])) {
					$info_reponse['ip'] = $reponse['ip'];
					unset($reponse['ip']);
				}
				$info_reponse['statut'] = $statut_reponses;
				$id_formulaires_reponse = sql_insertq(
					'spip_formulaires_reponses',
					$info_reponse
				);

				$insertions = array();
				foreach ($saisies_nom as $saisie) {
					if (isset($reponse[$saisie])) {
						$insertions[] = array(
							'id_formulaires_reponse' => $id_formulaires_reponse,
							'nom' => $saisie,
							'valeur' => is_array($reponse[$saisie]) ? serialize($reponse[$saisie]) : $reponse[$saisie]
						);
					}
				}
				if (count($insertions) > 0) {
					sql_insertq_multi(
						'spip_formulaires_reponses_champs',
						$insertions
					);
				}
			}
			$retours = array(
				'message_ok' => _T('formidable_importerreponse:message_insertions_nb', array('nb' => count($reponses))),
				'editable' => ' '
			);
			return $retours;
		} else {
			$retours = array(
				'message_erreur' => _T('formidable_importerreponse:message_fichier_sans_reponse'),
				'editable' => ' '
			);
			return $retours;
		}
	} else {
		$retours = array(
			'message_erreur' => _T('formidable_importerreponse:message_formulaire_inexistant'),
			'editable' => ' '
		);
		return $retours;
	}
}
