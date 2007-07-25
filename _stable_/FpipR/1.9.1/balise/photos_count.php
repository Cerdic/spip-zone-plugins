<?php
function balise_PHOTOS_COUNT_dist($p) {
  $photo_id = champ_sql('id_photo',$p);
  
  $type = champ_sql('type',$p);
  $id = champ_sql('id_contexte',$p);

  $photoset_id = "(($type == 'set')?$id:".champ_sql('id_photoset',$p).')';
  $group_id = "(($type == 'pool')?$id:".champ_sql('id_group',$p).')';
  $p->code = 'FpipR_photos_getContext('.$photo_id.','.$photoset_id.','.$group_id.',"count","_content")';
  return $p;
}
?>
