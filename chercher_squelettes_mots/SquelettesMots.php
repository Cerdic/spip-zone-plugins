<?php
function SquelettesMots_ajouter_onglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['config_chercher_squelettes_mots']= new Bouton(
											 '', 'Configurer Squelettes Mots',
											  generer_url_ecrire("config_chercher_squelettes_mots"));
  return $flux;
}
?>
