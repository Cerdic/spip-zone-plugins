<?php
function balise_ACCURACY_dist($p) {
  $accuracy = champ_sql('accuracy',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($accuracy)?$accuracy:FpipR_photos_geo_getLocation($id_photo,'accuracy'))";
  return $p;
}

?>
