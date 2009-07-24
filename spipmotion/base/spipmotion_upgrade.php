<?php
	
	$GLOBALS['spipmotion_base_version'] = 0.6;
	function spipmotion_verifier_base(){
		$version_base = $GLOBALS['spipmotion_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['spipmotion_base_version']) )
				|| (($current_version = $GLOBALS['meta']['spipmotion_base_version'])!=$version_base)){
			include_spip('base/spipmotion');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				maj_tables('spip_documents');
				echo 'Cr&eacute;ation de la base de spipmotion<br />Ajout des champs sur spip_documents';
				ecrire_meta('spipmotion_base_version',$current_version=$version_base);
			}
			if ($current_version<0.2){
				sql_alter("TABLE spip_spipmotion_attentes ADD `id_auteur` BIGINT(21) NOT NULL DEFAULT '0' AFTER `id_article`");
				sql_alter("TABLE spip_spipmotion_attentes ADD INDEX ( `id_auteur` )");
				ecrire_meta('spipmotion_base_version',$current_version=0.2);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.2';
			}
			if ($current_version<0.3){
				sql_alter("TABLE spip_documents ADD `duree` VARCHAR(255) DEFAULT '' NOT NULL AFTER `hauteur`");
				sql_alter("TABLE spip_documents ADD `framecount` INTEGER AFTER `duree`");
				sql_alter("TABLE spip_documents ADD `framerate` INTEGER AFTER `framecount`");
				sql_alter("TABLE spip_documents ADD `pixelformat` VARCHAR(255) DEFAULT '' NOT NULL AFTER `framerate`");
				sql_alter("TABLE spip_documents ADD `bitrate` INTEGER AFTER `pixelformat`");
				sql_alter("TABLE spip_documents ADD `videobitrate` INTEGER AFTER `bitrate`");
				sql_alter("TABLE spip_documents ADD `audiobitrate` INTEGER AFTER `videobitrate`");
				sql_alter("TABLE spip_documents ADD `audiosamplerate` INTEGER AFTER `audiobitrate`");
				sql_alter("TABLE spip_documents ADD `videocodec` VARCHAR(255) DEFAULT '' NOT NULL AFTER `audiosamplerate`");
				sql_alter("TABLE spip_documents ADD `audiocodec` VARCHAR(255) DEFAULT '' NOT NULL AFTER `videocodec`");
				sql_alter("TABLE spip_documents ADD `audiochannels` INTEGER AFTER `audiocodec`");
				ecrire_meta('spipmotion_base_version',$current_version=0.3);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.3';
			}
			if ($current_version<0.4){
				sql_alter("TABLE spip_spipmotion_attentes CHANGE `id_article` `id_objet` BIGINT(21) NOT NULL DEFAULT '0'");
				sql_alter("TABLE spip_spipmotion_attentes ADD `objet` VARCHAR(25) AFTER `id_objet`");
				ecrire_meta('spipmotion_base_version',$current_version=0.4);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.4';
			}
			if ($current_version<0.5){
				sql_alter("TABLE spip_documents ADD `id_orig` BIGINT(21) NOT NULL AFTER `audiochannels`");
				ecrire_meta('spipmotion_base_version',$current_version=0.5);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.5';
			}
			if ($current_version<0.6){
				sql_alter("TABLE spip_spipmotion_attentes ADD `extension` VARCHAR(10) DEFAULT '' NOT NULL AFTER `id_auteur`");
				ecrire_meta('spipmotion_base_version',$current_version=0.6);
				echo 'Mise &agrave; jour de la base de spipmotion en 0.6';
			}
			/**
			 * TODO : générer un htaccess dans le répertoire script_bash/
			 * TODO : insérer une préconfiguration par défaut
			 */
			ecrire_metas();
		}
	}
	
	function spipmotion_vider_tables() {
		include_spip('base/spipmotion');
		include_spip('base/abstract_sql');
		sql_drop_table("spip_spipmotion_attentes");
		effacer_meta('spipmotion_base_version');
	}
	
	function spipmotion_install($action){
		$version_base = $GLOBALS['spipmotion_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spipmotion_base_version']) AND ($GLOBALS['meta']['spipmotion_base_version']>=$version_base));
				break;
			case 'install':
				spipmotion_verifier_base();
				break;
			case 'uninstall':
				spipmotion_vider_tables();
				break;
		}
	}	
?>