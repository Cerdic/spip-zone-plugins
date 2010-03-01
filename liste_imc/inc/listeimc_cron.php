<?php

function cron_listeimc_cron($t) {
  include_spip('inc/listeimc_functions');
  
  generer_cities_html();
  
  return;
}

?>