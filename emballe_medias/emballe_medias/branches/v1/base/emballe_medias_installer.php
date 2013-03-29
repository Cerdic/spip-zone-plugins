<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * Quentin Drouet (kent1@arscenic.info)
 *
 * © 2008/2011 - Distribue sous licence GNU/GPL
 *
 * Installation du plugin
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'installation et de mise à jour du plugin
 * @param string $nom_meta_base_version Le nom de la meta
 * @param string $version_cible La version actuelle
 */
function emballe_medias_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/emballe_medias');
		// cas d'une installation
		if ($current_version==0.0){
			global $tables_principales;
			include_spip('base/create');
			maj_tables('spip_articles');
			/**
			 * On vérifie la présence d'au moins une rubrique sinon on en crée une "Medias"
			 */
			if(!sql_getfetsel('id_rubrique','spip_rubriques')){
				include_spip('action/editer_rubrique');
				$id_rubrique = insert_rubrique('0');
				revisions_rubriques($id_rubrique,array('titre'=>'Medias'));
			}else{
				$id_rubrique = 1;
			}
			$meta_config = array(
				'fichiers'=> array(
					'fichiers_videos' => array('flv','mp4,ogv'),
					'fichiers_audios' => array('mp3','ogg'),
					'fichiers_images' => array('jpg','png','gif'),
					'fichiers_textes' => array('doc','odt','pdf'),
					'file_size_limit' => @ini_get('upload_max_filesize') ? ((str_replace('M','',@ini_get('upload_max_filesize')) < str_replace('M','',@ini_get('post_max_size'))) ? str_replace('M','',@ini_get('upload_max_filesize')) : str_replace('M','',@ini_get('post_max_size'))) : '2',
					'file_upload_limit' => '1',
					'file_queue_limit' => '1'
				),
				'styles' => array(
					'largeur_img_previsu' => '450',
					'hauteur_img_previsu' => '450'
				)
			);
			if(is_array(lire_config('emballe_medias'))){
				$meta_config = array_merge($meta_config,lire_config('emballe_medias'));
			}
			ecrire_meta('emballe_medias',serialize($meta_config),'non');
			
			/**
			 * On ajoute les documents epub et lyx
			 */
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['epub'] = 'application/epub+zip';
			$tables_mime['lyx'] = 'application/x-lyx';

			$tables_documents['epub'] = 'Electronic publication';
			$tables_documents['lyx'] = 'Lyx file';
						
			// Init ou Re-init ==> replace pas insert
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
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}
		if (version_compare($current_version,"0.2","<")){
			if(!lire_config('emballe_medias')){
				/**
				 * On vérifie la présence d'au moins une rubrique sinon on en crée une "Medias"
				 */
				if(!sql_getfetsel('id_rubrique','spip_rubriques')){
					include_spip('action/editer_rubrique');
					$id_rubrique = insert_rubrique('0');
					revisions_rubriques($id_rubrique,array('titre'=>'Medias'));
				}else{
					$id_rubrique = 1;
				}
				$meta_config = array(
					'fichiers'=> array(
						'fichiers_videos' => array('flv','mp4','ogv'),
						'fichiers_audios' => array('mp3','ogg'),
						'fichiers_images' => array('jpg','png','gif'),
						'fichiers_textes' => array('doc','odt','pdf'),
						'file_size_limit' => @ini_get('upload_max_filesize') ? ((str_replace('M','',@ini_get('upload_max_filesize')) < str_replace('M','',@ini_get('post_max_size'))) ? str_replace('M','',@ini_get('upload_max_filesize')) : str_replace('M','',@ini_get('post_max_size'))) : '2',
						'file_upload_limit' => '1',
						'file_queue_limit' => '1'
					),
					'styles' => array(
						'largeur_img_previsu' => '450',
						'hauteur_img_previsu' => '450'
					)
				);
				ecrire_meta('emballe_medias',serialize($meta_config),'non');
			}
			ecrire_meta($nom_meta_base_version,$current_version="0.2");
		}
		if (version_compare($current_version,'0.2.1','<')){
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['lyx'] = 'application/x-lyx';
			
			$tables_documents['lyx'] = 'Lyx file';
			
			// Init ou Re-init ==> replace pas insert
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
			ecrire_meta($nom_meta_base_version,$current_version="0.2.1");
		}
		if (version_compare($current_version,'0.2.2','<')){
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['epub'] = 'application/epub+zip';
			
			$tables_documents['epub'] = 'Electronic publication';
			
			// Init ou Re-init ==> replace pas insert
		
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
			ecrire_meta($nom_meta_base_version,$current_version="0.2.2");
		}
	}
}

/**
 * La fonction de désinstallation du plugin
 * @param string $nom_meta_base_version Le nom de la méta
 */
function emballe_medias_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_articles DROP em_type");
	effacer_meta($nom_meta_base_version);
}
?>