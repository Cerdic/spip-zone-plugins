<?php

include_spip('inc/ckeditor_constantes') ;
include_spip('inc/ckeditor_lire_config') ;
include_spip('inc/ckeditor_tools') ;
include_spip("inc/toolbars") ;


function formulaires_configurer_ckeditor_p7_charger_dist() {
	($cfg = lire_config("ckeditor")) || ($cfg = array()) ;
	$plugin_dir = find_in_path('plugins/ckeditor') ;
	$plugins_cpt = 0 ;
	if (is_dir($plugin_dir)) {
		$dir = @opendir($plugin_dir) ;
		$fieldsets_plugin = array() ;
		while($item = @readdir($dir)) {

			if (is_file($plugin_dir.'/'.$item.'/plugin.js')) {
				$plugins_cpt++ ;
				$plugins_list[$plugins_cpt] = $item ;

			}
		}
		$error = 'ckeditor:aucun_plugin' ;
	} else {
		$error = 'ckeditor:aide_plugin' ;
	}
	if ($plugins_cpt>0) {
		$valeurs = array(
			'plugin_position_reference' => $cfg['plugin_position_reference'],
			'pluginbarreposition' => $cfg['pluginbarreposition']
		) ;
		$fieldsets_plugin[] = array(
			'saisie' => 'hidden', 
			'options' => array(
				'nom' => 'liste_plugins'
			)
		) ;
		$valeurs['liste_plugins'] = serialize($plugins_list) ;
		$datas = range(1, $plugins_cpt) ;
		foreach($plugins_list as $cpt => $nom) {
			$valeurs['chemin_'.$cpt] = $plugin_dir.'/'.$nom ;
			$valeurs['actif_'.$cpt] = $cfg['plugins'][$nom]['actif']?'on':'off' ;
			$valeurs['bouton_'.$cpt] = $cfg['plugins'][$nom]['bouton']?'on':'off' ;
			$valeurs['nom_bouton_'.$cpt] = $cfg['plugins'][$nom]['nom_bouton'] ;
			$valeurs['ordre_bouton_'.$cpt] = $cfg['plugins'][$nom]['ordre_bouton'] ;
			$fieldsets_plugin[] = array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'plugin_fs_'.$nom,
					'label' => 'plugin : '.ucfirst($nom),
					'pliable' => 'oui', 'plie' => 'nom'
				),
				'saisies' => array(
					array(
						'saisie' => 'hidden',
						'options' => array(
							'nom' => 'chemin_'.$cpt
						)
					),
					array(
						'saisie' => 'case',
						'options' => array(
							'nom' => 'actif_'.$cpt,
							'label_case' => _T('ckeditor:plugin_active')
						)
					),
					array(
						'saisie' => 'case',
						'options' => array(
							'nom' => 'bouton_'.$cpt,
							'label_case' => _T('ckeditor:plugin_bouton')
						)
					),
					array(
						'saisie' => 'input',
						'options' => array(
							'nom' => 'nom_bouton_'.$cpt,
							'label' => _T('ckeditor:nom_du_bouton')
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'ordre_bouton_'.$cpt,
							'label' => _T('ckeditor:ordre_du_bouton'),
							'datas' => $datas
						)
					)
				)
			) ;

		}
		
		$toolbar_datas = array() ;

		foreach($GLOBALS['toolbars'] as $toolbar) {
			foreach($toolbar as $tool => $size) {
				if (!ckeditor_tweaks_actifs('smileys') && ($tool == 'Smiley')) continue ;
				$toolbar_datas[$tool] = _T('ckeditor:tool_'.$tool) ;
			}
		}

		$valeurs['saisies_cke_page7'] = array(
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'liste_ck_plugins',
					'label' => _T('ckeditor:liste_plugins_ckeditor')
				),
				'saisies' => $fieldsets_plugin
			),
			array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'options_position',
					'label' => _T('ckeditor:plugins_barre_position')
				),
				'saisies' => array(
					array(
						'saisie' => 'radio',
						'options' => array(
							'nom' => 'pluginbarreposition',
							'defaut' => 'apres',
							'datas' => array(
								'avant'=>_T('ckeditor:avant'),
								'apres'=>_T('ckeditor:apres')
							)
						)
					),
					array(
						'saisie' => 'selection',
						'options' => array(
							'nom' => 'plugin_position_reference',
							'datas' => $toolbar_datas
						)
					)
				)
			)
		) ;
	} else {
		$valeurs['saisies_cke_page7'] = array(
			array(
				'saisie' => 'explication_spip',
				'options' => array(
					'nom' => 'erreur_plugin',
					'texte' => _T($error)
				)
			)
		) ;
	}
	return $valeurs ;
}

function formulaires_configurer_ckeditor_p7_verifier_dist() {
	$result = array() ;
	if (count($result)) {
		$result['message_erreur'] = _T('ckeditor:ck_ko').
			"<ul>\n".
				(count($result)?'<li>'.join("</li>\n<li>", $result)."</li>\n":'').
			"</ul>\n" ;
	}	
	return $result ;
}

function formulaires_configurer_ckeditor_p7_traiter_dist() {
	$result = array() ;
	$plugins_list = unserialize(_request('liste_plugins')) ;
	$plugins = array() ;
	foreach($plugins_list as $cpt => $nom) {
		$plugins[$nom]['actif'] = _request('actif_'.$cpt)=='on'?'1':null ;
		$plugins[$nom]['bouton'] = _request('bouton_'.$cpt)=='on'?'1':null ;
		$plugins[$nom]['nom_bouton'] = _request('nom_bouton_'.$cpt) ;
		$plugins[$nom]['ordre_bouton'] = _request('ordre_bouton_'.$cpt) ;
		$plugins[$nom]['chemin'] = _request('chemin_'.$cpt) ;
	}
	ecrire_config("ckeditor/plugins", $plugins) ;
	ecrire_config("ckeditor/plugin_position_reference", _request("plugin_position_reference")) ;
	ecrire_config("ckeditor/pluginbarreposition", _request("pluginbarreposition")) ;
	return $result ;
}

 ?>
