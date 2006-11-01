<?php
function boucle_FLICKR_GROUPS_GETINFO_dist($id_boucle,&$boucles) {
  include_spip('inc/FpipR_boucle_utils');
  $boucle = &$boucles[$id_boucle];
  $id_table = $boucle->id_table;
  $boucle->from[$id_table] =  "spip_fpipr_groups";

  $arguments = '';
  //on regarde dans les Where (critere de la boucle) si les arguments sont dispo.
  foreach($boucle->where as $w) {
	if($w[0] == "'?'") {
	  $w = $w[2];
	} 
	$key = str_replace("'",'',$w[1]);
	$val = $w[2];
	$key = str_replace("$id_table.",'',$key);
	if ($w[0] == "'='" && $key == 'id_group'){
	  $arguments[$key] = $val;
	} else 
	  erreur_squelette(_T('fpipr:mauvaisop',array('critere'=>$key,'op'=>$w[0])), $id_boucle);
  }
  $boucle->hash = FpipR_utils_calculer_hash('flickr.groups.getInfo',$arguments);
  return calculer_boucle($id_boucle, $boucles); 
  }
?>
