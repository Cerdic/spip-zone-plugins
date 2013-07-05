<?php

if (!defined("_ECRIRE_INC_VERSION")) return;    #securite
    
$GLOBALS['autorite'] = @unserialize($GLOBALS['meta']['autorite']);

##
## Dire aux crayons si les visiteurs anonymes ont des droits
##
if ((is_array($GLOBALS['autorite'])) AND 
    (
        ($GLOBALS['autorite']['espace_wiki'] AND 
            $GLOBALS['autorite']['espace_wiki_anonyme']
        ) 
        OR 
        ($GLOBALS['autorite']['espace_wiki_motsclef'] AND 
            $GLOBALS['autorite']['espace_wiki_motsclef_anonyme']
        )
    )) {
	if (!function_exists('analyse_droits_rapide')) {
	    function analyse_droits_rapide() {
		    return true;
	    }
	} else {
		$autorite_erreurs[] = 'analyse_droits_rapide';
    }
}

?>
