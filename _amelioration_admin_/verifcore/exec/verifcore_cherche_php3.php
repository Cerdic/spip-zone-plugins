<?php
define('_DIR_PLUGIN_VERIFCORE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));


function exec_verifcore_cherche_php3() {
  include_spip ("inc/presentation");
  include_spip ("inc/distant"); 
  include_spip ("inc/verifcore_fonctions"); 

  debut_page('configurations');
  echo '<br><br><br>';
  gros_titre("V&eacute;fication mis a jour");
  
  barre_onglets("configuration", "config_verifcore");
  
  debut_gauche();
  
  debut_droite();	
  debut_cadre_enfonce();
  

	
  $cwd = getcwd();
  chdir (_DIR_RACINE) ; 
  global $verifcore_fichierphp3 ;
    $verifcore_fichierphp3    = array () ;
  explorateur_php3(".") ;
  if( count($verifcore_fichierphp3) != 0){
    echo "Pour info lesfichiers  en extension php3 sont :\n<ul>" ;
    foreach ($verifcore_fichierphp3 as $file){
      echo "<li>$file</li>\n" ;
    }
    echo "</ul>\n" ;
  } 
  fin_cadre_enfonce();

  chdir ($cwd) ;
  fin_page();
}
?>
