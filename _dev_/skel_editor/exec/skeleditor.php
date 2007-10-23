<?php
//  display all skel files and folders

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_SKELEDITOR',(_DIR_PLUGINS.end($p)));


function tree_open_close_dir(&$current,$target,$current_file){
	if ($current == $target) return "";
	$tcur = explode("/",$current);
	$ttarg = explode("/",$target);
	$tcom = array();
	$output = "";
	// la partie commune
	while (reset($tcur)==reset($ttarg)){
		$tcom[] = array_shift($tcur);
		array_shift($ttarg);
	}
	// fermer les repertoires courant jusqu'au point de fork
	while($close = array_pop($tcur)){
		$output .= "</div>\n";
		$output .= fin_block();
		$output .= "</div>\n";
	}
	$chemin = implode("/",$tcom)."/";
	// ouvrir les repertoires jusqu'a la cible
	while($open = array_shift($ttarg)){
		$chemin .= $open . "/";
		if(!strstr($current_file,$chemin)){
			$output .= "<div>\n";
			$output .= bouton_block_invisible(md5($chemin));
			$output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/folder.png' alt='directory' /> $open";             
			$output .= debut_block_invisible(md5($chemin));
		}
		else {
			$output .= "<div>\n";
			$output .= bouton_block_visible(md5($chemin));
			$output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/folder.png' alt='directory' /> $open";             
			$output .= debut_block_visible(md5($chemin));
		}
		$output .= "<div style='line-height: 12px;border:1px solid #ededed;padding:4px;margin:4px 0'>\n";
	}
	$current = $target;
	return $output;
}

function show_skel_file($file_list,$current_file,$img_extension) {

  $output = "<div style='line-height: 12px;border:1px solid #ededed;padding:4px;margin:4px 0'>\n";
	$init_dir = $current_dir = dirname(reset($file_list));
	foreach($file_list as $file){
		$dir = dirname($file);
			
		if ($dir != $current_dir)
			$output .= tree_open_close_dir($current_dir,$dir,$current_file);
		if (!is_writable($dir))
			$output .= "<div style='background:#3ff'>";
		else
			$output .= "<div>";

		if ($file==$current_file)
			$expose=" style='background:#ff6'";
		else 
			$expose="";
		$path_parts = pathinfo($file);
		$extension =  $path_parts['extension'];
		$base = $path_parts['basename'];
		
		if (in_array($extension,$img_extension)) {
			 $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/img.png' alt='image' /> ";
			 $output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($file))."'$expose>$base</a>";
		} else {
		   $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."/img_pack/file.png' alt='file' /> ";
			 $output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($file))."'$expose>$base</a>";
		}
		$output .= "</div>\n";
	}
	$output .= tree_open_close_dir($current_dir,$init_dir,$current_file);
  $output .= "</div>\n";
  return $output;
}

// build select menu to choose directory
function editor_form_directory($path,$depth="") {
	$output = "";
	foreach($path as $dir){
		$tdir = explode('/',$dir);
		$myfile = array_pop($tdir);
		$depth = "";
		while(array_pop($tdir)) $depth.="&nbsp;&nbsp;";
		$output .= "<option value='$dir'>$depth$myfile</option>\n";              
	}
  return $output;  
}

// add file form
function editor_addfile($path_list) {
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
  $output .= editor_form_directory($path_list);      
  $output .= "</select><br /><input type='submit' name='sub' value='"._T("skeleditor:creer")."' />";
  $output .= "</form>\n";
  $output .= fin_block();
  return $output;        
}

// upload file form
function editor_uploadfile($path_list) {
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
  $output .= editor_form_directory($path_list);      
  $output .= "</select><br /><input type='submit' name='sub' value='"._T("skeleditor:upload")."' />";
  $output .= "</form>\n";
  
  $output .= fin_block();
  return $output;        
}


