<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3
 * Licence GNU/GPL
 * 2010-2017
 *
 * cf. paquet.xml pour plus d'infos.
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

 
/**
 * Installation/maj de la table evenement
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function simplecal_upgrade($nom_meta_base_version,$version_cible){

	// cas particulier :
	// si plugin pas installe mais que la table existe
	// considerer que c'est un upgrade depuis v 1.0
	// pour gerer l'historique des installations SPIP <=2.1
	if (!isset($GLOBALS['meta'][$nom_meta_base_version])){
		$trouver_table = charger_fonction('trouver_table','base');
		$trouver_table(''); // vider le cache des descriptions !
		if ($desc = $trouver_table('spip_evenements') and isset($desc['field']['id_article'])){
			ecrire_meta($nom_meta_base_version,'1.0');
		}
		// si pas de table en base, on fera une simple creation de base
	}

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_evenements')),
	);

	$maj['1.1'] = array(
		array('sql_alter',"TABLE spip_evenements ADD id_secteur bigint(21) NOT NULL DEFAULT '0' AFTER id_evenement"),
		array('sql_alter',"TABLE spip_evenements ADD id_rubrique bigint(21) NOT NULL DEFAULT '0' AFTER id_secteur"),
		array('sql_alter',"TABLE spip_evenements ADD INDEX id_secteur (id_secteur)"),
		array('sql_alter',"TABLE spip_evenements ADD INDEX id_rubrique (id_rubrique)"),
		array('sql_alter',"TABLE spip_evenements DROP INDEX id_auteur"),
		array('sql_alter',"TABLE spip_evenements DROP id_auteur"),
		array('sql_alter',"TABLE spip_evenements CHANGE id_objet id_objet BIGINT(21) NOT NULL DEFAULT '0'"),
	);

	$maj['1.2'] = array(
		array('sql_alter',"TABLE spip_evenements CHANGE maj maj timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"),
	);
	
	$maj['1.3'] = array(
		array('simplecal_meta_1_3'),
	);
	
	$maj['1.4'] = array(
		array('sql_alter',"TABLE spip_evenements ADD lien_titre VARCHAR(255) NOT NULL AFTER texte"),
		array('sql_alter',"TABLE spip_evenements ADD lien_url VARCHAR(255) NOT NULL AFTER lien_titre"),
		array('simplecal_meta_1_4'),
	);
	
	// SPIP 3
	include_spip('maj/svn10000'); // migration des mots cles/auteurs
	$maj['2.0.0'] = array(
		array('sql_alter',"TABLE spip_evenements ADD lang varchar(10) NOT NULL DEFAULT '' AFTER statut"),
		array('sql_alter',"TABLE spip_evenements ADD langue_choisie varchar(3) NULL DEFAULT 'non' AFTER lang"),
		array('maj_liens','mot','evenement'),
		array('sql_drop_table',"spip_mots_evenements"),
		array('maj_liens','auteur','evenement'),
		array('sql_drop_table',"spip_auteurs_evenements"),
		array('simplecal_meta_2_0_0'),
	);
	
	$maj['2.1.0'] = array(
		array('sql_alter',"TABLE spip_evenements ADD id_trad bigint(21) NOT NULL DEFAULT '0' AFTER langue_choisie"),
		array('sql_alter',"TABLE spip_evenements ADD INDEX id_trad (id_trad)"),
	);
	
	$maj['2.1.1'] = array(
		array('simplecal_check_2_1_1'),
	);
	
	$maj['2.1.2'] = array(
		array('simplecal_check_2_1_2'),
	);
	$maj['2.1.3'] = array(
		array('sql_alter',"TABLE spip_evenements CHANGE `statut` `statut` VARCHAR(20) NOT NULL DEFAULT '0'"),
		array('sql_alter',"TABLE spip_evenements ADD `horaire` VARCHAR(3) NOT NULL DEFAULT 'oui' AFTER date_fin"),
		array('simplecal_meta_1_5')
	);
	$maj['2.1.4'] = array(
		array('sql_update', 'spip_evenements', array('date_fin' => 'date_debut'), "date_fin = '0000-00-00 00:00:00'"),
	);
	$maj['2.1.5'] = array(
		array('sql_alter',"TABLE spip_evenements CHANGE `lieu` `lieu` TEXT NOT NULL DEFAULT '' AFTER texte"),
		array('sql_alter',"TABLE spip_evenements ADD `adresse` TEXT NOT NULL DEFAULT '' AFTER lieu"),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function simplecal_meta_1_3(){
	effacer_meta('simplecal_themeprive');
}

function simplecal_meta_1_4(){
	ecrire_meta('simplecal_descriptif', 'oui');
	ecrire_meta('simplecal_texte', 'oui');
	ecrire_meta('simplecal_lieu', 'oui');
	ecrire_meta('simplecal_lien', 'non');
}

function simplecal_meta_1_5(){
	ecrire_meta('simplecal_horaire', 'non');
}

function simplecal_meta_2_0_0(){
	// TODO : initialiser la colonne 'lang' avec la langue par defaut du site... 
	// sinon, on n'a pas le lien "[Changer]" sur le menu de langue
}

function simplecal_check_2_1_1(){
	// Pour ceux qui ont fait la 1ere installation sous SPIP3, les champs lien_titre et lien_url
	// n'etaient pas declares (oubli dans base/simplecal.php)...
	$res = spip_query("SHOW FULL COLUMNS FROM spip_evenements LIKE 'lien_titre'");
	if (!$row = sql_fetch($res)){
		sql_alter("TABLE spip_evenements ADD lien_titre VARCHAR(255) NOT NULL AFTER texte");
	}
	$res = spip_query("SHOW FULL COLUMNS FROM spip_evenements LIKE 'lien_url'");
	if (!$row = sql_fetch($res)){
		sql_alter("TABLE spip_evenements ADD lien_url VARCHAR(255) NOT NULL AFTER lien_titre");
	}
}

function simplecal_check_2_1_2(){
	// La meta 'simplecal_rubrique' pouvait prendre les valeurs ['non', 'secteur', 'partout']
	// En attendant https://core.spip.net/issues/2236, on restreint désormais à ['non', 'partout']
	// Du coup, on remplace en base, la valeur 'secteur' par 'partout'.
	
	$row = sql_fetsel("m.nom, m.valeur", "spip_meta as m", "m.nom='simplecal_rubrique'");
	if ($row and $row['valeur'] == "secteur") {
		ecrire_meta('simplecal_rubrique', 'partout');
	}
}

/**
 * Desinstallation/suppression des tables
 *
 * @param string $nom_meta_base_version
 */
function simplecal_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_evenements");
	
	effacer_meta('simplecal_autorisation_redac');
	effacer_meta('simplecal_rubrique');
	effacer_meta('simplecal_refobj');
	effacer_meta('simplecal_themepublic');
	effacer_meta('simplecal_descriptif');
	effacer_meta('simplecal_texte');
	effacer_meta('simplecal_lieu');
	effacer_meta('simplecal_lien');
	
	effacer_meta($nom_meta_base_version);
}
?>