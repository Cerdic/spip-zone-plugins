<?php

function explorateur_php3($repertoire){
  global $verifcore_fichierphp3;
  $id_dossier = opendir($repertoire);
  while ($fichier = readdir($id_dossier)) { 
    $id_fichier = "$repertoire"."/"."$fichier";
    if(is_file($id_fichier)) {
      if(preg_match('/^\.\/([0-9a-zA-Z\/\.\_\-\:]*\.php3)/', $id_fichier,$res)&&($id_fichier!="./inc-public.php3")){
      array_push( $verifcore_fichierphp3 , $res[1] ) ;
}
    }elseif(is_dir($id_fichier)&&!(preg_match('/^\./', $fichier))){
      explorateur_php3($id_fichier) ;
    }
  }

}

?>

