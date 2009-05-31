<?php
function balise_LOGO_PREVPHOTO_dist($p) {
  $id_photo = champ_sql('id_photo',$p);

  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';

  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo(FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','id'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','server'),FpipR_photos_getContext($id_photo,$photoset_id,$group_id,'prevphoto','secret'),$taille,'jpg')";	
  return $p;
}
?>
