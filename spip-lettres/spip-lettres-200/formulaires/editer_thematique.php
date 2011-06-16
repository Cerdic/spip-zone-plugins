<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;
 
function formulaires_editer_thematique_saisies($id_theme=0, $titre='', $id_rubrique=0, $expediteur_type='default', $expediteur_id=0, $retours_type='default', $retours_id=0){
	
	$types = array('default', 'webmaster', 'author', 'custom');

	if (!in_array($expediteur_type, $types))
	{
		if ($GLOBALS['meta']['spip_lettres_signe_par_auteurs'] == 'oui')
			$expediteur_type = 'author';
		else	
			$expediteur_type = 'default';
	}

	if (!in_array($retours_type, $types))
		$retours_type = 'default';
	
	$mes_saisies = array(
		array( // champ id_theme : champ caché
			'saisie' => 'hidden',
			'options' => array(
				'nom' => 'id_theme',
				'defaut' => $id_theme
			)
		),
		array( // hors fieldset : champ titre  : ligne de texte
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'obligatoire' => 'oui',
				'defaut' => $titre,
				'label' => ucfirst(_T('lettresprive:titre'))
			)
		), // fin champ titre
		array( // fielset
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'toutes_thematiques',
				'label' => _T('lettresprive:toutes_lettres_thematique')
			),
			'saisies' => array( // les champs dans le fieldset
				array( // champ expediteur_type : radio
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'expediteur_type',
						'label' => ucfirst(_T('lettresprive:email_expediteur')),
						'defaut' => $expediteur_type,
						'datas' => array( // types possibles
							'default' => _T('lettresprive:expediteur_defaut'),
							'webmaster' => _T('lettresprive:webmestre_site'),
							'author' => _T('lettresprive:auteur_lettre'),
							'custom' => _T('lettresprive:choisir_parmi_auteurs')
						)
					)
				), // fin champ expediteur_type
				array( // champ expediteur_id : auteurs
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_id',
						'defaut' => $expediteur_id,
						'statut' => '0minirezo',
						'option_intro' => _T('lettresprive:selectionner_auteur'),
						'afficher_si' => '@expediteur_type@ == "custom"'
					)
				),
				array( // champ retours_type : radio
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'retours_type',
						'label' => ucfirst(_T('lettresprive:email_return_path')),
						'defaut' => $retours_type,
						'datas' => array( // types possibles
							'default' => _T('lettresprive:expediteur_defaut'),
							'webmaster' => _T('lettresprive:webmestre_site'),
							'author' => _T('lettresprive:auteur_lettre'),
							'custom' => _T('lettresprive:choisir_parmi_auteurs')
						)
					)
				), // fin champ retours_type
				array( // champ retours_id : auteurs
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'retours_id',
						'defaut' => $retours_id,
						'statut' => '0minirezo',
						'option_intro' => _T('lettresprive:selectionner_auteur'),
						'afficher_si' => '@retours_type@ == "custom"'
					)
				),
				array( // champ id_rubrique : selecteur_rubrique
					'saisie' => 'selecteur_rubrique',
					'options' => array(
						'nom' => 'id_rubrique',
						'obligatoire' => 'oui',
						'defaut' => 'rubrique|'.$id_rubrique,
						'explication' => _T('lettresprive:choix_rubrique'),
						'afficher_rub_dans_langue_interface' => 'oui'
					)
				) // fin champ id_rubrique
			) // fin 'saisies'
		) // fin 'fieldset'
	); // fin $mes_saisies

	return $mes_saisies;
}

function formulaires_editer_thematique_traiter_dist(){
	$res = array();
	$aTypes = array('default','webmaster','author','custom');
	
	$id_theme = _request('id_theme');
	$aRubrique = _request('id_rubrique');
	$id_rubrique = str_replace('rubrique|', '', $aRubrique[0]);
	$titre = _request('titre');
	$expediteur_type = in_array( _request('expediteur_type'), $aTypes) ? _request('expediteur_type') : 'default';
	$expediteur_id = 'custom'== $expediteur_type ? _request('expediteur_id') : 0;
	$retours_type = in_array( _request('retours_type'), $aTypes) ? _request('retours_type') : 'default';
	$retours_id = 'custom'== $retours_type ? _request('retours_id') : 0;
	
	$modifications = array(
		'id_rubrique'				=> intval($id_rubrique),
		'titre'						=> sql_quote($titre),
		'expediteur_type'			=> sql_quote($expediteur_type),
		'expediteur_id'				=> intval($expediteur_id),
		'retours_type'				=> sql_quote($retours_type),
		'retours_id'				=> intval($retours_id)
	);

	$succes = sql_update('spip_themes', $modifications, 'id_theme=' . intval($id_theme));

	if ($succes)
	{
		$res['message_ok'] = _T('lettresprive:thematique_modifiee');
	}
	else
		$res['message_erreur'] = _T('lettresprive:thematique_erreur_modif');

	return $res;
}

 ?>
