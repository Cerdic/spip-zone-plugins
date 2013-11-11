<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


// melange un tableau
// cf .http://thread.gmane.org/gmane.comp.web.spip.devel/59465/
function quickvote_shuffle($array) {
   if (is_array($array)) 
           quickvote_shuffle_assoc($array);   
   return $array; 
}

// garder les pairs clé/valeur
function quickvote_shuffle_assoc(&$array) {
   $keys = array_keys($array); 
   shuffle($keys); 
   foreach($keys as $key) {
            $new[$key] = $array[$key];
        }               
   $array = $new;  
   return true;
}


?>