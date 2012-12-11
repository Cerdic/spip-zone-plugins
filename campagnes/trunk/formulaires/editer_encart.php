<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/*
 * Déclarer les champs du formulaire avec l'API de Saisies
 */
function formulaires_editer_encart_saisies_dist($id_encart='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => '<:encart:champ_titre_label:>',
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'identifiant',
				'label' => '<:encart:champ_identifiant_label:>',
				'explication' => '<:encart:champ_identifiant_explication:>',
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'regex',
				'options' => array(
					'modele' => '/^[\w]+$/'
				)
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'largeur',
				'label' => '<:encart:champ_largeur_label:>',
				'explication' => '<:encart:champ_largeur_explication:>',
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1
				)
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'hauteur',
				'label' => '<:encart:champ_hauteur_label:>',
				'explication' => '<:encart:champ_hauteur_explication:>',
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1
				)
			)
		),
#		array(
#			'saisie' => 'radio',
#			'options' => array(
#				'nom' => 'type',
#				'label' => '<:encart:champ_type_label:>',
#				'obligatoire' => 'oui',
#				'datas' => array(
#					'image' => '<:encart:champ_type_choix_image_label:>',
#					'texte' => '<:encart:champ_type_choix_texte_label:>',
#				),
#				'defaut' => 'image',
#			),
#		),
	);
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_encart_identifier_dist($id_encart='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_encart)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_encart_charger_dist($id_encart='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('encart',$id_encart,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_encart_verifier_dist($id_encart='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('encart',$id_encart, array('titre', 'identifiant'));
	
	// Vérifier que l'identifiant n'existe pas
	if (sql_getfetsel(
		'identifiant',
		'spip_encarts',
		array('identifiant = '.sql_quote(_request('identifiant')), 'id_encart != '.intval($id_encart))
	)){
		$erreurs['identifiant'] = _T('encart:champ_identifiant_erreur_existe_deja');
	}
	
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_encart_traiter_dist($id_encart='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('encart',$id_encart,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
