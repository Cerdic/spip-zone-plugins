<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_gestionml_saisies_dist(){
	$config = lire_config('gestionml');

	return array(
		array(
			'saisie' => 'radio',
			'options' => array(
				'nom' => 'hebergeur',
				'label' => _T('gestionml:label_hebergeur'),
				'defaut' => $config['hebergeur'],
            'datas' => array(
               '0' => _T('gestionml:label_hebergeur_simule'),
               '1' => 'OVH'
            )
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'config_ovh',
				'label' => _T('gestionml:config_ovh_legend'),
				'afficher_si' => _T('@hebergeur@ == 1')
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'serveur_distant',
						'label' => _T('gestionml:label_serveur_distant'),
						'explication' => _T('gestionml:explication_serveur_distant'),
						'obligatoire' => 'oui',
						'defaut' => $config['serveur_distant']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'domaine',
						'label' => _T('gestionml:label_domaine'),
						'explication' => _T('gestionml:explication_domaine'),
						'obligatoire' => 'oui',
						'defaut' => $config['domaine']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'identifiant',
						'label' => _T('gestionml:label_identifiant'),
						'explication' => _T('gestionml:explication_identifiant'),
						'obligatoire' => 'oui',
						'defaut' => $config['identifiant']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'mot_de_passe',
						'type' => 'password',
						'label' => _T('gestionml:label_mot_de_passe'),
						'explication' => _T('gestionml:explication_mot_de_passe'),
						'obligatoire' => 'oui',
						'defaut' => $config['mot_de_passe']
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'auteurs_listes',
				'label' => _T('gestionml:label_auteurs_listes'),
			),
			'saisies' => gestionml_auteurs_listes($config)
		)
	);
	
}

function gestionml_auteurs_listes($config) {
	$saisies = array() ;
	
	include_spip('inc/gestionml_api');
	$resultat = gestionml_api_listes_toutes(true) ;
	$nom_listes = array_keys($resultat['listes']) ;
	$listes = array_combine($nom_listes,$nom_listes) ;
	
	$auteurs = sql_allfetsel("id_auteur, nom", "spip_auteurs", "statut='0minirezo'", "", "nom");
	foreach($auteurs as $ligne)
	{
		spip_log("gestionml_auteurs_listes id_auteur ".$ligne['id_auteur']." config ".$config['listes_auteur_'.$ligne['id_auteur']],"gestionml");
		$saisies[] = array(
			'saisie' => 'selection_multiple',
			'options' => array(
				'nom' => 'listes_auteur_'.$ligne['id_auteur'],
				'label' => _T('gestionml:label_liste_de',array('nom' => $ligne['nom'])),
				'explication' => _T('gestionml:explication_liste_de',array('nom' => $ligne['nom'])),
				'cacher_option_intro' => 'oui',
				'defaut' => $config['listes_auteur_'.$ligne['id_auteur']],
				'datas' => $listes
			)
		) ;
	}
	return $saisies;
}

function formulaires_configurer_gestionml_verifier_dist(){
	$erreurs = array() ;
	
	if( _request('hebergeur') != "0" ) {
		$erreurs = gestionml_api_tester(_request('serveur_distant'), _request('identifiant'), _request('mot_de_passe')) ;
	}
	return ($erreurs);
}
?>