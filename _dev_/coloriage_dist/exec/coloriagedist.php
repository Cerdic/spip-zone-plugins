<?php
// ---------------------------------------------------------
//  Coloriage
// 
//  avertissement: code approximatif ... juste pour le plaisir de tester la fonction
//
//  version:  1.0
//  date:     2006.11.23
//  author:   erational <http://www.erational.org>
//  licence:  GPL
// ---------------------------------------------------------

include(dirname(__FILE__).'/../coloriagedist_fonctions.php');

// -------------------------------
// Main: Ma Change fond
// -------------------------------

function exec_coloriagedist(){  
  global $connect_statut;
  include_spip('inc/invalideur');
  
  
  $p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__file__))));
  define('_DIR_PLUGIN_COLORIAGEDIST',(_DIR_PLUGINS.end($p)));
  $path_plugin = dirname(__file__)."/../";
  
  // donnees CSS --------------------------------------------
  $css_data = array( 
    'coloriage_html_bg' => '',
    'coloriage_page_bg' => '',
    'coloriage_a'  => '',
    'coloriage_a_hover'  => '',
    'coloriage_entete_bg' => '',
    'coloriage_contenu_col' => '',
    'coloriage_pied_bg' => '', 
  );
  
  
  // main ------------------------------------------------------  
  debut_page(_T('coloriagedist:change_fond')); 

	if ($connect_statut == "0minirezo") {
	  // Action
	  $flag = false;
	  foreach ($css_data as $css_part=>$val) {
	  	if (isset($_POST["$css_part"])) {
	  	    $flag = true;	  	    
          $value = substr($_POST["$css_part"],0,6); // verif minimale: couleur FF6600 (chaine de 6)
    	    ecrire_meta($css_part, $value);
    			ecrire_metas();    		
    	}	  
	  }
	  if ($flag) { 	// bourrin: on vide tout le cache
      // FIXME : supprimer uniquement trouver le fichier squelette CSS, cf inc/invalideur
	   	supprime_invalideurs(); 
      purger_repertoire(_DIR_CACHE, 0);
      /* ... trouver comment supprimer uniquement spip.php?page=css du cache ??
     include_spip('public/cacher');
     $item = array("page"=>"css_coloriage");
     echo generer_nom_fichier_cache($item);
     */ 
    }
	
	  // HTML output 
	  echo "<link rel='stylesheet' type='text/css' href='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/colorpicker.css'>\n"; // FIXME a injecter ds head    
    echo "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/lib/prototype.js' type=\"text/javascript\"></script>\n";
    echo "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/scriptaculous/scriptaculous.js' type=\"text/javascript\"></script>\n";
    echo "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/yahoo.color.js' type=\"text/javascript\"></script>\n";
    echo "<script src='"._DIR_PLUGIN_COLORIAGEDIST."colorpicker/colorpicker.js' type=\"text/javascript\"></script>\n"; // cette ligne fait planter IE
	  
	  gros_titre(_T('coloriagedist:change_fond'));
    debut_gauche();
  
    debut_boite_info();
    echo _T('coloriagedist:info');
    fin_boite_info();
    
    debut_droite();
	    
    echo "<form method='post'><input type='hidden' name='exec' value='change_fond' />";
    $str1 = "";
    $str2 = "";
    $str3 = "";
    foreach ($css_data as $css_part=>$val) {
           // search existing value, if not init
           if (!isset($GLOBALS['meta'][$css_part])) {	
          			ecrire_meta($css_part, 'FF6600'); // i luv orange ;)
          			ecrire_metas();     
           } 	
           $value =  $GLOBALS['meta'][$css_part]; 
                      
           // display
           $str1 .= _T("coloriagedist:$css_part")."<br />\n";
           $str1 .= "<input type='text' id='$css_part' name='$css_part' value='$value' ><br />\n";           
           $str2 .= "new Control.ColorPicker(\"$css_part\", { IMAGE_BASE : \""._DIR_PLUGIN_COLORIAGEDIST."colorpicker/img/\" });";  // BUG FIXME  \n ou retour ligne font planter IE
           $str3 .= "\"$css_part\",";
    }
    echo "$str1<br /><input type='submit' value='"._T("coloriagedist:enregistre_couleurs")."' />";
    echo "<script type=\"text/javascript\">$str2</script>";  // BUG IE ???
    /* la 2eme syntaxe fait aussi planter IE
    echo "<script type=\"text/javascript\">";
    echo "[$str3].each(function(idx) {";
    echo "  new Control.ColorPicker(idx)";
    echo "});";
    echo "</script>";*/   
    fin_page();

	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
}

?>
