<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//
// recuperer la page lettre avec le contexte
// 
function malettre_get_contents($patron,$id_edito=0,$selection,$selection_eve,$lang) {

 $date = date('Y-m-d');
 
 // on passe la globale ien_implicite_cible_public en true 
 // pour avoir les liens internes en public (en non prive d'apres le contexte)
 // credit de l'astuce: denisb & rastapopoulos
 $GLOBALS['lien_implicite_cible_public'] = true;

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
  
  // on revient a la config initiale
  unset($GLOBALS['lien_implicite_cible_public']);

  return $texte_patron;	
  			          	
}
?>