<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Associer les <formXX> issus de f&t aux articles concernes
 */
function formidable_associer_forms() {
	include_spip('inc/rechercher');
	include_spip('inc/editer_liens');
	$forms = sql_allfetsel('*', 'spip_formulaires', 'identifiant REGEXP '.sql_quote('^form[0-9]+$'));
	foreach ($forms as $form) {
		if (!sql_countsel('spip_formulaires_liens', 'id_formulaire='.intval($form['id_formulaire']))) {
			$articles = array();
			$id = $form['identifiant'];
			#var_dump($id);
			$res = recherche_en_base("/<{$id}[>|]/", 'article');
			#var_dump($res);
			if (count($res) and isset($res['article'])) {
				foreach ($res['article'] as $id_article => $details) {
					$articles[] = $id_article;
				}
			}
			#var_dump($form['id_formulaire']);
			#var_dump($articles);
			objet_associer(array('formulaire' => array($form['id_formulaire'])), array('article' => $articles));
		}
		if (time()>_TIME_OUT) {
			return;
		}
	}
}

/**
 * Importer les formulaires de f&t
 */
function formidable_importer_forms() {
	$trouver_table = charger_fonction('trouver_table', 'base');
	if ($trouver_table('spip_forms')) {
		sql_alter('TABLE spip_forms ADD id_formulaire bigint(21) NOT NULL DEFAULT 0');

		include_spip('echanger/formulaire/forms');

		$forms = sql_allfetsel('*', 'spip_forms', 'id_formulaire=0 AND type_form='.sql_quote('').' OR type_form='.sql_quote('sondage'), '', 'id_form');
		foreach ($forms as $form) {
			$formulaire = array();
			// configurer le formulaire (titre etc)
			forms_configure_formulaire($form, $formulaire);

			// identifiant formXX puisqu'on est en installation, pas de risque de conflits
			// et facilite la migration de modele
			$formulaire['identifiant'] = 'form' . $form['id_form'];
			// on peut faire ca aussi puisqu'on est a l'installation
			$formulaire['id_formulaire'] = $form['id_form'];

			$fields = sql_allfetsel('*', 'spip_forms_champs', 'id_form='.intval($form['id_form']), '', 'rang');
			foreach ($fields as $field) {
				$choix = sql_allfetsel('*', 'spip_forms_champs_choix', 'id_form='.intval($form['id_form']).' AND champ='.sql_quote($field['champ']), '', 'rang');
				if (count($choix)) {
					$field['choix'] = $choix;
				}

				if ($saisie = forms_champ_vers_saisie($field)) {
					$formulaire['saisies'][] = $saisie;
				}
			}

			// les traitements
			forms_configure_traitement_formulaire($form, $formulaire);

			// si ce formulaire a des reponses on le met en publie
			if (sql_countsel('spip_forms_donnees', 'id_form='.intval($form['id_form']))) {
				$formulaire['statut'] = 'publie';
			}

			$id_formulaire = forms_importe_en_base($formulaire);
			spip_log('Import spip_forms #'.$form['id_form']." en spip_formulaires #$id_formulaire", 'maj'._LOG_INFO_IMPORTANTE);

			sql_update('spip_forms', array('id_formulaire' => $id_formulaire), 'id_form='.intval($form['id_form']));

			if (time()>_TIME_OUT) {
				return;
			}
		}
	}
	include_spip('inc/drapeau_edition');
	debloquer_tous($GLOBALS['visiteur_session']['id_auteur']);
}

function formidable_importer_forms_donnees() {
	$trouver_table = charger_fonction('trouver_table', 'base');
	if ($trouver_table('spip_forms')) {
		sql_alter('TABLE spip_forms_donnees ADD id_formulaires_reponse bigint(21) NOT NULL DEFAULT 0');

		// 2 champs de plus pour ne pas perdre des donnees
		sql_alter("TABLE spip_formulaires_reponses ADD url varchar(255) NOT NULL default ''");
		sql_alter("TABLE spip_formulaires_reponses ADD confirmation varchar(10) NOT NULL default ''");

		// table de correspondance id_form=>id_formulaire
		$rows = sql_allfetsel('id_form,id_formulaire', 'spip_forms', 'id_formulaire>0');
		$trans = array();
		foreach ($rows as $row) {
			$trans[$row['id_form']] = $row['id_formulaire'];
		}

		$rows = sql_allfetsel('*', 'spip_forms_donnees', sql_in('id_form', array_keys($trans)).' AND id_formulaires_reponse=0', '', 'id_donnee', '0,100');
		do {
			foreach ($rows as $row) {
				#var_dump($row);
				$reponse = array(
					'id_formulaires_reponse' => $row['id_donnee'], // conserver le meme id par facilite (on est sur une creation de base)
					'id_formulaire' => $trans[$row['id_form']],
					'date' => $row['date'],
					'ip' => $row['ip'],
					'id_auteur' => $row['id_auteur'],
					'cookie' => $row['cookie'],
					'statut' => $row['statut'],
					'url' => $row['url'],
					'confirmation' => $row['confirmation'],
				);

				#var_dump($reponse);
				$id_formulaires_reponse = sql_insertq('spip_formulaires_reponses', $reponse);
				#var_dump($id_formulaires_reponse);
				if ($id_formulaires_reponse) {
					$donnees = sql_allfetsel(
						"$id_formulaires_reponse as id_formulaires_reponse,champ as nom,valeur",
						'spip_forms_donnees_champs',
						'id_donnee='.intval($row['id_donnee'])
					);
					$data = array();
					foreach ($donnees as $donnee) {
						$data[$donnee['nom']][] = $donnee;
					}
					$ins = array();
					foreach ($data as $nom => $valeurs) {
						if (count($valeurs) == 1) {
							$ins[] = reset($valeurs);
						} else {
							$v = array();
							foreach ($valeurs as $valeur) {
								$v[] = $valeur['valeur'];
							}
							$valeurs[0]['valeur'] = serialize($v);
							$ins[] = $valeurs[0];
						}
					}
					sql_insertq_multi('spip_formulaires_reponses_champs', $ins);
					// et on marque la donnee pour ne pas la rejouer
					sql_update('spip_forms_donnees', array('id_formulaires_reponse' => $id_formulaires_reponse), 'id_donnee='.intval($row['id_donnee']));
				}
				if (time()>_TIME_OUT) {
					return;
				}
			}
			if (time()>_TIME_OUT) {
				return;
			}
		} while ($rows = sql_allfetsel('*', 'spip_forms_donnees', sql_in('id_form', array_keys($trans)).' AND id_formulaires_reponse=0', '', 'id_donnee', '0,100'));
	}
}

