<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function ieconfig_saisies_export() {
$saisies = array (
		// Options d'export
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'ieconfig_export',
				'label' => '<:ieconfig:label_ieconfig_export:>',
				'icone' => 'img/ieconfig-export.png'
			),
			'saisies' => array(
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'ieconfig_export_nom',
						'label' => '<:ieconfig:label_ieconfig_export_nom:>',
						'obligatoire' => 'oui',
						'defaut' => $GLOBALS['meta']['nom_site'].' - '.date('Y/m/d')
					)
				),
				array(
					'saisie' => 'textarea',
					'options' => array(
						'nom' => 'ieconfig_export_description',
						'label' => '<:ieconfig:label_ieconfig_export_description:>',
						'rows' => 4
					)
				),
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'ieconfig_export_explication',
						'texte' => '<:ieconfig:texte_ieconfig_export_explication:>'
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'ieconfig_export_choix',
						'label' => '<:ieconfig:label_ieconfig_export_choix:>',
						'cacher_option_intro' => 'oui',
						'defaut' => 'telecharger',
						'datas' => array(
							'sauvegarder' => '<:ieconfig:item_sauvegarder:>',
							'telecharger' => '<:ieconfig:item_telecharger:>'
						)
					)
				)
			)
		)
	);
	// Gestion des plugins utilisant le pipeline ieconfig_metas
	$ieconfig_metas = array();
	foreach(pipeline('ieconfig_metas',array()) as $prefixe => $data){
		if (isset($data['icone'])) {
			$icone = chemin_image($data['icone']);
			if (!$icone) $icone = find_in_path($data['icone']);
			if ($icone) $icone = '<img src="'.$icone.'" alt="" style="margin-left:-50px; margin-right:34px;" />';
		} else $icone= '';
		$ieconfig_metas[$prefixe] = $icone . (isset($data['titre']) ? $data['titre'] : $prefixe);
	}
	if (count($ieconfig_metas)>0)
		$saisies[] = array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'metas_fieldset',
				'label' => _T('ieconfig:label_configurations_a_exporter'),
				'icone' => 'config-export-16.png'
			),
			'saisies' => array(
				array(
					'saisie' => 'checkbox',
					'options' => array(
						'nom' => 'export_metas',
						'label' => _T('ieconfig:label_configurations_a_exporter'),
						'tout_selectionner' => 'oui',
						'datas' => $ieconfig_metas
					)
				)
			)
		);
	
	// On passe via le pipeline ieconfig (pour les cas particuliers)
	$saisies = pipeline('ieconfig',array(
		'args' => array(
			'action' => 'form_export'
		),
		'data' => $saisies
	));
	return $saisies;
}

function formulaires_ieconfig_export_charger_dist() {
	$saisies = ieconfig_saisies_export();
	$contexte = array(
		'_saisies' => $saisies
	);
	
	return array_merge(saisies_charger_champs($saisies),$contexte);
}

function formulaires_ieconfig_export_verifier_dist() {
	include_spip('inc/saisies');
	return saisies_verifier(ieconfig_saisies_export());
}

function formulaires_ieconfig_export_traiter_dist() {
	$export = array();
	$export['nom'] = _request('ieconfig_export_nom');
	if (_request('ieconfig_export_description') != '')
		$export['description'] = _request('ieconfig_export_description');
	
	// On passe via le pipeline ieconfig
	$export = pipeline('ieconfig',array(
		'args' => array(
			'action' => 'export'
		),
		'data' => $export
	));
	
	// Gestion des plugins utilisant le pipeline ieconfig_metas
	$export_metas = _request('export_metas');
	if (!is_array($export_metas)) $export_metas = array();
	
	foreach(pipeline('ieconfig_metas',array()) as $prefixe => $data){
		if(in_array($prefixe,$export_metas)) {
			$export_plugin = array();
			if(isset($data['metas_brutes']))
				foreach(explode(',',$data['metas_brutes']) as $meta) {
					// On teste le cas ou un prefixe est indique (dernier caractere est *)
					if (substr($meta,-1)=='*') {
						$p = substr($meta,0,-1);
						foreach ($GLOBALS['meta'] as $m => $v) {
							if (substr($m,0,strlen($p))==$p)
								$export_plugin[$m] = $v;
						}
					} elseif (isset($GLOBALS['meta'][$meta]))
						$export_plugin[$meta] = $GLOBALS['meta'][$meta];
				}
			if(isset($data['metas_serialize']))
				foreach(explode(',',$data['metas_serialize']) as $meta) {
					// On teste le cas ou un prefixe est indique (dernier caractere est *)
					if (substr($meta,-1)=='*') {
						$p = substr($meta,0,-1);
						foreach ($GLOBALS['meta'] as $m => $v) {
							if (substr($m,0,strlen($p))==$p)
								$export_plugin[$m] = unserialize($v);
						}
					} elseif (isset($GLOBALS['meta'][$meta]))
						$export_plugin[$meta] = unserialize($GLOBALS['meta'][$meta]);
				}
			if (count($export_plugin)>0)
				$export[$prefixe] = $export_plugin;
		}
	}
	
	// On encode en yaml
	include_spip('inc/yaml');
	$export = yaml_encode($export,array('inline'=>20));
	
	// Nom du fichier
	include_spip('inc/texte');
	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(array(",\W,is",",_(?=_),",",_$,"),array("_","",""), couper(translitteration(trim($GLOBALS['meta']['nom_site'])),30,""))
	  : 'spip';
	$filename = $site.'_'.date('Y-m-d_H-i').'.yaml';
	
	// Si telechargement
	if(_request('ieconfig_export_choix')=='telecharger') {
		refuser_traiter_formulaire_ajax();
		set_request('action', 'courcircuiter_affichage_usage_memoire'); // Pour empêcher l'extension dev d'ajouter un div avec l'usage mémoire.
		Header("Content-Type: text/x-yaml;");
		Header("Content-Disposition: attachment; filename=$filename");
		Header("Content-Length: ".strlen($export));
		echo $export;
		exit;
	} else {
		sous_repertoire(_DIR_TMP, 'ieconfig');
		if (ecrire_fichier(_DIR_TMP . 'ieconfig/'.$filename , $export))
			return array('message_ok' => _T('ieconfig:message_ok_export',array('filename'=>$filename)));
		else
			return array('message_erreur' => _T('ieconfig:message_erreur_export',array('filename'=>$filename)));
	}
}

?>