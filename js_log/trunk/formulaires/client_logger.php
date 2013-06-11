<?php

function formulaires_client_logger_traiter_dist () {

  $nav = _request('nav');
  $err_msg = _request('err_msg');
  $log_msg = $nav . ' : ' . $err_msg;
  spip_log($log_msg, 'erreurs_js');
  return array();
}

?>