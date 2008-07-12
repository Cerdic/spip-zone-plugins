<?php
// installation de spip2spip 
//  - table sup pour stocker les flux 
//  - ajout groupe spip2spip
//
// TODO: 
// - a installer directement via l'interface plugin


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

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
  $sql = "SELECT COUNT(titre) AS c FROM ".$table_prefix."_groupes_mots WHERE titre='- spip2spip -'";
  $k = spip_fetch_array(spip_query($sql));
  if ($k['c']==1){
    debut_cadre_relief();
    echo "<div style='color:red'>"._T('spiptospip:installed')."</div>";
    echo "<div style='color:green;margin:10px 0'>"._T('spiptospip:install_spip2spip_99')."</div>";
    fin_cadre_relief();
    fin_page();
    exit;
  }
  fin_page();
  
}
?>