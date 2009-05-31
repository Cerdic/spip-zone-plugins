<?php
function balise_LOGO_PHOTO_dist($p) {
  $server = champ_sql('server',$p);
  $id_photo = champ_sql('id_photo',$p);
  $secret = champ_sql('secret',$p);
  $originalformat = champ_sql('originalformat',$p);
  $originalsecret = champ_sql('originalsecret',$p);
  $farm = champ_sql('farm',$p);
  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_logo_photo($id_photo,$farm,$server,$secret,$taille,$originalformat,$originalsecret)";	
  return $p;
}
?>
