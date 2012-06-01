<?php
// ---------------------------------------------------------
//  Ma lettre - archives
// ---------------------------------------------------------

include(dirname(__FILE__).'/../inc_malettre.php');


function exec_malettre_archive(){  
  global $connect_statut;
	global $connect_toutes_rubriques;
	include_spip("inc_presentation");

  // parametre
  $path_archive = "lettre";
  $path = _DIR_IMG.$path_archive;
  $path_url_public_archive = "../IMG/lettre";

  // main ------------------------------------------------------

  if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		$page = "malettre";	

		//--		
		$agir = _request('agir');
		if ($agir == "del") {
	      $file = _request('f');	      
	      $file = str_replace("..","",$file);  // basic secu
	      $file = str_replace("/","",$file);
	      @unlink("$path/$file");
	      $file_txt = substr($file,0,-4)."txt";
	      @unlink("$path/$file_txt");	      
	      header("location:?exec=malettre_archive");	      
        exit;  
    } else {
        $commencer_page = charger_fonction('commencer_page', 'inc');
        echo $commencer_page(_T('malettre:ma_lettre'),_T('malettre:ma_lettre'),_T('malettre:ma_lettre'));
                	  	
        echo debut_gauche('', true);   
        debut_boite_info(true);
        echo "<p><a href='?exec=malettre'>"._T('malettre:ecrire_nouvelle')."</a></p>";
        fin_boite_info(true);
        
        echo debut_droite("", true);	        
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
        $output = "<table style='font-family:arial;font-size:0.9em;'>";
        foreach ($lettres_path as $k=>$lettre_path) {
          $date_lettre =  substr($lettre_path, 13 , 2).".".substr($lettre_path, 11 , 2).".".substr($lettre_path, 7 , 4);
          if (substr($lettre_path,-8,1)=="_")  // retro compat 
                $lang = "[".substr($lettre_path,-7,2)."]";  
           else $lang = "";
          
          $output .= "<tr>";
          $output .= "<td><small>$lang</small></td>";
          $output .= "<td>"._T('malettre:lettre_du')."  $date_lettre</td>";
          $output .= "<td><a href='#' onclick=\"malettref.location.href='$path_url_public_archive/$lettre_path'\" style='color:green;'>"._T('malettre:voir')."</a></td>";
          $output .= "<td><a href='$path_url_public_archive/$lettre_path' target='_blank' style='color:green;'>HTML</a></td>";
          // txt ?
          $lettre_path_txt = substr($lettre_path,0,-4)."txt";          
          if (is_file($path."/".$lettre_path_txt)) $output .= "<td><a href='$path_url_public_archive/$lettre_path_txt' target='_blank' style='color:green;'>TXT</a></td>";
                                              else $output .= "<td></td>";
          $output .= "<td><a href='?exec=malettre_archive&amp;agir=del&amp;f=$lettre_path'  onclick='return confirm(\""._T('malettre:effacer_confirm')."\");' style='color:red;'>"._T('malettre:effacer')."</a></td>";
          $output .= "</tr>";

          
  	    } 
  	    $output  .= "</table>";
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