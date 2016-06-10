<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_spip2spip_syndic_dist($t){
  include_spip("inc/spip2spip");

  // on syndique le site dont la date de syndication est la plus ancienne  
  if ($row = sql_fetsel("id_spip2spip","spip_spip2spips","","","maj")) {
          spip_log("spip2spip - syndication site id=".$row["id_spip2spip"]);
          spip2spip_syndiquer($row["id_spip2spip"],"cron");
  } 
  return 1;
}

?>