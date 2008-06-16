<?php
  if (!defined("_ECRIRE_INC_VERSION")) return;
  
	function aff_zone_installation($num_version){
		include_spip('base/aff_zone_tables');
    include_spip('base/create');
    include_spip('base/abstract_sql');

  // création de spip_mots_syndic_articles si la table n'existe pas (?)
    creer_base();
    
  // forcer l'utilisation des mots clés
    if (lire_meta('articles_mots') == 'non') ecrire_meta('articles_mots', 'oui');
    
  // forcer l'utilisation des sites syndiqués
    if (lire_meta('activer_sites') == 'non') ecrire_meta('activer_sites', 'oui');
    if (lire_meta('activer_syndic') == 'non') ecrire_meta('activer_syndic', 'oui');
    
  // création du groupe de mots clé et des mots clés de statut
    $Terreur = array();
    if (sql_countsel('spip_mots', "titre IN ('stable','test','dev','experimental')") == 0) {
        sql_insertq('spip_groupes_mots', 
                   array('titre'=>'statut des plugins', 'descriptif'=>'les statuts possibles des plugins')
                  );
        if (mysql_error() != '') die('erreur creation du groupe de mots cles pour les statuts '.mysql_error());
        $id_groupe = mysql_insert_id();
        
        $Tstatuts = array('stable','test','dev','experimental');
        foreach ($Tstatuts as $st) {
          sql_insertq('spip_mots', 
                      array('titre'=>$st, 'id_groupe'=>$id_groupe)
                     );
          if (mysql_error() != '') $Terreurs[] = 'erreur creation du mot cle '.$st.': '.mysql_error();
        }
    }
    
  // syndication de la zone dans la rubrique 1
    if (sql_countsel('spip_syndic', "url_syndic = 'http://files.spip.org/spip-zone/ref.rss.xml.gz'") == 0) {
        sql_insertq('spip_syndic', 
                      array('id_rubrique'=>1, 'id_secteur'=>1, 'nom_site'=>'SPIP-zone - liste des plugins',
                            'url_site'=>'http://trac.rezo.net/trac/spip-zone/browser/_plugins_',
                            'url_syndic'=>'http://files.spip.org/spip-zone/ref.rss.xml.gz',
                            'date'=>date('Y-m-d H:i:s'),
                            'syndication'=>'oui', 'statut'=>'publie', 'resume'=>'non'
                      )
                   );
        if (mysql_error() != '') $Terreurs[] = 'erreur enregistrement de la syndication de la Zone '.$st.': '.mysql_error();
    }
    
  // stocker le num de version dans spip_meta
    ecrire_meta('aff_zone_version',$num_version);
    
    if (count($Terreurs) != 0) echo implode('<br>',$Terreurs);
	}
	
	function aff_zone_desinstallation() {
		effacer_meta('aff_zone_version');
		ecrire_metas();
	}
	
	function aff_zone_install($action){
    // vérifier les droits
      global $connect_statut, $connect_toutes_rubriques;
      if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
          debut_page(_T('titre'), "aff_zone", "plugin");
          echo _T('avis_non_acces_page');
          fin_page();
          exit;
      }

    // récupérer le numéro de version
      $Tplugins_actifs = liste_plugin_actifs();
      $version_script = $Tplugins_actifs['AFF_ZONE']['version'];

    // install/désinstall ? 
		  switch ($action){
			case 'install':
				if (lire_meta('aff_zone_version') != $version_script) aff_zone_installation($version_script);
				break;
			case 'uninstall':
				aff_zone_desinstallation();
				break;
		}
	}	
?>