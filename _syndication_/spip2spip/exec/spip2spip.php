<?php

include(dirname(__FILE__).'/../inc-spip2spip.php');

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

// -------------------------------
// Main: SPIP2SPIP
// -------------------------------

function exec_spip2spip(){
  //include ("inc.php");
  //include_ecrire ("inc_acces.php");
  //include_ecrire ("inc_config.php");
  //include_ecrire ("inc-spip2spip.php");
  //include_ecrire("inc_presentation");
  
  global $table_prefix;
  
  //------------------------------- 
  // Some action ?
  //-------------------------------
  if (isset($_GET['action'])) {
  	// del ?
  	if ($_GET['action'] == 'del') {
  		$my_id = $_GET['id'];
  		$sql = "DELETE FROM ".$table_prefix."_spip2spip WHERE id = $my_id LIMIT 1";
  		spip_query($sql);
  	}
  	// add ?
  	if ($_GET['action'] == 'add') {
  		$my_url  = addslashes(trim($_GET['url']));
  		$my_site = addslashes(trim($_GET['site']));
  		$sql = "INSERT INTO ".$table_prefix."_spip2spip VALUES ('', '".$my_site."' ,'".$my_url."','0000-00-00 00:00:00');";		
  		spip_query($sql);
  	}
  }
  
  //------------------------------- 
  // Main
  //-------------------------------
  
  debut_page(_T('spiptospip:copy_spip2spip'), "administration", "configuration","contenu");
  echo "<br / ><br />";
  gros_titre(_T('spiptospip:copy_spip2spip'));
  debut_gauche();
  insert_shortcut();
  debut_boite_info();
  echo _T('spiptospip:intro_spip2spip');
  fin_boite_info();
    
    
    
  debut_droite();
  
  //
  // spip2spip installed ?
  $sql = "SELECT COUNT(titre) AS c FROM ".$table_prefix."_groupes_mots WHERE titre='- spip2spip -'";
  $k = spip_fetch_array(spip_query($sql));
  if ($k['c']!=1){
    debut_cadre_relief();
    echo "<div style='color:red'>"._T('spiptospip:not_installed')."</div>";
    fin_cadre_relief();
    fin_page();
    exit;
  }
  
  
  
  //
  // gestion des sites
  debut_cadre_relief();
  echo "<h3>"._T('spiptospip:site_manage')."</h3>\n";
  
  // sites inscrits
  $sql = "SELECT * FROM ".$table_prefix."_spip2spip ORDER BY site_titre";
  $result_copie = spip_query($sql);
  echo "<table border='0' cellpadding=3 cellspacing=0 width='100%' class='arial2'>\n";
  echo "<tr style='background:#ff6600;font-weight:bold;'>\n";
  echo "<td>"._T('spiptospip:site_available')."</td>\n";
  echo "<td colspan=\"3\">"._T('spiptospip:last_syndic')."</td>\n";
  echo "</tr>\n";
  $i = 0;
  while($row_copie  = spip_fetch_array($result_copie)) {  
    $couleur = ($i++ % 2) ? '#FFFFFF' : $couleur_claire;
  	echo "<tr bgcolor='$couleur'>";
  	echo "\t<td><a href='".$row_copie["site_rss"]."' target='_blank'>".$row_copie["site_titre"]."</a></td>\n";
  	echo "\t<td>".substr($row_copie["last_syndic"],0,-3)."</td>\n";
  	echo "\t<td><a href='?exec=spip2spip_syndic&amp;id_site=".$row_copie["id"]."' class='verdana2'>"._T('spiptospip:action_syndic')."</a></td>\n";
  	echo "\t<td><a href='?exec=spip2spip&amp;action=del&amp;id=".$row_copie["id"]."' class='verdana2'>"._T('spiptospip:action_delete')."</a></td>\n";
  	echo "</tr>\n";
  }
  echo "</table>\n";
  
  // ajouter un site
  echo "<h4>"._T('spiptospip:site_add')."</h4>\n";
  echo "<form name='cp' method='get'>\n";
  echo "<label for='site'>"._T('spiptospip:form_s2s_1')."<br/><input id=\"site\" name=\"site\" type=\"text\" size=\"50\"/><br />\n";
  echo "<label for='url'>"._T('spiptospip:form_s2s_2')."</label><br /><input name=\"url\" type=\"text\" size=\"50\"/><br />\n";
  echo "<input name=\"action\" type=\"hidden\" value=\"add\"/>\n";
  echo "<input name=\"exec\" type=\"hidden\" value=\"spip2spip\"/>\n";
  echo "<input type=\"submit\" value=\""._T('spiptospip:form_s2s_3')."\"></form>";
  fin_cadre_relief();
  
  //
  // memo
  debut_cadre_relief();
  echo "<p class='verdana2'>"._T('spiptospip:how_to')."</p>";
  fin_cadre_relief();
  
  fin_page();
} 


?>
