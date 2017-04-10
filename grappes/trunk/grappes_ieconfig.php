<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline ieconfig pour l'import/export de configuration
 * 
 *
 * @see https://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation
 * @param array $flux
 * @return array
 */
function grappes_ieconfig($flux){
    include_spip('inc/texte');
	$action = $flux['args']['action'];
    
    // Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'grappes_export',
					'label' => '<:grappes:export_titre:>',
					'icone' => 'grappes-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'selection_grappes_multiple',
						'options' => array(
							'nom' => 'grappes_a_exporter',
							'label' => '<:grappes:export_choix_label:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Tableau d'export
	if ($action=='export' && is_array(_request('grappes_a_exporter')) && count(_request('grappes_a_exporter'))>0) {
		// Spécifier que le plugin grappe est necessité
		// 
		$flux['data']['necessite'][] = 'grappes';
		$flux['data']['grappes'] = array();
		include_spip('base/abstract_sql');
		foreach (_request('grappes_a_exporter') as $identifiant) {
			$objet_export = sql_fetsel('*','spip_grappes','identifiant = '.sql_quote($identifiant));
			// On enlève jsute les champs qui ne sont pas necessaires à un import
			// Au cas ou des champs extras seraient présents
			unset($objet_export['id_grappe'],
				  $objet_export['date'],$objet_export['maj'],
				  $objet_export['visites'],
				  $objet_export['referers'],
				  $objet_export['popularite']);
            $flux['data']['grappes'][$identifiant] = $objet_export;
		}
	}
	
	// Formulaire d'import
	if ($action=='form_import'&& isset($flux['args']['config']['grappes'])&& is_array($flux['args']['config']['grappes'])&& count($flux['args']['config']['grappes'])>0){
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'grappes_import',
					'label' => '<:grappes:import_titre:>',
					'icone' => 'grappes-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'import_explication',
							'texte' => '<:grappes:import_choix:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['grappes'] as $identifiant => $objet_export) {
			if (sql_countsel('spip_grappes','identifiant = '.sql_quote($identifiant))>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'page_importer_'.$identifiant,
						'label' => $identifiant.(isset($objet_export['titre']) ? ' ('.typo($objet_export['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'attention' => '<:fragments_core:ieconfig_attention_meme_identifiant:>',
						'datas' => array(
							'non' => '<:fragments_core:ieconfig_ne_pas_importer:>',
							'renommer' => '<:fragments_core:ieconfig_renommer:>',
							'remplacer' => '<:fragments_core:ieconfig_remplacer:>'
						)
					)
				);
			} else {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'grappe_importer_'.$identifiant,
						'label' => $identifiant.(isset($objet_export['titre']) ? ' ('.typo($objet_export['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
                        'attention' => '<:grappes:formulaire_ieconfig_grappe_meme_identifiant:>',
						'datas' => array(
							'non' => '<:grappes:formulaire_ieconfig_ne_pas_importer:>',
							'renommer' => '<:grappes:formulaire_ieconfig_renommer:>',
							'remplacer' => '<:grappes:formulaire_ieconfig_remplacer:>'
						)
					)
				);
			}
		}
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Import des grappes
	if ($action=='import'&& isset($flux['args']['config']['grappes'])&& is_array($flux['args']['config']['grappes'])&& count($flux['args']['config']['grappes'])>0) {
		foreach ($flux['args']['config']['grappes'] as $identifiant => $identifiant_datas) {
		
			$choix = _request('grappe_importer_'.$identifiant);
			include_spip('base/abstract_sql');

			if ($choix == 'remplacer') {
				$id_grappe = intval(sql_getfetsel('id_grappe','spip_grappes','identifiant = '.sql_quote($identifiant)));
				supprimer_grappe($id_grappe);
			}
			
			if ($choix == 'renommer')
				$identifiant_datas['titre'] = $identifiant_datas['titre'].'_'.time();
				
			if (in_array($choix, array('importer','remplacer','renommer'))) {
				$identifiant_datas['titre'] = isset($identifiant_datas['titre']) ? $identifiant_datas['titre'] : '';
				$id_page = sql_insertq('spip_grappes',$identifiant_datas);
			}
		}
	}
    
    
    return $flux;
}

/**
 * supprimer_grappe
 *
 * fonction de suppression d'une grappe
 *
 * @param $id_grappe
*/
function supprimer_grappe($id_grappe){
    sql_delete("spip_grappes", "id_grappe = $id_grappe");
}