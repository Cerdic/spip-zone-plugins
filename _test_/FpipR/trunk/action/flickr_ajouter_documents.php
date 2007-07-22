<?php

function action_flickr_ajouter_documents() {
  include_spip('base/abstract_sql'); 
  include_spip('inc/flickr_api'); 
  include_spip('inc/securiser_action');


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
		  foreach($allphotos->photos as $photo) {
			$photos[] = $photo->id.'@#@'.$photo->secret;
		  }
		}
	  } else {
		$photos = _request('photos');
	  }
	  
	  include_spip('inc/ajouter_documents');
	  $ajouter_document = charger_fonction('ajouter_documents', 'inc');
	  foreach($photos as $info) {
		list($id_photo,$secret) = split('@#@',$info);
		$id_photo= intval($id_photo);
		$photo_sizes = flickr_photos_getSizes($id_photo,$row['auth_topen']);
		if($photo_sizes) {
		  $url = '';
		  $width = '';
		  $height = '';
		  $url_m = '';
		  $width_m = '';
		  $height_m = '';
		  foreach($photo_sizes['sizes']['size'] as $size) {
			if($size['label'] == 'Original') {
			  $url = $size['source'];
			  $width = $size['width'];
			  $height = $size['height'];
			  break;
			} else if($size['label'] == 'Medium') {
				$url_m = $size['source'];
				$width_m = $size['width'];
				$height_m = $size['height'];
			}
		  }
		  if(!$url) {
			$url = $url_m;
			$width = $width_m;
			$height = $height_m;
		  }
		  $cnt =spip_abstract_fetsel(array('id_document'),array('spip_documents'),array("fichier='$url'","distant='oui'"));
		  if(!$cnt) {
			$empty = array();
			$photo_details = flickr_photos_getInfo($id_photo,$secret,$row['auth_token']);
			$date = date('Y-m-d H:i:s');
			$ajouter_document($url,$photo_details->title,$type,$id,'distant',0,$empty);
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
			  $q = "UPDATE ".$table_prefix."_documents SET titre = "._q("<html>$title</html>").", descriptif = "._q('<html>'.filtrer_entites($photo_details->description).'</html>').", largeur=".intval($width).', hauteur='.intval($height);
			  if($photo_details->date_taken) $q .=", date= "._q($photo_details->date_taken);
			  $q .=" WHERE id_document=".$doc_row['id_document'];
			  spip_query($q);
			  include_spip('inc/plugin');
			  //ATTENTION TODO, on s'attend a trouver tag-machine dans _dev_, mauvaise idee.
			  if(in_array('_dev_/tag-machine',liste_plugin_actifs())) {
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
			$link =spip_abstract_fetsel(array('id_document,id_article'),array('spip_documents_'.$type.'s'),array("id_$type=$id","id_document=".$cnt['id_document']));
			if(!$link) {
			  spip_abstract_insert('spip_documents_'.$type.'s',"(id_$type,id_document)","($id,".$cnt['id_document'].')');
			}
		  }
		}
	  }

	  if(!$redirect) {
		$redirect = generer_url_ecrire($type.'s',"id_$type=$id",true);
	  }
	  redirige_par_entete(urldecode($redirect));
	}
  }
}

?>
