<?php

// variante repliee de la fonction de l'affichage de l'arbre des repertoires
// http://doc.spip.org/@tree_open_close_dir
function skeleditor_tree_open_close_dir(&$current,$target,$current_file){
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
		$output .= fin_block();		
	}
	$chemin = implode("/",$tcom)."/";
	// ouvrir les repertoires jusqu'a la cible
	while($open = array_shift($ttarg)){
		$chemin .= $open . "/";
		if(!strstr($current_file,$chemin)){
      $output .= bouton_block_depliable("<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/folder.png' alt='directory' style='float:left;' /> $open",false,md5($chemin));
		}
		else {
      $output .= bouton_block_depliable("<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/folder.png' alt='directory' style='float:left;' /> $open",false,md5($chemin));
		}
		$output .= "<div style='line-height: 12px;border:1px solid #ededed;padding:4px;margin:4px 0;display:none;' id='".md5($chemin)."'>\n";
	}
	$current = $target;
	return $output;
}

function skeleditor_afficher_dir_skel($file_list,$current_file,$img_extension) {

  $output = "<div style='line-height: 12px;border:1px solid #ededed;padding:4px;margin:4px 0'>\n";  
	$init_dir = $current_dir = dirname(reset($file_list));
	foreach($file_list as $file){
		$dir = dirname($file);
			
		if ($dir != $current_dir)
			$output .= skeleditor_tree_open_close_dir($current_dir,$dir,$current_file);
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
			 $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/img.png' alt='image' /> ";
			 $output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($file))."'$expose>$base</a>";
		} else {
		   $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/file.png' alt='file' /> ";
			 $output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($file))."'$expose>$base</a>";
		}
		$output .= "</div>\n";
	}
	$output .= skeleditor_tree_open_close_dir($current_dir,$init_dir,$current_file);
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
function skeleditor_addfile($path_list) {
  //$output = bouton_block_invisible('editor_newfile');
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_nouveau");
  //$output .= debut_block_invisible('editor_newfile');  
  $output .= "<form method='get'>\n"; 
  $output .= "<input type='hidden' name='exec' value='skeleditor' />"; 
  $output .= "<input type='hidden' name='operation' value='new' />"; 
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
function skeleditor_uploadfile($path_list) {
  //$output = "<br />".bouton_block_invisible('editor_uploadfile');
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_200/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_upload");
  //$output .= debut_block_invisible('editor_uploadfile');
  
  $output .= "<form method='post' enctype='multipart/form-data' >\n";  
  $output .= "<input type='hidden' name='exec' value='skeleditor' />"; 
  $output .= "<input type='hidden' name='operation' value='upload' />";
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
  //$output .= bouton_block_invisible("hors_boucle")._T("skeleditor:parseur_horsboucle");  
  //$output .= debut_block_invisible("hors_boucle");
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
     //$output .= bouton_block_invisible("skel_parser_$k")." BOUCLE$k";
     $output .= " <span style='color:#888'>(".strtoupper($val->type_requete).")</span>";
     //$output .= debut_block_invisible("skel_parser_$k");
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
			_DIR_RACINE.'squelettes-dist/:'.
			_DIR_RACINE.'formulaires/:'.
			_DIR_RESTREINT;
			
    // Ajouter dist/
		$path = _DIR_RACINE.'squelettes-dist/:' . $path;

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

?>