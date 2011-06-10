<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('skeleditor_fonctions');

function exec_skeleditor(){
  
  global $spip_lang_right;
  $img_extension = array("jpg","png","gif","ico","bmp");
  $listed_extension = array("htm","html","xml","svg","php","php3","php4","py","sh","sql","css","rdf","txt","nfo","log","js","as","csv");

  // check rights
  global $connect_statut;
	global $connect_toutes_rubriques;
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
		$commencer_page = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"));
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	
	// globals 
  $dossier_squelettes = reset(get_spip_path());
  	
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){  // utile ?
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");     	
	}
	
	$files_editable = parse_path($dossier_squelettes,array_merge($listed_extension,$img_extension));	
	$files_editable = sort_directory_first($files_editable,$dossier_squelettes); // utile ?
	$path_list = array_keys(array_flip(array_map('dirname',$files_editable)));

	// --------------------------------------------------------------------------- 
	// operation ? 
	// ---------------------------------------------------------------------------
	$log = "";
	$safe_flag = false;
	// POST request ?
	if (isset($_POST['editor'])) {      // save file ?
	     $editor = $_POST['editor'];
	     $editor = str_replace("&lt;/textarea","</textarea",$editor); // exception: textarea closing tag           
	     if (isset($_GET['f'])){
	     	 $file_name = $_GET['f'];		         
	     } else $file_name = "";
	     if (is_writable($file_name) && check_file_allowed($file_name,$files_editable)) {
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
               $target = ($_POST['target'])."/".$_FILES['upf']['name'];    // security
					     if (check_file_allowed($target,$files_editable,true)) {     // security
					           $_GET['f'] = $target;					           
                     $_GET['operation'] = 'preview';
                     if (file_exists($target)) {
                        $log = "<span style='color:red'>"._T('skeleditor:erreur_overwrite')."</span>";
                     } else {
                        $ok = @copy($tmp_name, $target);                 
                        if (!$ok) $ok = @move_uploaded_file($tmp_name, $target);
                        if (!$ok) $log = "<span style='color:red'>"._T('skeleditor:erreur_droits')."</span>";
                             else $log = "<span style='color:green'>"._T('skeleditor:fichier_upload_ok')."</span>";  
                     } 
               } else {
                     $log = "<span style='color:red'>"._T('skeleditor:erreur_sansgene')."</span>";
               }
					     	             

      }     
  } 
   
  // GET request ?
  $operation = "";
	if (isset($_GET['f'])) {
	    $file_name = $_GET['f'];
	    
	    // check security first	    
	    if (isset($_GET['target'])) {      // exception: new file
	         $target = $_GET['target'];
           $file_name =  $target."/".$file_name;
           $safe_flag = check_file_allowed($file_name,$files_editable,true); 
      } else if (isset($_FILES['upf'])) { // exception: upload file
           $safe_flag = check_file_allowed($file_name,$files_editable,true); 
      } else {
           $safe_flag = check_file_allowed($file_name,$files_editable);
      }	                          
	    
	    if (isset($_GET['operation']) && $safe_flag) { // any operation on file ?
          $operation = $_GET['operation'];          
          if ($operation=="delete") {                // delete the file 
            @unlink($file_name);          
          } else if ($operation=="download") {       // download the file 
            if ($file_tmp = @file("$file_name")) {
                  $file_name_nopath = basename($file_name);
                  $file_str = implode ('',$file_tmp);
                  //header("Content-type: text/plain"); // text/plain or binary .... 
                  header("Content-Disposition: attachment; filename=\"$file_name_nopath\"");
                  echo $file_str; 
                  exit; 
            }
          } else if ($operation=="new") {            // add new file
            if (isset($_GET['target'])) {                 
			          // FIXME: check if allowed extension ?                   
                if (is_file($file_name)) {  // security : ovewrite ?
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
          // refresh file list after operation
          $files_editable = parse_path($dossier_squelettes,array_merge($listed_extension,$img_extension));
      } 
  } else {
      $file_name = "";  
  }
  
  

  // ---------------------------------------------------------------------------
  // HTML output 
  // ---------------------------------------------------------------------------
	$commencer_page = charger_fonction('commencer_page', 'inc');
  $out = $commencer_page(_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"),_T("skeleditor:editer_skel"));
   
  $out .= gros_titre(_T('skeleditor:editer_skel'),'',false);
  $out .= debut_gauche('', true);
  $out .= debut_boite_info(true)._T('skeleditor:skeleditor_description')."<p>"._T("skeleditor:skeleditor_dossier")." <strong>$dossier_squelettes</strong></p>".skeleditor_afficher_dir_skel($files_editable,$file_name,$img_extension).skeleditor_addfile($path_list).skeleditor_uploadfile($path_list);
  $out .= fin_boite_info(true);
  
	$out .=  debut_droite('', true);

	// something to do ?	
	if ($file_name!="") { 
       if ($safe_flag) {         
         $out .= "<div>"._T("skeleditor:fichier")."<strong>$file_name</strong> $log</div>\n"; // add extra infos on file:  size ? date ? ...
         if ($operation=="delete") {
           $out .= "<p style='color:green'>"._T("skeleditor:fichier_efface_ok")."</p>\n";
         } else { 
             // tools bar
             $out .= "<div id='skel_toolbar' style='width:100%;text-align:right;'>\n";
             $out .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/action_dl.png' alt='download' /><a href=\"?exec=skeleditor&amp;f=".urlencode($file_name)."&amp;operation=download\">"._T("skeleditor:telecharger")."</a>";
             $out .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/action_del.png' alt='delete' /><a href=\"?exec=skeleditor&amp;f=".urlencode($file_name)."&amp;operation=delete\" onclick=\"javascript:return confirm('"._T("skeleditor:effacer_confirme")."');\">"._T("skeleditor:effacer")."</a>";
             $out .= "</div>\n";             
             // img or text ?
             $extension =  strtolower(substr($file_name, strrpos($file_name,".")+1)); 
             if (in_array($extension,$img_extension)) {     // display file as img
                $out .= "<div style='border:1px solid #333;padding:20px;background:#eee'><img src='$file_name' alt='picture' /></div>\n";
                 list($width, $height) = @getimagesize($file_name);
                 $out .= "<small>$width x $height pixels</small>\n";                 
             } else {  // edit file as text  
                if ($file_tmp = @file("$file_name")) {
                    $file_str = implode ('',$file_tmp);                  
                    // FIXME pour l'instant on n'affiche plus le debug de boucle
                    // if (($extension=='html') && (_request(debug)!='true')) $out .=  skel_parser($file_str); // experimental                            	        
                    $file_str = str_replace("&","&amp;",$file_str); //  preserve html entities
		                $file_str = str_replace("</textarea","&lt;/textarea",$file_str); // exception: textarea closing tag                    
  								  //$out .= generer_url_post_ecrire('skeleditor',"retour=skeleditor&f=".urlencode($file_name));
  								  $out .= "<form method='post' operation='?exec=skeleditor&f=".urlencode($file_name)."'>"; //FIX temporaire --> tout integrer ds CVT								 
                    $out .= "<textarea name='editor' cols='80' rows='50'>$file_str</textarea>\n";               
  									$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='operation' value='"._T("skeleditor:sauver")."' class='fondo'></div>";
          	        $out .= "</form>\n";       	        
  
                } else {
                    $out .= "<p style='color:red'>"._T("skeleditor:erreur_ouvert_ecrit")."</p>\n";
                }   
             }
         }
      } else { // security failure
        $out .= "<div style='color:red'>"._T('skeleditor:erreur_sansgene')."</div>\n";      
      }
  } else {
      $out .= "<p>"._T("skeleditor:fichier_choix")."</p>\n";
  }
  
  // pied
  echo $out, fin_gauche(), fin_page();
}
?>