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
  

  
  debut_droite();	
	
  $cwd = getcwd();
  chdir (_DIR_RACINE) ; 
  global $verifcore_fichierphp3 ;
  $verifcore_fichierphp3    = array () ;
  explorateur_php3(".") ;
  if( count($verifcore_fichierphp3) != 0){
    debut_cadre_trait_couleur("", false, "","Recherche fichiers php3");
    echo "Depuis spip 1.9, les fichiers php3 ont disparu du coeur de spip (mis &agrave; part inc-public.html) c'est pourquoi je vous propose de lister les fichiers php3 qui vous reste sur votre serveur.<br />\n" ;
    echo "Suivez les conseils de l'article  <a href=\"http://www.spip.net/fr_article3370.html\" >la migration vers spip 1.9 de spip.net</a><br /> " ;
    echo "Voici la liste des fichiers php3 : <br />\n" ;
    foreach ($verifcore_fichierphp3 as $file){
      echo "<li>$file</li>\n" ;
    }
    echo "</ul>\n" ;
    fin_cadre_trait_couleur();
  } else {
    debut_cadre_trait_couleur("", false, "","R&eacute;ussite"); 
    echo "Il n\' y a aucun fichier php3\n" ;
    fin_cadre_trait_couleur();
  }
  
  
  chdir ($cwd) ;
  fin_page();
}
?>
