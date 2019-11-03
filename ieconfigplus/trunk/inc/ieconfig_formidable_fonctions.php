<?php

if (!defined("_ECRIRE_INC_VERSION")) return;



/**
 * Fonction d'export/import pour la Pipeline ieconfig
 *
 * @param array $flux
 * @param string $action
 * @return array
 */
function ieconfig_formidable(&$flux, $action){
	// Formulaire d'export
	if ($action=='form_export') {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'formidable_export',
					'label' => '<:ieconfigplus:formidable_export_titre:>',
					'icone' => 'formulaire-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'formulaire_formidable_multiple',
						'options' => array(
							'nom' => 'formidable_a_exporter',
							'label' => '<:ieconfigplus:formidable_choix_export:>',
							'cacher_option_intro' => 'oui'
						)
					)
				)
			)
		);
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Tableau d'export
	if ($action=='export' && is_array(_request('formidable_a_exporter')) && count(_request('formidable_a_exporter'))>0) {
		$flux['data']['formidable'] = array();
		include_spip('base/abstract_sql');
		//include_spip('echanger/formulaire/yaml');
		foreach (_request('formidable_a_exporter') as $identifiant) {
			$formulaire = sql_fetsel(array('id_formulaire','identifiant','titre'),'spip_formulaires','identifiant = '.sql_quote($identifiant));
                        $id_formulaire = $formulaire['id_formulaire'];
			unset($formulaire['id_formulaire']);
			$formulaire['entrees'] = exporter_formulaire($id_formulaire);
			$flux['data']['formidable'][$identifiant] = $formulaire;
		}
	}

	// Formulaire d'import
	if ($action=='form_import' && isset($flux['args']['config']['formidable']) && is_array($flux['args']['config']['formidable']) && count($flux['args']['config']['formidable'])>0) {
		$saisies = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'formidable_import',
					'label' => '<:formidable:editer_formidable_titre:>',
					'icone' => 'menu-16.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'explication',
						'options' => array(
							'nom' => 'formidable_import_explication',
							'texte' => '<:formidable:formulaire_ieconfig_choisir_formidable_a_importer:>'
						)
					)
				)
			)
		);
		foreach ($flux['args']['config']['formidable'] as $identifiant => $formulaire) {
			if (sql_countsel('spip_formulaires','identifiant = '.sql_quote($identifiant))>0) {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'formidable_importer_'.$identifiant,
						'label' => $identifiant.(isset($formulaire['titre']) ? ' ('.typo($formulaire['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'attention' => '<:formidable:formulaire_ieconfig_menu_meme_identifiant:>',
						'datas' => array(
							'non' => '<:formidable:formulaire_ieconfig_ne_pas_importer:>',
							'renommer' => '<:formidable:formulaire_ieconfig_renommer:>',
							'remplacer' => '<:formidable:formulaire_ieconfig_remplacer:>'
						)
					)
				);
			} else {
				$saisies[0]['saisies'][] = array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'formidable_importer_'.$identifiant,
						'label' => $identifiant.(isset($formulaire['titre']) ? ' ('.typo($formulaire['titre']).')' : ''),
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'non' => '<:formidable:formulaire_ieconfig_ne_pas_importer:>',
							'importer' => '<:formidable:formulaire_ieconfig_importer:>'
						)
					)
				);
			}
		}
		$flux['data'] = array_merge($flux['data'],$saisies);
	}

	// Import de la configuration
	if ($action=='import' && isset($flux['args']['config']['formidable']) && is_array($flux['args']['config']['formidable']) && count($flux['args']['config']['formidable'])>0) {
		foreach ($flux['args']['config']['formidable'] as $identifiant => $formulaire) {
			$choix = _request('formidable_importer_'.$identifiant);
			include_spip('base/abstract_sql');
			//include_spip('inc/formidable');
			// include_spip('action/editer_menu');
			if ($choix == 'remplacer') {
				$id_formulaire = intval(sql_getfetsel('id_formulaire','spip_formulaires','identifiant = '.sql_quote($identifiant)));
				formulaire_supprimer($id_formulaire);
			}
			if ($choix == 'renommer')
				$identifiant = $identifiant.'_'.time();
			if (in_array($choix,array('importer','remplacer','renommer'))) {
				$titre = isset($menu['titre']) ? $menu['titre'] : '';
				$id_menu = sql_insertq('spip_formulaires',array(
					'identifiant' => $identifiant,
					'titre' => $titre
				));
				if (isset($formulaire['entrees']))
					formulaire_importer($formulaire['entrees']);
			}
		}
	}// ./end test defined(_DIR_PLUGIN_FORMIDABLE)


	return($flux);
}

