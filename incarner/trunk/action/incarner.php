<?php

function action_incarner_dist () {

  if ( ! autoriser('incarner')) {
    include_spip('inc/headers');
    http_status(403);
    exit();
  }

  include_spip('inc/auth');
  $login  = _request('login');
  $auteur = auth_identifier_login($login, '');

}