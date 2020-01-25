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
	$verifier(_request('date_debut'), 'date', array('normaliser' => 'datetime'), $date_debut);
	$verifier(_request('date_fin'), 'date', array('normaliser' => 'datetime'), $date_fin);
	$cle_ou_valeur = _request('cle_ou_valeur');
	$chemin = false;
	$content_type = '';

	if (_request('type_export') == 'csv') {
		$chemin = exporter_formulaires_reponses($id_formulaire, ',', $statut_reponses, $date_debut, $date_fin, $cle_ou_valeur);
		if (pathinfo($chemin, PATHINFO_EXTENSION) === 'csv') {
			$content_type = "text/comma-separated-values; charset=" . $GLOBALS['meta']['charset'];
		}
	} elseif (_request('type_export') == 'xls') {
		$chemin = exporter_formulaires_reponses($id_formulaire, 'TAB', $statut_reponses, $date_debut, $date_fin, $cle_ou_valeur);
		if (pathinfo($chemin, PATHINFO_EXTENSION) === 'xls') {
			$content_type = "text/comma-separated-values; charset=iso-8859-1";
		}
	}

	if ($chemin) {
		formidable_retourner_fichier($chemin, basename($chemin), $content_type);
	} else {
		$retours['editable']       = 1;
		$retours['message_erreur'] = _T('formidable:info_aucune_reponse');
	}

	return $retours;
}


/**
 * Exporter toutes les réponses d'un formulaire dans un fichier
 *
 * @param integer $id_formulaire
 * @param string $delim (Délimiteur ',' ou 'TAB')
 * @param string $statut_reponses
 * @param string $date_debut
 * @param string $date_fin
 * @param string $cle_ou_valeur
 * @return string|false Chemin du fichier d’export CSV, XLS ou ZIP
 */
function exporter_formulaires_reponses($id_formulaire, $delim = ',', $statut_reponses = 'publie', $date_debut = '', $date_fin = '',$cle_ou_valeur = 'valeur') {
	$exporter_csv = charger_fonction('exporter_csv', 'inc/', true);
	if (!$exporter_csv) {
		return false;
	}

	list($formulaire, $reponses) = obtenir_formulaire_reponses($id_formulaire, $statut_reponses, $date_debut, $date_fin);
	if (!$formulaire or !$reponses) {
		return false;
	}

	list($reponses_completes, $saisies_fichiers) = preparer_formulaire_reponses($formulaire, $reponses, $statut_reponses, $cle_ou_valeur);
	if (!$reponses_completes) {
		return false;
	}

	$fichier_csv = $exporter_csv('reponses-formulaire-' . $formulaire['identifiant'], $reponses_completes, $delim, null, false);

	// si pas de saisie fichiers, on envoie directement le csv
	if (!count($saisies_fichiers)) {
		return $fichier_csv;
	}

	$fichier_zip = sous_repertoire(_DIR_CACHE, 'export') . 'reponses-formulaire-' . $formulaire['identifiant'] . '.zip';
	include_spip('inc/formidable_fichiers');
	return formidable_zipper_reponses_formulaire($formulaire['id_formulaire'], $fichier_zip, $fichier_csv, $saisies_fichiers);
}


function obtenir_formulaire_reponses($id_formulaire, $statut_reponses = 'publie', $date_debut = '', $date_fin = '') {
	// on fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	if (
		$id_formulaire > 0
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire)
		and $reponses = sql_allfetsel(
			'*',
			'spip_formulaires_reponses',
			'id_formulaire = ' . intval($id_formulaire) . ($statut_reponses == 'publie' ? ' and statut = "publie"' : '')
			. (strlen($date_debut) > 0 ? ' and date >= "' . $date_debut . '"' : '')
			. (strlen($date_fin) > 0 ? ' and date <= "' . date('Y-m-d H:i:s', strtotime($date_fin. ' + 1 days')) . '"' : '')
		)) {
		if(!lire_config('formidable/exporter_adresses_ip')){
			foreach ($reponses as $key => $reponse) {
				unset($reponses[$key]['ip']);
			}
		}
		return array($formulaire, $reponses);
	}
	return array(null, null);
}


