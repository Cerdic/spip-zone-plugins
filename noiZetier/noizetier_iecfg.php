<?php

/**
 * Pipeline iecfg pour l'import/export de configuration
 *
 * @param array $flux
 * @return array
 */
function noizetier_iecfg($flux){
	$action = $flux['args']['action'];
	
	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'noizetier_export',
					'label' => '<:noizetier:editer_noizetier_titre:>'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'noizetier_export_explication',
							'texte' => '<:noizetier:iecfg_noizetier_export_explication:>'
						)
					),
					array(
						'saisie' => 'oui_non',
						'options' => array(
							'nom' => 'noizetier_export_option',
							'label' => '<:noizetier:iecfg_noizetier_export_option:>',
							'defaut' => ''
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Tableau d'export
	if ($action=='export' && _request('noizetier_export_option')=='on') {
		include_spip('inc/noizetier');
		$flux['data']['noizetier'] = noizetier_tableau_export();
	}
	
	// Formulaire d'import
	if ($action=='form_import' && isset($flux['args']['config']['noizetier'])) {
		$texte_explication = '';
		if (isset($flux['args']['config']['noizetier']['noisettes'])) {
			$texte_explication .= _T('noizetier:formulaire_liste_pages_config');
			$pages = array();
			foreach($flux['args']['config']['noizetier']['noisettes'] as $noisette)
				$pages[] = $noisette['type'].'-'.$noisette['composition'];
			$pages = array_unique($pages);
			foreach ($pages as $page)
				$texte_explication .= '<br />&raquo; '.rtrim($page,'-');
		}
		if (isset($flux['args']['config']['noizetier']['noizetier_compositions'])) {
			$texte_explication .= '<br />'._T('noizetier:formulaire_liste_compos_config');
			foreach($flux['args']['config']['noizetier']['noizetier_compositions'] as $type => $compositions)
				foreach ($compositions as $composition => $compo)
					$texte_explication .= '<br />&raquo; '.$type.'-'.$composition;
		}
		if (isset($flux['args']['config']['noizetier']['noizetier_compositions'])) {
			$saisies = array(
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'noizetier_export',
						'label' => '<:noizetier:editer_noizetier_titre:>'
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'noizetier_export_explication',
								'texte' => $texte_explication
							)
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_type_import',
								'label' => '<:noizetier:formulaire_type_import:>',
								'explication' => '<:noizetier:formulaire_type_import_explication:>',
								'defaut' => '',
								'option_intro' => '<:noizetier:iecfg_ne_pas_importer:>',
								'datas' => array(
									'fusion' => '<:noizetier:formulaire_import_fusion:>',
									'remplacer' => '<:noizetier:formulaire_import_remplacer:>'
								)
							)
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_import_compos',
								'label' => '<:noizetier:formulaire_import_compos:>',
								'defaut' => 'oui',
								'datas' => array(
									'oui' => '<:noizetier:oui:>',
									'non' => '<:noizetier:non:>'
								)
							)
						)
					)
				)
			);
		} else {
			$saisies = array(
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => 'noizetier_export',
						'label' => '<:noizetier:editer_noizetier_titre:>'
					),
					'saisies' => array(
						array(
							'saisie' => 'explication',
							'options' => array(
								'nom' => 'noizetier_export_explication',
								'texte' => $texte_explication
							)
						),
						array(
							'saisie' => 'selection',
							'options' => array(
								'nom' => 'noizetier_type_import',
								'label' => '<:noizetier:formulaire_type_import:>',
								'explication' => '<:noizetier:formulaire_type_import_explication:>',
								'defaut' => '',
								'option_intro' => '<:noizetier:iecfg_ne_pas_importer:>',
								'datas' => array(
									'fusion' => '<:noizetier:formulaire_import_fusion:>',
									'remplacer' => '<:noizetier:formulaire_import_remplacer:>'
								)
							)
						),
						array(
							'saisie' => 'hidden',
							'options' => array(
								'nom' => 'noizetier_import_compos',
								'defaut' => 'non',
							)
						)
					)
				)
			);
		}
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Import de la configuration
	if ($action=='form_import' && isset($flux['args']['config']['noizetier']) 
		&& isset($flux['args']['request']['noizetier_type_import']) && $flux['args']['request']['noizetier_type_import'])!='' {
		include_spip('inc/noizetier');
		if (!noizetier_importer_configuration(
			$flux['args']['request']['noizetier_type_import'],
			$import_compos = $flux['args']['request']['noizetier_import_compos'],
			$flux['args']['config']['noizetier']
		))
			$flux['data'] .= _T('noizetier:iecfg_probleme_import_config').'<br />';
	}
	
	return($flux);
}

?>