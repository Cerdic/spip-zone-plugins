<?php

  if (!defined("_ECRIRE_INC_VERSION")) return;

	function csv2spip_installation($num_version){
  // creer la table spip_tmp_csv2spip à l'install du plugin plutôt qu'à chaque utilisation du plugin
		include_spip('inc/csv2spip_tables');
    include_spip('base/create');
    include_spip('base/abstract_sql');
    creer_base();
     
  // syndication de la zone dans la rubrique 1
/*    if (sql_countsel('spip_syndic', "url_syndic = 'http://files.spip.org/spip-zone/ref.rss.xml.gz'") == 0) {
        sql_insertq('spip_syndic', 
                      array('id_rubrique'=>1, 'id_secteur'=>1, 'nom_site'=>'SPIP-zone - liste des plugins',
                            'url_site'=>'http://trac.rezo.net/trac/spip-zone/browser/_plugins_',
                            'url_syndic'=>'http://files.spip.org/spip-zone/ref.rss.xml.gz',
                            'date'=>date('Y-m-d H:i:s'),
                            'syndication'=>'oui', 'statut'=>'publie', 'resume'=>'non'
                      )
                   );
        if (sql_error() != '') $Terreurs[] = 'erreur enregistrement de la syndication de la Zone '.$st.': '.sql_error();
    }
*/    
  // stocker le num de version dans spip_meta
    ecrire_meta('csv2spip_version',$num_version);
    
    if (count($Terreurs) != 0) echo implode('<br>',$Terreurs);
    
	}
	
	function csv2spip_desinstallation() {
		effacer_meta('csv2spip_version');
		ecrire_metas();
	}

	function csv2spip_install($action){
    // vérifier les droits
    global $connect_statut, $connect_toutes_rubriques;
    include_spip('inc/autoriser');
    if(!autoriser('webmestre')) {
        include_spip('inc/minipres');
        echo minipres();
        die(_T('csvspip:reserve_webmestres'));
    }

    // récupérer le numéro de version
      $Tplugins_actifs = liste_plugin_actifs();
      $version_script = $Tplugins_actifs['CSV2SPIP']['version'];

    // install/désinstall ? 
		  switch ($action){
			case 'install':
				if (lire_meta('csv2spip_version') != $version_script) csv2spip_installation($version_script);
				break;
			case 'uninstall':
				csv2spip_desinstallation();
				break;
		}
	}

?>
