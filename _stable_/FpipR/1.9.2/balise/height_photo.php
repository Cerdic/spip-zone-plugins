<?php
function balise_HEIGHT_PHOTO_dist($p) {
  $id_photo = champ_sql('id_photo',$p);
  $id_prim = champ_sql('primary_photo',$p);
  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_taille_photo($id_photo?$id_photo:$id_prim,$taille,'height')";	
  return $p;
}
?>
