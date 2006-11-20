<?php

// compare les variables Post avec la valeau de la solution...
function comparaison($tableau_grille){
    $erreurs=0; $vides=0;
    foreach($tableau_grille as $ligne =>$contenu_ligne){
        $ligne++;
        foreach ($contenu_ligne as $colonne =>$cellule){
            $colonne++;
			
            //compare les valeurs du tableau PHP avec les variables POST
			if ($cellule!='*') {
	            if (trim($GLOBALS["col".$colonne."lig".$ligne])=='') $vides++;
    	        elseif (strtoupper($GLOBALS["col".$colonne."lig".$ligne])!=strtoupper($cellule)) $erreurs++;
			}	
		}
	}
    return array($erreurs, $vides);
}

?>