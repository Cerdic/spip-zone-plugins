<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Sur les forums articles, verifier si date pas perime
function forum_perime_formulaire_charger($flux){

	// On traite uniquement les forums d'articles
	if ($flux['args']['form']=="forum"){
  
          $objet = $flux['args']['args'][0];
          $id_objet =  $flux['args']['args'][1];
          
          if ($objet=="article") {
                   include_spip('inc/config');
                   $duree = intval(lire_config('forum_perime/duree', 0));
                   if ($duree > 0) {
                         $date_perimee = date('Y-m-d H:i:s',mktime(date("H") , 0, 0, date("m")  , date("d")-$duree, date("Y")));
                        if (!sql_countsel("spip_articles", "id_article=".intval($id_objet)." AND date > '$date_perimee'"))
                                    return false;                           
                   }
          }
	}


	return $flux;
}

?>