function preparer_formulaire_reponses($formulaire, $reponses, $statut_reponses, $cle_ou_valeur = 'valeur') {
	include_spip('inc/puce_statut');
	include_spip('inc/saisies');
	include_spip('facteur_fonctions');
	include_spip('inc/filtres');

	$id_formulaire = $formulaire['id_formulaire'];
	$reponses_completes = array();
	$saisies_fichiers = array();

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
			$options = $saisie['options'];
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
	$titres = pipeline(
		'formidable_exporter_formulaire_reponses_titres',
		array(
			'args' => array('id_formulaire' => $id_formulaire, 'formulaire' => $formulaire),
			'data' => $titres,
		)
	);

	$reponses_completes[] = $titres;

	// sélectionner tous les auteurs d’un coup. Évite N requetes SQL…
	$ids_auteurs = array_filter(array_map('intval', array_column($reponses, 'id_auteur')));
	$auteurs = sql_allfetsel('id_auteur, nom', 'spip_auteurs', sql_in('id_auteur', $ids_auteurs));
	$auteurs = array_column($auteurs, 'nom', 'id_auteur');

	// Sélectionner toutes valeurs des réponses d’un coup. Éviten N requetes SQL...
	$ids_reponses = array_column($reponses, 'id_formulaires_reponse');
	$_reponses_valeurs = sql_allfetsel(
		'id_formulaires_reponse, nom, valeur',
		'spip_formulaires_reponses_champs',
		array(
			sql_in('id_formulaires_reponse', $ids_reponses),
			//sql_in('nom', array_keys($saisies)) // ralentit la requête, et inutile
		),
		'',
		'id_formulaires_reponse ASC'
	);

	// grouper par identifiant de réponse
	$reponses_valeurs = array();
	foreach ($_reponses_valeurs as $r) {
		if (empty($reponses_valeurs[$r['id_formulaires_reponse']])) {
			$reponses_valeurs[$r['id_formulaires_reponse']] = array();
		}
		$reponses_valeurs[$r['id_formulaires_reponse']][$r['nom']] = $r['valeur'];
	}
	unset($_reponses_valeurs);

	// Ensuite tous les champs
	$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');

	// On parcourt chaque réponse
	foreach ($reponses as $i => $reponse) {
		// Est-ce qu'il y a un auteur avec un nom
		$nom_auteur = '';
		if ($id_auteur = intval($reponse['id_auteur'])) {
			$nom_auteur = !empty($auteurs[$id_auteur]) ? $auteurs[$id_auteur] : '';
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

		// Liste de toutes les valeurs
		$valeurs = $reponses_valeurs[$reponse['id_formulaires_reponse']];

		foreach ($saisies as $nom => $saisie) {
			if ($saisie['saisie'] != 'explication') {

				// Saisie de type fichier ?
				if ($saisie['saisie'] == 'fichiers') {
					$_valeurs = $tenter_unserialize($valeurs[$nom]);
					//tester s'il y a des saisies parmi les fichiers
					if (is_array($_valeurs) and $_valeurs) {
						$chemin = _DIR_FICHIERS_FORMIDABLE . 'formulaire_' . $id_formulaire . '/reponse_' . $reponse['id_formulaires_reponse'];
						foreach ($_valeurs as $v) {
							$chemin_fichier = $chemin . '/' . $saisie['options']['nom'] . '/' . $v['nom'];
							if (file_exists($chemin_fichier)) {
								$saisies_fichiers[] = $chemin_fichier;
							}
						}
					}
				}

				$valeur = isset($valeurs[$nom]) ? $valeurs[$nom] : '';
				$reponse_complete[] = formidable_generer_valeur_texte_saisie($valeur, $saisie, $cle_ou_valeur);
			}
		}

		// On passe la ligne de réponse dans un pipeline
		$reponse_complete = pipeline(
			'formidable_exporter_formulaire_reponses_reponse',
			array(
				'args' => array(
					'id_formulaire' => $id_formulaire,
					'formulaire' => $formulaire,
					'reponse' => $reponse,
				),
				'data' => $reponse_complete,
			)
		);

		// On ajoute la ligne à l'ensemble des réponses
		$reponses_completes[] = $reponse_complete;
	}

	return array($reponses_completes, $saisies_fichiers);
}


/**
 * Cette fonction retourne le texte d’une réponse pour un type de saisie donnée.
 *
 * On limite les calculs lorsque 2 valeurs/types de saisies sont identiques
 * de fois de suite.
 *
 * @param string $valeur
 * @param array $saisie
 * @param string $cle_ou_valeur
 * @return string
 */
function formidable_generer_valeur_texte_saisie($valeur, $saisie, $cle_ou_valeur = 'valeur') {
	static $resultats = array();
	static $tenter_unserialize = null;
	if (is_null($tenter_unserialize)) {
		$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');
	}

	$hash = ($saisie['saisie'] . ':'  . serialize($saisie['options']) . ':' . $valeur);

	if (!isset($resultats[$hash])) {
		$valeur = $tenter_unserialize($valeur);
		// Il faut éviter de passer par là… ça prend du temps…
		$resultats[$hash] = facteur_mail_html2text(
			recuperer_fond(
				'saisies-vues/_base',
				array_merge(
					array(
						'valeur_uniquement' => 'oui',
						'type_saisie'       => $saisie['saisie'],
						'valeur'            => $valeur,
						'cle_ou_valeur'			=> $cle_ou_valeur
					),
					$saisie['options']
				)
			)
		);

	}

	return $resultats[$hash];
}
