<?php
/**
 * Plugin SkelEditor
 * Editeur de squelette en ligne
 * (c) 2007-2010 erational
 * Licence GPL-v3
 *
 */

function skeleditor_dossier(){
	include_spip('inc/skeleditor');
	return skeleditor_path_editable();
}


/**
 * Afficher l'arborescence du dossier squelette
 *
 * @param string $path_base
 * @param string $current_file
 * @return string
 */
function skeleditor_afficher_dir_skel($path_base,$current_file) {
	include_spip('inc/skeleditor');
	include_spip('inc/presentation');
	$file_list = skeleditor_files_editables($path_base);
	$current_file = substr($current_file,strlen($path_base));

  $output = "<div id='arbo'><div class='dir'>\n";
	$init_dir = $current_dir = "";
	foreach($file_list as $file){
		$dir = substr(dirname($file),strlen($path_base));
		$file = substr($file,strlen($path_base));

		if ($dir != $current_dir)
			$output .= skeleditor_tree_open_close_dir($current_dir,$dir,$current_file);

		$class="fichier";
		if (!is_writable($path_base.$dir))
			$class .= " readonly";

		$class .= ($file==$current_file?" on":'');

		$icon = "file";
		if (preg_match(',('._SE_EXTENSIONS_IMG.')$,',$file))
			$icon = "image";

		$output .= "<a href='".generer_url_ecrire('skeleditor','f='.urlencode($path_base.$file))."' class='$class'>"
						. "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_210/img_pack/se-$icon-16.png' alt='$icon' /> "
						.basename($file)."</a>";
	}
	$output .= skeleditor_tree_open_close_dir($current_dir,$init_dir,$current_file);
  $output .= "</div></div>\n";
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
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_210/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_nouveau");
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
  $output .= "<img src='"._DIR_PLUGIN_SKELEDITOR."spip_210/img_pack/action_add.png' alt='new' />"._T("skeleditor:fichier_upload");
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


function skeleditor_codemirror($filename,$editable=true){
	if (!$filename)
		return "";

	$infos = pathinfo($filename);
	switch($infos['extension']){
		case 'sh':
		case 'txt':
		case 'nfo':
		case 'log':
		case 'csv':
			$parsers = null;
			break;
		case 'as':
		case 'js':
			$parsers = array("tokenizejavascript.js", "parsejavascript.js");
			$css = array("css/jscolors.css");
			// autoMatchParens: true
			break;
		case 'css':
			$parsers = array("parsecss.js");
			$css = array("css/csscolors.css");
			break;
		case 'xml':
		case 'svg':
		case 'rdf':
			$parsers = array("parsexml.js");
			$css = array("css/xmlcolors.css");
			#continuousScanning: 500,
			break;
		case 'sql':
			$parsers = array("../contrib/sql/js/parsesql.js");
			$css = array("css/sqlcolors.css");
			#textWrapping: false,
			break;
		case 'py':
			$parsers = array("../contrib/python/js/parsepython.js");
			$css = array("css/pythoncolors.css");
      #  lineNumbers: true,
      #  textWrapping: false,
      #  indentUnit: 4,
      #  parserConfig: {'pythonVersion': 2, 'strictErrors': true}
			break;

		case 'php':
		case 'html':
		case 'htm':
		default:
			$parsers = array("parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                     "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js",
                     "../contrib/php/js/parsephphtmlmixed.js");
			$css = array("css/xmlcolors.css", "css/jscolors.css", "css/csscolors.css", "contrib/php/css/phpcolors.css");
			break;
	}

	if (is_null($parsers))
		return "";

	$readonly = "";
	if (!$editable)
		$readonly = "readonly: true,\n";

	$dir = _DIR_PLUGIN_SKELEDITOR.'spip_210/codemirror/';
	$dirjs = _DIR_PLUGIN_SKELEDITOR.'spip_210/codemirror/js/';

	$script = '<script src="'._DIR_PLUGIN_SKELEDITOR.'spip_210/codemirror/js/codemirror.js" type="text/javascript"></script>
<script type="text/javascript">
var editor;
function init_code_edit(){
	if (jQuery("#code").is(":visible")){
		editor = CodeMirror.fromTextArea(\'code\', {
			height: "550px",
			parserfile: ["'.implode('", "',$parsers).'"],
			stylesheet: ["'.$dir.implode('", "'.$dir,$css).'"],
			path: "'._DIR_PLUGIN_SKELEDITOR.'spip_210/codemirror/js/",
			continuousScanning: 500,
			textWrapping: false,
			readOnly: jQuery("#code").attr("readonly"),
			lineNumbers: true
		});
	}
}
init_code_edit();
</script>
<style type="text/css">
.codewrap {border:1px solid #333;background:#fff;font-size:11px;clear:both;}
.codewrap.readonly {background:#ddd;}
.CodeMirror-line-numbers {
        width: 2.2em;
        color: #aaa;
        background-color: #eee;
        text-align: right;
        padding-right: .3em;
        font-size: 10pt;
        font-family: monospace !important;
        padding-top: .4em;
				line-height:15px;
      }
.CodeMirror-line-numbers *{
		font-family: monospace !important;
}

</style>
';
	return $script;
}
?>