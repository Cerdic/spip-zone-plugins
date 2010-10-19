<?php
//
// gestion des sites syndiques par spip2spip
//
include_once(dirname(__FILE__).'/../spiptospip_fonctions.php');

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

// -------------------------------
// Main: SPIP2SPIP
// -------------------------------

function exec_spip2spip(){

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
  // action 
  //-------------------------------
	// suppression site
  if (_request('agir') == 'del') 
      sql_delete("spip_spip2spip","id=".(int) _request('id'));

  // ajout site
  if (_request('agir') == 'add') {      
  		$my_url  = addslashes(trim(_request('url')));
  		$my_site = addslashes(trim(_request('site')));
  		sql_delete("spip_spip2spip","site_rss='$my_url'"); // pas doublons  		
  	  sql_insertq("spip_spip2spip", array(
                   'site_titre' => $my_site, 'site_rss' => $my_url));
  }

  
  //------------------------------- 
  // Main
  //-------------------------------
  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"),_T("spiptospip:copy_spip2spip"));
  echo gros_titre(_T('spiptospip:copy_spip2spip'),'',false);
  echo debut_gauche('', true);
  echo debut_boite_info(true)._T('spiptospip:intro_spip2spip');
  if (function_exists(lire_config)) echo "<p><a href='?exec=cfg&cfg=spip2spip'>"._T('spiptospip:config_spip2spip')."</a></p>";
  echo fin_boite_info(true);
 
   
  //
  // gestion des sites
  echo debut_droite('', true);
  echo debut_cadre_relief(true);  
  echo "<h3>"._T('spiptospip:site_manage')."</h3>\n";

  // sites inscrits
  $result = sql_select("*","spip_spip2spip", "", "", "site_titre");

  echo "<table border='0' cellpadding=3 cellspacing=0 width='100%' class='arial2'>\n";
  echo "<tr style='background:#ff6600;font-weight:bold;'>\n";
  echo "<td>"._T('spiptospip:site_available')."</td>\n";
  echo "<td colspan=\"3\">"._T('spiptospip:last_syndic')."</td>\n";
  echo "</tr>\n";
  $i = 0;
  while ($row = sql_fetch($result)) {  
    $couleur = ($i++ % 2) ? '#FFFFFF' : $couleur_claire;
  	echo "<tr bgcolor='$couleur'>";
  	echo "\t<td><a href='".$row["site_rss"]."'>".$row["site_titre"]."</a></td>\n";
  	echo "\t<td>".substr($row["last_syndic"],0,-3)."</td>\n";
  	echo "\t<td><a href='?exec=spip2spip_syndic&amp;id_site=".$row["id"]."' class='verdana2'>"._T('spiptospip:action_syndic')."</a></td>\n";
  	echo "\t<td><a href='?exec=spip2spip&amp;agir=del&amp;id=".$row["id"]."' class='verdana2'>"._T('spiptospip:action_delete')."</a></td>\n";
  	echo "</tr>\n";
  }
  echo "</table>\n";
  
  // formulaire ajout site
  echo "<h4>"._T('spiptospip:site_add')."</h4>\n";
  echo "<form name='cp' method='post' action='?exec=spip2spip'>\n";
  echo "<label for='site'>"._T('spiptospip:form_s2s_1')."<br/><input id=\"site\" name=\"site\" type=\"text\" size=\"50\"/><br />\n";
  echo "<label for='url'>"._T('spiptospip:form_s2s_2')."</label><br /><input name=\"url\" type=\"text\" size=\"50\"/><br />\n";
  echo "<input name=\"agir\" type=\"hidden\" value=\"add\" />\n";
  echo "<input type=\"submit\" value=\""._T('spiptospip:form_s2s_3')."\" /></form>";
  echo fin_cadre_relief(true);
  
  //
  // memo
  echo debut_cadre_relief(true);
  echo "<p class='verdana2'>"._T('spiptospip:how_to')."</p>";
  echo fin_cadre_relief(true);
  
  // pied
  echo fin_gauche().fin_page();
} 


?>