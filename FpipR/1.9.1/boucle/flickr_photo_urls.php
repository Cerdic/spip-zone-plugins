<?php

function boucle_FLICKR_PHOTO_URLS_dist($id_boucle, &$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_urls";
 
  if($boucles[$boucle->id_parent]->id_table != 'fpipr_photo_details') 
	erreur_squelette(_T('fpipr:mauvaise_imbrication',array('boucle'=>'URLS')), $id_boucle);
 
  //BOUCLE VIDE PARCE QU'ON NE TRAVAIL QUE SUR LA TABLE CREE PAR GETINFO
 
  return calculer_boucle($id_boucle, $boucles); 
  }

?>
