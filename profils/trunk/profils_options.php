<?php
/**
 * Les fonctions toujours chargées Profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclarer les saisies des auteurs comme si c'était de base
 * 
 */
function formulaires_editer_auteur_saisies_dist(
	$id_auteur = 'new',
	$retour = '',
	$associer_objet = '',
	$config_fonc = 'auteurs_edit_config',
	$row = array(),
	$hidden = ''
) {
	include_spip('inc/session');
	include_spip('inc/filtres');
	
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'nom',
				'label' => _T('entree_nom_pseudo_2'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'email',
				'label' => ($id_auteur == session_get('id_auteur')) ? _T('entree_adresse_email') : _T('entree_adresse_email_2'),
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'bio',
				'label' => ($id_auteur == session_get('id_auteur')) ? _T('entree_infos_perso') : _T('entree_infos_perso_2'),
				'explication' => _T('entree_biographie'),
				'rows' => 4,
				'cols' => 40,
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'pgp',
				'label' => ($id_auteur == session_get('id_auteur')) ? _T('entree_cle_pgp') : _T('entree_cle_pgp_2'),
				'rows' => 4,
				'cols' => 40,
			),
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'liens_sites',
				'label' => _T('info_site_web'),
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'nom_site',
						'label' => ($id_auteur == session_get('id_auteur')) ? _T('entree_nom_site') : _T('entree_nom_site_2'),
					),
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'url_site',
						'label' => ($id_auteur == session_get('id_auteur')) ? _T('entree_url') : _T('entree_url_2'),
					),
				),
			),
		),
	);
	
	return $saisies;
}
