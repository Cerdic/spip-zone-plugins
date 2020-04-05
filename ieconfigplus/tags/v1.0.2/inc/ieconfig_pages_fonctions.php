<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline ieconfig pour l'import/export de configuration
 * des objets pages uniques
 *
 * @see http://contrib.spip.net/Importeur-Exporteur-de-configurations-documentation
 * @todo gérer lors de l'import si on as des identifiants identiques
 *
 * @param array $flux
 * @param string $action (form_export|form_import)
 * @return array
 */
function ieconfig_pages(&$flux, $action){

	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'pages_export',
					'label' => '<:ieconfigplus:pages_export_titre:>',
					'icone' => 'pages-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'selection_pages_multiple',
						'options' => array(
							'nom' => 'pages_a_exporter',
							'label' => '<:ieconfigplus:pages_choix_export:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Tableau d'export
	if ($action=='export' && is_array(_request('pages_a_exporter')) && count(_request('pages_a_exporter'))>0) {
		// Spécifier que le plugin page est necessité
		//
		$flux['data']['necessite'][] = 'pages';
		$flux['data']['pages'] = array();
		include_spip('base/abstract_sql');
		foreach (_request('pages_a_exporter') as $page) {
			$page_unique = sql_fetsel('*','spip_articles','page = '.sql_quote($page));
			// On enlève juste les champs qui ne sont pas necessaires à un import
			// Au cas ou des champs extras seraient présents
			unset($page_unique['id_article'],
				  $page_unique['date'],$page_unique['maj'],$page_unique['date_redac'],
				  $page_unique['visites'],
				  $page_unique['referers'],
				  $page_unique['popularite']);

			$flux['data']['pages'][$page] = $page_unique;
		}
	}

	// Formulaire d'import
	if ($action=='form_import'
		&& isset($flux['args']['config']['pages'])
		&& is_array($flux['args']['config']['pages'])
		&& count($flux['args']['config']['pages'])>0){
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'pages_import',
					'label' => '<:ieconfigplus:pages_import_titre:>',
					'icone' => 'pages-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'pages_import_explication',
							'texte' => '<:ieconfigplus:pages_choix_import:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['pages'] as $page => $page_unique) {
			if (sql_countsel('spip_articles','page = '.sql_quote($page))>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'page_importer_'.$page,
						'label' => $page.(isset($page_unique['titre']) ? ' ('.typo($page_unique['titre']).')' : ''),
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
						'nom' => 'page_importer_'.$page,
						'label' => $page.(isset($page_unique['titre']) ? ' ('.typo($page_unique['titre']).')' : ''),
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

	// Import des pages
	if ($action=='import'&& isset($flux['args']['config']['pages'])&& is_array($flux['args']['config']['pages'])&& count($flux['args']['config']['pages'])>0) {
		foreach ($flux['args']['config']['pages'] as $page => $page_datas) {

			$choix = _request('page_importer_'.$page);
			include_spip('base/abstract_sql');

			if ($choix == 'remplacer') {
				$id_page = intval(sql_getfetsel('id_article','spip_articles','page = '.sql_quote($page)));
				supprimer_page($id_page);
			}

			if ($choix == 'renommer')
				$page_datas['titre'] = $page_datas['titre'].'_'.time();

			if (in_array($choix, array('importer','remplacer','renommer'))) {
				$page_datas['titre'] = isset($page_datas['titre']) ? $page_datas['titre'] : '';
				$id_page = sql_insertq('spip_articles',$page_datas);
			}
		}
	}

	return($flux);
}

/**
 * exporter_page()
 *
 * fonction d'export d'une page unique
 *
 *
 * @param $id_article
 *
*/
function exporter_page($id_article){
    include_spip('base/abstract_sql');
	$id_article = intval($id_article);
	if ($id_article > 0){
		// On récupère la page
		$page = sql_fetsel('*','spip_articles','id_article = '.$id_article);
        return $page;
	}
}

/**
 * supprimer_page
 *
 * fonction de suppression d'une page unique
 *
 * @param $id_article
*/
function supprimer_page($id_article){
    sql_delete("spip_articles", "id_article = $id_article");
}


?>
