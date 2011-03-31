<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_produit_saisies($id_produit='new', $id_rubrique=0, $retour=''){
	include_spip('inc/config');
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_titre_label'),
				'defaut' => _T('info_sans_titre'),
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'reference',
				'label' => _T('produits:produit_champ_reference_label'),
			)
		),
		array(
			'saisie' => 'selecteur_rubrique',
			'options' => array(
				'nom' => 'id_parent',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_rubrique_label'),
				'defaut' => 'rubrique|'.$id_rubrique
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prix_ht',
				'obligatoire' => 'oui',
				'label' => _T('produits:produit_champ_prix_ht_label'),
				'defaut' => 0,
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0
				)
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('produits:produit_champ_taxe_label'),
				'explication' => _T('produits:produit_champ_taxe_explication', array('taxe'=>lire_config('produits/taxe', 0))),
				'defaut' => '' // = null
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0,
					'max' => 1
				)
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'rows' => '3',
				'label' => _T('produits:produit_champ_descriptif_label'),
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'texte',
				'label' => _T('produits:produit_champ_texte_label'),
			)
		),
	);
}

function formulaires_editer_produit_charger($id_produit='new', $id_rubrique=0, $retour=''){
	include_spip('inc/editer');
	$contexte = formulaires_editer_objet_charger('produit', $id_produit, $id_rubrique, 0, $retour, '');
	$contexte['id_parent'] = 'rubrique|'.($contexte['id_rubrique']?$contexte['id_rubrique']:$id_rubrique);
	unset($contexte['id_produit']);
	unset($contexte['id_rubrique']);
	return $contexte;
}

function formulaires_editer_produit_verifier($id_produit='new', $id_rubrique=0, $retour=''){
	include_spip('inc/editer');
	return formulaires_editer_objet_verifier('produit', $id_produit);
}

function formulaires_editer_produit_traiter($id_produit='new', $id_rubrique=0, $retour=''){
	include_spip('inc/editer');
	
	// On reformule l'id_parent
	$id_parent = _request('id_parent');
	$id_parent = str_replace('rubrique|', '', $id_parent);
	set_request('id_parent', $id_parent);
		
	$retours = formulaires_editer_objet_traiter('produit',$id_produit,$id_rubrique,0,$retour);
	return $retours;
}

?>
