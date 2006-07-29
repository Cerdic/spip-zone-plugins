<?php

function action_flickr_ajouter_documents() {
  include_spip('base/abstract_sql'); 

  $hash = _request('hash');
  $id = _request('id');
  $type = _request('type');
  $action = _request('action');
  $arg = _request('arg');
  $redirect = _request('redirect');
  $id_auteur = _request('id_auteur');

  $set = _request('set');
  if($set == 'oui') {
	include_spip('inc/flickr_api'); 
	$from = array('spip_auteurs');
	$select = array('flickr_token','flickr_nsid');
	$where = array('id_auteur='.$id_auteur);
	$row = spip_abstract_fetsel($select,$from,$where);
	$photos = array();
	if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	  $sets = _request('sets');
	  foreach($sets as $s) {
		$allphotos = flickr_photosets_getPhotos($s,'','',$row['auth_token']);
		foreach($allphotos as $photo) {
		  $photos[] = $photo->source('o')."@#@".$photo->title;
		}
	  }
	}
  } else {
	$photos = _request('photos');
  }

  include_spip('inc/actions');
  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	include_spip('inc/getdocument');
	foreach($photos as $photo) {
	  list($url,$title)=split('@#@',$photo);
	  $empty = array();
	  ajouter_un_document($url,$title,$type,$id,'distant',0,$empty);
	}
	redirige_par_entete(urldecode($redirect));
  }
  exit;
}

?>
