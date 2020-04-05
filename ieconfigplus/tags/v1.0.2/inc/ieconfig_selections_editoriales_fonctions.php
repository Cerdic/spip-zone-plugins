<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline ieconfig pour l'import/export
 * des objets selection editoriales
 *
 * @see http://contrib.spip.net/Selections-editoriales
 *
 * @param array $flux
 * @return array
 */
function ieconfig_selections_editoriales(&$flux, $action){

	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'selections_editoriales_export',
					'label' => '<:ieconfigplus:selections_editoriales_export_titre:>',
					'icone' => 'selection-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'selection_selections_editoriales_multiple',
						'options' => array(
							'nom' => 'selections_editoriales_a_exporter',
							'label' => '<:ieconfigplus:selections_editoriales_choix_export:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Tableau d'export
	if ($action=='export'
		&& is_array(_request('selections_editoriales_a_exporter'))
		&& count(_request('selections_editoriales_a_exporter'))>0
	) {
		// Spécifier que le plugin selections_editoriales est necessité ?
		//
		$flux['data']['necessite'][] = 'selections_editoriales';
		$flux['data']['selections_editoriales'] = array();
		include_spip('base/abstract_sql');
		foreach (_request('selections_editoriales_a_exporter') as $selection) {

			$selection_datas = sql_fetsel('*','spip_selections','id_selection = '.sql_quote($selection));
			// On enlève juste les champs qui ne sont pas necessaires à un import
			// Au cas ou des champs extras seraient présents
			unset($selection_datas['id_selection'],
						$selection_datas['referers'],
						$selection_datas['popularite'],
						$selection_datas['visites'],
						$selection_datas['date'],
						$selection_datas['maj']);

      $flux['data']['selections_editoriales'][$selection] = $selection_datas;
		}
	}

	// Formulaire d'import
	if ($action=='form_import'
		&& isset($flux['args']['config']['selections_editoriales'])
		&& is_array($flux['args']['config']['selections_editoriales'])
		&& count($flux['args']['config']['selections_editoriales'])>0
	){
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'selections_editoriales_import',
					'label' => '<:ieconfigplus:selections_editoriales_import_titre:>',
					'icone' => 'selection-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'selections_editoriales_import_explication',
							'texte' => '<:ieconfigplus:selections_editoriales_choix_import:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['selections_editoriales'] as $selection => $selection_datas) {
			if (sql_countsel('spip_selections','id_selection = '.intval($selection))>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'selections_editoriales_importer_'.$selection,
						'label' => $selection.(isset($selection_datas['titre']) ? ' ('.typo($selection_datas['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'attention' => '<:ieconfigplus:ieconfig_attention_meme_identifiant:>',
						'datas' => array(
							'non' => '<:ieconfigplus:ieconfig_ne_pas_importer:>',
							'renommer' => '<:ieconfigplus:ieconfig_renommer:>',
							'remplacer' => '<:ieconfigplus:ieconfig_remplacer:>'
						)
					)
				);
			} else {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'selections_editoriales_importer_'.$selection,
						'label' => $selection.(isset($selection_datas['titre']) ? ' ('.typo($selection_datas['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'non' => '<:ieconfigplus:ieconfig_ne_pas_importer:>',
							'importer' => '<:ieconfigplus:ieconfig_importer:>'
						)
					)
				);
			}
		}
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Import des selections
	if ($action=='import'
			&& isset($flux['args']['config']['selections_editoriales'])
			&& is_array($flux['args']['config']['selections_editoriales'])
			&& count($flux['args']['config']['selections_editoriales']) > 0) {

		foreach ($flux['args']['config']['selections_editoriales'] as $selection => $selection_datas) {

			$choix = _request('selections_editoriales_importer_'.$selection);
			include_spip('base/abstract_sql');

			if ($choix == 'remplacer') {
				$id_selection = intval(sql_getfetsel('id_selection','spip_selections','id_selection = '.sql_quote($selection)));
				supprimer_page($id_selection);
			}

			if ($choix == 'renommer')
				$selection_datas['titre'] = $selection_datas['titre'].'_'.time();

			if (in_array($choix, array('importer','remplacer','renommer'))) {
				$selection_datas['titre'] = isset($selection_datas['titre']) ? $selection_datas['titre'] : '';
				$id_selection = sql_insertq('spip_selections',$selection_datas);
			}
		}
	}

	return($flux);
}

/**
 * exporter_selection()
 *
 * fonction d'export d'une selection editoriale
 *
 *
 * @param $id_selection
 *
*/
function exporter_selection($id_selection){
    include_spip('base/abstract_sql');
	$id_selection = intval($id_selection);
	if ($id_selection > 0){
		// On récupère la selection
		$id_selection = sql_fetsel('*','spip_selections','id_selection = '.$id_selection);
        return $id_selection;
	}
}




?>
