<?php

//
// functions
function malettre_get_contents($patron,$id_edito=0,$selection,$lang) {
  // inspi: spip-listes: exec/import_patron.php (merci booz)  
  $date = date('Y-m-d');
  
	$contexte_patron = array('date' => $date,                           
                           'id_edito'=>$id_edito,
                           'selection'=>$selection,
                           'lang'=>$lang);
  // on utilise recupere_page et pas recupere fond pour eviter d'avoir des adresses privees (redirect)   
  $url = generer_url_public("$patron",'',true);
	foreach ($contexte_patron as $k=>$v)
			$url = parametre_url($url,$k,$v,'&');
	$texte_patron = recuperer_page($url) ;
	
	// passer tout ca en unicode pour eviter certains problemes
	//include_spip('inc/charsets');
	//$texte_patron = charset2unicode($texte_patron);
	
  return $texte_patron;	
  			          	
}


?>