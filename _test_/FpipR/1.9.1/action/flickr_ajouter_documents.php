<?php

function action_flickr_ajouter_documents() {
  include_spip('base/abstract_sql'); 
  include_spip('inc/flickr_api'); 
  include_spip('inc/actions');


  $hash = _request('hash');
  $id = intval(_request('id'));
  $type = addslashes(_request('type'));
  $action = _request('action');
  $arg = _request('arg');
  $redirect = _request('redirect');
  $id_auteur =  intval($GLOBALS['auteur_session']['id_auteur']);

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
		$id_photo = intval($id_photo);
		$photo_details = flickr_photos_getInfo($id_photo,$secret,$row['auth_token']);
		if($photo_details->id) {
		  $empty = array();
		  $url = $photo_details->source('o');
		  $cnt =spip_abstract_fetsel(array('id_document'),array('spip_documents'),array("fichier='$url'","distant='oui'"));
		  if(!$cnt) {
			$date = date('Y-m-d H:i:s');
			ajouter_un_document($url,$photo_details->title,$type,$id,'distant',0,$empty);
			$date2 = date('Y-m-d H:i:s');
			$from = array('spip_documents');
			$select = array('id_document');
			$where = array("distant='oui'","fichier='$url'","maj >= '$date'","maj <= '$date2'");
			$doc_row = spip_abstract_fetsel($select,$from,$where);
			if($doc_row['id_document']) {
			  global $table_prefix;
			  $title = $photo_details->title;
			  if($photo_details->owner_nsid != $row['flickr_nsid']) {
				$title = _T('fpipr:par',array('title'=>$title,'user'=>(($photo_details->owner_username)?$photo_details->owner_username:$photo_details->owner_nsid),'url'=>'http://www.flickr.com/people/'.$photo_details->owner_nsid));
			  }
			  include_spip('inc/filtres');
			  $q = "UPDATE ".$table_prefix."_documents SET titre = '<html>".$title."</html>', descriptif = '<html>".filtrer_entites($photo_details->description)."</html>'";
			  if($photo_details->date_taken) $q .=", date= '".$photo_details->date_taken."'";
			  $q .=" WHERE id_document=".$doc_row['id_document'];
			  spip_query($q);
			  include_spip('inc/plugin');
			  if(in_array('tag-machine',liste_plugin_actifs())) {
				include_spip('inc/tag-machine');
				foreach($photo_details->tags as $tag) {
				  if($tag->raw) {
					$t = new Tag($tag->raw,'FlickrTag');
					$t->ajouter($doc_row['id_document'],'documents','id_document');
				  }
				}
			  }
			}
		  } else {
			
			spip_abstract_insert('spip_documents_'.$type.'s',"(id_$type,id_document)","($id,".$cnt['id_document'].')');
		  }
		}
	  }

	  	  redirige_par_entete(urldecode($redirect));
	}
  }
}

?>
