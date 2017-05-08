<?php
define('_DIR_PLUGIN_VERIFCORE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

function exec_config_verifcore() {
  include_spip ("inc/presentation");
  include_spip ("inc/distant"); 
 
  debut_page('configurations');
  echo '<br><br><br>';
  gros_titre("V&eacute;fication mis a jour");
  
  barre_onglets("configuration", "config_verifcore");
  
  debut_droite();	
  
  $fichier_inexistant = array() ;
  $fichier_modifie = array() ;
  $fichier_supprime =array() ;
  $dossier_supprime =array() ;
  $branche = "";
  $dateversion = "";
	
  $cwd = getcwd();
  chdir (_DIR_RACINE) ; 
  $fich =recuperer_page("https://zone.spip.org/trac/spip-zone/browser/_plugins_/_test_/verifcore/refcorespip191.txt?format=txt");
  if($fich){
    $tab_fic = preg_split( ",\n," ,"$fich") ;

    foreach ( $tab_fic as $contenu ){
      trim($contenu) ;
      if(preg_match('/svn:\/\/trac.rezo.net\/spip\/([0-9a-zA-Z\/\.\_\-]*)/', $contenu,$res))
	{
	  $branche = "$res[1]" ;
	}else if(preg_match('/^temps:([0-9]*)/', $contenu,$res))
	{
	  $dateversion = date( 'd-m-Y H:i:s', "$res[1]") ;
	}else if(preg_match('/([0-9a-zA-Z\/\.\_\-]*)\s*:([0-9]*):([0-9]*)/', $contenu,$res))
	{
	  if(!is_file("$res[1]")){
	    $fichier_inexistant[] = "$res[1]" ;
	  }elseif(filesize("$res[1]")!= "$res[2]"||(filemtime("$res[1]")<$res[3]) ){
	    $fichier_modifie[] =  "$res[1]" ;
	  }
	}
    }

    $fich =recuperer_page("https://zone.spip.org/trac/spip-zone/browser/_plugins_/_test_/verifcore/fichier_repertoire_supprimer_depuis_svn6797.txt?format=txt"); 
    if($fich){
      $tab_fic = preg_split( ",\n," ,"$fich") ;
      $com = 0 ;
      foreach ( $tab_fic as $contenu ){
	trim($contenu) ;
	if(preg_match('/Fichiers supprim�s/', $contenu )){
	  $com = 1 ;  
	}elseif(preg_match('/Dossiers supprim�s/', $contenu )){
	  $com = 2 ;  
	}elseif(preg_match('/([0-9a-zA-Z\/\.\_\-]*)/', $contenu,$res)&&$com==1){
	  if(is_file("$res[1]")){
	    $fichier_supprime[] = "$res[1]" ;	
	  }
	}elseif(preg_match('/([0-9a-zA-Z\/\.\_\-]*)/', $contenu,$res)&&$com==2){
	  if(is_dir("$res[1]")){
	    $dossier_supprime[] = "$res[1]" ;
	  }
	}
      }
    }

    debut_cadre_trait_couleur("", false, "","Pr&eacute;ambule" );
    echo "Ce programme effectue une comparaison entre vos fichiers et ceux de la version $branche dat&eacute; du $dateversion<br />" ;
    fin_cadre_trait_couleur();
    if( count($fichier_inexistant) == 0 && count($fichier_modifie) == 0 && count($fichier_supprime) == 0 && count($dossier_supprime) == 0 ){
      debut_cadre_trait_couleur("", false, "","Fichiers modifi&eacute;" );
      echo "Tout va tr&egrav;s bien vos fichiers correspondent bien &agrave; la version $branche" ;
    }else{
      if( count($fichier_inexistant) != 0){ 
 	debut_cadre_trait_couleur("", false, "","Fichiers manquants" );
	echo "ATTENTION DANGER il vous manque les fichiers suivants\n<ul>" ;
	foreach ($fichier_inexistant as $file){
	  echo "<li>$file(<a href=\"http://trac.rezo.net/trac/spip/browser/$branche/$file?format=txt\">Voir l'original</a></li>\n" ;
	}
	echo "</ul>\n" ;
     fin_cadre_trait_couleur();
      }
      
      if( count($fichier_modifie) != 0){
	debut_cadre_trait_couleur("", false, "","Fichiers modifi&eacute;s" );
	echo "ATTENTION vos fichiers suivants sont modifi&eacute;s par rapport &agrave;  ceux de la version $branche:\n<ul>" ;
	foreach ($fichier_modifie as $file){
	  echo "<li>$file(<a href=\"http://trac.rezo.net/trac/spip/browser/$branche/$file?format=txt\">Voir l'original</a>)</li>\n" ;
	}
	echo "</ul>\n" ;
	fin_cadre_trait_couleur();
      }
      
      if( count($fichier_supprime) != 0){
	debut_cadre_trait_couleur("", false, "","Fichiers obsol&egrave;tes" );
	echo "Pour info les fichiers suivants sont obsol&egrave;tes pour $branche, vous pouvez les supprimez\n<ul>" ;
	foreach ($fichier_supprime as $file){
	  echo "<li>$file</li>\n" ;
	}
	echo "</ul>\n" ;
    fin_cadre_trait_couleur();
      }
      
      if( count($dossier_supprime) != 0){
	debut_cadre_trait_couleur("", false, "","R&eacute;pertoires obsol&egrave;tes" );
	echo "Pour info les r&eacute;pertoires suivants sont obsol&egrave;tes pour  $branche, vous pouvez les supprimez\n<ul>" ;
	foreach ($dossier_supprime as $file){
	  echo "<li>$file</li>\n" ;
	}
	echo "</ul>\n" ;
	fin_cadre_trait_couleur();
      }
    } 

  }else{ 
    debut_cadre_trait_couleur("", false, "","IMPOSSIBLE fichier r&eacute;f&eacute;rent manquant" );
    echo "Attention le fichier https://zone.spip.org/trac/spip-zone/browser/_plugins_/_test_/verifcore/refcorespip191.txt?format=txt n\'existe plus\n<br />" ;
    echo "Contactez auteur du plugin rudjob chez gmail.com" ;
    fin_cadre_trait_couleur();
  } 
   debut_cadre_trait_couleur("", false, "","Recherche php3" );
  echo "Vous avez la possibilit&eacute; de chercher tous les fichiers d'extension php3 qui se trouve sur votre serveur<br />" ;
  echo "En effet depuis spip 1.9 les fichiers php3 sont devenus obsol&egrave;tes mis &agrave;  part inc-public.php3 &agrave; la racine de votre site<br />" ;
  echo "Pour avoir la liste de tous ces fichiers cliquez sur le lien suivant <a href=\"".generer_url_ecrire("verifcore_cherche_php3")."\" > cherche le php3 </a>" ;
    fin_cadre_trait_couleur();

  chdir ($cwd) ;
  fin_page();
}
?>
