<?php
function balise_LATITUDE_dist($p) {
  $latitude = champ_sql('latitude',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($latitude)?$latitude:FpipR_photos_geo_getLocation($id_photo,'latitude'))";
  return $p;
}
?>
