<?php
function balise_FLICKR_PHOTO_ID_dist($p) {
  $fichier = champ_sql('fichier',$p);
  $p->code = "FpipR_get_flickr_photo_id($fichier)";
  return $p;
}
?>
