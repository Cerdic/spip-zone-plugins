<?php
// TODO: 
// .. show subdirectory content (show/hide)

//  display all skel files and folders
function show_skel_file($path) {  
  $listed_extension = array("htm","html","php","css","txt","js");

  $path = "../$path";
  $output = "<div style='line-height: 12px;border:1px solid #ededed;padding:4px;margin:4px 0'>\n";
  $folder = dir($path); 
  while ($myfile = $folder->read()) {
      $entirePath  = $path."/".$myfile;
        
      if (substr($myfile,0,1) !=".") {  // exclude hidden files and directories
          // writable ?
          if (!is_writable($entirePath))   $output .= "<div style='background:#3ff'>";
                                    else   $output .= "<div>";
          // directory of file ?
          if (is_dir($entirePath)) {
             $output .= "<img src='../plugins/skel_editor/img_pack/folder.png' alt='file' /> ";
             $output .= "<a href=\"?exec=skeleditor&amp;file=$entirePath\">$myfile</a>";
             $output .= show_skel_file(substr($path,3)."/".$myfile); // recursive !
          } else { 
             $extension =  strtolower(substr($myfile, strrpos($myfile,".")+1));
             if (in_array($extension,$listed_extension)) {         
              $output .= "<img src='../plugins/skel_editor/img_pack/file.png' alt='file' /> ";
              $output .= "<a href=\"?exec=skeleditor&amp;file=$entirePath\">$myfile $extension</a>";
             }
          }         
          $output .= "</div>\n";
      }
  } 
  $output .= "</div>\n";
  return $output;
}

// -------------------------------
// Main 
// ------------------------------
function exec_skeleditor(){
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
	
	// globals
	global $dossier_squelettes;
	
   
  // HTML output  
	debut_page("Editer le squelette", "naviguer", "plugin");
	
  debut_gauche();
	debut_boite_info();
	echo ("<p>Permet d'&eacute;diter les fichiers du squelette en cours</p>");	
	echo "dossier squelette: <strong>$dossier_squelettes</strong><br />";
	echo show_skel_file($dossier_squelettes);
	fin_boite_info();
	
	debut_droite();
	echo "<h2>Editer le squelette<h2>\n";
	
  echo "<form method='post' action='?exec=skeleditor&amp;retour=skeleditor''>\n";
	echo "<input type='submit' name='action' value='Sauver' />";
	echo "</form>\n";
  
  fin_page();
}
?>
