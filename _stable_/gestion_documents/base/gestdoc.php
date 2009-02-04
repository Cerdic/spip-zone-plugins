<?php
/**
 * Plugin Portfolio/Gestion des documents
 * Licence GPL (c) 2006-2008 Cedric Morin, romy.tetue.net
 *
 */

function gestdoc_declarer_tables_interfaces($interface){
	$interface['exceptions_des_tables']['documents']['media']=array('types_documents', 'media');
	return $interface;
}

function gestdoc_declarer_tables_principales($tables_principales){
	
	$tables_principales['spip_types_documents']['field']['media'] = "varchar(10) DEFAULT 'file' NOT NULL";
	$tables_principales['spip_documents']['field']['statut'] = "varchar(10) DEFAULT '0' NOT NULL";
	$tables_principales['spip_documents']['field']['credits'] = "varchar(255) DEFAULT '' NOT NULL";	
	$tables_principales['spip_documents']['field']['date_publication'] = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";
	$tables_principales['spip_documents']['field']['brise'] = "tinyint DEFAULT 0";
	return $tables_principales;
}

function gestdoc_declarer_tables_auxiliaires($tables_auxiliaires){


	return $tables_auxiliaires;
}

function gestdoc_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/abstract_sql');
			
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

			sql_alter("TABLE spip_documents ADD statut varchar(10) DEFAULT '0' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.2",'non');
		}
		if (version_compare($current_version,'0.3','<')){
			include_spip('base/abstract_sql');
			// ajouter un champ
			sql_alter("TABLE spip_documents ADD date_publication datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
			// vider le cache des descriptions de tables
			$trouver_table = charger_fonction('trouver_table','base');
			$trouver_table(false);
			// reinit les statuts pour ceux qui avaient subi une version 0.2 bugguee
			sql_updateq('spip_documents',array('statut'=>'0'));
			// ecrire la version pour ne plus passer la
			ecrire_meta($nom_meta_base_version,$current_version="0.3",'non');
		}			
		if (version_compare($current_version,'0.4','<')){
			// recalculer tous les statuts en tenant compte de la date de publi des articles...
			$res = sql_select('id_document','spip_documents',"statut='0'");
			include_spip('action/editer_document');
			while ($row = sql_fetch($res))
				instituer_document($row['id_document']);
			ecrire_meta($nom_meta_base_version,$current_version="0.4",'non');
		}
		if (version_compare($current_version,'0.5','<')){
			include_spip('base/abstract_sql');
			// ajouter un champ
			sql_alter("TABLE spip_documents ADD brise tinyint DEFAULT 0");
			// vider le cache des descriptions de tables
			$trouver_table = charger_fonction('trouver_table','base');
			$trouver_table(false);
			ecrire_meta($nom_meta_base_version,$current_version="0.5",'non');
		}
		if (version_compare($current_version,'0.6','<')){
			include_spip('base/abstract_sql');
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
			ecrire_meta($nom_meta_base_version,$current_version="0.6",'non');
		}
		if (version_compare($current_version,'0.7','<')){
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_documents ADD credits varchar(255) DEFAULT '' NOT NULL");
			ecrire_meta($nom_meta_base_version,$current_version="0.7",'non');
		}
	}
}

?>