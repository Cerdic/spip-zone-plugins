<?php

//
// recuperer la page lettre avec le contexte
// 
function malettre_get_contents($patron,$id_edito=0,$selection,$selection_eve,$lang) {

 $date = date('Y-m-d');
 $texte_patron =  recuperer_fond(
		"public/$patron",
		array(
          'date' => $date,                           
           'id_edito'=>$id_edito,
           'selection'=>$selection,
           'selection_eve'=>$selection_eve,
           'lang'=>$lang 		
          )
	);
	
  return $texte_patron;	
  			          	
}


?>