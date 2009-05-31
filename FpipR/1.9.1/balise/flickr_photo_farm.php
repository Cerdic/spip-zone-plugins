<?php
function balise_FLICKR_PHOTO_FARM_dist($p) {
  $fichier = champ_sql('fichier',$p);
  $p->code = "FpipR_get_flickr_photo_farm($fichier)";
  return $p;
}
?>
