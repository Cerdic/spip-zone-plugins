<?php 

function balise_SPIP_PFC($p){

  $p->code = 'recuperer_fond(\'fonds/spip_phpfreechat\')';
 
  $p->interdire_scripts = false;
        
  return $p;
} 

?>