<?php
  if (!defined("_ECRIRE_INC_VERSION")) return;
  
	function aff_zone_installation($num_version){
		include_spip('base/aff_zone_tables');
    include_spip('base/create');
    include_spip('base/abstract_sql');
    
  // création de spip_mots_syndic_articles si la table n'existe pas (?)
    creer_base();
    
  // création du groupe de mots clé et des mots clés de statut
    $Terreur = array();
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
    
  // syndication de la zone dans la rubrique 1
    sql_insertq('spip_syndic', 
                  array('id_rubrique'=>1, 'id_secteur'=>1, 'nom_site'=>'SPIP-zone - liste des plugins',
                        'url_site'=>'http://trac.rezo.net/trac/spip-zone/browser/_plugins_',
                        'url_syndic'=>'http://files.spip.org/spip-zone/ref.rss.xml.gz',
                        'syndication'=>'oui', 'statut'=>'publie', 'resume'=>'non'
                  )
               );
    if (mysql_error() != '') $Terreurs[] = 'erreur enragistrement de la syndication de la Zone '.$st.': '.mysql_error();
    
  // stocker le num de version dans spip_meta
    ecrire_meta('aff_zone_version',$num_version);
    
    if (count($Terreurs) != 0) echo implode('<br>',$Terreurs);
	}
	
	function aff_zone_desinstallation() {
//		spip_query("DROP TABLE spip_mots_syndic_articles");
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

	  // définir comme constante le chemin du répertoire du plugin
      $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
      $pp = explode("/", end($p));
      define('_DIR_PLUGIN_AFF_ZONE',(_DIR_PLUGINS.$pp[0]));
      
    // récupérer le numéro de version dans plugin.xml
      $Tlecture_fich_plugin = file(_DIR_PLUGIN_AFF_ZONE.'/plugin.xml');
      $stop_prochain = 0;
      foreach ($Tlecture_fich_plugin as $ligne) {
          if ($stop_prochain == 1) {
           $version_script = $ligne;
           break;
          }
          if (substr_count($ligne, '<version>') > 0) {
           $stop_prochain = 1;
          }
      }
    
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