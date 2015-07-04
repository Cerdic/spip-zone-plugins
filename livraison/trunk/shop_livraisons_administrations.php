<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Shop Livraisons
 *
 * @plugin     Shop Livraisons
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Shop_livraison\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Shop Livraisons.
 *
 * Vous pouvez :
 *
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function shop_livraisons_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('inc/config');
	$maj = array();
	
	/*preremplir les objets prix en compte par ce plugin si prix objets l'a déjà défini*/
	$config_livraison=lire_config('shop_livraison',array());	
	if(!isset($config_livraison['objets_livraison'])){
		$config_objets_prix=lire_config('prix_objets/objets_prix','');
		$config_livraison['objets_livraison']=$config_objets_prix?$config_objets_prix:'';		
	}
	

    /*Installation des tables et champs aditionnels*/
	$maj['create'] = array(array('maj_tables', array('spip_livraison_montants', 'spip_livraison_zones','spip_pays')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_pays')));
	$maj['1.2.0'] = array( array('ecrire_config', 'shop_livraison', $config_livraison));

    /*Installation de champs via le plugin champs extras*/
    include_spip('inc/cextras');
    include_spip('base/shop_livraisons');
    if(function_exists(cextras_api_upgrade)){
        cextras_api_upgrade(shop_livraisons_declarer_champs_extras(), $maj['create']);   
        cextras_api_upgrade(shop_livraisons_declarer_champs_extras(), $maj['1.1.5']);
        cextras_api_upgrade(shop_livraisons_declarer_champs_extras(), $maj['1.2.0']);		
    }
    
    
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Shop Livraisons.
 * 
 * Vous devez :
 *
 * - nettoyer toutes les données ajoutées par le plugin et son utilisation
 * - supprimer les tables et les champs créés par le plugin. 
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function shop_livraisons_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_livraison_montants");
	sql_drop_table("spip_livraison_zones");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('spip_livraison_montant', 'spip_livraison_zone')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('spip_livraison_montant', 'spip_livraison_zone')));
	sql_delete("spip_forum",                 sql_in("objet", array('spip_livraison_montant', 'spip_livraison_zone')));

	effacer_meta($nom_meta_base_version);
}

?>