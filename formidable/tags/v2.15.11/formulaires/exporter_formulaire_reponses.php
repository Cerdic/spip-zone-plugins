<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/formidable');
include_spip('inc/config');

function formulaires_exporter_formulaire_reponses_charger($id_formulaire = 0) {
	$contexte                  = array();
	$contexte['id_formulaire'] = intval($id_formulaire);

	return $contexte;
}

function formulaires_exporter_formulaire_reponses_verifier($id_formulaire = 0) {
	$erreurs = array();

	return $erreurs;
}

function formulaires_exporter_formulaire_reponses_traiter($id_formulaire = 0) {
	$retours = array();
	$statut_reponses  = _request('statut_reponses');

	if (_request('type_export') == 'csv') {
		$ok = exporter_formulaires_reponses($id_formulaire, ',', $statut_reponses);
	} elseif (_request('type_export') == 'xls') {
		$ok = exporter_formulaires_reponses($id_formulaire, 'TAB', $statut_reponses);
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
function exporter_formulaires_reponses($id_formulaire, $delim = ',', $statut_reponses = 'publie') {
	include_spip('inc/puce_statut');
	// on ne fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	if ($id_formulaire > 0
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire)
		and $reponses = sql_allfetsel('*', 'spip_formulaires_reponses', 'id_formulaire = ' . $id_formulaire . ($statut_reponses == 'publie' ? ' and statut = "publie"' : ''))
	) {

		include_spip('inc/saisies');
		include_spip('facteur_fonctions');
		include_spip('inc/filtres');
		$reponses_completes = array();

		// La première ligne des titres
		$titres  = array(
			_T('public:date'),
			_T('formidable:reponses_auteur'),
			_T('formidable:reponses_ip')
		);
		if ($statut_reponses != 'publie') {
			$titres[] = _T('formidable:reponse_statut');
		}

		$saisies = saisies_lister_par_nom(unserialize($formulaire['saisies']), false);
		foreach ($saisies as $nom => $saisie) {
			if ($saisie['saisie'] != 'explication') {    // on exporte tous les champs sauf explications
				$options  = $saisie['options'];
				$titres[] = sinon($options['label_case'], sinon($options['label'], $nom));
			}
		}

		// On passe la ligne des titres de colonnes dans un pipeline
		$titres = pipeline(
			'formidable_exporter_formulaire_reponses_titres',
			array(
				'args' => array('id_formulaire'=>$id_formulaire, 'formulaire'=>$formulaire),
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

		// On parcourt chaque réponse
		foreach ($reponses as $i => $reponse) {
			// Est-ce qu'il y a un auteur avec un nom
			$nom_auteur = '';
			if ($id_auteur = intval($reponse['id_auteur'])) {
				$nom_auteur = !empty($auteurs[$id_auteur]) ? $auteurs[$id_auteur] : '';
			}

			// Le début de la réponse avec les infos (date, auteur, etc)
			$reponse_complete = array(
				$reponse['date'],
				$nom_auteur,
				$reponse['ip']
			);
			if ($statut_reponses != 'publie') {
				$reponse_complete[] = statut_texte_instituer('formulaires_reponse', $reponse['statut']);
			}

			// Ensuite tous les champs

			// Liste de toutes les valeurs
			$valeurs = $reponses_valeurs[$reponse['id_formulaires_reponse']];

			foreach ($saisies as $nom => $saisie) {
				if ($saisie['saisie'] != 'explication') {
					$valeur = isset($valeurs[$nom]) ? $valeurs[$nom] : '';
					$reponse_complete[] = formidable_generer_valeur_texte_saisie($valeur, $saisie);
				}
			}
			
			// On passe la ligne de réponse dans un pipeline
			$reponse_complete = pipeline(
				'formidable_exporter_formulaire_reponses_reponse',
				array(
					'args' => array('id_formulaire'=>$id_formulaire, 'formulaire'=>$formulaire, 'reponse'=>$reponse),
					'data' => $reponse_complete,
				)
			);

			// On ajoute la ligne à l'ensemble des réponses
			$reponses_completes[] = $reponse_complete;
		}

		if ($reponses_completes and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)) {
			$exporter_csv('reponses-formulaire-' . $formulaire['identifiant'], $reponses_completes, $delim);
			exit();
		}
	} else {
		return false;
	}
}

/**
 * Cette fonction retourne le texte d’une réponse pour un type de saisie donnée.
 *
 * On limite les calculs lorsque 2 valeurs/types de saisies sont identiques
 * de fois de suite.
 *
 * @param string $valeur
 * @param array $saisie
 * @return string
 */
function formidable_generer_valeur_texte_saisie($valeur, $saisie) {
	static $resultats = [];

	$hash = ($saisie['saisie'] . ':'  . serialize($saisie['options']) . ':' . $valeur);

	if (!isset($resultats[$hash])) {
		// Il faut éviter de passer par là… ça prend du temps…
		if (is_array(unserialize($valeur))) {
			$valeur = unserialize($valeur);
		}

		$resultats[$hash] = facteur_mail_html2text(
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

	return $resultats[$hash];
}
