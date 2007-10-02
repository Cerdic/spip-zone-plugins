<?php

function action_blockreferer_manage() {
  include_spip('base/abstract_sql'); 
  include_spip('inc/actions');

  $hash = _request('hash');
  $action = _request('action');
  $arg = _request('arg');
  $redirect = _request('redirect');
  $id_auteur =  intval($GLOBALS['auteur_session']['id_auteur']);
  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	spip_log('block:'+_request('url_refer'));	
	redirige_par_entete(urldecode($redirect));
  }
  }

?>
