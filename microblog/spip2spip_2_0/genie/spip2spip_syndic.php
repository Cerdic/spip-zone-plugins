<?php

function genie_spip2spip_syndic_dist($t){

  // on syndique le site dont la date de syndication est la plus ancienne  
  if ($row = sql_fetsel("id","spip_spip2spip","","","last_syndic")) {
          spip_log("spip2spip - syndication site id=".$row["id"]);
          spip2spip_syndiquer($row["id"],"cron");
  }
  return 1;
}

?>