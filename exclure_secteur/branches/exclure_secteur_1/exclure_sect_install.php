<?php
function exclure_sect_install($action){
    switch ($action){
        case "install":
            if (!lire_config('secteur/exclure_sect')){
                ecrire_config('secteur/exclure_sect',array());
                }

            return;
        
    	 case "uninstall":
            if (lire_config('secteur')){
                effacer_config('secteur');
                }

            return;
    	case 'test':
       		if(lire_config('secteur')){
    			return true;	
    		}
    		else {
    			return false;
    		}
    }
	return ;
}


?>