/**
 * exporter_formulaire
 *
 * tiré de la function echanger/formulaire/yaml
*/
function exporter_formulaire($id_formulaire){
  include_spip('base/abstract_sql');
	include_spip('inc/yaml');
	$id_formulaire = intval($id_formulaire);

	if ($id_formulaire > 0){
		// On récupère le formulaire
		$formulaire = sql_fetsel(
			'*',
			'spip_formulaires',
			'id_formulaire = '.$id_formulaire
		);

		// On décompresse les trucs sérialisés
		$formulaire['saisies'] = unserialize($formulaire['saisies']);
		$formulaire['traitements'] = unserialize($formulaire['traitements']);

    return $formulaire;
	}
}

function formulaire_importer($formulaire){



        // Si le decodage marche on importe alors le contenu
        if (is_array($formulaire)){
                // include_spip('action/editer_formulaire');
                // On enlève les champs inutiles
                unset($formulaire['id_formulaire']);
                // On vérifie que l'identifiant n'existe pas déjà
                $deja = sql_getfetsel(
                        'id_formulaire',
                        'spip_formulaires',
                        'identifiant = '.sql_quote($formulaire['identifiant'])
                );
                if ($deja){
                        $formulaire['identifiant'] .= '_'.date('Ymd_His');
                }

                // On insère un nouveau formulaire
                $id_formulaire = ieconfig_formulaire_insserer();
                // Si ça a marché on transforme les tableaux et on modifie les champs
                if ($id_formulaire > 0){
                        if (is_array($formulaire['saisies'])){
                                $formulaire['saisies'] = serialize($formulaire['saisies']);
                        }
                        if (is_array($formulaire['traitements'])){
                                $formulaire['traitements'] = serialize($formulaire['traitements']);
                        }

                        $erreur = ieconfig_formulaire_modifier($id_formulaire, $formulaire);
                }
        }

}


/**
 * Crée un nouveau formulaire et retourne son ID
 *
 * @return int id_formulaire
 */
function ieconfig_formulaire_insserer() {
	$champs = array(
		'statut' => 'prop',
		'date_creation' => date('Y-m-d H:i:s'),
	);

	$id_formulaire = sql_insertq("spip_formulaires", $champs);


	return $id_formulaire;
}

function formulaire_supprimer($id_formulaire){
        sql_delete("spip_formulaires", "idformulaire=$id_$id_formulaire");
}


/**
 * Appelle la fonction de modification d'un formulaire
 *
 * @param int $id_formulaire
 * @param array|null $set
 * @return string
 */
function ieconfig_formulaire_modifier($id_formulaire, $set=null) {
	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$err = '';

	$c = collecter_requests(
		// white list
		objet_info('formulaire','champs_editables'),
		// black list
		array('statut'),
		// donnees eventuellement fournies
		$set
	);


	$invalideur = "id='id_formulaire/$id_formulaire'";
	if ($err = objet_modifier_champs('formulaire', $id_formulaire,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
		),
		$c))
		return $err;

	// Modification de statut, changement de rubrique ?
	$c = collecter_requests(array('statut'),array(),$set);
	include_spip("action/editer_objet");
	$err = objet_instituer('formulaire',$id_formulaire, $c);

	return $err;
}


?>
