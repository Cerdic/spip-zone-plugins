<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline ieconfig pour l'import/export de configuration
 *
 * @param array $flux
 * @return array
 */
function menus_ieconfig($flux){
	include_spip('inc/texte');
	$action = $flux['args']['action'];
	
	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'menus_export',
					'label' => '<:menus:editer_menus_titre:>',
					'icone' => 'menu-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'menus_multiple',
						'options' => array(
							'nom' => 'menus_a_exporter',
							'label' => '<:menus:formulaire_ieconfig_menus_a_exporter:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Tableau d'export
	if ($action=='export' && is_array(_request('menus_a_exporter')) && count(_request('menus_a_exporter'))>0) {
		$flux['data']['menus'] = array();
		include_spip('base/abstract_sql');
		include_spip('action/exporter_menu');
		foreach (_request('menus_a_exporter') as $identifiant) {
			$menu = sql_fetsel(array('id_menu','titre','css'),'spip_menus','identifiant = '.sql_quote($identifiant));
			$id_menu = $menu['id_menu'];
			unset($menu['id_menu']);
			$menu['entrees'] = exporter_menu_recursif($id_menu);
			$flux['data']['menus'][$identifiant] = $menu;
		}
	}
	
	// Formulaire d'import
	if ($action=='form_import' && isset($flux['args']['config']['menus']) && is_array($flux['args']['config']['menus']) && count($flux['args']['config']['menus'])>0) {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'menus_import',
					'label' => '<:menus:editer_menus_titre:>',
					'icone' => 'menu-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'menus_import_explication',
							'texte' => '<:menus:formulaire_ieconfig_choisir_menus_a_importer:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['menus'] as $identifiant => $menu) {
			if (sql_countsel('spip_menus','identifiant = '.sql_quote($identifiant))>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'menus_importer_'.$identifiant,
						'label' => $identifiant.(isset($menu['titre']) ? ' ('.typo($menu['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'attention' => '<:menus:formulaire_ieconfig_menu_meme_identifiant:>',
						'datas' => array(
							'non' => '<:menus:formulaire_ieconfig_ne_pas_importer:>',
							'renommer' => '<:menus:formulaire_ieconfig_renommer:>',
							'remplacer' => '<:menus:formulaire_ieconfig_remplacer:>'
						)
					)
				);
			} else {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'menus_importer_'.$identifiant,
						'label' => $identifiant.(isset($menu['titre']) ? ' ('.typo($menu['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'non' => '<:menus:formulaire_ieconfig_ne_pas_importer:>',
							'importer' => '<:menus:formulaire_ieconfig_importer:>'
						)
					)
				);
			}
		}
		$flux['data'] = array_merge($flux['data'],$saisies);
	}
	
	// Import de la configuration
	if ($action=='import' && isset($flux['args']['config']['menus']) && is_array($flux['args']['config']['menus']) && count($flux['args']['config']['menus'])>0) {
		foreach ($flux['args']['config']['menus'] as $identifiant => $menu) {
			$choix = _request('menus_importer_'.$identifiant);
			include_spip('base/abstract_sql');
			include_spip('inc/menus');
			include_spip('action/editer_menu');
			if ($choix == 'remplacer') {
				$id_menu = intval(sql_getfetsel('id_menu','spip_menus','identifiant = '.sql_quote($identifiant)));
				menus_supprimer_menu($id_menu);
			}
			if ($choix == 'renommer')
				$identifiant = $identifiant.'_'.time();
			if (in_array($choix,array('importer','remplacer','renommer'))) {
				$titre = isset($menu['titre']) ? $menu['titre'] : '';
				$css = isset($menu['css']) ? $menu['css'] : '';
				$id_menu = sql_insertq('spip_menus',array(
					'identifiant' => $identifiant,
					'titre' => $titre,
					'css' => $css
				));
				if (isset($menu['entrees']))
					menus_importer($menu['entrees'], $id_menu);
			}
		}
	}
	
	return($flux);
}

?>