<?php

function balise_LOGO_OWNER_dist($p) {
  $user_id = champ_sql('user_id',$p);
  $server = champ_sql('icon_server',$p);
  $p->code = "FpipR_logo_owner($user_id,$server)";	
  return $p;
}

?>
