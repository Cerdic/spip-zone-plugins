<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline ieconfig pour l'import/export des
 * groupes de mots-clefs
 * l'entree de tableau yaml doit être le prefix d'un plugin
 * on nomme donc l'export mots.
 *
 * @see http://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation
 *
 *
 * @param array $flux
 * @param string $action (form_export|form_import)
 * @return array
 */
function ieconfig_mots(&$flux, $action){

	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'groupe_mots_export',
					'label' => '<:ieconfigplus:mots_export_titre:>',
					'icone' => 'mot-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'selection_groupes_mots_multiple',
						'options' => array(
							'nom' => 'groupe_mots_a_exporter',
							'label' => '<:ieconfigplus:mots_choix_export:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Tableau d'export
	if ($action=='export' && is_array(_request('groupe_mots_a_exporter')) && count(_request('groupe_mots_a_exporter'))>0) {
		$flux['data']['mots'] = array();
		include_spip('base/abstract_sql');
		foreach (_request('groupe_mots_a_exporter') as $id_groupe_mots) {
			$groupe = sql_fetsel('*','spip_groupes_mots','id_groupe = '.sql_quote($id_groupe_mots));
			$groupe['mots'] = sql_allfetsel('*','spip_mots', 'id_groupe = '.$id_groupe_mots);
			// On enlève juste les champs qui ne sont pas necessaires à un import
			// Au cas ou des champs extras seraient présents
			//unset($groupe['id_groupe']);

			// La clef doit être un prefix
			$flux['data']['mots'][$id_groupe_mots] = $groupe;
		}
	}

	// Formulaire d'import
	if ($action=='form_import'
		&& isset($flux['args']['config']['mots'])
		&& is_array($flux['args']['config']['mots'])
		&& count($flux['args']['config']['mots'])>0){

		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'groupe_mots_import',
					'label' => '<:ieconfigplus:mots_import_titre:>',
					'icone' => 'mot-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'groupe_mots_import_explication',
							'texte' => '<:ieconfigplus:mots_choix_import:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['mots'] as $id_groupe => $groupe) {
			if (sql_countsel('spip_groupes_mots','id_groupe = '.$id_groupe)>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'groupe_importer_'.$id_groupe,
						'label' => $id_groupe.(isset($groupe['titre']) ? ' ('.typo($groupe['titre']).')' : ''),
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
						'nom' => 'groupe_importer_'.$id_groupe,
						'label' => $id_groupe.(isset($groupe['titre']) ? ' ('.typo($groupe['titre']).')' : ''),
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

	// Import des groupes et mots
	if ($action=='import'
		&& isset($flux['args']['config']['mots'])
		&& is_array($flux['args']['config']['mots'])
		&& count($flux['args']['config']['mots'])>0) {

		foreach ($flux['args']['config']['mots'] as $id_groupe => $groupe_datas) {

			$choix = _request('groupe_importer_'.$id_groupe);
			include_spip('base/abstract_sql');

			// On supprime toutes les entrée groupe et mot
			// on les réinssère ensuite
			if ($choix == 'remplacer') {
				supprimer_groupe_mots($id_groupe);
			}

			if ($choix == 'renommer'){
				$groupe_datas['titre'] = $groupe_datas['titre'].'_'.time();
				unset($groupe_datas['id_groupe']);

			}


			if (in_array($choix, array('importer','remplacer','renommer'))) {
				$groupe_datas['titre'] = isset($groupe_datas['titre']) ? $groupe_datas['titre'] : '';

        // extraire les mots clefs de la description du groupe
				$mots = $groupe_datas['mots'];
				unset($groupe_datas['mots']);
        // inssertion du groupe
				$id_groupe = sql_insertq('spip_groupes_mots', $groupe_datas);
				// insertion des mots
				$groupe_mots = sql_insertq_multi('spip_mots', $mots);
			}
		}
	}

	return($flux);
}


/**
 * supprimer_groupe_mots
 *
 * fonction de suppression d'un groupe de mots'
 *
 * @param $id_groupe
*/
function supprimer_groupe_mots($id_groupe){
		// supprimer les mots
		sql_delete('spip_mots', 'id_groupe ='.$id_groupe);
    sql_delete('spip_groupes_mots', 'id_groupe = '.$id_article);
}


?>
