<?php
/**
 * Plugin Emballe Medias
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 * b_b (http://http://www.weblog.eliaz.fr)
 *
 * © 2008/2012 - Distribue sous licence GNU/GPL
 *
 * Installation du plugin
 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'installation et de mise à jour du plugin
 * @param string $nom_meta_base_version Le nom de la meta
 * @param string $version_cible La version actuelle
 */
function emballe_medias_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_articles')),
		array('emballe_medias_initialiser',array())
	);
	$maj['0.2.3'] = array(
		array('emballe_medias_initialiser',array()),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * La fonction de désinstallation du plugin
 * @param string $nom_meta_base_version Le nom de la méta
 */
function emballe_medias_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_articles DROP em_type");
	effacer_meta('emballe_medias');
	effacer_meta($nom_meta_base_version);
}

function emballe_medias_initialiser(){
	include_spip('inc/config');
	if(!lire_config('emballe_medias')){
		/**
		 * On vérifie la présence d'au moins une rubrique sinon on en crée une "Medias"
		 */
		if(!sql_getfetsel('id_rubrique','spip_rubriques')){
			include_spip('action/editer_rubrique');
			$id_rubrique = rubrique_inserer('0');
			rubrique_modifier($id_rubrique,array('titre'=>'Medias'));
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
}
?>