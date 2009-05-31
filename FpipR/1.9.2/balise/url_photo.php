<?php

function balise_URL_PHOTO_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "FpipR_generer_url_photo($user_id,$id_photo)";
  return $p;
}

?>
