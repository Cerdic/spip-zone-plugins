<?php
/**
 * Plugin Guestbook
 * (c) 2013 Yohann Prigent (potter64), Stephane Santon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function guestbook_v2_convert() {
	include_spip('base/upgrade');

	echo "<h3>Conversion de la table de Guestbook V2...</h3>";

	echo "Table spip_guestbook : Renommage des champs...<br/>";
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN id_message id_guestmessage bigint(21) NOT NULL");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN message guestmessage text NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN email email varchar(255) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN nom nom varchar(100) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN prenom prenom varchar(100) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN pseudo pseudo varchar(100) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN statut statut varchar(20) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN ip ip varchar(15) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN note note int(2)");
	sql_alter( "TABLE spip_guestbook CHANGE COLUMN date date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
	echo "Table spip_guestbook : Renommage de la table...<br/>";
	sql_alter( "TABLE spip_guestbook RENAME spip_guestmessages");

	echo "Table spip_guestbook_reponses : Renommage des champs...<br/>";
	sql_alter( "TABLE spip_guestbook_reponses CHANGE COLUMN id_reponse id_guestreponse bigint(21) NOT NULL");
	sql_alter( "TABLE spip_guestbook_reponses CHANGE COLUMN id_message id_guestmessage bigint(21) NOT NULL");
	sql_alter( "TABLE spip_guestbook_reponses CHANGE COLUMN message guestreponse text NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook_reponses CHANGE COLUMN statut statut varchar(20) NOT NULL DEFAULT ''");
	sql_alter( "TABLE spip_guestbook_reponses CHANGE COLUMN date date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
	echo "Table spip_guestbook_reponses : Renommage de la table...<br/>";
	sql_alter( "TABLE spip_guestbook_reponses RENAME spip_guestreponses");
}

/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
**/
function guestbook_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	# quelques exemples
	# (que vous pouvez supprimer !)
	# 
	# $maj['create'] = array(array('creer_base'));
	#
	# include_spip('inc/config')
	# $maj['create'] = array(
	#	array('maj_tables', array('spip_xx', 'spip_xx_liens')),
	#	array('ecrire_config', array('guestbook', array('exemple' => "Texte de l'exemple")))
	#);
	#
	# $maj['1.1.0']  = array(array('sql_alter','TABLE spip_xx RENAME TO spip_yy'));
	# $maj['1.2.0']  = array(array('sql_alter','TABLE spip_xx DROP COLUMN id_auteur'));
	# $maj['1.3.0']  = array(
	#	array('sql_alter','TABLE spip_xx CHANGE numero numero int(11) default 0 NOT NULL'),
	#	array('sql_alter','TABLE spip_xx CHANGE texte petit_texte mediumtext NOT NULL default \'\''),
	# );
	# ...

 	$maj['2.99.0'] = array(
	  array( 'guestbook_v2_convert', array()),
	);
	$maj['3.0.0'] = array(
		array('maj_tables', array('spip_guestmessages', 'spip_guestreponses')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function guestbook_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_guestmessages");
	sql_drop_table("spip_guestreponses");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('guestmessage', 'guestreponse')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('guestmessage', 'guestreponse')));
	sql_delete("spip_forum",                 sql_in("objet", array('guestmessage', 'guestreponse')));

	effacer_meta($nom_meta_base_version);
}

?>