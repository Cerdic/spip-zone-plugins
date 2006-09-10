<?php
  // script pour genere entre autres le fichier fichier_repertoire_supprimer_depuis_svn6797.txt
  // utiliser pour detecter les fichiers obsoletes de 
$cwd = getcwd();

//######## Parametres  ###################

$local_svn = "tmppourcreationsvn/" ;
$local_export = "tmppourexport/" ;
#numero de version de spip 1.9 1 er juillet 
$rev_num = 6797 ;
$file_transfert = "fichier_repertoire_ajoute_modifie_depuis_svn".$rev_num ;
$file_delete = "fichier_repertoire_supprimer_depuis_svn".$rev_num ;
$chemin_absolu_svn_premier = "/spip/" ;
$chemin_absolu_svn_deuxieme = "/branches/spip-1.9/" ;
$repertoire_de_base =  $cwd ;


//####### déclaration des variables globale Ne touchez pas #####################
$fileadd    = array() ;
$repadd     = array() ;
$filedelete = array() ;
$repdelete  = array() ;
$chemin_absolu_local_svn =  "$cwd"."/"."$local_svn"  ;
$chemin_absolu_export =  "$cwd"."/"."$local_export"  ;
//####### Fin déclaration variable globale #####################

function  add_file_or_directory($nom_fichier){
  $i = 0 ;
  global $fileadd ;
  global $repadd ;
  global $filedelete ;
  global $repdelete ;
  $fichier_decompo = explode("/", $nom_fichier );
  $nom_fichier  = implode( "/", $fichier_decompo );
  if ( preg_match( '/[0-9a-zA-Z_-]*\.[0-9a-zA-Z]*/',end($fichier_decompo) )) {
    array_pop($fichier_decompo);
    $fichier_decompo_rep = implode( "/", $fichier_decompo);
    if(in_array($nom_fichier, $filedelete)){
      $i = 0 ;
      array_push( $fileadd, $nom_fichier ) ;
      foreach( $filedelete as $file){ 
	if( $file == $nom_fichier){
	  array_splice( $filedelete, $i, 1) ;
	  break ;
	}
	$i++ ;
      }
      if(in_array($fichier_decompo_rep, $repdelete)) {
	$i = 0 ;
	array_push( $repadd, $fichier_decompo_rep ) ;
	foreach( $repdelete as $file){ 
	  if( $file == $fichier_decompo_rep ){
	    array_splice( $repdelete, $i, 1) ;
	    break ;
	  }
	  $i++ ;
	}
      }
    } else {
      if(!(in_array($nom_fichier, $fileadd))) {	
	array_push( $fileadd, $nom_fichier) ;
	if(!(in_array($fichier_decompo_rep, $repadd))){
	  array_push( $repadd, $fichier_decompo_rep ) ;
	}	
      }
    }
  } else {
    if(in_array($nom_fichier, $repdelete)){
      $i = 0 ; 
      array_push( $repadd, $nom_fichier ) ;
      foreach( $repdelete as $file){ 
	if($file == $nom_fichier){
	  array_splice( $repdelete, $i, 1) ;
	  break ;
	}
	$i++ ;
      }
    } else {
      if(!(in_array( $nom_fichier, $repadd ))){
	array_push( $repadd, $nom_fichier ) ;
      }
    }
  }
}

function delete_file_or_directory($nom_fichier){
  $i = 0 ;
  global $fileadd ;
  global $repadd ;
  global $filedelete ;
  global $repdelete ;
  $fichier_decompo = explode("/", $nom_fichier );
  $nom_fichier  = implode( "/", $fichier_decompo );
  if ( preg_match( '/[0-9a-zA-Z_-]*\.[0-9a-zA-Z]*/',end($fichier_decompo) )) { 
    array_pop($fichier_decompo);
    $fichier_decompo_rep = implode( "/", $fichier_decompo);

    if(in_array($nom_fichier, $fileadd)) {
      $i = 0 ; 
      array_push( $filedelete, $nom_fichier) ;
      foreach( $fileadd as $file){ 
	if( $file == $nom_fichier){
	  //          $tututu =$fileadd["$i"] ;
	  array_splice( $fileadd, $i, 1) ;
	  break ;
	}
	$i++ ;
      }
    } else {
      if(!(in_array($nom_fichier, $filedelete))) {	
	array_push( $filedelete, $nom_fichier) ;
      }
    }
  } else {
    if (in_array($nom_fichier, $repadd)) {
      $i = 0 ; 
      array_push( $repdelete, $nom_fichier ) ;
      foreach( $repadd as $file){ 
	if( $file  == $nom_fichier){
	  array_splice(  $repadd, $i, 1) ;
	  break ;
	}
	$i++ ;
      }
    } else {
      if (!(in_array($nom_fichier, $repdelete))) {
	array_push( $repdelete, $nom_fichier ) ;
      }
    }
  }
}

