<?php
function balise_ISFRIEND_dist($p) {
  $isfriend = champ_sql('isfriend',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($isfriend)?$isfriend:FpipR_photos_getPerms($id_photo,'isfriend'))";
  return $p;
}

?>
