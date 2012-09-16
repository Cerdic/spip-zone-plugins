<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function ieconfig_saisies_import() {
	// Etape de selection du fichier
	if (!_request('_code_yaml') or _request('annuler') or _request('importer')) {
		$saisies = array (
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'ieconfig_import_choix_fichier',
					'label' => '<:ieconfig:label_ieconfig_import_choix_fichier:>',
					'icone' => 'img/ieconfig-import.png'
				),
				'saisies' => array(
					array(
						'saisie' => 'input',
						'options' => array(
							'type' => 'file',
							'nom' => 'ieconfig_import_fichier',
							'label' => '<:ieconfig:label_ieconfig_import_fichier:>',
							'explication' => '<:ieconfig:explication_ieconfig_import_fichier:>'
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'type' => 'selection',
							'nom' => 'ieconfig_import_local',
							'label' => '<:ieconfig:label_ieconfig_import_local:>',
							'explication' => '<:ieconfig:explication_ieconfig_import_local:>',
							'datas' => ieconfig_config_locales()
						)
					)
				)
			)
		);
	// Options d'importations
	} else {
		include_spip('inc/yaml');
		$config = yaml_decode(_request('_code_yaml'));
		$texte_explication = '<b>'._T('ieconfig:texte_nom').'</b> '._T_ou_typo($config['nom']);
		if ($config['description']!= '')
			$texte_explication .= '<br /><b>'._T('ieconfig:texte_description').'</b> '._T_ou_typo($config['description']);
		// On identifie les entr�es ne correspondant pas � un plugin
		// Ou bien non d�clar�es dans ieconfig_metas
		// Convention : les cl�s du tableau de config correspondent aux pr�fixes des plugins
		$entrees = $config;
		unset($entrees['nom']);
		unset($entrees['description']);
		unset($entrees['necessite']);
		$entrees = array_map('strtolower',array_keys($entrees));
		$plugins = array_map('strtolower',array_keys(unserialize($GLOBALS['meta']['plugin'])));
		$entrees_prises_en_charge = array_merge(array_keys(pipeline('ieconfig_metas',array())),$plugins);
		$plugins_manquants = array_diff($entrees,$entrees_prises_en_charge);
		if (count($plugins_manquants)>0)
			$texte_explication .= '<p class="reponse_formulaire reponse_formulaire_erreur">'._T('ieconfig:texte_plugins_manquants',array('plugins' => implode(', ',$plugins_manquants))).'</p>';
		
		$saisies = array(
			array(
				'saisie' => 'explication',
				'options' => array(
					'nom' => 'import_details',
					'texte' => $texte_explication
				)
			)
		);
		
		// On passe via le pipeline ieconfig
		$saisies = pipeline('ieconfig',array(
			'args' => array(
				'action' => 'form_import',
				'config' => $config
			),
			'data' => $saisies
		));
		
		// Gestion des plugins utilisant le pipeline ieconfig_metas
		foreach(pipeline('ieconfig_metas',array()) as $prefixe => $data){
			if(isset($config[$prefixe]))
				$saisies[] = array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => $prefixe,
						'label' => isset($data['titre']) ? $data['titre'] : $prefixe,
						'icone' => isset($data['icone']) ? $data['icone'] : ''
					),
					'saisies' => array(
						array(
							'saisie' => 'oui_non',
							'options' => array(
								'nom' => 'import_'.$prefixe,
								'label' => _T('ieconfig:label_importer'),
								'defaut' => ''
							)
						)
					)
				);
		}
	}
	return $saisies;
}

function formulaires_ieconfig_import_charger_dist() {
	include_spip('inc/saisies');
	$saisies = ieconfig_saisies_import();
	$contexte = array(
		'_saisies' => $saisies,
	);
	if (_request('_code_yaml') and !_request('annuler') and !_request('importer'))
		$contexte['_code_yaml'] = _request('_code_yaml');
	return array_merge(saisies_charger_champs($saisies),$contexte);
}

function formulaires_ieconfig_import_verifier_dist() {
	$erreurs = array();
	// Etape de selection du fichier
	if (!_request('_code_yaml')) {
		// On a rien transmis et pas de fichier local
		if (!_request('ieconfig_import_local') AND $_FILES['ieconfig_import_fichier']['name']=='')
			$erreurs['message_erreur'] = _T('ieconfig:message_erreur_fichier_import_manquant');
	}
	// Options d'import
	else {
		include_spip('inc/saisies');
		$erreurs = saisies_verifier(ieconfig_saisies_import());
	}
	return $erreurs;
}

function formulaires_ieconfig_import_traiter_dist() {
	// Si on est � l'�tape de s�lection d'un fichier de configuration
	// On place le code YAML dans le contexte
	if (!_request('_code_yaml')) {
		if ($_FILES['ieconfig_import_fichier']['name']!='')
			$fichier = $_FILES['ieconfig_import_fichier']['tmp_name'];
		else
			$fichier = _request('ieconfig_import_local');
		lire_fichier($fichier, $code_yaml);
		set_request('_code_yaml',$code_yaml);
	}
	// Si on valide l'import
	elseif (_request('importer') && _request('_code_yaml')) {
		include_spip('inc/yaml');
		$config = yaml_decode(_request('_code_yaml'));
		
		// On passe via le pipeline ieconfig
		$message_erreur = pipeline('ieconfig',array(
			'args' => array(
				'action' => 'import',
				'config' => $config
			),
			'data' => ''
		));
		
		// Gestion des plugins utilisant le pipeline ieconfig_metas
		foreach(pipeline('ieconfig_metas',array()) as $prefixe => $data){
			if(_request('import_'.$prefixe)=='on' && isset($config[$prefixe])) {
				if(isset($data['metas_brutes']))
					foreach(explode(',',$data['metas_brutes']) as $meta)
						if (isset($config[$prefixe][$meta]))
							ecrire_meta($meta,$config[$prefixe][$meta]);
				if(isset($data['metas_serialize']))
					foreach(explode(',',$data['metas_serialize']) as $meta)
						if (isset($config[$prefixe][$meta]))
							ecrire_meta($meta,serialize($config[$prefixe][$meta]));
			}
		}
		
		ecrire_metas();
		
		if ($message_erreur!='')
			return array('message_erreur' => $message_erreur);
		else
			return array('message_ok' => _T('ieconfig:message_ok_import'));
	}
}

// Renvoie la liste des fichiers de configurations pr�sents dans un sous-r�pertoires ieconfig/
function ieconfig_config_locales() {
	static $liste_config = null;
	
	if (is_null($liste_config)){
		include_spip('inc/yaml');
		$liste_config = array();
		$match = ".+[.]yaml$";
		foreach (array_merge(find_all_in_path('ieconfig/', $match),find_all_in_path(_DIR_TMP.'ieconfig/', $match)) as $fichier => $chemin) {
			$config = yaml_decode_file($chemin);
			// On regarde s'il y a un necessite
			$ok = true;
			if (isset($config['necessite'])) {
				if (!is_array($config['necessite']))
					$config['necessite'] = array($config['necessite']);
				foreach($config['necessite'] as $plugin)
					if (!defined('_DIR_PLUGIN_'.strtoupper($plugin)))
						$ok = false;
			}
			//on v�rifie s'il y a un champs nom
			if ($ok) {
				if (isset($config['nom']))
					$liste_config[$chemin] = _T_ou_typo($config['nom']);
				else
					$liste_config[$chemin] = $fichier;
			}
		}
	}
	return $liste_config;
}


?>