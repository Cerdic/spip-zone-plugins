<?php
function balise_LOGO_GROUP_dist($p) {
  $id_group = champ_sql('id_group',$p);
  $server = champ_sql('iconserver',$p);
  $p->code = "FpipR_logo_owner($id_group,$server)";	
  return $p;
}
?>
