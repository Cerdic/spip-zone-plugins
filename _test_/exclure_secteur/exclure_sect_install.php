<?php
function exclure_sect_install($action){
    switch ($action){
        case "install":
            if (!lire_config('secteur/exclure_sect')){
                ecrire_config('secteur/exclure_sect',array());
                }

            break;
        
    }


}


?>