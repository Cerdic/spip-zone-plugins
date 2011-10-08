<?php

/**
 * Plugin Simple Calendrier pour Spip 3.0
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

/**
 * Installation/maj des tables forum
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function simplecal_upgrade($nom_meta_base_version,$version_cible){

	// cas particulier :
	// si plugin pas installe mais que la table existe
	// considerer que c'est un upgrade depuis v 1.0.0
	// pour gerer l'historique des installations SPIP <=2.1
	if (!isset($GLOBALS['meta'][$nom_meta_base_version])){
		$trouver_table = charger_fonction('trouver_table','base');
		$trouver_table(''); // vider le cache des descriptions !
		if ($desc = $trouver_table('spip_evenements') and isset($desc['field']['id_article'])){
			ecrire_meta($nom_meta_base_version,'1.0.0');
		}
		// si pas de table en base, on fera une simple creation de base
	}

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_evenements')),
	);
    //1.1
	$maj['1.1.0'] = array(
        array('sql_alter',"TABLE spip_evenements ADD id_secteur bigint(21) NOT NULL DEFAULT '0' AFTER id_evenement"),
        array('sql_alter',"TABLE spip_evenements ADD id_secteur bigint(21) NOT NULL DEFAULT '0' AFTER id_evenement"),
        array('sql_alter',"TABLE spip_evenements ADD id_rubrique bigint(21) NOT NULL DEFAULT '0' AFTER id_secteur"),
        array('sql_alter',"TABLE spip_evenements ADD INDEX id_secteur (id_secteur)"),
        array('sql_alter',"TABLE spip_evenements ADD INDEX id_rubrique (id_rubrique)"),
        array('sql_alter',"TABLE spip_evenements DROP INDEX id_auteur"),
        array('sql_alter',"TABLE spip_evenements DROP id_auteur"),
        array('sql_alter',"TABLE spip_evenements CHANGE id_objet id_objet BIGINT(21) NOT NULL DEFAULT '0'")
	);
	$maj['1.2.0'] = array(
        array('sql_alter',"TABLE spip_evenements CHANGE maj maj timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
    );
    
    //TODO JULIEN : intercaler ici (1.3) la suppression du meta theme prive...
    // Le point d'après devient donc 1.4 (car 1.3 = derniere version sous SPIP 2.1
    
    
    // TODO JULIEN : initialiser ces colonnes avec 'non' et la langue par défaut du site...
    // sinon, on n'a pas le lien "[Changer]" sur le menu de langue
    // TODO JULIEN : migrer dans spip_mots_objets et spip_auteurs_objets
    $maj['1.3.0'] = array(
        array('sql_alter',"TABLE spip_evenements ADD lang varchar(10) NOT NULL DEFAULT '' AFTER statut"),
        array('sql_alter',"TABLE spip_evenements ADD langue_choisie varchar(3) NULL DEFAULT 'non' AFTER lang"),
        array('sql_drop_table',"spip_mots_evenements"),
        array('sql_drop_table',"spip_auteurs_evenements")
    );
    
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
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
    effacer_meta('simplecal_themeprive');
    effacer_meta('simplecal_themepublic');
	effacer_meta($nom_meta_base_version);
}

?>
