<?php

//======================================================================
//question d'auth
//======================================================================

function balise_FLICKR_TOKEN_dist($p) {
  $id = champ_sql('id_auteur',$p);
  $p->code = "FpipR_getAuthToken($id)";
  return $p;
}

?>
