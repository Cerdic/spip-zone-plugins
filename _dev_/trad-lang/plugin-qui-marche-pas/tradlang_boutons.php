<?php


function tradlang_ajouterOnglets($flux) {

if($flux['args']=='config_lang')
    $flux['data']['tradlang']= new Bouton( 
	  "traductions-24.gif", _L('tradlang:gestion_des_traductions'),
	  generer_url_ecrire("tradlang"));
  return $flux;
}


?>
