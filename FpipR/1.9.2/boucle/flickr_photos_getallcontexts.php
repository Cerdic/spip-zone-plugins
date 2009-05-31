 <?php
 function boucle_FLICKR_PHOTOS_GETALLCONTEXTS_dist($id_boucle,&$boucles) {
	include_spip('inc/FpipR_boucle_utils');
   $boucle = &$boucles[$id_boucle];
   $id_table = $boucle->id_table;
   $boucle->from[$id_table] =  "spip_fpipr_contextes";
   $arguments = FpipR_utils_search_args($boucle,$id_table,array('id_photo','type'));
   $null = null;
   $boucle->hash = FpipR_utils_calculer_hash('flickr.photos.getAllContexts',$arguments,$null);
   return calculer_boucle($id_boucle, $boucles); 
 }

 ?>
