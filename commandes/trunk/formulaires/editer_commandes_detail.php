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
 * Description des saisies du formulaire d'édition d'un détail de commande
 *
 * @param int|string $id_commande
 *     Identifiant du commande. 'new' pour une nouvelle commande.
 * @param string $retour
 *     URL de redirection après le traitement
 * @return array
 *     Description des saisies
 */
function formulaires_editer_commandes_detail_saisies($id_commandes_detail='new', $retour=''){
	// Il est possible de prédéfinir la commande en donnant la référence
	$reference_defaut = _request('reference');
	
	return array(
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
			'saisie' => 'input',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('commandes:detail_champ_descriptif_label'),
				'explication' => _T('commandes:detail_champ_descriptif_explication'),
				'obligatoire' => 'oui',
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'objet',
				'label' => _T('commandes:detail_champ_objet_label'),
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'id_objet',
				'label' => _T('commandes:detail_champ_id_objet_label'),
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1,
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prix_unitaire_ht',
				'label' => _T('commandes:detail_champ_prix_unitaire_ht_label'),
				'obligatoire' => 'oui',
				'defaut' => 0,
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
				'nom' => 'quantite',
				'label' => _T('commandes:detail_champ_quantite_label'),
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1,
				),
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'taxe',
				'label' => _T('commandes:detail_champ_taxe_label'),
				'placeholder' => 0.2,
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0,
				),
			),
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
function formulaires_editer_commande_identifier_dist($id_commandes_detail='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_commandes_detail)));
}

/**
 * Chargement du formulaire d'édition d'un détail de commande
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
function formulaires_editer_commandes_detail_charger($id_commandes_detail='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('commandes_detail', $id_commandes_detail, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	unset($valeurs['id_commandes_detail']); // ?
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition d'un détail de commande
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
function formulaires_editer_commandes_detail_verifier($id_commandes_detail='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();
	
	// La référence doit être celle d'une vraie commande existante !
	if (
		$reference = _request('reference')
		and !$id_commande = sql_getfetsel('id_commande', 'spip_commandes', 'reference = '.sql_quote($reference))
	) {
		$erreurs['reference'] = _T('commandes:erreur_reference_inexistante');
	}
	// Si c'est le cas, on remplit le champ id_commande
	else {
		set_request('id_commande', $id_commande);
	}
	
	// Si le descriptif est vide ET qu'il y a un objet valide, on remplit le descriptif
	if (
		!_request('descriptif')
		and $objet = _request('objet')
		and $id_objet = _request('id_objet')
		and $descriptif = generer_info_entite($id_objet, $objet, 'titre')
	) {
		set_request('descriptif', $descriptif);
	}
	
	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'un détail de commande
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
function formulaires_editer_commandes_detail_traiter($id_commandes_detail='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('commandes_detail', $id_commandes_detail, '', '', $retour, $config_fonc, $row, $hidden);
}


?>
