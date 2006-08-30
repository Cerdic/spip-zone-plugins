<?php

function comparaison($tableau_grille){// compare les variables Post avec la valeau de la solution...
    $erreurs=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne++;
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
            
            if (strtoupper($GLOBALS["col".$colonne."lig".$ligne])!=strtoupper($cellule) and $cellule!='*') //compare les valeurs du tableau PHP avec les variables POST
               {$erreurs++;
               
         		}
         	
               
            }
            }
    return $erreurs;}

?>