<?php
/**
 * ePUB reader
 * Lecteur de fichiers ePUB
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * 2011-2012 - Distribué sous licence GNU/GPL
 *
 * Script d'installation :
 * Ajout de l'extension epub dans SPIP si elle n'est pas présente
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function epubreader_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			/**
			 * On ajoute le format epub dans la base des types de documents
			 */
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['epub'] = 'application/epub+zip';
			
			$tables_documents['epub'] = 'Electronic publication';
		
			$freplace = sql_serveur('replace', $serveur);
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
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

function epubreader_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}
?>