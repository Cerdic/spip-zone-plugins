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
			'saisie' => 'locomotions_durees',
			'options' => array(
				'nom' => 'locomotions_durees',
				'label' => _T('itineraire:champ_locomotions_durees'),
				'defaut' => array('actives' => array('pied')),
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
				'nom' => 'longueur',
				'label' => _T('itineraire:champ_longueur_label'),
				'explication' => _T('itineraire:champ_longueur_explication'),
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
	$id_itineraire = intval($id_itineraire);
	
	// Enlever les 0 superflus
	$valeurs['longueur'] = floatval($valeurs['longueur']);
	
	// Pour les trucs numériques, laisser vide si c'est 0
	foreach (array('longueur', 'denivele', 'difficulte') as $champ_num){
		if ($valeurs[$champ_num] == 0){
			$valeurs[$champ_num] = '';
		}
	}
	
	// On ajoute locomotions_durees
	$valeurs['locomotions_durees'] = array();
	// Si c'est une modif on cherche l'existant
	if ($id_itineraire > 0
		and $locomotions = sql_allfetsel('*', 'spip_itineraires_locomotions', 'id_itineraire = '.$id_itineraire)
		and is_array($locomotions)
	){
		$valeurs['locomotions_durees'] = array('actives'=>array(), 'durees'=>array());
		foreach ($locomotions as $locomotion){
			$valeurs['locomotions_durees']['actives'][] = $locomotion['type_locomotion'];
			// Seulement s'il y a une durée
			if ($duree = $locomotion['duree']){
				$h = floor($duree/3600);
				$m = floor(($duree-$h*3600)/60);
				if ($h) { $valeurs['locomotions_durees']['durees'][$locomotion['type_locomotion']]['heures'] = $h; }
				if ($m) { $valeurs['locomotions_durees']['durees'][$locomotion['type_locomotion']]['minutes'] = $m; }
			}
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
	$erreurs = formulaires_editer_objet_verifier('itineraire',$id_itineraire, array('titre'));
	
	if ($locomotions_durees = _request('locomotions_durees')){
		// S'il y a au moins une cochée
		if (!empty($locomotions_durees['actives'])) {
			$verifier = charger_fonction('verifier', 'inc/');
			foreach($locomotions_durees['actives'] as $type_locomotion){
				if (!in_array($type_locomotion, array_keys($GLOBALS['itineraires_locomotions']))) {
					$erreurs['locomotions_durees'] = _T('itineraire:erreur_type_locomotion_inconnu');
				}
				else{
					// Si heures ou minutes sont en erreur
					if (
						$erreur = $verifier($locomotions_durees['durees'][$type_locomotion]['heures'], 'entier', array('min'=>0))
						or $erreur = $verifier($locomotions_durees['durees'][$type_locomotion]['minutes'], 'entier', array('min'=>0, 'max'=>59))
					){
						$erreurs['locomotions_durees'] = $erreur;
					}
				}
			}
		}
	}
	
	return $erreurs;
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
	$id_itineraire = intval($retours['id_itineraire']);
	
	// On traite les locomotions et durées
	// On supprime tout pour cet itinéraire
	sql_delete('spip_itineraires_locomotions', 'id_itineraire = '.$id_itineraire);
	// On ajoute la nouvelle config
	$locomotions_durees = _request('locomotions_durees');
	foreach ($locomotions_durees['actives'] as $type_locomotion){
		sql_insertq(
			'spip_itineraires_locomotions',
			array(
				'id_itineraire' => $id_itineraire,
				'type_locomotion' => $type_locomotion,
				'duree' => intval($locomotions_durees['durees'][$type_locomotion]['heures'])*3600 + intval($locomotions_durees['durees'][$type_locomotion]['minutes']) * 60,
			)
		);
	}
	
	return $retours;
}


?>