chdir ("$repertoire_de_base")  ;

//creation repertoire où sera stocker provisoirement la svn
if ( is_dir($local_svn)) {
  exec( "chmod -R 777  $local_svn" ) ;
  exec( "rm -r $local_svn" ) ;
 }
exec( "mkdir -p $local_svn" ) ;

//creation repertoire qui servira a faire l'archive
if ( is_dir($local_export)) {
  exec( "chmod -R 777  $local_export" ) ;
  exec( "rm -r $local_export" ) ;
 }
exec( "mkdir -p $local_export" ) ;

chdir ("$chemin_absolu_local_svn") ;

exec( "svn checkout svn://trac.rezo.net/spip/branches/spip-1.9 ." ) ;

## Imprimer svn log dans le fichier diff_svnlog
exec( "svn log  -r $rev_num:HEAD  -v > ../diff_svnlog" ) ;
chdir ("$repertoire_de_base")  ;

$chemin_absolu_svn_premier = "/spip" ;
$chemin_absolu_svn_deuxieme = "/branches/spip-1.9" ;

$chemin_absolu_svn = $chemin_absolu_svn_premier ;
// Analyse du svn log pour découvrir les fichiers ajoutés modifié ou supprimés
$split_chemin_absolu_svn = explode("/", $chemin_absolu_svn ) ;

$tmpfic = fopen("diff_svnlog", "r");
while (!feof($tmpfic)) {
  $contenu =trim(fgets($tmpfic, 4096));
  if ( preg_match('/^(\w)?\s{1}(\/\S*)(\s\(de\s)?([0-9a-zA-Z\/\._-]*)?.*/',$contenu,$res )) {

    $file = $res[2] ;  
    $split_file = explode("/", $file ) ;
    $tab_debut_fichier = array() ;
    if ( count($split_file) >= count($split_chemin_absolu_svn) ) {
      foreach ($split_chemin_absolu_svn as $av) {
	array_push( $tab_debut_fichier, array_shift($split_file)) ;
      }
      $debut_fichier = implode( "/", $tab_debut_fichier ) ;
      $typemodif = $res[1] ;

      if ( ($debut_fichier == "$chemin_absolu_svn") || ($file == "$chemin_absolu_svn_deuxieme")) {
     
	if ($file == "$chemin_absolu_svn_deuxieme") {
	  $chemin_absolu_svn = $chemin_absolu_svn_deuxieme ;
	  $split_chemin_absolu_svn = explode("/", $chemin_absolu_svn ) ;
	} else {
	  $file  = implode( "/", $split_file );
	  if ( $res[4] != "" ) {
	    $fichier_changer = $res[4] ;
	    $split_fichier_changer = explode("/", $fichier_changer ) ;
	    $tab_debut_fichier_changer = array() ;
	    if ( count($split_fichier_changer) >= count($split_chemin_absolu_svn) ) {
	      foreach ($split_chemin_absolu_svn as $av) {
		array_push( $tab_debut_fichier_changer, array_shift($split_fichier_changer)) ;
	      }
	      $debut_fichier_changer = implode( "/", $tab_debut_fichier_changer );
	      $fichier_changer = implode( "/", $split_fichier_changer );
	      if ( ($debut_fichier_changer == $chemin_absolu_svn)  ) {
		if ( preg_match( '/[0-9a-zA-Z_-]*\.[0-9a-zA-Z]*/',end($split_fichier_changer) )) {
		  delete_file_or_directory($fichier_changer) ;
		  add_file_or_directory( $file ) ;	
		} else { 
		  delete_file_or_directory($fichier_changer) ;
		  //eliminer le repertoire $fichier_changer qui a été bougé
                  chdir ("$local_svn") ;
		  exec("ls -R $file>temporaire_mouvement"); 
		  $tmptmpfic = fopen("temporaire_mouvement", "r");
		  $new_directory = '';
		  while (!feof($tmptmpfic)) {
		    $conten =trim(fgets($tmptmpfic, 4096));
		    if (  preg_match('/^([0-9a-zA-Z\/\._-]*):.*/', $conten, $resu) ) {
		      $new_directory = $resu[1] ;
		    } elseif ( $conten != '' &&  preg_match('/[0-9a-zA-Z_-]*\.[0-9a-zA-Z]*/', $conten)  && $new_directory != '' ) {
		      add_file_or_directory($new_directory."/".$conten) ;
		    } elseif ( $conten != '' &&  preg_match('/[0-9a-zA-Z_-]*\.[0-9a-zA-Z]*/', $conten)  && $new_directory == '' ) {
		      add_file_or_directory($conten) ;
		    }
		  }
		  fclose($tmptmpfic) ;
		  exec("rm temporaire_mouvement");
		  chdir ("$repertoire_de_base") ;
		}
	      }
	    }
	  } else {
	    if ( ($typemodif == "A") || ($typemodif == "M") ) {
	      add_file_or_directory($file) ;
	    } elseif ( $typemodif == "D" ) {
	      delete_file_or_directory($file) ;
	    }
	  }
	}
      }
    }
  }
}
fclose ($tmpfic) ;
exec( "rm diff_svnlog" ) ;

