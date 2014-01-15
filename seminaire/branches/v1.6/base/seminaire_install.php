<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/cextras_gerer');
include_spip('base/seminaire');

function seminaire_upgrade($nom_meta_base_version,$version_cible){
		 	// forcer l'utilisation des mots clés
    if (lire_meta('articles_mots') == 'non') ecrire_meta('articles_mots', 'oui');	
  //	 création du groupe de mots clé Type d'événement et de ses mots clés	
    $Terreur = array();
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
	};//fin du if

  		
// création du groupe de mots clés Catégorie et de ses mots cles
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
			// insérer des champs dans la table evenements (a conserver pour reference ulterieure)			//	sql_alter("TABLE spip_evenements ADD name text NOT NULL");
			//	sql_alter("TABLE spip_evenements ADD origin text NOT NULL");
			//	sql_alter("TABLE spip_evenements ADD abstract text NOT NULL");
			//	sql_alter("TABLE spip_evenements ADD notes text NOT NULL");
				   
  		// création des champs
	$champs = seminaire_declarer_champs_extras();
	installer_champs_extras($champs, $nom_meta_base_version, $version_cible);
}

function seminaire_vider_tables($nom_meta_base_version) {
	$champs = seminaire_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
}
?>
