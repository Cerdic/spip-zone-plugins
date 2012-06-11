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
  
  // hack pourri en attendant mieux pour gerer les urls internes
  // piste denisb: explorer le 3er argument de recuperer_fond $options['compil'][0]
  $texte_patron = str_replace("ecrire/?exec=article&", "?page=article&",$texte_patron);
  $texte_patron = str_replace("ecrire/?exec=rubrique&", "?page=rubrique&",$texte_patron);
  $texte_patron = str_replace("ecrire/?exec=auteur&", "?page=auteur&",$texte_patron);
	
  return $texte_patron;	
  			          	
}
?>