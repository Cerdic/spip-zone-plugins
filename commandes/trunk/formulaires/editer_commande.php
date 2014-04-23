<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_commande_saisies($id_commande='new', $retour=''){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reference',
				'label' => _T('commandes:reference_label'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'auteurs',
			'options' => array(
				'nom' => 'id_auteur',
				'label' => _T('commandes:contact_label'),
				'class' => 'chosen'
			)
		),
		array(
			'saisie' => 'date',
			'options' => array(
				'nom' => 'date',
				'label' => _T('commandes:date_commande_label'),
				'horaire' => 'oui'
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
	$valeurs = formulaires_editer_objet_charger('commande',$id_commande,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
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
	//normaliser les champs de dates
	// chaque saisie $date est un tableau avec une entrée "date" (jour/mois/annee) et une entrée "heure" séparée (heures:minutes)
	$type_dates = array('date','date_envoi','date_paiement');
	foreach ($type_dates as $type_date){
		$date = _request($type_date);
		if ($date and is_array($date)){
			list($jour, $mois, $annee) = explode('/',$date['date']);
			list($heures, $minutes) = explode(':',$date['heure']);
			$date = ($date['date'] ? "$annee-$mois-$jour" : '0000-00-00') ." ". ($date['heure'] ? "$heures:$minutes:00" : '00:00:00');
			$date = normaliser_date($date);
		} else {
			$date = '0000-00-00 00:00:00';
		}
		set_request($type_date,$date);
	}
	return formulaires_editer_objet_verifier('commande', $id_commande, array('reference'));
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
	return formulaires_editer_objet_traiter('commande',$id_commande,'','',$retour,$config_fonc,$row,$hidden);
}


?>
