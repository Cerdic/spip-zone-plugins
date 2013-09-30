<?php
/**
 * Gestion du formulaire de d'édition de itineraire
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_itineraire
 *     Identifiant du itineraire. 'new' pour un nouveau itineraire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraire source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraire, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_itineraire_identifier_dist($id_itineraire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_itineraire)));
}

/**
 * Saisies du formulaire d'édition de itineraire
 *
 * Déclarer les saisies de formulaire à utiliser et les vérifications
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_itineraire
 *     Identifiant du itineraire. 'new' pour un nouveau itineraire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraire source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraire, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_itineraire_saisies_dist($id_itineraire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	include_spip('inc/config');
	$difficulte_max = lire_config('itineraires/difficulte_max', 5);
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('itineraire:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('itineraire:champ_texte_label'),
				'rows' => 10,
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'depart',
				'label' => _T('itineraire:champ_depart_label'),
				'explication' => _T('itineraire:champ_depart_explication'),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'balisage',
				'label' => _T('itineraire:champ_balisage_label'),
				'explication' => _T('itineraire:champ_balisage_explication'),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'distance',
				'label' => _T('itineraire:champ_distance_label'),
				'explication' => _T('itineraire:champ_distance_explication'),
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0,
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'denivele',
				'label' => _T('itineraire:champ_denivele_label'),
				'explication' => _T('itineraire:champ_denivele_explication'),
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 0,
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'difficulte',
				'label' => _T('itineraire:champ_difficulte_label'),
				'explication' => _T('itineraire:champ_difficulte_explication', array('min'=>0, 'max'=>$difficulte_max)),
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 0,
					'max' => $difficulte_max,
				),
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'boucle',
				'label' => _T('itineraire:champ_boucle_label'),
				'label_case' => _T('itineraire:champ_boucle_label_case'),
				'valeur_oui' => 1,
				'valeur_non' => 0,
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'transport',
				'label' => _T('itineraire:champ_transport_label'),
				'label_case' => _T('itineraire:champ_transport_label_case'),
				'valeur_oui' => 1,
				'valeur_non' => 0,
			),
		),
		array(
			'saisie' => 'case',
			'options' => array(
				'nom' => 'handicap',
				'label' => _T('itineraire:champ_handicap_label'),
				'label_case' => _T('itineraire:champ_handicap_label_case'),
				'valeur_oui' => 1,
				'valeur_non' => 0,
			),
		),
	);
	
	return $saisies;
}

/**
 * Chargement du formulaire d'édition de itineraire
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_itineraire
 *     Identifiant du itineraire. 'new' pour un nouveau itineraire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraire source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraire, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_itineraire_charger_dist($id_itineraire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('itineraire',$id_itineraire,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	// Pour les trucs numériques, laisser vide si c'est 0
	foreach (array('distance', 'denivele', 'difficulte') as $champ_num){
		if ($valeurs[$champ_num] == 0){
			$valeurs[$champ_num] = '';
		}
	}
	
	// On ajoute l'identifiant dans l'envoi
	$valeurs['_hidden'] .= '<input type="hidden" name="id_itineraire" value="'.$id_itineraire.'" />';
	
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de itineraire
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_itineraire
 *     Identifiant du itineraire. 'new' pour un nouveau itineraire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraire source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraire, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_itineraire_verifier_dist($id_itineraire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('itineraire',$id_itineraire, array('titre'));
}

/**
 * Traitement du formulaire d'édition de itineraire
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_itineraire
 *     Identifiant du itineraire. 'new' pour un nouveau itineraire.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un itineraire source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du itineraire, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_itineraire_traiter_dist($id_itineraire='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	foreach (array('boucle', 'transport', 'handicap') as $case){
		if (!_request($case)){ set_request($case, 0); }
	}
	$retours = formulaires_editer_objet_traiter('itineraire',$id_itineraire,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $retours;
}


?>
