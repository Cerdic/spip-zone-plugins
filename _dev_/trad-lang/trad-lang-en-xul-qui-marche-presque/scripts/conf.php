<?php


class config {
  
  var $debug_level = 10; // MAX = 10 (aucun log)
  var $debug_out = 3;  // 0=fichier log apache, 3=fichier '$err_log'
  var $err_log = "/tmp/debug.log";

  function config () {}
}

$g_conf = new config();

?>
