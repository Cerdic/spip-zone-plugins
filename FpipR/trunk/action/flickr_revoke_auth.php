<?php

function action_flickr_revoke_auth() {
  $redirect = _request('redirect');
  $hash = _request('hash');
  $id_auteur =  intval($GLOBALS['auteur_session']['id_auteur']);
  $arg = _request('arg');
  $action = _request('action');
   
  include_spip('inc/securiser_action');
  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	include_spip('base/abstract_sql');
	global $table_prefix;
   spip_query("UPDATE ".$table_prefix."_auteurs SET flickr_nsid = '', flickr_token = '' WHERE id_auteur=$id_auteur");
	redirige_par_entete(urldecode($redirect));
  }
}

?>
