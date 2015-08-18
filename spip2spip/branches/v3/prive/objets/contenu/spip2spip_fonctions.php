<?php
//  spip2spip
//
// syndication manuelle

if (!defined("_ECRIRE_INC_VERSION")) return;

function spip2spip_syndiquer_manuel($id_spip2spip){
  include_spip("inc/spip2spip");
  $log = -1;

  // on syndique le site dont la date de syndication est la plus ancienne  
  if ($row = sql_fetsel("id_spip2spip","spip_spip2spips","id_spip2spip=".intval($id_spip2spip))) {
          spip_log("spip2spip - syndication site id=".$row["id_spip2spip"]);
          $log = spip2spip_syndiquer($row["id_spip2spip"],"html");
  } 
  return $log;
}

?>