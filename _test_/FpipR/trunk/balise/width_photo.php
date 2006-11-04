<?php
function balise_WIDTH_PHOTO_dist($p) {
  $id_photo = champ_sql('id_photo',$p);
  $taille =  calculer_liste($p->param[0][1],
									$p->descr,
									$p->boucles,
									$p->id_boucle);
  $p->code = "FpipR_taille_photo($id_photo,$taille,'width')";	
  return $p;
}
?>
