<?php
/**
 * Gestion du formulaire de d'édition de droits_contrat
 *
 * @plugin     Ayants droit
 * @copyright  2016
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Ayantsdroit\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_droits_contrat_saisies_dist($objet, $id_objet, $id_droits_contrat='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$saisies = array(
		array(
			'saisie' => 'droits_ayants',
			'options' => array(
				'nom' => 'id_droits_ayant',
				'label' => _T('droits_contrat:champ_id_droits_ayant_label'),
				'class' => 'chosen',
				'option_intro' => _T('droits_contrat:champ_id_droits_ayant_inconnu'),
			),
		),
		array(
			'saisie' => 'licences',
			'options' => array(
				'nom' => 'id_licence',
				'label' => _T('droits_contrat:champ_id_licence_label'),
				'class' => 'chosen',
				'defaut' => 1,
				'cacher_option_intro' => 'oui',
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_debut',
				'label' => _T('droits_contrat:champ_date_debut_label'),
			),
			'verifier' => array(
				'type' => 'date',
				'options' => array(
					'normaliser' => 'datetime',
				),
			),
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_fin',
				'label' => _T('droits_contrat:champ_date_fin_label'),
			),
			'verifier' => array(
				'type' => 'date',
				'options' => array(
					'normaliser' => 'datetime',
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'montant',
				'label' => _T('droits_contrat:champ_montant_label'),
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'credits',
				'label' => _T('droits_contrat:champ_credits_label'),
				'explication' => _T('droits_contrat:champ_credits_explication'),
				'rows' => 4,
			),
		),
	);
	
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_droits_contrat
 *     Identifiant du droits_contrat. 'new' pour un nouveau droits_contrat.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un droits_contrat source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du droits_contrat, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_droits_contrat_identifier_dist($objet, $id_objet, $id_droits_contrat='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_droits_contrat)));
}

/**
 * Chargement du formulaire d'édition de droits_contrat
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_droits_contrat
 *     Identifiant du droits_contrat. 'new' pour un nouveau droits_contrat.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un droits_contrat source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du droits_contrat, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_droits_contrat_charger_dist($objet, $id_objet, $id_droits_contrat='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('droits_contrat',$id_droits_contrat,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	unset($valeurs['id_droits_contrat']);
	unset($valeurs['objet']);
	unset($valeurs['id_objet']);
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de droits_contrat
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_droits_contrat
 *     Identifiant du droits_contrat. 'new' pour un nouveau droits_contrat.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un droits_contrat source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du droits_contrat, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_droits_contrat_verifier_dist($objet, $id_objet, $id_droits_contrat='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('droits_contrat', $id_droits_contrat);
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de droits_contrat
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_droits_contrat
 *     Identifiant du droits_contrat. 'new' pour un nouveau droits_contrat.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un droits_contrat source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du droits_contrat, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_droits_contrat_traiter_dist($objet, $id_objet, $id_droits_contrat='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// Pour une création : l'attachement du contrat est demandé dès l'appel du formulaire
	if (!intval($id_droits_contrat)) {
		set_request('objet', $objet);
		set_request('id_objet', $id_objet);
	}
	
	// Pas de dates nulles
	if (!_request('date_debut')) {
		set_request('date_debut', '0000-00-00 00:00:00');
	}
	if (!_request('date_fin')) {
		set_request('date_fin', '0000-00-00 00:00:00');
	}
	
	$retours = formulaires_editer_objet_traiter('droits_contrat', $id_droits_contrat, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	
	return $retours;
}
