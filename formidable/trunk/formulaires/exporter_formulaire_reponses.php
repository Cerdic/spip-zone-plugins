<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
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

	if (_request('type_export') == 'csv') {
		exporter_formulaires_reponses($id_formulaire);
	}
	else if (_request('type_export') == 'xls') {
		exporter_formulaires_reponses($id_formulaire, "TAB");
	}

	return $retours;
}


/*
 * Exporter toutes les réponses d'un formulaire (anciennement action/exporter_formulaire_reponses)
 * @param integer $id_formulaire
 * @return unknown_type
 */
function exporter_formulaires_reponses($id_formulaire, $delim = ",") {

	// on ne fait des choses seulements si le formulaire existe et qu'il a des enregistrements
	$ok = false;
	if (
		$id_formulaire > 0
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = ' . $id_formulaire)
		and $reponses = sql_allfetsel('*', 'spip_formulaires_reponses', 'id_formulaire = ' . $id_formulaire . ' and statut = ' . sql_quote('publie'))
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
		$saisies = saisies_lister_par_nom(unserialize($formulaire['saisies']), false);
		foreach ($saisies as $nom => $saisie) {
			if ($saisie['saisie'] != "explication") {    // on exporte tous les champs sauf explications
				$options  = $saisie['options'];
				$titres[] = sinon($options['label_case'], sinon($options['label'], $nom));
			}
		}
		$reponses_completes[] = $titres;

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
				$reponse['date'],
				$nom_auteur,
				$reponse['ip']
			);

			// Ensuite tous les champs
			foreach ($saisies as $nom => $saisie) {
				if ($saisie['saisie'] != "explication") {
					$valeur = sql_getfetsel(
						'valeur',
						'spip_formulaires_reponses_champs',
						'id_formulaires_reponse = ' . intval($reponse['id_formulaires_reponse']) . ' and nom = ' . sql_quote($nom)
					);
					if (is_array(unserialize($valeur))) {
						$valeur = unserialize($valeur);
					}
					$reponse_complete[] = facteur_mail_html2text(
						recuperer_fond(
							'saisies-vues/_base',
							array_merge(
								array(
									'valeur_uniquement' => 'oui',
									'type_saisie'       => $saisie['saisie'],
									'valeur'            => $valeur
								),
								$saisie['options']
							)
						)
					);
				}
			}

			// On ajoute la ligne à l'ensemble des réponses
			$reponses_completes[] = $reponse_complete;
		}

		if ($reponses_completes and $exporter_csv = charger_fonction('exporter_csv', 'inc/', true)) {
			$exporter_csv('reponses-formulaire-' . $formulaire['identifiant'], $reponses_completes, $delim);
			exit();
		}
	}
}

?>
