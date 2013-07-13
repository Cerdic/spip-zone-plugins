<?php
/**
 * Plugin Séminaire LATP
 * (c) 2012 Amaury Adon
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

	include_spip('inc/cextras');
	include_spip('base/seminaire');
	include_spip('inc/meta');


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function seminaire_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj['create']);
/**activer les mots clés et leur configuration avancée s'ils ne le sont pas déjà**/
	if ($GLOBALS['meta']['articles_mots']!=oui){
	 	ecrire_meta("articles_mots", "oui");
	 	ecrire_meta("config_precise_groupes", "oui");
	 	ecrire_meta("documents_objets", "spip_evenements");	
	 	}
/**activer les révisions sur les événements**/
	// ce qui existe déjà
	$versions = unserialize($GLOBALS['meta']['objets_versions']);
	// ce que j'ajoute
	$versionnage_des_evenements = array('spip_evenements');
	// merge des 2
	$versions = array_merge($versions,$versionnage_des_evenements);
	// balançons dans meta
	ecrire_meta('objets_versions',serialize($versions)); 
 /**Creer le groupe de mots clés Type pour les types d'événements**/
	if (sql_countsel('spip_mots', "titre IN ('seminaire','groupe de travail','evenement important')") == 0) 
   	{
			$id_groupe = sql_insertq('spip_groupes_mots', 
			array('titre'=>'Type', 'descriptif'=>_T('seminaire:mots_cles_techniques_kitcnrs'),'tables_liees'=>'evenements', 'minirezo'=>'oui','comite'=>'oui')
                 );
	if (sql_error() != '') die((_T('seminaire:erreur_install_groupe_technique ')).sql_error());
       
        $Tstatuts = array('séminaire','groupe de travail','événement important');
	foreach ($Tstatuts as $st) 
		{
		sql_insertq('spip_mots', 
			array('titre'=>$st, 'descriptif'=>$st, 'id_groupe'=>$id_groupe, 'type'=>'Type')
				);
		if (sql_error() != '') $Terreur[] = (_T('erreur_creation_mot_cle')).$st.': '.sql_error();
    	};
	};
/** création du groupe de mots clés Catégorie et de ses mots cles pours les équipes **/
    if (sql_countsel('spip_mots', "titre IN ('Algèbre, Dynamique et Topologie','Analyse Appliquée', 'Analyse et Géométrie', 'FRUMAM', 'Géométrie et Singularités', 'Guide d’ondes et milieux stratiﬁés', 'Probabilités et statistiques', 'Séminaire des doctorants', 'Théorie des nombres')") == 0) 
    {
        $id_groupe = sql_insertq('spip_groupes_mots',array('titre'=>'Catégorie', 'descriptif'=> _T('seminaire:mots_cles_categories'), 'tables_liees'=>'articles', 'minirezo'=>'oui','comite'=>'oui')
                  );
        if (sql_error() != '') die((_T('seminaire:erreur_install_groupe_coordonnees')).sql_error());
        
        $Tstatuts = array('Algèbre, Dynamique et Topologie','Analyse Appliquée', 'Analyse et Géométrie', 'FRUMAM', 'Géométrie et Singularités', 'Guide d’ondes et milieux stratiﬁés', 'Probabilités et statistiques', 'Séminaire des doctorants', 'Théorie des nombres');
        foreach ($Tstatuts as $st) {
          sql_insertq('spip_mots', 
              array('titre'=>$st, 'id_groupe'=>$id_groupe, 'type'=>'Catégorie')
                  );
          if (sql_error() != '') $Terreurs[] = (_T('erreur_creation_mot_cle')).$st.': '.sql_error();
        }
    }

	$maj['1.0.1'] = array(
/*Copie de abstract vers descriptif*/
	array('sql_update',"spip_evenements", array('descriptif'=>'abstract')),
	array('sql_alter',"TABLE spip_evenements DROP abstract"),
/*on change name en attendee*/
	array('sql_alter',"TABLE spip_evenements ADD attendee text NOT NULL"),
	array('sql_update',"spip_evenements", array('attendee'=>'name')),
	array('sql_alter',"TABLE spip_evenements DROP name"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function seminaire_vider_tables($nom_meta_base_version) {


	effacer_meta($nom_meta_base_version);
}

?>