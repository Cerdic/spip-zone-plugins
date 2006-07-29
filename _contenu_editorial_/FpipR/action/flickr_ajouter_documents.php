<?php

function action_flickr_ajouter_documents() {
  $hash = _request('hash');
  $id = _request('id');
  $type = _request('type');
  $photos = _request('photos');
  $action = _request('action');
  $arg = _request('arg');
  $redirect = _request('redirect');
  $id_auteur = _request('id_auteur');

  include_spip('inc/actions');
  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	include_spip('inc/getdocument');
	foreach($photos as $photo) {
	  list($url,$title)=split('@#@',$photo);
	  $empty = array();
	  include_spip('base/abstract_sql');
	  var_dump(ajouter_un_document($url,$title,$type,$id,'distant',0,$empty));
	}
	redirige_par_entete(urldecode($redirect));
  }
  exit;
}

?>
