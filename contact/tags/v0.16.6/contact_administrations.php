<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');

// Installation et mise à jour
function contact_upgrade($nom_meta_version_base, $version_cible) {

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_messages')),
	);

	include_spip('maj/svn10000'); //pour maj_liens
	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_messages')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);

}

// Désinstallation
function contact_vider_tables($nom_meta_version_base) {

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

	// Pour les liens, id_message est id_objet/objet
	$in_messages = sql_in(
		'id_objet',
		$messages
	);

	// On supprime les documents qui ne sont rattaches qu'aux messages
	// ainsi que leur liens en passant par supprimer_lien_document
	include_spip('action/dissocier_document');
	$s = sql_select(
		array('id_document','id_objet'),
		'spip_documents_liens',
		$in_messages." AND objet='message'"
	);
	while ($t = sql_fetch($s)) {
		supprimer_lien_document($t['id_document'], 'message', $t['id_objet'], true);
	}
	// On supprimer les liens avec les auteurs
	sql_delete('spip_auteurs_liens', $in_messages." AND objet='message'");

	// On supprime les messages, mais pas la table qui peut etre utilise par organiseur
	sql_delete(
		'spip_messages',
		'type = '.sql_quote('contac')
	);
	// On efface la version entregistrée
	effacer_meta($nom_meta_version_base);

}