chdir ("$repertoire_de_base")  ;
$tmpfic = fopen ("$file_transfert.txt", "w+" ) ; 
chdir ("$chemin_absolu_local_svn") ;
fwrite($tmpfic , "Dossiers ajoutés\n" ) ;
exec( "mkdir -p $local_export/" ) ;
foreach( $repadd as $v) { 
  exec( "mkdir -p $local_export/$v" ) ;
  fwrite($tmpfic ,"$v\n") ;
}
chdir ("$chemin_absolu_local_svn") ;
fwrite($tmpfic ,"\n\nFichiers ajoutés\n") ;
foreach( $fileadd as $v ) {
  exec( "cp --parents $v ../$local_export/" ) ;
  fwrite($tmpfic,"$v\n") ;	
}
fclose($tmpfic) ;


chdir ("$chemin_absolu_export")  ;
// decommenter ligne suivante pour obtenir un tgz des fichiers modifié entre spip 1.9 et sp 1.9.1
//exec( "tar cvfz $file_transfert.tgz *" ) ;
//chdir ("$repertoire_de_base")  ;
//exec( "mv $local_export/$file_transfert.tgz ." ) ;

chdir ("$chemin_absolu_export")  ;
// decommenter ligne suivante pour obtenir un zip des fichiers modifié entre spip 1.9 et sp 1.9.1
//exec( "zip -r $file_transfert *" ) ;
//chdir ("$repertoire_de_base")  ;
//exec( "mv $local_export/$file_transfert.zip ." ) ;

chdir ("$repertoire_de_base")  ;

//creation du fichier désirer
$tmpfic = fopen ("$file_delete.txt", "w+" ) ; 
fwrite($tmpfic , "\n\nFichiers supprimés\n") ;	
foreach($filedelete as $v) {
  fwrite($tmpfic, "$v\n") ;	
}

fwrite($tmpfic, "\n\nDossiers supprimés\n") ;

foreach( $repdelete as $v ) {
  fwrite($tmpfic, "$v\n") ;
}
fclose($tmpfic) ;

echo "Les fichiers à transférer sont listés dans le fichier $file_transfert.txt et une archive est faite dans $file_transfert.tgz\n Les fichiers à supprimer sont listés dans le fichier $file_delete.txt\n" ;


#suppression des répertoires temporaires
exec( "chmod -R 777  $local_svn" ) ;
exec( "rm -r $local_svn" ) ;
exec( "chmod -R 777  $local_export" ) ;
exec( "rm -r $local_export" ) ;

//fin script
?>