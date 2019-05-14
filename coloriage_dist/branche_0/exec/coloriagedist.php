<?php
// ---------------------------------------------------------
//  Coloriage
// --------------------------------------------------------- 


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

include(dirname(__FILE__).'/../coloriagedist_fonctions.php');

// -------------------------------
// Main: Ma Change fond
// -------------------------------

function exec_coloriagedist(){  
  global $connect_statut;
  include_spip('inc/invalideur');

  
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
  $commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('coloriagedist:change_fond'), "editer", "editer");
  

	if ($connect_statut == "0minirezo") {
	  // Action
	  $flag = false;
	  foreach ($css_data as $css_part=>$val) {
	    if (_request($css_part)) {	  	
	  	    $flag = true;	  	    
          $value = substr($_POST["$css_part"],0,7); // verif minimale: #couleur FF6600 (chaine de 7)
    	    ecrire_meta($css_part, $value);
    			ecrire_metas();    		
    	}	  
	  }
	  if ($flag) 	
        suivre_invalideur("id=css_coloriage'"); 
    

	  echo gros_titre(_T('coloriagedist:change_fond'),'', false);

    // colonne gauche
    echo debut_gauche('', true);  
    echo  debut_boite_info(true);
    echo _T('coloriagedist:info');
    echo fin_boite_info(true);
    echo "<div id=\"picker\"></div>";

    // centre
    echo debut_droite('', true); 
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