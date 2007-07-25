<?php
function balise_URL_PHOTOSET_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $id_photoset = champ_sql('id_photoset',$p);
  $p->code = "FpipR_generer_url_photoset($user_id,$id_photoset)";
  return $p;
}
?>
