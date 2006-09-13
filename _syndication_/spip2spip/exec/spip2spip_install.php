<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

//  FIXME: 
//  - verifier le groupe - spip2spip - n'a pas deja ete installe (pour eviter de recreer le groupe)


// -------------------------------
// Main: SPIP2SPIP-INSTALL
// -------------------------------

function exec_spip2spip_install(){ 
  global $table_prefix;
  
  //------------------------------- 
  // Main
  //-------------------------------
  debut_page(_T('spiptospip:copy_spip2spip'), "administration", "configuration","contenu");
  echo "<br / ><br />";
  gros_titre(_T('spiptospip:install_spip2spip'));
  debut_gauche();
  debut_boite_info();
  echo _T('spiptospip:intro_spip2spip');
  fin_boite_info();
  
  debut_droite();
  
  //
  // spip2spip installed ?
  $sql = "SELECT COUNT(titre) AS c FROM ".$table_prefix."_groupes_mots WHERE titre='spip2spip'";
  $k = spip_fetch_array(spip_query($sql));
  if ($k['c']==1){
    debut_cadre_relief();
    echo "<div style='color:red'>"._T('spiptospip:installed')."</div>";
    echo "<div style='color:green;margin:10px 0'>"._T('spiptospip:install_spip2spip_99')."</div>";
    fin_cadre_relief();
    fin_page();
    exit;
  }
  
  //
  // install
  
  debut_cadre_relief();
  // creer table spip2spip
  echo "<h4>"._T('spiptospip:install_spip2spip_1')."</h4>\n";
  $sql ="CREATE TABLE ".$table_prefix."_spip2spip (
    `id` int(5) NOT NULL auto_increment,
    `site_titre` varchar(254) NOT NULL default '',
    `site_rss` varchar(254) NOT NULL default '',
    `last_syndic` timestamp,
    PRIMARY KEY  (`id`)
  );";  
  spip_query($sql);
  
  // ajout du groupe mot 
  echo "<h4>"._T('spiptospip:install_spip2spip_3')."</h4>\n";
  $sql = "INSERT INTO ".$table_prefix."_groupes_mots VALUES ('', '- spip2spip -', '".addslashes(_T('spiptospip:install_spip2spip_4'))."', '".addslashes(_T('spiptospip:install_spip2spip_5'))."', 'non', '', 'oui', '', 'oui', '', 'oui', 'oui', 'non', '')";
  spip_query($sql); 
  
  echo "<div style='color:green;margin:10px 0'>"._T('spiptospip:install_spip2spip_99')."</div>";
  
  fin_cadre_relief();
  fin_page();
  
}
?>