// fonction qui remplace la fonction initiale (voir ecrire/public/debug.php)
// pour permettre d'éditer la page même s'il y a une erreur fatale
function erreur_squelette($message='', $lieu='') {
	global $tableau_des_erreurs;
	global $auteur_session;
	static $runs;

	if (is_array($message)) list($message, $lieu) = $message;

	spip_log("Erreur squelette (skel editor): $message | $lieu ("
		. $GLOBALS['fond'].".html)");
	$GLOBALS['bouton_admin_debug'] = true;
	$tableau_des_erreurs[] = array($message, $lieu);
	// Eviter les boucles infernales
	//if (++$runs > 4) {
	    // des la 1ere erreur, on arrete le parsing des boucles et on passe en mode debug
	    echo _T('skeleditor:erreur_parsing');
	    echo "<code style='display:block;color:#f00;padding:10px;background:#fff;border:1px solid #999'>$lieu</code>\n";
      echo "<p><a href=\"?exec=skeleditor&amp;f=".urlencode(_request(f))."&amp;debug=true\">"._T('skeleditor:editer_skel_debug')."</a></p>";      	       
			exit;  
	//}
}



// skeleton parsing (more details:  public/phrase_html)
function skel_parser($skel_str) {
  include_spip("public/interfaces");
  include_spip("public/phraser_html"); 
  //include_spip("public/debug"); 

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

// security
function check_file_allowed($file,$files_editable,$new = false) {
	if (in_array($file,$files_editable))	return true;  // known file
	 else if ($new){ // new file ?	   
		 if (in_array(dirname($file),array_map('dirname',$files_editable)))	return true; // known directory    
	}
	return false;
}

// recupere le chemin du squelette a editer: dist ? plugin squelette ou squelettes ?
function get_spip_path(){
	static $path_a = array();
	static $c = '';

	// on calcule le chemin si le nombre de plugins a change
	if ($c != count($GLOBALS['plugins']).$GLOBALS['dossier_squelettes']) {	
		$c = count($GLOBALS['plugins']).$GLOBALS['dossier_squelettes'];
	
		// Chemin standard depuis l'espace public
		$path = defined('_SPIP_PATH') ? _SPIP_PATH : 
			_DIR_RACINE.':'.
			_DIR_RACINE.'dist/:'.
			_DIR_RACINE.'formulaires/:'.
			_DIR_RESTREINT;
			
    // Ajouter dist/
		$path = _DIR_RACINE.'dist/:' . $path;

		// Ajouter les repertoires des plugins 
    /*	solution trop globale: il faut ajouter seulement les plugins de type "squelettes"
		if ($GLOBALS['plugins'])
			$path = _DIR_PLUGINS
				. join(':'._DIR_PLUGINS, $GLOBALS['plugins'])
				. ':' . $path;
		*/	
    if (count(get_plugin_squelette())>0) 
        $path = join(':', get_plugin_squelette()).':'.$path;
    
		// Ajouter squelettes/
		if (@is_dir(_DIR_RACINE.'squelettes'))
			$path = _DIR_RACINE.'squelettes/:' . $path;
		
		// Et le(s) dossier(s) des squelettes nommes
		if ($GLOBALS['dossier_squelettes'])
			foreach (explode(':', $GLOBALS['dossier_squelettes']) as $d)
				$path = 
					($d[0] == '/' ? '' : _DIR_RACINE) . $d . '/:' . $path;

		// nettoyer les / du path
		$path_a = array();
		foreach (explode(':', $path) as $dir) {
			if (strlen($dir) AND substr($dir,-1) != '/')
				$dir .= "/";
			$path_a[] = $dir;
		}
				
	}
	return $path_a;
}

// recupere les plugins de type squelette
function get_plugin_squelette() {
  // alternative 1: liste des plugins squelettes manuelle (blip, sarka, ...?)
  // alternative 2: on scanne les plugins: si article.html et sommaire.html present ? sans doute un plugin squelette 
  $plugin_squelette = array();
	if ($GLOBALS['plugins']) {
	   foreach($GLOBALS['plugins'] as $k) {	    
	       if (@is_file(_DIR_PLUGINS."$k/article.html")&&@is_file(_DIR_PLUGINS."$k/sommaire.html")) 
                                                            $plugin_squelette[] = _DIR_PLUGINS.$k."/";        
     }
  }
  return $plugin_squelette;
}

function parse_path($dir,$extensions){
	$pattern = "\.(".implode("|",$extensions).")$";
	$liste = preg_files($dir, $pattern);
	return $liste;
}

// tri la liste des fichiers en placant ceux a la racine en premier
function sort_directory_first($files,$root) {
  $files_root = array();
  $files_directory = array();
  foreach($files as $file) {
      if (dirname($file)."/" != $root) $files_directory[] = $file;
                                  else $files_root[] = $file;
  }
  return array_merge($files_root,$files_directory);
}

// -------------------------------
// Main 
// ------------------------------
function exec_skeleditor(){
  include_spip("inc/presentation");
  global $spip_lang_right;
  $img_extension = array("jpg","png","gif","ico","bmp");
  $listed_extension = array("htm","html","xml","svg","php","php3","php4","py","sh","sql","css","rdf","txt","nfo","log","js","as","csv");

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
  $dossier_squelettes = reset(get_spip_path());
  	
	if (@is_readable(_DIR_SESSIONS."charger_plugins_fonctions.php")){  // utile ?
		// chargement optimise precompile
		include_once(_DIR_SESSIONS."charger_plugins_fonctions.php");     	
	}
	
	$files_editable = parse_path($dossier_squelettes,array_merge($listed_extension,$img_extension));	
	$files_editable = sort_directory_first($files_editable,$dossier_squelettes); // utile ?
	$path_list = array_keys(array_flip(array_map('dirname',$files_editable)));

	// --------------------------------------------------------------------------- 
	// Action ? 
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
                     $_GET['action'] = 'preview';
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
  $action = "";
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
	    
	    if (isset($_GET['action']) && $safe_flag) { // any action on file ?
          $action = $_GET['action'];          
          if ($action=="delete") {                // delete the file 
            @unlink($file_name);          
          } else if ($action=="download") {       // download the file 
            if ($file_tmp = @file("$file_name")) {
                  $file_name_nopath = basename($file_name);
                  $file_str = implode ('',$file_tmp);
                  //header("Content-type: text/plain"); // text/plain or binary .... 
                  header("Content-Disposition: attachment; filename=\"$file_name_nopath\"");
                  echo $file_str; 
                  exit; 
            }
          } else if ($action=="new") {            // add new file
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
          // refresh file list after action
          $files_editable = parse_path($dossier_squelettes,array_merge($listed_extension,$img_extension));
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
	echo show_skel_file($files_editable,$file_name,$img_extension);
	fin_boite_info();
	echo "<br />";
	debut_boite_info();
	echo editor_addfile($path_list);
	echo editor_uploadfile($path_list);
  fin_boite_info();
	
	debut_droite();

	// something to do ?	
	if ($file_name!="") { 
       if ($safe_flag) {         
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
                 list($width, $height) = @getimagesize($file_name);
                 echo "<small>$width x $height pixels</small>\n";                 
             } else {  // edit file as text  
                if ($file_tmp = @file("$file_name")) {
                    $file_str = implode ('',$file_tmp);                  
                    if (($extension=='html') && (_request(debug)!='true')) echo  skel_parser($file_str); // experimental                            	        
                    $file_str = str_replace("</textarea","&lt;/textarea",$file_str); // exception: textarea closing tag
  								  echo generer_url_post_ecrire('skeleditor',"retour=skeleditor&f=".urlencode($file_name));
                    echo "<textarea name='editor' cols='80' rows='50'>$file_str</textarea>\n";               
  									echo "<div style='text-align:$spip_lang_right'><input type='submit' name='action' value='"._T("skeleditor:sauver")."' class='fondo'></div>";
          	        echo "</form>\n";       	        
  
                } else {
                    echo "<p style='color:red'>"._T("skeleditor:erreur_ouvert_ecrit")."</p>\n";
                }   
             }
         }
      } else { // security failure
        echo "<div style='color:red'>"._T('skeleditor:erreur_sansgene')."</div>\n";      
      }
  } else {
      echo "<p>"._T("skeleditor:fichier_choix")."</p>\n";
  }
  
  fin_page();
}
?>
