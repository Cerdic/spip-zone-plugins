<?php


function iecfg_saisies_import() {
	// Etape de selection du fichier
	if (!_request('_code_yaml') or _request('annuler') or _request('importer')) {
		$saisies = array (
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'iecfg_import_choix_fichier',
					'label' => '<:iecfg:label_iecfg_import_choix_fichier:>'
				),
				'saisies' => array(
					array(
						'saisie' => 'input',
						'options' => array(
							'type' => 'file',
							'nom' => 'iecfg_import_fichier',
							'label' => '<:iecfg:label_iecfg_import_fichier:>',
							'explication' => '<:iecfg:explication_iecfg_import_fichier:>'
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'type' => 'selection',
							'nom' => 'iecfg_import_local',
							'label' => '<:iecfg:label_iecfg_import_local:>',
							'explication' => '<:iecfg:explication_iecfg_import_local:>',
							'cacher_option_intro' => 'oui',
							'datas' => iecfg_config_locales()
						)
					)
				)
			)
		);
	// Options d'importations
	} else {
		include_spip('inc/yaml');
		$config = yaml_decode(_request('_code_yaml'));
		$texte_explication = '<b>'._T('iecfg:texte_nom').'</b> '._T_ou_typo($config['nom']);
		if ($config['description']!= '')
			$texte_explication .= '<br /><b>'._T('iecfg:texte_description').'</b> '._T_ou_typo($config['description']);
		$saisies = array(
			array(
				'saisie' => 'explication',
				'options' => array(
					'nom' => 'import_details',
					'texte' => $texte_explication
				)
			)
		);
		// Le fichier contient-il une configuration des contenus du site
		if (isset($config['spip_contenu'])) {
			$texte_explication = _T('iecfg:texte_spip_contenu_import_explication');
			$i = 0;
			foreach($config['spip_contenu'] as $meta => $valeur)
				if ($GLOBALS['meta'][$meta] != $valeur) {
					$texte_explication .= '<br />&raquo; '.$meta.' : '.$GLOBALS['meta'][$meta].' -> '.$valeur;
					$i++;
				}
			if ($i>0)
				$saisies_spip_contenu = array(
					array(
						'saisie' => 'fieldset',
						'options' => array(
							'nom' => 'spip_contenu',
							'label' => '<:spip:onglet_contenu_site:>'
						),
						'saisies' => array(
							array(
								'saisie' => 'explication',
								'options' => array(
									'nom' => 'spip_contenu_explication',
									'texte' => $texte_explication
								)
							),
							array(
								'saisie' => 'oui_non',
								'options' => array(
									'nom' => 'spip_contenu_importer',
									'label' => '<:iecfg:label_importer:>',
									'defaut' => '',
								)
							)
						)
					)
				);
			else
				$saisies_spip_contenu = array(
					array(
						'saisie' => 'fieldset',
						'options' => array(
							'nom' => 'spip_contenu',
							'label' => '<:spip:onglet_contenu_site:>'
						),
						'saisies' => array(
							array(
								'saisie' => 'explication',
								'options' => array(
									'nom' => 'spip_contenu_explication',
									'texte' => '<:iecfg:texte_configuration_identique:>'
								)
							)
						)
					)
				);
			$saisies = array_merge($saisies,$saisies_spip_contenu);
		}
		// On passe via le pipeline iecfg
		$saisies = pipeline('iecfg',array(
			'args' => array(
				'action' => 'form_import',
				'config' => $config
			),
			'data' => $saisies
		));
		// On identifie les éléments de config correspondant à des plugins non actifs
		// Convention : les clés du tableau de config correspondent aux préfixes des plugins
	}
	return $saisies;
}

function formulaires_iecfg_import_charger_dist() {
	$contexte = array(
		'_saisies' => iecfg_saisies_import()
	);
	if (_request('_code_yaml'))
		$contexte['_code_yaml'] = _request('_code_yaml');
	$contexte = array_merge($_POST,$contexte);
	if (_request('annuler') or _request('importer'))
		unset($contexte['_code_yaml']);
	return $contexte;
}

function formulaires_iecfg_import_verifier_dist() {
	$erreurs = array();
	// Etape de selection du fichier
	if (!_request('_code_yaml')) {
		// On a rien transmis et pas de fichier local
		if (!_request('iecfg_import_local') AND $_FILES['iecfg_import_fichier']['name']=='')
			$erreurs['message_erreur'] = _T('iecfg:message_erreur_fichier_import_manquant');
	}
	// Options d'import
	else {
		include_spip('inc/saisies');
		$erreurs = saisies_verifier(iecfg_saisies_import());
	}
	return $erreurs;
}

function formulaires_iecfg_import_traiter_dist() {
	// Si on est à l'étape de sélection d'un fichier de configuration
	// On place le code YAML dans le contexte
	if (!_request('_code_yaml')) {
		if ($_FILES['iecfg_import_fichier']['name']!='')
			$fichier = $_FILES['iecfg_import_fichier']['tmp_name'];
		else
			$fichier = _request('iecfg_import_local');
		lire_fichier($fichier, $code_yaml);
		set_request('_code_yaml',$code_yaml);
	}
	// Si on valide l'import
	elseif (_request('importer') && _request('_code_yaml')) {
		include_spip('inc/yaml');
		$config = yaml_decode(_request('_code_yaml'));
		
		// Le fichier contient-il une configuration des contenus du site à importer
		if (isset($config['spip_contenu']) && _request('spip_contenu_importer')=='on') {
			foreach($config['spip_contenu'] as $nom => $valeur)
				ecrire_meta($nom,$valeur);
			ecrire_metas();
		}
		
		// On passe via le pipeline iecfg
		$message_erreur = pipeline('iecfg',array(
			'args' => array(
				'action' => 'import',
				'config' => $config,
				'request' => $_POST
			),
			'data' => ''
		));
		
		if ($message_erreur!='')
			return array('message_erreur' => $message_erreur);
		else
			return array('message_erreur' => _T('message_ok_import'));
	}
}

// Renvoie la liste des fichiers de configurations présents dans un sous-répertoires iecfg/
function iecfg_config_locales() {
	static $liste_config = null;
	
	if (is_null($liste_config)){
		include_spip('inc/yaml');
		$liste_config = array();
		$match = ".+[.]yaml$";
		foreach (array_merge(find_all_in_path('iecfg/', $match),find_all_in_path('tmp/iecfg/', $match)) as $fichier => $chemin) {
			$config = yaml_decode_file($chemin);
			//on vérifie s'il y a un champs nom
			if (isset($config['nom']))
				$liste_config[$chemin] = _T_ou_typo($config['nom']);
			else
				$liste_config[$chemin] = $fichier;
		}
	}
	return $liste_config;
}


?>