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
			// Création des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			
			echo "Création des tables de messageries si inexistantes.<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}
		
		/*if (version_compare($version_actuelle,'0.5','<')){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			// Modification de contact
			sql_alter('');
						
			// On change la version
			echo "Mise à jour du plugin contact en version 0.5<br/>";
			ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
		}*/
	
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
