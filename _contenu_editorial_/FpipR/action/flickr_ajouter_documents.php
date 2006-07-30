<?php

function action_flickr_ajouter_documents() {
  include_spip('base/abstract_sql'); 
  include_spip('inc/flickr_api'); 
  include_spip('inc/actions');


  $hash = _request('hash');
  $id = _request('id');
  $type = _request('type');
  $action = _request('action');
  $arg = _request('arg');
  $redirect = _request('redirect');
  $id_auteur = _request('id_auteur');

  if (!verifier_action_auteur("$action-$arg", $hash,$id_auteur)) {
	include_spip('inc/minipres');
	minipres(_T('info_acces_interdit'));
  } else {
	$from = array('spip_auteurs');
	$select = array('flickr_token','flickr_nsid');
	$where = array('id_auteur='.$id_auteur);
	$row = spip_abstract_fetsel($select,$from,$where);
	$photos = array();
	if($row['flickr_nsid'] != '' && $row['flickr_token'] != '') {
	  $set = _request('set');
	  if($set == 'oui') {
		$sets = _request('sets');
		foreach($sets as $s) {
		  $allphotos = flickr_photosets_getPhotos($s,'','',$row['auth_token']);
		  foreach($allphotos as $photo) {
			$photos[] = $photo->id.'@#@'.$photo->secret;
		  }
		}
	  } else {
		$photos = _request('photos');
	  }
	  
	  include_spip('inc/getdocument');
	  foreach($photos as $info) {
		list($id_photo,$secret) = split('@#@',$info);
		$photo_details = flickr_photos_getInfo($id_photo,$secret,$row['auth_token']);
		$empty = array();
		$url = $photo_details->source('o');
		$date = date('Y-m-d H:i:s');
		ajouter_un_document($url,$photo_details->title,$type,$id,'distant',0,$empty);
		$date2 = date('Y-m-d H:i:s');
		$from = array('spip_documents');
		$select = array('id_document');
		$where = array("distant='oui'","fichier='$url'","maj >= '$date'","maj <= '$date2'");
		$row = spip_abstract_fetsel($select,$from,$where);
		if($row['id_document']) {
		  global $table_prefix;
		  $q = "UPDATE ".$table_prefix."_documents SET titre = '".$photo_details->title."', descriptif = '".$photo_details->description."'";
		  if($photo_details->date_taken) $q .=", date= '".$photo_details->date_taken."'";
		  $q .=" WHERE id_document=".$row['id_document'];
		  spip_query($q);
		}
	  }
	  redirige_par_entete(urldecode($redirect));
	}
  }
}

?>
