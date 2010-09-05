<?php
/**
* Plugin GetID3
* par BoOz, kent1
*
* Copyright (c) 2007-2010
* Logiciel libre distribué sous licence GNU/GPL.
*
* Définition des tables
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function getid3_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			echo '<p>'._T('getid3:install_ajout_champs_documents').'</p>';
		}
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.1);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.1'));
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/create');
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.2'));
		}
		if (version_compare($current_version,'0.3','<')){
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['3ga'] = 'audio/3ga';
			$tables_mime['aac'] = 'audio/x-aac';
			$tables_mime['ac3'] = 'audio/x-aac';
			$tables_mime['aifc'] = 'audio/x-aifc';
			$tables_mime['amr'] = 'audio/amr';
			$tables_mime['ape'] = 'audio/x-monkeys-audio';
			$tables_mime['m4r'] = 'audio/aac';
			$tables_mime['wma'] = 'audio/x-ms-wma';
			
			$tables_sequences['3ga'] = '3GP Audio File';
			$tables_sequences['aac'] = 'Advanced Audio Coding';
			$tables_sequences['ac3'] = 'AC-3 Compressed Audio';
			$tables_sequences['aifc'] = 'Compressed AIFF Audio';
			$tables_sequences['amr'] = 'Adaptive Multi-Rate Audio';
			$tables_sequences['ape'] = 'Monkey\'s Audio File';
			$tables_sequences['m4r'] = 'iPhone Ringtone';
			$tables_sequences['wma'] = 'Windows Media Audio';
			
			// Init ou Re-init ==> replace pas insert
		
			$freplace = sql_serveur('replace', $serveur);
			spip_log($tables_mime,'id3');
			foreach ($tables_mime as $extension => $type_mime) {
				if (isset($tables_images[$extension])) {
					$titre = $tables_images[$extension];
					$inclus='image';
				}
				else if (isset($tables_sequences[$extension])) {
					$titre = $tables_sequences[$extension];
					$inclus='embed';
				}
				else {
					$inclus='non';
					if (isset($tables_documents[$extension]))
						$titre = $tables_documents[$extension];
					else
						$titre = '';
				}
		
				$freplace('spip_types_documents',
					array('mime_type' => $type_mime,
						'titre' => $titre,
						'inclus' => $inclus,
						'extension' => $extension,
						'upload' => 'oui'
					),
					'', $serveur);
			}
			
			ecrire_meta($nom_meta_base_version,$current_version=0.3);
			echo _T('getid3:install_mise_a_jour_base',array('version'=>'0.3'));
		}
	}
}

function getid3_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>