<?php
include_spip('inc/meta');

// Installation et mise à jour
function contact_upgrade($nom_meta_version_base, $version_cible){

	$version_actuelle = '0.0';
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		
		if (version_compare($version_actuelle,'0.0','=')){
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
	$maj = array();
	$maj['create'] = array(		
		array('maj_tables',array('spip_messages')),
	);

	include_spip('maj/svn10000'); //pour maj_liens
	$maj['0.2.0'] = array(	
		array('maj_liens','auteur','message'),
		array('sql_drop_table',"spip_auteurs_messages"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
	}

}

// Désinstallation
function contact_vider_tables($nom_meta_version_base){

	include_spip('base/abstract_sql');
	
	// On recupere tous les messages de contact
	$messages = sql_allfetsel(
		'id_message',
		'spip_messages',
		'type = '.sql_quote('contac')
	);
	$messages = array_map('reset', $messages);
	$in = sql_in(
		'id_messages',
		$messages
	);
	
	// On supprime les messages
	sql_delete(
		'spip_messages',
		'type = '.sql_quote('contact')
	);
	// On supprime les liens
	sql_delete('spip_auteurs_liens', array($in, "objet='message'"));
		
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);

}

?>
