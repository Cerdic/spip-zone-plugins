<?php
define('_DIR_PLUGIN_SKELEDITOR',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__))))))));

//  display all skel files and folders
function show_skel_file($path,$current_file) {  
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
             $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/folder.png' alt='directory' /> $myfile";             
             if(!strstr($current_file,$entirePath)) $output .= debut_block_invisible(md5($myfile));             
             $output .= show_skel_file(substr($path,3)."/".$myfile,$current_file); // recursive !
             if(!strstr($current_file,$entirePath)) $output .= fin_block();
          } else { 
             if ($entirePath==$current_file) $expose=" style='background:#ff6'";
                                        else $expose="";
             $extension =  strtolower(substr($myfile, strrpos($myfile,".")+1));             
             if (in_array($extension,$listed_extension)) {
                $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/file.png' alt='file' /> ";
                $output .= "<a href=\"?exec=skeleditor&amp;f=".urlencode($entirePath)."\"$expose>$myfile</a>";
             } else if (in_array($extension,$img_extension)) {
                $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/img.png' alt='file' /> ";
                $output .= "<a href=\"?exec=skeleditor&amp;f=".urlencode($entirePath)."\"$expose>$myfile</a>";
             } else {
                $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/unknown.png' alt='unknown' /> ";
                $output .= "<span style='color:#aaa'>$myfile</span>";
             }
          }         
          $output .= "</div>\n";
      }
  } 
  $output .= "</div>\n";
  return $output;
}

// build select menu to choose directory
function editor_form_directory($path,$depth="") { 
  $path = "../$path";
  $folder = dir($path); 
  if ($depth=="") $output .= "<option value='$path'>(racine)</option>\n"; 
  $depth .= "&nbsp;&nbsp;&nbsp;&nbsp;";
  while ($myfile = $folder->read()) {
      $entirePath  = $path."/".$myfile;        
      if (substr($myfile,0,1) !=".") {  // exclude hidden files and directories                 
          if (is_dir($entirePath)) {
              $output .= "<option value='$entirePath'>$depth$myfile</option>\n";              
              $output .= editor_form_directory(substr($entirePath,3),$depth); // recursive !           
          }
      }      
  }
  return $output;  
}

// add file form
function editor_addfile() {
  global $dossier_squelettes;
  $output = bouton_block_invisible('editor_newfile');
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_nouveau");
  $output .= debut_block_invisible('editor_newfile');  
  $output .= "<form method='get'>\n"; 
  $output .= "<input type='hidden' name='exec' value='skeleditor' />"; 
  $output .= "<input type='hidden' name='action' value='new' />"; 
  //$output .= "nom du fichier:<br />\n";
  $output .= "<input type='text' name='f' value='untitled.html' onfocus=\"this.value=''\" />"; 
  $output .= _T("skeleditor:target")."<br />\n"; 
  $output .= "<select name='target'><br />\n"; 
  $output .= editor_form_directory($dossier_squelettes);      
  $output .= "</select><br /><input type='submit' name='sub' value='"._T("skeleditor:creer")."' />";
  $output .= "</form>\n";
  $output .= fin_block();
  return $output;        
}

// upload file form
function editor_uploadfile() {
  global $dossier_squelettes;
  $output = "<br />".bouton_block_invisible('editor_uploadfile');
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_upload");
  $output .= debut_block_invisible('editor_uploadfile');
  
  $output .= "<form method='post' enctype='multipart/form-data' >\n";  
  $output .= "<input type='hidden' name='exec' value='skeleditor' />"; 
  $output .= "<input type='hidden' name='action' value='upload' />";
  $output .= "<input type='hidden' name='MAX_FILE_SIZE' value='200000' />"; 
  $output .= "<input type='file' name='upf'/>"; 
  $output .= _T("skeleditor:target")."<br />\n"; 
  $output .= "<select name='target'><br />\n"; 
  $output .= editor_form_directory($dossier_squelettes);      
  $output .= "</select><br /><input type='submit' name='sub' value='"._T("skeleditor:upload")."' />";
  $output .= "</form>\n";
  
  $output .= fin_block();
  return $output;        
}

