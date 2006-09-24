<?php
// petit script qui genere le fichier refcorespip191.txt
// ce script est à lancer en local surtout par surtout par sur un serveur
// vu que ce script utilise la commande svn
// ce fichier sera utiliser ensuite par le plugin verifcore
// la premiere ligne contient svn://trac.rezo.net/spip/branches/spip-1.9
// la seconde ligne contient la date de creation de  refcorespip191.txt
// ensuite toutes les lignes sont composées de trois parties
// nom du fichier:taille du fichier:date de derniere modification svn du fichier

$cwd = getcwd();

// creation d'un repertoire temporatoire pour mettre la version svn
$repert = "reppourcreationsvntmp/";
exec("mkdir -p  $repert");
chdir ("$repert") ;
exec("svn checkout svn://trac.rezo.net/spip/branches/spip-1.9 .");
$taille_fichier =array() ;
$date_fichier =array() ;
$date_fichier2 =array() ;

function remplir_tableau($d){
  $args = func_get_args();
  $lefic  = array_shift($args);
  global $date_fichier;
  global $date_fichier2;
  global $taille_fichier ;
  
  $taille_fichier["$lefic"] = filesize($lefic) ; 
  $date_fichier2["$lefic"] = filemtime($lefic)  ;
  exec("svn info $lefic  > ../toto");
  $test_fin = 0 ;
  $tmpfic = fopen("../toto", "r");
  while ((!feof($tmpfic))&&($test_fin==0)) {
    $contenu = fgets($tmpfic, 4096);
    trim($contenu) ; 
    if(preg_match('/\.*:\s*(\d*)-(\d*)-(\d*)\s*(\d*):(\d*):(\d*)/', $contenu,$res)){
      $ladate =mktime($res[4], $res[5], $res[6], $res[2], $res[3], $res[1] ) ;
      $date_fichier["$lefic"] = $ladate ;
      $test_fin = 1 ;
    }
  }
  fclose($tmpfic);
}

function explorateur($repertoire){
  $id_dossier = opendir($repertoire);
  while ($fichier = readdir($id_dossier)) { 
    $id_fichier = "$repertoire"."/"."$fichier";
    if(is_file($id_fichier)) {
      preg_match('/^\.\/(.*)/', $id_fichier,$res) ; 
      remplir_tableau( $res[1] ) ;	
    }elseif(is_dir($id_fichier)&&!(preg_match('/^\./', $fichier))){
      explorateur($id_fichier) ;
    }
  }
}

//on lance l'exploration des fichiers
explorateur(".") ;


chdir ("$cwd") ;
// on cree maintenant le fichier  refcorespip191.txt avec les informations récolté dans explorateur()
$fich = fopen("refcorespip191.txt", "w+"); 
exec("svn info $repert > toto");
$test_fin = 0 ; 
$tmpfic = fopen("toto", "r");
while ((!feof($tmpfic))&&($test_fin==0)) {
  $contenu = fgets($tmpfic, 4096);
  trim($contenu) ; 
  if(preg_match('/URL\s*:\s*([0-9a-zA-Z\/\.\_\-\:]*)/', $contenu,$res)){
    fwrite($fich, "$res[1]\n") ;
  }
 }
fclose($tmpfic);
fwrite($fich, "temps:".time()."\n") ;
foreach ($taille_fichier as $cle=>$valeur){
  fwrite($fich, "$cle:$valeur:") ;
  $tutu =  $date_fichier["$cle"] ;
  fwrite($fich, "$tutu\n");
}
fclose($fich);
 exec("rm toto");
 exec("chmod -R 777 $repert");
 exec("rm -r $repert");
?>