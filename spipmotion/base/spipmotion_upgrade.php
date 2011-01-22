<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

function spipmotion_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/spipmotion');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		if (version_compare($current_version,'0.0','<=')){
			creer_base();
			maj_tables('spip_documents');
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);

			$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
			$ffmpeg_binaires('',true);
			
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['ts'] = 'video/MP2T';
			$tables_mime['mts'] = 'video/MP2T';
			$tables_mime['m2ts'] = 'video/MP2T';
			
			$tables_sequences['ts'] = 'MPEG transport stream';
			$tables_sequences['mts'] = 'AVCHD MPEG-2 transport stream';
			$tables_sequences['m2ts'] = 'BDAV MPEG-2 Transport Stream';
			
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

			echo '<p>'._T('spipmotion:install_creation_base').'</p>';
			echo '<p>'._T('spipmotion:install_ajout_champs_documents').'</p>';
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			sql_alter("TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT '0' AFTER `id_article`");
			sql_alter("TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )");
			ecrire_meta($nom_meta_base_version,$current_version=0.2);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.2)).'</p>';
		}
		if (version_compare($current_version,'0.3','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.3);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.3)).'</p>';
		}
		if (version_compare($current_version,'0.4','<')){
			sql_alter("TABLE spip_spipmotion_attentes CHANGE `id_article` `id_objet` BIGINT(21) NOT NULL DEFAULT '0'");
			sql_alter("TABLE spip_spipmotion_attentes ADD `objet` VARCHAR(25) AFTER `id_objet`");
			ecrire_meta($nom_meta_base_version,$current_version=0.4);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.4)).'</p>';
		}
		if (version_compare($current_version,'0.5','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.5);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.5)).'</p>';
		}
		if (version_compare($current_version,'0.6','<')){
			maj_tables('spip_spipmotion_attentes');
			ecrire_meta($nom_meta_base_version,$current_version=0.6);
			echo '<p>'._T('spipmotion:install_maj_base',array('version'=>0.6)).'</p>';
		}
		if (version_compare($current_version,'0.7','<')){
			maj_tables('spip_documents');
			ecrire_meta($nom_meta_base_version,$current_version=0.7);
		}
		if (version_compare($current_version,'0.7.1','<')){
			/**
			 * Récupérer la configuration de FFMPEG sur le système et la mettre dans les métas
			 */
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);
			ecrire_meta($nom_meta_base_version,$current_version='0.7.1');
		}
		if (version_compare($current_version,'0.7.2','<')){
			/**
			 * On change le champs pixelformat
			 */
			sql_alter("TABLE spip_documents CHANGE `pixelformat` `pixelformat` VARCHAR(255) DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.2');
		}
		if (version_compare($current_version,'0.7.3','<')){
			/**
			 * On récupère les informations de spipmotion si possible
			 */
			$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
			$ffmpeg_infos(true);

			$ffmpeg_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
			$ffmpeg_binaires('',true);

			maj_tables('spip_spipmotion_attentes');
			/**
			 * On invalide le cache
			 */
			include_spip('inc/invalideur');
			suivre_invalideur("1");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.3');
		}
		if (version_compare($current_version,'0.7.4','<')){
			sql_alter("TABLE spip_documents CHANGE `framerate` `framerate` FLOAT");
			ecrire_meta($nom_meta_base_version,$current_version='0.7.4');
		}
		if (version_compare($current_version,'0.7.5','<')){
			global $tables_images, $tables_sequences, $tables_documents, $tables_mime;
			$tables_mime['ts'] = 'video/MP2T';
			$tables_mime['mts'] = 'video/MP2T';
			$tables_mime['m2ts'] = 'video/MP2T';
			
			$tables_sequences['ts'] = 'MPEG transport stream';
			$tables_sequences['mts'] = 'AVCHD MPEG-2 transport stream';
			$tables_sequences['m2ts'] = 'BDAV MPEG-2 Transport Stream';
			
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
			ecrire_meta($nom_meta_base_version,$current_version='0.7.5');
		}
		/**
		 * TODO : générer un htaccess dans le répertoire script_bash/
		 * TODO : insérer une préconfiguration par défaut
		 */
	}
}

/**
 * 
 * Désinstallation du plugin
 * 
 * On supprime : 
 * -* Les metas de configuration
 * -* Les metas de vérification des programmes
 * -* La table spip_spipmotion_attentes
 * 
 * On laisse :
 * -* Les nouveaux champs sur la table spip_documents
 * 
 * @param float $nom_meta_base_version
 */
function spipmotion_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table("spip_spipmotion_attentes");
	effacer_meta($nom_meta_base_version);
	effacer_meta('spipmotion_ffmpeg_casse');
	effacer_meta('spipmotion_casse');
	effacer_meta('spipmotion_ffmpeg2theora_casse');
	effacer_meta('spipmotion_flvtool_casse');
	effacer_meta('spipmotion_qt-faststart_casse');
	effacer_meta('spipmotion_spipmotionsh_casse');
	effacer_meta('spipmotion_ffmpeg-php_casse');
	effacer_meta('safe_mode');
	effacer_meta('spipmotion_safe_mode_exec_dir');
}
?>