<?php

function boucle_FLICKR_PHOTOSETS_COMMENTS_GETLIST_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_comments";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_photoset'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = FpipR_utils_calculer_hash('flickr.photosets.comments.getList',$arguments);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
