<?php
/**
 * Plugin Agenda pour Spip 2.0
 * Licence GPL
 * 
 *
 */

function gestdoc_declarer_tables_interfaces($interface){
	$interface['exceptions_des_tables']['documents']['media']=array('types_documents', 'media');
	return $interface;
}

function gestdoc_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_types_documents']['field']['media'] = "varchar(10) DEFAULT 'file' NOT NULL";
	return $tables_principales;
}

function gestdoc_declarer_tables_auxiliaires($tables_auxiliaires){


	return $tables_auxiliaires;
}

function gestdoc_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/abstract_sql');
			/*include_spip('base/create');
			creer_base();*/
			
			sql_alter("TABLE spip_types_documents ADD media varchar(10) DEFAULT 'file' NOT NULL");
			// mettre a jour les bonnes valeurs
			// les cas evidents
			sql_updateq('spip_types_documents',array('media'=>'image'),"mime_type REGEXP '^image/'");
			sql_updateq('spip_types_documents',array('media'=>'audio'),"mime_type REGEXP '^audio/'");
			sql_updateq('spip_types_documents',array('media'=>'video'),"mime_type REGEXP '^video/'");
			// les cas particuliers ...
			sql_updateq('spip_types_documents',array('media'=>'video'),"mime_type='application/ogg'");
			sql_updateq('spip_types_documents',array('media'=>'video'),"mime_type='application/x-shockwave-flash'");
			sql_updateq('spip_types_documents',array('media'=>'image'),"mime_type='application/illustrator'");
			sql_updateq('spip_types_documents',array('media'=>'image'),"mime_type='application/illustrator'");
			sql_updateq('spip_types_documents',array('media'=>'video'),"mime_type='application/mp4'");
			
			#ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}
}

?>