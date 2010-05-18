<?php
// version spip2spip manuelle
// (la version automatique spip2spip/spip2.php est appelle via le cron)

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once(dirname(__FILE__).'/../spiptospip_fonctions.php');
include_spip('inc/presentation');

//------------------------------- 
// Main
//-------------------------------
function exec_spip2spip_syndic(){
 
  global $connect_statut;
  global $connect_toutes_rubriques;  
  //------------------------------- 
  // droits  - FIXME ? en SPIP 2 utiliser autoriser ?????
  //-------------------------------
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {   
          $commencer_page = charger_fonction('commencer_page', 'inc');
          echo $commencer_page(_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"));
	        echo _T('avis_non_acces_page');
          echo fin_gauche().fin_page();
	        exit;
  }
  
                            
  //------------------------------- 
  // Main
  //-------------------------------
  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"));
  echo gros_titre(_T('spiptospip:copy_spip2spip'),'',false);
  echo debut_gauche('', true);
  echo debut_boite_info(true)._T('spiptospip:intro_spip2spip');
  echo fin_boite_info(true);
  
  echo debut_droite('', true);
  echo debut_cadre_relief(true);
  
  // recuperer les sites à syndiquer  
  if (_request('id_site'))
    $result = sql_select("id","spip_spip2spip","id=".(int) _request('id_site'));
   else 
    $result = sql_select("id","spip_spip2spip");   // on syndique tous les sites
      
  while ($row_site = sql_fetch($result)) {     
     echo spip2spip_syndiquer($row_site["id"],"html");      
  }
  
  echo "<div style='margin:20px 0'><a href='?exec=spip2spip'>"._T('spiptospip:back')."</a></div>\n";
  echo fin_cadre_relief();
  
  // pied
  echo fin_gauche().fin_page();
}
?>