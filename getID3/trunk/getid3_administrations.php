<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function getid3_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents')),
		array('getid3_verifier_binaires',array())
	);
	$maj['0.1'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.2'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.3.1'] = array(
		array('getid3_verifier_binaires',array())
	);
	$maj['0.4.0'] = array(
		array('getid3_upgrade_compat_spipmotion',array())
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function getid3_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

function getid3_verifier_binaires(){
	$getid3_binaires = charger_fonction('getid3_verifier_binaires','inc');
	$getid3_binaires(true);
}

function getid3_upgrade_compat_spipmotion(){
	$desc = sql_showtable('spip_documents', true, $connect);
	/**
	 * Soit on transfère les anciens canaux en audiochannels si le champs audiochannels existe
	 * Soit on fait juste un alter table 
	 */
	if (is_array($desc['field']) && isset($desc['field']['canaux']) && isset($desc['field']['audiochannels'])) {
		$res = sql_select("*","spip_documents","canaux > 0");
		while($row = sql_fetch($res)){
			sql_updateq('spip_documents',array('audiochannels'=>$row['canaux'],'canaux'=>0),'id_document='.intval($row['id_document']));
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter('TABLE spip_documents DROP canaux');
	}else if(isset($desc['field']['canaux'])){
		sql_alter("TABLE spip_documents CHANGE `canaux` `audiochannels` TEXT DEFAULT '' NOT NULL");
	}
	
	/**
	 * Soit on transfère les anciens bitrate_mode en audiobitratemode si le champs audiobitratemode existe
	 * Soit on fait juste un alter table 
	 */
	if (is_array($desc['field']) && isset($desc['field']['bitrate_mode']) && isset($desc['field']['audiobitratemode'])) {
		$res = sql_select("*","spip_documents","bitrate_mode != ''");
		while($row = sql_fetch($res)){
			sql_updateq('spip_documents',array('audiobitratemode'=>$row['bitrate_mode'],'bitrate_mode'=>''),'id_document='.intval($row['id_document']));
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter('TABLE spip_documents DROP bitrate_mode');
	}else if(isset($desc['field']['bitrate_mode'])){
		sql_alter("TABLE spip_documents CHANGE `bitrate_mode` `audiobitratemode` TEXT DEFAULT '' NOT NULL");
	}
	
	/**
	 * On crée le champs audiobitrate s'il n'existe pas
	 * On transfère les anciens bitrate en audiobitratemode dans les champs audiobitrate vides
	 */
	if(is_array($desc['field']) && !isset($desc['field']['audiobitrate'])) {
		sql_alter("TABLE `spip_documents` ADD `audiobitrate` INT NOT NULL"); 
	}
	if (is_array($desc['field']) && isset($desc['field']['bitrate']) && isset($desc['field']['audiobitrate'])) {
		$res = sql_select("*","spip_documents","audiobitrate = '' AND bitrate > 0");
		while($row = sql_fetch($res)){
			sql_updateq('spip_documents',array('audiobitrate'=>$row['bitrate']),'id_document='.intval($row['id_document']));
			if (time() >= _TIME_OUT)
				return;
		}
	}
}
?>