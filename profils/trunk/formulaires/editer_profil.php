<?php
/**
 * Gestion du formulaire de d'édition de profil
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');
include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/profils');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_profil
 *     Identifiant du profil. 'new' pour un nouveau profil.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un profil source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du profil, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_profil_identifier_dist($id_profil = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_profil)));
}

/**
 * Saisies du formulaire d'édition de profil
 *
 * Déclarer les saisies utilisées pour générer le formulaire.
 *
 * @param int|string $id_profil
 *     Identifiant du profil. 'new' pour un nouveau profil.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un profil source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du profil, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_profil_saisies_dist($id_profil = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	// Les colonnes à cocher
	$data_cols = array(
		'inscription' => _T('profil:champ_config_colonne_inscription_label'),
		'edition' => _T('profil:champ_config_colonne_edition_label'),
		'obligatoire' => _T('profil:champ_config_colonne_obligatoire_label'),
	);
	
	// Les saisies des auteurs
	$saisies_auteur = profils_chercher_saisies_objet('auteur');
	$data_rows_auteur = saisies_lister_labels($saisies_auteur);
	
	// Récupérer les types de coordonnées
	if (defined('_DIR_PLUGIN_COORDONNEES')) {
		$coordonnees_types_numeros = coordonnees_lister_types_coordonnees('numero');
	}
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'id_profil',
				'type' => 'hidden',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('profil:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'identifiant',
				'label' => _T('profil:champ_identifiant_label'),
				'obligatoire' => 'oui',
			),
		),
	);
	$groupe_auteur = array(
		'saisie' => 'fieldset',
		'options' => array(
			'nom' => 'groupe_auteur',
			'label' => _T('profil:champ_groupe_auteur_label'),
		),
		'saisies' => array(
			array(
				'saisie' => 'choix_grille',
				'options' => array(
					'nom' => 'config[auteur]',
					'caption' => _T('profil:champ_config_auteur_caption'),
					'conteneur_class' => 'pleine_largeur',
					'multiple' => 'oui',
					'data_cols' => $data_cols,
					'data_rows' => $data_rows_auteur,
				),
			),
		),
	);
	// Coordoonnées pour l'auteur si plugin idoine
	if (defined('_DIR_PLUGIN_COORDONNEES')) {
		$groupe_auteur['saisies'][] = array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'config[activer_coordonnees_auteur]',
				'label_case' => _T('profil:champ_config_activer_coordonnees_auteur_label_case'),
				'conteneur_class' => 'pleine_largeur',
			),
		);
		$groupe_auteur['saisies'][] = array(
			'saisie' => 'profil_coordonnees',
			'options' => array(
				'nom' => 'config[coordonnees][auteur]',
				'conteneur_class' => 'pleine_largeur',
				'caption' => _T('profil:champ_config_coordonnees_auteur_caption'),
				'afficher_si' => '@config[activer_coordonnees_auteur]@ == "on"',
				'caption_explication' => _T('profil:champ_config_coordonnees_explication'),
			),
		);
	}
	// On ajoute le groupe au formulaire
	$saisies[] = $groupe_auteur;
	
	// Si le plugin C&O est là
	if (defined('_DIR_PLUGIN_CONTACTS')) {
		// L'organisation
		$groupe_organisation = array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'groupe_organisation',
				'label' => _T('profil:champ_groupe_organisation_label'),
			),
			'saisies' => array(),
		);
		// La case pour activer l’organisation
		$groupe_organisation['saisies'][] = array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'config[activer_organisation]',
				'conteneur_class' => 'pleine_largeur',
				'label_case' => _T('profil:champ_config_activer_organisation_label_case'),
			),
		);
		// Le champ libre pour donner une légende de groupe de champs
		$groupe_organisation['saisies'][] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'config[activer_groupe_organisation]',
				'label' => _T('profil:champ_config_activer_groupe_organisation_label'),
				'explication' => _T('profil:champ_config_activer_groupe_explication'),
				'afficher_si' => '@config[activer_organisation]@ == "on"',
			),
		);
		// On récupère les champs d'orga, que les noms
		$saisies_organisation = profils_chercher_saisies_objet('organisation');
		$data_rows_organisation = saisies_lister_labels($saisies_organisation);
		$groupe_organisation['saisies'][] = array(
			'saisie' => 'choix_grille',
			'options' => array(
				'nom' => 'config[organisation]',
				'caption' => _T('profil:champ_config_organisation_caption'),
				'conteneur_class' => 'pleine_largeur',
				'multiple' => 'oui',
				'data_cols' => $data_cols,
				'data_rows' => $data_rows_organisation,
				'afficher_si' => '@config[activer_organisation]@ == "on"',
			),
		);
		// Coordoonnées pour l'organisation si plugin idoine
		if (defined('_DIR_PLUGIN_COORDONNEES')) {
			$groupe_organisation['saisies'][] = array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'config[activer_coordonnees_organisation]',
					'label_case' => _T('profil:champ_config_activer_coordonnees_organisation_label_case'),
					'conteneur_class' => 'pleine_largeur',
					'afficher_si' => '@config[activer_organisation]@ == "on"',
				),
			);
			$groupe_organisation['saisies'][] = array(
				'saisie' => 'profil_coordonnees',
				'options' => array(
					'nom' => 'config[coordonnees][organisation]',
					'conteneur_class' => 'pleine_largeur',
					'caption' => _T('profil:champ_config_coordonnees_organisation_caption'),
					'afficher_si' => '@config[activer_organisation]@ == "on" && @config[activer_coordonnees_organisation]@ == "on"',
					'caption_explication' => _T('profil:champ_config_coordonnees_explication'),
				),
			);
		}
		// On ajoute le groupe au formulaire
		$saisies[] = $groupe_organisation;
		
		// Le contact
		$groupe_contact = array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'groupe_contact',
				'label' => _T('profil:champ_groupe_contact_label'),
			),
			'saisies' => array(),
		);
		// La case pour activer le contact
		$groupe_contact['saisies'][] = array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'config[activer_contact]',
				'conteneur_class' => 'pleine_largeur',
				'label_case' => _T('profil:champ_config_activer_contact_label_case'),
			),
		);
		$groupe_contact['saisies'][] = array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'config[activer_contact_explication]',
				'texte' => _T('profil:champ_config_activer_contact_explication_texte'),
				'afficher_si' => '@config[activer_organisation]@ == "on"',
			),
		);
		// Le champ libre pour donner une légende de groupe de champs
		$groupe_contact['saisies'][] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'config[activer_groupe_contact]',
				'label' => _T('profil:champ_config_activer_groupe_contact_label'),
				'explication' => _T('profil:champ_config_activer_groupe_explication'),
				'afficher_si' => '@config[activer_contact]@ == "on"',
			),
		);
		// On récupère les champs de contact, que les noms
		$saisies_contact = profils_chercher_saisies_objet('contact');
		$data_rows_contact = saisies_lister_labels($saisies_contact);
		$groupe_contact['saisies'][] = array(
			'saisie' => 'choix_grille',
			'options' => array(
				'nom' => 'config[contact]',
				'caption' => _T('profil:champ_config_contact_caption'),
				'conteneur_class' => 'pleine_largeur',
				'multiple' => 'oui',
				'data_cols' => $data_cols,
				'data_rows' => $data_rows_contact,
				'afficher_si' => '@config[activer_contact]@ == "on"',
			),
		);
		// Coordoonnées pour le contact si plugin idoine
		if (defined('_DIR_PLUGIN_COORDONNEES')) {
			$groupe_contact['saisies'][] = array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'config[activer_coordonnees_contact]',
					'label_case' => _T('profil:champ_config_activer_coordonnees_contact_label_case'),
					'conteneur_class' => 'pleine_largeur',
					'afficher_si' => '@config[activer_contact]@ == "on"',
				),
			);
			$groupe_contact['saisies'][] = array(
				'saisie' => 'profil_coordonnees',
				'options' => array(
					'nom' => 'config[coordonnees][contact]',
					'conteneur_class' => 'pleine_largeur',
					'caption' => _T('profil:champ_config_coordonnees_contact_caption'),
					'afficher_si' => '@config[activer_contact]@ == "on" && @config[activer_coordonnees_contact]@ == "on"',
					'caption_explication' => _T('profil:champ_config_coordonnees_explication'),
				),
			);
		}
		// On ajoute le groupe au formulaire
		$saisies[] = $groupe_contact;
		
		// S'il y a l'option pour utiliser plusieurs annuaires
		if (lire_config('contacts_et_organisations/utiliser_annuaires')) {
			$saisies[] = array(
				'saisie' => 'fieldset',
				'options' => array(
					'label' => _T('contacts:annuaire'),
					'nom' => 'groupe_annuaire',
					'afficher_si' => '@config[activer_organisation]@ == "on" || @config[activer_contact]@ == "on"',
				),
				'saisies' => array(
					array(
						'saisie' => 'annuaires',
						'options' => array(
							'nom' => 'config[id_annuaire]',
							'label' => _T('profil:champ_config_id_annuaire_label'),
							'explication' => _T('profil:champ_config_id_annuaire_explication'),
						),
					),
				),
			);
		}
	}
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de profil
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_profil
 *     Identifiant du profil. 'new' pour un nouveau profil.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un profil source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du profil, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_profil_charger_dist($id_profil = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('profil', $id_profil, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	$valeurs['config'] = unserialize($valeurs['config']);
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de profil
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_profil
 *     Identifiant du profil. 'new' pour un nouveau profil.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un profil source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du profil, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_profil_verifier_dist($id_profil = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();
	$config = _request('config');
	
	// On teste l'identifiant, ne doit pas exister
	if (
		$identifiant = _request('identifiant')
		and sql_getfetsel('id_profil', 'spip_profils', array('identifiant = '.sql_quote($identifiant), 'id_profil != '.intval($id_profil)))
	) {
		$erreurs['identifiant'] = _T('profil:erreur_identifiant_existant');
	}
	
	// On teste si on a bien un email obligatoire
	if (!profils_chercher_champ_email_principal($config)) {
		$erreurs['message_erreur'] = _T('profil:erreur_email_obligatoire');
	}
	
	// On normalise certaines choses dans les coordonnées
	$config = formulaires_editer_profil_traiter_coordonnees($config);
	set_request('config', $config);
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de profil
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_profil
 *     Identifiant du profil. 'new' pour un nouveau profil.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un profil source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du profil, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_profil_traiter_dist($id_profil = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	set_request('config', serialize(_request('config')));
	
	$retours = formulaires_editer_objet_traiter('profil', $id_profil, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	return $retours;
}

/**
 * Nettoie les coordonnées non utilisées dans le tableau de config du profil
 * 
 * @param array $config 
 * 		Tableau de configuration d'un profil
 * @return array
 * 		Retourne le tableau de config nettoyé
 */
function formulaires_editer_profil_traiter_coordonnees($config) {
	foreach (array('auteur', 'organisation', 'contact') as $objet) {
		if (!isset($config["activer_coordonnees_$objet"]) or !$config["activer_coordonnees_$objet"]) {
			unset($config['coordonnees'][$objet]);
		}
		else {
			foreach($config['coordonnees'][$objet] as $coordonnee => $champs) {
				// Pour chacun des champs
				foreach ($champs as $cle => $champ) {
					// S'il n'y a ni inscription, ni édition, on supprime le champ de la liste
					if (!isset($champ['inscription']) and !isset($champ['edition'])) {
						unset($config['coordonnees'][$objet][$coordonnee][$cle]);
					}
				}
				// On refait l'ordre
				$config['coordonnees'][$objet][$coordonnee] = array_values($config['coordonnees'][$objet][$coordonnee]);
			}
		}
	}
	
	return $config;
}
