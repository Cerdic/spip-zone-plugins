<?php

function balise_URL_GROUP_dist($p) {
  $id = champ_sql('id_group',$p);
  $p->code = "FpipR_generer_url_group($id)";	
  return $p;
}

?>
