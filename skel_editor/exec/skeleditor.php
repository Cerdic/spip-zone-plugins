<?php
//  display all skel files and folders
function show_skel_file($path) {  
  $listed_extension = array("htm","html","xml","svg","php","php3","php4","py","sh","sql","css","rdf","txt","nfo","log","js","as");
  $img_extension = array("jpg","png","gif","ico","bmp");

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
             $output .= bouton_block_invisible(md5($myfile));
             $output .= "<img src='../plugins/skel_editor/img_pack/folder.png' alt='directory' /> $myfile";             
             $output .= debut_block_invisible(md5($myfile));             
             $output .= show_skel_file(substr($path,3)."/".$myfile); // recursive !
             $output .= fin_block();
          } else { 
             $extension =  strtolower(substr($myfile, strrpos($myfile,".")+1));             
             if (in_array($extension,$listed_extension)) {
                $output .= "<img src='../plugins/skel_editor/img_pack/file.png' alt='file' /> ";
                $output .= "<a href=\"?exec=skeleditor&amp;f=".urlencode($entirePath)."\">$myfile</a>";
             } else if (in_array($extension,$img_extension)) {
                $output .= "<img src='../plugins/skel_editor/img_pack/img.png' alt='file' /> ";
                $output .= "<a href=\"?exec=skeleditor&amp;f=".urlencode($entirePath)."\">$myfile</a>";
             } else {
                $output .= "<img src='../plugins/skel_editor/img_pack/unknown.png' alt='unknown' /> ";
                $output .= "<span style='color:#aaa'>$myfile</span>";
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
  $img_extension = array("jpg","png","gif","ico","bmp");

  // check rights
  global $connect_statut;
	global $connect_toutes_rubriques;
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
		debut_page(_T('titre'), "sauver_config", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	// Version de base, a supprimer donc...
	// globals
	// $GLOBALS['dossier_squelettes']);
	
	// Va chercher le repertoire des squelettes et seulement celui-ci
	// NB: Ce fichier peut initialiser $dossier_squelettes (old-style)
	// donc il faut l'inclure "en globals"
	if ($f = include_spip('mes_fonctions', false)) {
		global $dossier_squelettes;
		@include ($f); 
	}
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");     	
	}
	
	// Action ?
	$log = "";
	if (isset($_POST['editor'])) {      // save file ?
	     $editor = $_POST['editor'];
	     $editor = str_replace("&lt;/textarea","</textarea",$editor); // exception: textarea closing tag	     
	     if (isset($_GET['f'])) $file_name = $_GET['f'];
	                       else $file_name = "";
	     $file_name = "..".str_replace("..", "", $file_name);    // security	     
	     if (is_writable($file_name)) {
             if (!$handle = fopen($file_name, 'w')) {
                 $log = "<span style='color:red'>erreur: impossible d'ouvrir le fichier</span>";
             } else if (fwrite($handle, $editor) === FALSE) {
                 $log = "<span style='color:red'>erreur: impossible d'&ecute;crire dans le fichier</span>";           
             } else {
                 $log = "<span style='color:green'>fichier sauvegardé @ ".date('H:m')."</span>";
                 fclose($handle);
             }        
	          
       } else {
            $log = "<span style='color:red'>erreur: fichier non éditable en écriture</span>";
       }
  }
	   
  // HTML output  
	debut_page("Editer le squelette", "naviguer", "plugin");
	
  debut_gauche();
	debut_boite_info();
	echo ("<p>Permet d'&eacute;diter les fichiers du squelette en cours</p>");	
	echo "dossier squelette: <strong>$dossier_squelettes</strong><br />";
	echo show_skel_file($dossier_squelettes);
	fin_boite_info();
	
	debut_droite();

	// something to do ?	
	if (isset($_GET['f'])) {
	     $file_name = $_GET['f'];
       $file_name = "..".str_replace("..", "", $file_name);    // security       
       echo "<div>Fichier: <strong>$file_name</strong> $log</div>\n"; // add extra infos on file:  size ? date ? ...
       // img or text ?
       $extension =  strtolower(substr($file_name, strrpos($file_name,".")+1)); 
       if (in_array($extension,$img_extension)) {     // display file as img
          echo "<div style='border:1px solid #333;padding:20px;background:#eee'><img src='$file_name' alt='picture' /></div>\n";
       } else {  // edit file as text  
          if ($file_tmp = @file("$file_name")) {
              $file_str = implode ('',$file_tmp);
              $file_str = str_replace("</textarea","&lt;/textarea",$file_str); // exception: textarea closing tag              
              echo "<form method='post' action='?exec=skeleditor&amp;retour=skeleditor&amp;f=".urlencode($file_name)."'>\n";
              echo "<textarea name='editor' cols='80' rows='50'>$file_str</textarea>\n";               
    	        echo "<input type='submit' name='action' value='Sauver' />";	        
    	        echo "</form>\n";
          } else {       
              echo "<p>Erreur: impossible d'ouvrir ou d'éditer ce fichier.</p>\n";
          }   
       }     
      
  } else {
      echo "<p>Choississez le fichier que vous voulez &eacute;diter ou visualiser.</p>\n";
  }
  
  fin_page();
}
?>
