<?php


class debug {
 
  function log($level, $string) 
  {
    global $g_conf;

    if ($level>=$g_conf->debug_level)
      error_log(strftime("%D-%T", time())."-".$level."-".$string."\n", 3, $g_conf->err_log);
  }

  function debug () {}
}

$g_deb = new debug();

?>
