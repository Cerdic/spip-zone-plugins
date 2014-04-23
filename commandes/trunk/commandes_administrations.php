<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function commandes_upgrade($nom_meta_base_version, $version_cible) {
    include_spip('commandes_fonctions');
    include_spip('inc/config');
    $maj = array();
    
    $config = lire_config('commandes');
    if (!is_array($config)) {
            $config = array();
        }
    $id_webmestre = commandes_id_premier_webmestre();
    $config = array_merge(array(
                'duree_vie' => '1',
                'activer' => '',
                'quand' => array_keys(commandes_lister_statuts()),
                'expediteur' => 'webmaster',
                'expediteur_webmaster' => $id_webmestre,
                'expediteur_administrateur' => '',
                'expediteur_email' => '',
                'vendeur' => 'webmaster',
                'vendeur_webmaster' => $id_webmestre,
                'vendeur_administrateur' => '',
                'vendeur_email' => '',
                'client' => 'on'
        ), $config);

    $maj['create'] = array(
            array(
                   'maj_tables', array('spip_commandes','spip_commandes_details'),
                   ),
             array(
                   'ecrire_config','commandes',$config
                   ),                  
            );
    $maj['0.2']  = array(  
            array('maj_tables', array('spip_commandes_details'))
            );
    $maj['0.3']  = array(  
           'ecrire_config','commandes',array('duree_vie'=>3600)
        );  
                           
    include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function commandes_vider_tables($nom_meta_base_version) {


	sql_drop_table("spip_commandes,spip_commandes_details");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('commande')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('commande')));

	effacer_meta($nom_meta_base_version);
}

?>