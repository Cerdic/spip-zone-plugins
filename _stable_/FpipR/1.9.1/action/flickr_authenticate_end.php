<?php

function action_flickr_authenticate_end() {
  $redirect = _request('redirect');
  $hash = _request('hash');
  $id_auteur =  intval($GLOBALS['auteur_session']['id_auteur']);
  $arg = _request('arg');
  $action = _request('action');
   
  include_spip('inc/actions');
  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	include_spip('inc/flickr_api');
	flickr_authenticate_end($id_auteur,$arg);
	redirige_par_entete(urldecode($redirect));
  }
}

?>
