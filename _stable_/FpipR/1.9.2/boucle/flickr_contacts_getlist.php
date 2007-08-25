<?php

function boucle_FLICKR_CONTACTS_GETLIST_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_people";

  $possible_criteres = array('nsid','filter');

  $arguments = FpipR_utils_search_criteres($boucle,$possible_criteres,$boucles,$id_boucle);

  $boucle->hash = FpipR_utils_calculer_hash('flickr.contacts.getList',$arguments,$boucle);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
