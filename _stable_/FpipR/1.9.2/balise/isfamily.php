<?php
function balise_ISFAMILY_dist($p) {
  $isfamily = champ_sql('isfamily',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($isfamily)?$isfamily:FpipR_photos_getPerms($id_photo,'isfamily'))";
  return $p;
}
?>
