<?php
// ---------------------------------------------------------
//  Ma lettre - archives
// ---------------------------------------------------------

include(dirname(__FILE__).'/../inc_malettre.php');


function exec_malettre_archive(){  
  global $connect_statut;
	global $connect_toutes_rubriques;
	include_ecrire("inc_presentation");

  // parametre
  $path_archive = "IMG/lettre";
  $path = _DIR_RACINE.$path_archive;


  // main ------------------------------------------------------

  if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		$page = "malettre";	

		//--		
		$action = _request('action');
		if ($action == "del") {
	      $file = _request('f');
	      $file = str_replace("..","",$file);  // basic secu
	      $file = str_replace("/","",$file);
	      @unlink("$path/$file");
	      header("location:?exec=malettre_archive");	      
        exit;  
    } else {
    	  debut_page(_T('malettre:ma_lettre'));	  	
        debut_gauche();    
        debut_boite_info();
        echo "<p><a href='?exec=malettre'>"._T('malettre:ecrire_nouvelle')."</a></p>";
        fin_boite_info();
        
        debut_droite();	        
        echo "<h3>"._T('malettre:archives')."</h3>";        
          if (!$folder = dir($path))   {
            echo _T('malettre:erreur_lecture');
          	return false; 
        }
        $c = 0;
        
        // lecture des lettres disponibles (archives anti-chrono)
        $lettres_path = array();
        $output = "";
        
        while ($myfile = $folder->read())   { 
          $entirePath  = $path."/".$myfile;          
          $ext_start = substr($myfile, 0 , 6);
          $ext = substr($myfile, -4 , 4);
          if ($ext_start=="lettre" && $ext == "html") {   
              $lettres_path[] = $myfile; 
              $c++;
          }         		 
        }
        
        arsort($lettres_path);
        foreach ($lettres_path as $k=>$lettre_path) {
          $date_lettre =  substr($lettre_path, 13 , 2).".".substr($lettre_path, 11 , 2).".".substr($lettre_path, 7 , 4);        
          $output .= " - <a href=\"../$path_archive/$lettre_path\" target='_blank' />"._T('malettre:lettre_du')."  $date_lettre</a>";
          $output .= " : <a href='#' onclick=\"malettref.location.href='../$path_archive/$lettre_path'\" style='color:green;'>"._T('malettre:voir')."</a>";
          $output .= " - <a href='?exec=malettre_archive&amp;action=del&amp;f=$lettre_path'  onclick='return confirm(\""._T('malettre:effacer_confirm')."\");' style='color:red;'>"._T('malettre:effacer')."</a><br />\n";            
  	    } 
        echo $output;
        echo "<p><small>$c "._T('malettre:lettres_dispo')."</small></p>";
        echo "<iframe width=\"750\" height=\"500\" src='' id='malettref' name='malettref'></iframe>\n";
		}
		//--				
	
		
	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
	
	echo fin_page();
}

?>