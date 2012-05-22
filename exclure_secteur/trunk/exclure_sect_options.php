<?php

if (!function_exists('critere_tout_voir_dist')){
        function critere_tout_voir_dist($idb, &$boucles, $crit) {   
            $boucle = &$boucles[$idb];
            $boucle->modificateur['tout_voir'] = true;
    }
}
?>
