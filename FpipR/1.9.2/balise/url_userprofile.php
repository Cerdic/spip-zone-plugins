<?php

function balise_URL_USERPROFILE_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $p->code = "FpipR_generer_url_owner($user_id,2)";
  return $p;
}

?>