// skeleton parsing (more details:  public/phrase_html)
function skel_parser($skel_str) {
  include_spip("public/interfaces");
  include_spip("public/phraser_html");

  //$output .= _T('skeleditor:parseur_titre'); 
  $output .= "<div style='background: #eef; border:1px solid #eee;padding:10px;font-size:0.82em;font-family:Verdana'>";
  
  $boucles = array(); 
  $b = public_phraser_html($skel_str, 0, $boucles, 'skel_editor'); 
  $boucles = array_reverse($boucles,TRUE);
  
  /* parse outside boucles */
  $output .= bouton_block_invisible("hors_boucle")._T("skeleditor:parseur_horsboucle");  
  $output .= debut_block_invisible("hors_boucle");
  $output .= "<div style='background: #fff;padding:10px;'>"; 
  foreach($b as $k=>$val) { 
     if ($val->type == "champ") $output .= "<span style='color:#c30;background:#eee'>#".$val->nom_champ."</span>";
         else if ($val->type == "texte") $output .="<pre style='background:#ddd;margin:0;display:inline;'>&nbsp;".htmlspecialchars($val->texte)."</pre>";
         else if ($val->type == "include") $output .= "<span style='color:#30c;background:#cff;'>(INCLURE)</span>"; 
  }
  $output .= "</div>\n";
  $output .= fin_block()."<br />";
  
  /* parse boucles */
  foreach($boucles as $k=>$val) {
     /* version gentle */ 
     $output .= bouton_block_invisible("skel_parser_$k")." BOUCLE$k";
     $output .= " <span style='color:#888'>(".strtoupper($val->type_requete).")</span>";
     $output .= debut_block_invisible("skel_parser_$k");
     $output .= "<div style='background: #fff;padding:10px;'>";  
        if ($val->id_parent) $output .= "<strong>id_parent:</strong> BOUCLE$val->id_parent<br />";      
        if ($val->param) $output .= "<strong>"._T('skeleditor:parseur_param')."</strong>".skel_parser_param($val->param)."<br />";       
        $output .= "<strong>"._T('skeleditor:parseur_contenu')."</strong><br />"; 
        $output .= skel_parser_affiche( _T('skeleditor:parseur_avant'),$val->avant, '#cc9');       
        $output .= skel_parser_affiche( _T('skeleditor:parseur_milieu'),$val->milieu, '#fc6');
        $output .= skel_parser_affiche( _T('skeleditor:parseur_apres'),$val->apres, '#fcc');
        $output .= skel_parser_affiche( _T('skeleditor:parseur_altern'),$val->altern, '#cfc'); 
     $output .= "</div>\n";
     $output .= fin_block()."<br />";
      
     /* version brute */ 
     /*    	           
     $output .= "<strong>BOUCLE$k</strong><br />\n";
     foreach (get_object_vars($val) as $prop => $val2) {
          $output .= "\t<br />$prop = $val2\n";
          if (is_array($val2)) {
              foreach($val2 as $k3=>$val3) {
                  $output .= "\t\t<br>........................$k3 = $val3\n";
                  if (is_object($val3)) {
                      foreach (get_object_vars($val3) as $prop4 => $val4) {
                          $output .= "\t\t<br>+++........................( $prop4 = $val4 )\n"; 
                          if (is_array($val4 )) {
                               foreach($val4 as $k5=>$val5) {
                                  $output .= "\t\t<br>++++++...............$k5 = $val5 )\n";
                                  foreach($val5 as $k6=>$val6) {
                                    $output .= "\t\t<br>+++++++++++.........$k6 = $val6 )\n";
                                  } 
                               }
                          }
                      }
                  }
              }
        }
    }*/
    
 }       	         
                                   	        
  $output .= "</div>";
  return $output;
}

// affiche le code pour le parseur
function skel_parser_affiche($titre, $content, $bgcolor = '#fc6') {
   $output = "";
   $output .= "<div style='background:$bgcolor'>$titre</div>";
   foreach ($content as $k => $str) {
         if ($str->type == "champ") $output .= "<span style='color:#c30;background:#eee'>#".$str->nom_champ."</span>";
         else if ($str->type == "texte") $output .="<pre style='background:#ddd;margin:0;display:inline;'>&nbsp;".htmlspecialchars($str->texte)."</pre>";
         else if ($str->type == "include") $output .= "<span style='color:#30c;background:#cff;'>(INCLUDE)</span>"; 
    }
   return $output;
}

