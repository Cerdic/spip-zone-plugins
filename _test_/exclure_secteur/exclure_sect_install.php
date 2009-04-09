<?php
function exclure_sect_install($action){
    switch ($action){
        case "install":
            ecrire_config('secteur/exclure_sect',array());

            break;
        
    }


}


?>