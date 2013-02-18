<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_manuelsite_saisies_dist(){
	$config = lire_config('manuelsite',array());

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fcontenu_manuelsite',
				'label' => _T('manuelsite:legende_contenu')
			),
			'saisies' => array(
				array(
					'saisie' => 'selecteur_article',
					'options' => array(
						'nom' => 'id_article',
						'label' => _T('manuelsite:label_id_article'),
						'explication' => _T('manuelsite:explication_id_article'),
						'obligatoire' => 'oui',
						'defaut' =>'article|'.$config['id_article'],
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'cacher_public',
						'label' => _T('manuelsite:label_cacher_public'),
						'explication' => _T('manuelsite:explication_cacher_public'),
						'defaut' => $config['cacher_public']
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'intro',
						'label' => _T('manuelsite:label_intro'),
						'explication' => _T('manuelsite:explication_intro'),
						'class' => 'porte_plume_partout',
						'defaut' => $config['intro']
					)
				),
				array(
					'saisie' => 'email',
					'options' => array(
						'nom' => 'email',
						'label' => _T('manuelsite:label_email'),
						'explication' => _T('manuelsite:explication_email'),
						'defaut' => $config['email']
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fapparence_manuelsite',
				'label' => _T('manuelsite:legende_apparence')
			),
			'saisies' => array(
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'afficher_bord_gauche',
						'label' => _T('manuelsite:label_afficher_bord_gauche'),
						'explication' => _T('manuelsite:explication_afficher_bord_gauche'),
						'defaut' => $config['afficher_bord_gauche']
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'largeur',
						'label' => _T('manuelsite:label_largeur'),
						'explication' => _T('manuelsite:explication_largeur'),
						'defaut' => $config['largeur'],
						'afficher_si' => '@afficher_bord_gauche@ == "on"' 
					)
				),
				array(
					'saisie' => 'couleur',
					'options' => array(
						'nom' => 'background_color',
						'label' => _T('manuelsite:label_background_color'),
						'explication' => _T('manuelsite:explication_background_color'),
						'defaut' => $config['background_color'],
						'afficher_si' => '@afficher_bord_gauche@ == "on"' 
					)
				)
			)
		)
	);

}

function formulaires_configurer_manuelsite_verifier(){
	// On la garde en mémoire dans le hit pour une utilisation dans le pipeline de traitement
	set_request('ancien_cacher_public', lire_config('manuelsite/cacher_public'));
	return array();
}

/**
 * Pipeline
 * Invalider le cache pour tout changement de configuration
 *
 * @param array $flux
 * @return array
 */
function manuelsite_formulaire_traiter($flux){
	$id_article = preg_replace('(article\|)','',_request('id_article'));
	
	ecrire_config('manuelsite/id_article',$id_article[0]);
	
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return $flux;
}

?>