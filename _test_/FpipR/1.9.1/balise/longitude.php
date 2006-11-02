<?php
function balise_LONGITUDE_dist($p) {
  $longitude = champ_sql('longitude',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($longitude)?$longitude:FpipR_photos_geo_getLocation($id_photo,'longitude'))";
  return $p;
}
?>
