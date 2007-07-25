<?php
function balise_FLICKR_PHOTO_SECRET_dist($p) {
  $fichier = champ_sql('fichier',$p);
  $p->code = "FpipR_get_flickr_photo_secret($fichier)";
  return $p;
}
?>
