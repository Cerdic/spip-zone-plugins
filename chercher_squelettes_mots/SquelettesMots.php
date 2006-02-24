<?php
function SquelettesMots_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_chercher_squelettes_mots']= new Bouton(
											 '', 'Configurer Squelettes Mots',
											  generer_url_ecrire("config_chercher_squelettes_mots"));
  return $flux;
}

/*
pas de tel point d'entree.
function SquelettesMots_ajouter_boite_gauche($arguments) {  
  global $connect_statut, $connect_toutes_rubriques, $spip_lang;
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	if($arguments['args']['exec'] == 'articles') {
	  include('chercher_squelette.php');
	  
	$ext = $GLOBALS['extension_squelette'];
	  $arguments['data'] .= '<div class="cadre-info verdana1">'._T('SquelettesMots:utiliserasquelette',array('squelette' =>substr(cherher_squelette('article',$arguments['args']['id_rubrique'],$spip_lang),strpos('/')))).".$ext</div>";
	}
  }
  return $arguments;
}*/

?>
