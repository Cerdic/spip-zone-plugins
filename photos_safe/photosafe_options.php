<?php

 function balise_MAT($p){
     $p->code = "calculer_balise_MAT()";
     return $p;
 }

 function calculer_balise_MAT(){
    $cmdligne = "mat -v 2>&1";
    exec($cmdligne, $output, $return_var);
    //spip_log($output,'photosafe');
    return $output[0];
 }


?>
