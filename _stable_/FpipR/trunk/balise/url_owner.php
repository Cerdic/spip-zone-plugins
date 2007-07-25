<?php


function balise_URL_OWNER_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $p->code = "FpipR_generer_url_owner($user_id,0)";
  return $p;
}

?>
