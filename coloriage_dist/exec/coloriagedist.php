<?php
// ---------------------------------------------------------
//  Coloriage
// 
//  avertissement: code approximatif ... juste pour le plaisir de tester la fonction
//
//  version:  2.0
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
          $value = substr($_POST["$css_part"],0,7); // verif minimale: #couleur FF6600 (chaine de 7)
    	    ecrire_meta($css_part, $value);
    			ecrire_metas();    		
    	}	  
	  }
	  if ($flag) { 	
      // FIXME : supprimer uniquement trouver le fichier squelette CSS, cf inc/invalideur	   	
      //purger_repertoire(_DIR_CACHE, 0); // bourrin: on vide tout le cache
       suivre_invalideur("page LIKE '%css_coloriage'"); // syntaxte correcte ? 
    }
	
	  // HTML output 
	  gros_titre(_T('coloriagedist:change_fond'));
    debut_gauche();
    
  
    debut_boite_info();
    echo _T('coloriagedist:info');
    fin_boite_info();
    echo "<div id=\"picker\"></div>";

    
    debut_droite();	    
    echo "<form method='post'><input type='hidden' name='exec' value='change_fond' />";
    $str1 = "";
    $str2 = "";
    $str3 = "";
    foreach ($css_data as $css_part=>$val) {
           // search existing value, if not init
           if (!isset($GLOBALS['meta'][$css_part])) {	
          			ecrire_meta($css_part, '#FF6600'); 
          			ecrire_metas();     
           } 	
           $value =  $GLOBALS['meta'][$css_part];  // FIXME: economisez les meta en utilisant plugin cfg ? 
                      
           // display
           $str1 .= "<label for='$css_part'>"._T("coloriagedist:$css_part")."</label><br />\n";
           $str1 .= "<input type='text' id='$css_part' name='$css_part' value='$value'  class='colorwell' /><br />\n"; 
    }
    echo "$str1<br /><input type='submit' value='"._T("coloriagedist:enregistre_couleurs")."' />";
    fin_page();

	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
}

?>
