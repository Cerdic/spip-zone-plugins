<?php

function action_incarner_dist () {

  include_spip('inc/headers');

  if ( ! autoriser('incarner')) {
    include_spip('inc/headers');
    http_status(403);
    exit();
  }

  include_spip('inc/auth');
  if ($login  = _request('login')) {
    $auteur = auth_identifier_login($login, '');
    auth_loger($auteur);
  } else if (_request('logout')) {
    redirige_par_entete(html_entity_decode(generer_url_action('logout', 'logout=public', False, True)));
  }

}