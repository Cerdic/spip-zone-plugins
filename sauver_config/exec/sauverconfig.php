<?php

function exec_sauverconfig(){
  include_spip("inc/presentation");

  // check rights
  global $connect_statut;
	global $connect_toutes_rubriques;
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
		debut_page(_T('titre'), "sauver_config", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
  // parameters
  $str_separateur = "----------------------------------------------\n";
  $hidden_fields = array("noyau", "index_table","alea_ephemere","alea_ephemere_ancien","alea_ephemere_date", "request_restauration");  // champs à cacher / exclure
     
  // create config log
  $log = "";
  $log = $str_separateur;
  $log .= "Fichier de configuration ".strtoupper($GLOBALS['meta']['nom_site'])."\n\n";
  $log .= "URL:\t".$GLOBALS['meta']['adresse_site']."\n";
  $log .= "Date:\t".date("Y-m-d")."\n";
  $log .= $str_separateur; 
    
  foreach ($GLOBALS['meta'] as $k=>$val) {
    if (!in_array($k, $hidden_fields)) 
        $log .= "$k: $val\n";      
  };
  $log .= $str_separateur;
  
  // some action ?  download file
  if (isset($_POST['action'])) { 
        header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename=\"CONFIG.txt\"");
        echo $log;   
        exit;  
  }

  // HTML output  
	debut_page("Sauver la configuration", "sauver-config", "plugin");
	
  debut_gauche();
	debut_boite_info();
	echo ("<p>Permet de conserver la configuration de votre spip dans un fichier texte.</p><p>Ce fichier peut &ecirc;tre utilis&eacute; pour vos archives ou pour une r&eacute;installation ult&eacute;rieure.</p>");
	fin_boite_info();
	
	debut_droite();
	echo "<h2>Sauver la configuration<h2>\n";
	
  echo "<form method='post' action='?exec=sauverconfig&amp;retour=sauverconfig''>\n";
	echo "<textarea cols='55' rows='35'>\n";
  echo $log;
	echo "</textarea>\n";
	echo "<input type='submit' name='action' value='T&eacute;l&eacute;charger' />";
	echo "</form>\n";
  
  fin_page();
}
?>
