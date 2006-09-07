<?php
define('_DIR_PLUGIN_VERIFCORE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__).'/..'))))));

function exec_config_verifcore() {
  include_spip ("inc/presentation");
  include_spip ("inc/distant"); 
 
  debut_page('configurations');
  echo '<br><br><br>';
  gros_titre("V&eacute;fication mis a jour");
  
  barre_onglets("configuration", "config_verifcore");
  
  debut_gauche();
  
  debut_droite();	
  debut_cadre_enfonce();
  
  $fichier_inexistant = array() ;
  $fichier_modifie = array() ;
  $fichier_supprime =array() ;
  $dossier_supprime =array() ;
  $branche = "";
  $dateversion = "";
	
  $cwd = getcwd();
  chdir (_DIR_RACINE) ; 
  $fich =recuperer_page("http://zone.spip.org/trac/spip-zone/browser/_plugins_/_amelioration_admin_/verifcore/refcorespip191.txt?format=txt");
  if($fich){
    $tab_fic = split( "\n" ,"$fich") ;

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

    $fich =recuperer_page("http://zone.spip.org/trac/spip-zone/browser/_plugins_/_amelioration_admin_/verifcore/fichier_repertoire_supprimer_depuis_svn6797.txt?format=txt"); 
    if($fich){
      $tab_fic = split( "\n" ,"$fich") ;
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

  
    echo "Ce programme effectue une comparaison entre vos fichiers et ceux de la version $branche dat&eacute; du $dateversion<br />" ;
    if( count($fichier_inexistant) == 0 && count($fichier_modifie) == 0 && count($fichier_supprime) == 0 && count($dossier_supprime) == 0 ){
      echo "Tout va tr&egrav;s bien vos fichiers correspondent bien &agrave; la version $branche" ;
    }else{
      if( count($fichier_inexistant) != 0){
	echo "ATTENTION DANGER il vous manque les fichiers suivants\n<ul>" ;
	foreach ($fichier_inexistant as $file){
	  echo "<li>$file(<a href=\"http://trac.rezo.net/trac/spip/browser/$branche/$file?format=txt\">Voir l'original</a></li>\n" ;
	}
	echo "</ul>\n" ;
      }
      
      if( count($fichier_modifie) != 0){
	echo "ATTENTION vos fichiers suivants sont modifi&eacute;s par rapport &agrave;  ceux de la version $branche:\n<ul>" ;
	foreach ($fichier_modifie as $file){
	  echo "<li>$file(<a href=\"http://trac.rezo.net/trac/spip/browser/$branche/$file?format=txt\">Voir l'original</a>)</li>\n" ;
	}
	echo "</ul>\n" ;
      }
      
      if( count($fichier_supprime) != 0){
	echo "Pour info les fichiers suivant sont obsol&egrave;tes pour $branche, vous pouvez les supprimez\n<ul>" ;
	foreach ($fichier_supprime as $file){
	  echo "<li>$file</li>\n" ;
	}
	echo "</ul>\n" ;
      }
      
      if( count($dossier_supprime) != 0){
	echo "Pour info les r&eacute;pertoires suivants sont obsol&egrave;tes pour  $branche, vous pouvez les supprimez\n<ul>" ;
	foreach ($dossier_supprime as $file){
	  echo "<li>$file</li>\n" ;
	}
	echo "</ul>\n" ;
      }
    } 

  }else{
    echo "Attention le fichier http://zone.spip.org/trac/spip-zone/browser/_plugins_/_amelioration_admin_/verifcore/refcorespip191.txt?format=txt n\'existe plus\n<br />" ;
    echo "Contactez auteur du plugin rudjob chez gmail.com" ;
  }
  
  fin_cadre_enfonce();

  chdir ($cwd) ;
  fin_page();
}
?>
