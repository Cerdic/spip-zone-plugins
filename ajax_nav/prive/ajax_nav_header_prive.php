<?php
function ajax_nav_header_prive($flux) {
  
  $flux .= "<link type='text/css' href='" . find_in_path("prive/ajax_nav_prive.css") . "' rel='stylesheet' />";
  return $flux;
}
?>