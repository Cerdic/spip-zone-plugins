<?php
/**
 * Gestion du formulaire de d'édition d'une commande
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Formulaires
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Description des saisies du formulaire d'édition d'une commande
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Description des saisies
 */
function formulaires_editer_commande_saisies($id_commande='new', $retour=''){
	include_spip('inc/config');
	// Il est possible de prédéfinir un auteur
	if (!$id_auteur_defaut = _request('id_auteur')) {
		$id_auteur_defaut = 0;
	}
	// Prégénérer une référence possible
	if ($fonction_reference = charger_fonction('commandes_reference', 'inc/')) {
		$reference_defaut = $fonction_reference($id_auteur_defaut);
	}
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reference',
				'label' => _T('commandes:reference_label'),
				'obligatoire' => 'oui',
				'defaut' => $reference_defaut,
			)
		),
		array(
			'saisie' => 'auteurs',
			'options' => array(
				'nom' => 'id_auteur',
				'label' => _T('commandes:contact_label'),
				'class' => 'chosen',
				'defaut' => $id_auteur_defaut
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date',
				'label' => _T('commandes:date_commande_label'),
				'horaire' => 'oui',
				'obligatoire' => 'oui',
				'defaut' => date('Y-m-d H:i:s'),
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_paiement',
				'label' => _T('commandes:date_paiement_label'),
				'horaire' => 'oui'
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date_envoi',
				'label' => _T('commandes:date_envoi_label'),
				'horaire' => 'oui'
			)
		),
	);
	
	if (
		$id_commande = intval($id_commande)
		and $id_commande > 0
		and $echeances_type = sql_getfetsel('echeances_type', 'spip_commandes', 'id_commande = '.$id_commande)
	) {
		$saisies[] = array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'bank_uid',
				'label' => _T('commandes:label_bank_uid'),
				'explication' => _T('commandes:explication_bank_uid'),
			),
		);
	}
	
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un commande source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du commande, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_commande_identifier_dist($id_commande='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_commande)));
}

/**
 * Chargement du formulaire d'édition d'une commande
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un commande source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du commande, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_commande_charger($id_commande='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('commande', $id_commande, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	unset($valeurs['id_commande']); // ?
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition d'une commande
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un commande source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du commande, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_commande_verifier($id_commande='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// normaliser les champs de dates
	// chaque saisie $date est un tableau avec une entrée "date" (jour/mois/annee) et une entrée "heure" séparée (heures:minutes)
	$type_dates = array('date','date_paiement','date_envoi');
	foreach ($type_dates as $type_date){
		$date = _request($type_date);
		if (isset($date['date']) and $date['date']){
			list($jour, $mois, $annee) = explode('/',$date['date']);
			list($heures, $minutes) = explode(':',$date['heure']);
			$date = ($date['date'] ? "$annee-$mois-$jour" : '0000-00-00') ." ". ($date['heure'] ? "$heures:$minutes:00" : '00:00:00');
			$date = normaliser_date($date);
		} else {
			$date = '0000-00-00 00:00:00';
		}
		set_request($type_date, $date);
	}
	
	$erreurs = formulaires_editer_objet_verifier('commande', $id_commande, array('reference'));
	
	// On vérifie qu'il n'y a pas déjà une commande avec la même référence
	// seulement si c'est pour une nouvelle commande
	if (
		!intval($id_commande)
		and $reference = _request('reference')
		and sql_getfetsel('id_commande', 'spip_commandes', 'reference = '.sql_quote($reference))
	) {
		$erreurs['reference'] = _T('commandes:erreur_reference_existante');
	}
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'une commande
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un commande source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du commande, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_commande_traiter($id_commande='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('commande', $id_commande, '', '', $retour, $config_fonc, $row, $hidden);
}


?>
