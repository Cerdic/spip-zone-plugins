<?php
function balise_ISPUBLIC_dist($p) {
  $ispublic = champ_sql('ispublic',$p);
  $id_photo = champ_sql('id_photo',$p);
  $p->code = "(($ispublic)?$ispublic:FpipR_photos_getPerms($id_photo,'ispublic'))";
  return $p;
}
?>