// parse param value
function skel_parser_param($str,$output='') { 
  $output = "";
  if (is_array($str)) {
      foreach($str as $k2=>$val2) {
        //$output .= ".....$k2=>$val2 ($c)<br />";
        $output .= skel_parser_param($val2,$output); // recursive
      }  
  } else if (is_object($str)) {
        $output .= " {".$str->texte."} "; 
        /*foreach (get_object_vars($str) as $prop4 => $val4) {
             $output .= "\t\t<br>...........( $prop4 = $val4 )\n"; 
        } */  
  } 
  return $output; 
}

// security: avoid access to other directories
function safe_path($str) {
  global $dossier_squelettes;
    
  $str = str_replace("..", "", trim($str));
  while (substr($str,0,1)=="/")  $str = substr($str,1);
  if (substr($str,0,strlen($dossier_squelettes))!=$dossier_squelettes) return "";
                                                                 else return $str;  
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
		debut_page(_T('titre'), "skel_editor", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	
	// globals 
  global $dossier_squelettes;
  if ($dossier_squelettes=="") $dossier_squelettes="dist";
	
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){  // utile ?
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");     	
	}

	// --------------------------------------------------------------------------- 
	// Action ? 
	// ---------------------------------------------------------------------------
	$log = "";
	// POST request ?
	if (isset($_POST['editor'])) {      // save file ?
	     $editor = $_POST['editor'];
	     $editor = str_replace("&lt;/textarea","</textarea",$editor); // exception: textarea closing tag	     
	     if (isset($_GET['f'])) $file_name = $_GET['f'];
	                       else $file_name = "";
	     $file_name = "../".safe_path($file_name);    // security	     
	     if (is_writable($file_name)) {
             if (!$handle = fopen($file_name, 'w')) {
                 $log = "<span style='color:red'>"._T("skeleditor:erreur_ouverture_fichier")."</span>";
             } else if (fwrite($handle, $editor) === FALSE) {
                 $log = "<span style='color:red'>"._T("skeleditor:erreur_ecriture_fichier")."</span>";           
             } else {
                 $log = "<span style='color:green'>"._T("skeleditor:fichier_sauvegarde_date").date('H:i')."</span>";
                 fclose($handle);
             }  
             
       } else {
            $log = "<span style='color:red'>"._T("skeleditor:erreur_edition_ecriture")."</span>";
       }
  }	
  
  // FILES request ?
  if (isset($_FILES['upf'])) {    // upload file ?
      $tmp_name = $_FILES['upf']['tmp_name'];
      if (isset($_POST['target'])) {      
               $target = "../".safe_path($_POST['target'])."/".$_FILES['upf']['name'];    // security
               $_GET['f'] = $target;
               $_GET['action'] = 'preview';
               if (file_exists($target)) {
                  $log = "<span style='color:red'>"._T('skeleditor:erreur_overwrite')."</span>";
               } else {
                  $ok = @copy($tmp_name, $target);                 
                  if (!$ok) $ok = @move_uploaded_file($tmp_name, $target);
                  if (!$ok) $log = "<span style='color:red'>"._T('skeleditor:erreur_droits')."</span>";
                       else $log = "<span style='color:green'>"._T('skeleditor:fichier_upload_ok')."</span>";                         
                       
               } 
      }     
  } 
   
  // GET request ?
  $action = "";
	if (isset($_GET['f'])) {
	    $file_name = $_GET['f']; 
      if (isset($_GET['target'])) {
          $target = "../".safe_path($_GET['target']);
          $file_name =  $target."/".$file_name;
      }                       	    
      $file_name = "../".safe_path($file_name); 
                  
      if (isset($_GET['action'])) {
          $action = $_GET['action'];          
          if ($action=="delete") {                // delete the file            
            @unlink($file_name);          
          } else if ($action=="download") {       // download the file              
              if ($file_tmp = @file("$file_name")) {
                $file_name_nopath = substr($file_name, strrpos($file_name,"/")+1); 
                $file_str = implode ('',$file_tmp);
                //header("Content-type: text/plain"); // text/plain or binary .... 
                header("Content-Disposition: attachment; filename=\"$file_name_nopath\"");
                echo $file_str; 
                exit; 
              }
          } else if ($action=="new") {            // add new file
            if (isset($_GET['target'])) {               
               if (substr($target,3, strlen($dossier_squelettes))== $dossier_squelettes) { // security 2: right place ?                  
                   // FIXME: check if extension available ?                   
                   if (is_file($file_name)) { // security 3: ovewrite ?
                    $log = "<span style='color:red'>"._T("skeleditor:erreur_overwrite")."</span>";
                   } else {
                     if (!$handle = fopen($file_name, 'w')) {
                         $log = "<span style='color:red'>"._T("skeleditor:erreur_droits")."</span>";
                     } else if (fwrite($handle, "...") === FALSE) {
                         $log = "<span style='color:red'>"._T("skeleditor:erreur_droits")."</span>";           
                     } else {
                         $log = "<span style='color:green'>"._T("skeleditor:fichier_sauvegarde_date").date('H:i')."</span>";
                         fclose($handle);
                     }
                    }
               }               	
            }
           
             
              
          }
      } 
  } else {
      $file_name = "";  
  }
  
  

  // ---------------------------------------------------------------------------
  // HTML output 
  // ---------------------------------------------------------------------------
	debut_page(_T("skeleditor:editer_skel"), "naviguer", "plugin");	
  debut_gauche();
	debut_boite_info();
	echo "<p>"._T("skeleditor:skeleditor_description")."</p>\n";	
	echo _T("skeleditor:skeleditor_dossier")." <strong>$dossier_squelettes</strong><br />";
	echo show_skel_file($dossier_squelettes,$file_name);
	fin_boite_info();
	echo "<br />";
	debut_boite_info();
	echo editor_addfile();
	echo editor_uploadfile();
  fin_boite_info();
	
	debut_droite();

	// something to do ?	
	if ($file_name!="") {   
       echo "<div>"._T("skeleditor:fichier")."<strong>$file_name</strong> $log</div>\n"; // add extra infos on file:  size ? date ? ...
       if ($action=="delete") {
         echo "<p style='color:green'>"._T("skeleditor:fichier_efface_ok")."</p>\n";
       } else { 
           // tools bar
           echo "<div id='skel_toolbar' style='width:100%;text-align:right;'>\n";
           echo "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_dl.png' alt='download' /><a href=\"?exec=skeleditor&amp;f=".urlencode($file_name)."&amp;action=download\">"._T("skeleditor:telecharger")."</a>";
           echo "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/action_del.png' alt='delete' /><a href=\"?exec=skeleditor&amp;f=".urlencode($file_name)."&amp;action=delete\" onclick=\"javascript:return confirm('"._T("skeleditor:effacer_confirme")."');\">"._T("skeleditor:effacer")."</a>";
           echo "</div>\n";
           // img or text ?
           $extension =  strtolower(substr($file_name, strrpos($file_name,".")+1)); 
           if (in_array($extension,$img_extension)) {     // display file as img
              echo "<div style='border:1px solid #333;padding:20px;background:#eee'><img src='$file_name' alt='picture' /></div>\n";
           } else {  // edit file as text  
              if ($file_tmp = @file("$file_name")) {
                  $file_str = implode ('',$file_tmp);                  
                  if ($extension=='html') echo  skel_parser($file_str); // experimental         	        
                  $file_str = str_replace("</textarea","&lt;/textarea",$file_str); // exception: textarea closing tag                                
                  echo "<form method='post' action='?exec=skeleditor&amp;retour=skeleditor&amp;f=".urlencode($file_name)."'>\n";
                  echo "<textarea name='editor' cols='80' rows='50'>$file_str</textarea>\n";               
        	        echo "<input type='submit' name='action' value='"._T("skeleditor:sauver")."' />";	        
        	        echo "</form>\n";       	        

              } else {
                  echo "<p>"._T("skeleditor:erreur_ouvert_ecrit")."</p>\n";
              }   
           }
       }
  } else {
      echo "<p>"._T("skeleditor:fichier_choix")."</p>\n";
  }
  
  fin_page();
}
?>
