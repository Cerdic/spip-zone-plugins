<?php

//
// functions
function malettre_get_contents($patron,$id_edito=0,$selection,$selection_eve,$lang) {
  // inspi: spip-listes: exec/import_patron.php (merci booz)  
  $date = date('Y-m-d');
  
	$contexte_patron = array('date' => $date,                           
                           'id_edito'=>$id_edito,
                           'selection'=>$selection,
                           'selection_eve'=>$selection_eve,
                           'lang'=>$lang);
  // on utilise recupere_page et pas recupere fond pour eviter d'avoir des adresses privees (redirect)   
  $url = generer_url_public("$patron",'',true);
	foreach ($contexte_patron as $k=>$v)
			$url = parametre_url($url,$k,$v,'&');
	$texte_patron = recuperer_page($url) ;
	
  return $texte_patron;	
  			          	
}


?>