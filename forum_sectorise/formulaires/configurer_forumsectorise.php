<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_configurer_forumsectorise_saisies_dist(){
	$config = lire_config('forumsectorise');

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'parsecteur',
				'label' => _T('forumsectorise:label_parsecteur')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'explication',
						'texte' => _T('forumsectorise:configurer_explication')
					)
				),
				array(
					'saisie' => 'secteur',
					'options' => array(
						'nom' => 'ident_secteur',
						'label' => _T('forumsectorise:label_ident_secteur'),
						'explication' => _T('forumsectorise:explication_ident_secteur'),
						'multiple' => 'oui',
						'defaut' => $config['ident_secteur']
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'type',
						'label' => _T('forumsectorise:label_type'),
						'explication' => _T('forumsectorise:explication_type'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['type'],
						'datas' => array(
							'pos' => _T('forumsectorise:bouton_radio_publication_immediate'),
							'pri' => _T('forumsectorise:bouton_radio_moderation_priori'),
							'abo' => _T('forumsectorise:bouton_radio_enregistrement_obligatoire'),
							'non' => _T('forumsectorise:bouton_radio_info_pas_de_forum')
						)
					)
				),
				array(
					'saisie' => 'radio',
					'options' => array(
						'nom' => 'option',
						'label' => _T('forumsectorise:label_option'),
						'explication' => _T('forumsectorise:explication_option'),
						'multiple' => 'oui',
						'defaut' => $config['option'],
						'datas' => array(
							'futur' => _T('forumsectorise:bouton_radio_articles_futurs'),
							'saufnon' => _T('forumsectorise:bouton_radio_articles_tous_sauf_forum_desactive'),
							'tous' => _T('forumsectorise:bouton_radio_articles_tous')
						)
					)
				)
			)
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'pourtoussecteurs',
				'label' => _T('forumsectorise:label_pourtoussecteurs')
			),
			'saisies' => array(
				array(
					'saisie' => 'case',
					'options' => array(
						'nom' => 'masqueroptions',
						'label' => _T('forumsectorise:label_masqueroptions'),
						'label_case' => _T('forumsectorise:label_case_masqueroptions'),
						'defaut' => $config['masqueroptions']
					)
				)
			)
		)
	);

}


/**
 * Pipeline
 * Invalider le cache si l'option de config "cacher_public" a ete modifee
 * Puis poursuivre le traitement normal de sauvegarde des paramètres
 *
 * @param array $flux
 * @return array
 */

function forumsectorise_formulaire_traiter($flux){
	if( $flux['args']['form'] == "configurer_forumsectorise" ) {

		$tab_secteur = _request('ident_secteur');
		$type = _request('type');
		$option = _request('option');
		$config = lire_config('forumsectorise');
		
		if ($tab_secteur != $config['ident_secteur']) {
			include_spip('inc/invalideur');
			purger_repertoire(_DIR_SKELS);
		}
	
		// Appliquer les changements de moderation forum
		// option : futur, saufnon, tous
		if (in_array($option,array('tous', 'saufnon')) && count($tab_secteur)) {
			$where1 = ($option == 'saufnon') ? "accepter_forum != 'non'" : '';
			$where2 = sql_in('id_secteur',$tab_secteur) ;
			if(($where1!= '') && ($where2 != '')) {
				$where = $where1 . ' AND ' . $where2 ;
			} else {
				$where = $where1 . $where2 ;
			}
			sql_updateq('spip_articles', array('accepter_forum'=>$type), $where);
		}
	}
	return $flux;

}


?>