<?php
  /*
   * Boucle FpipR pour l'API Flickr
   * 
   *
   * Auteur :
   * Pierre Andrews
   * Inspire de la boucles XML de Cedric Morin
   * © 2006 - Distribue sous licence GNU/GPL
   *
   */

  // Definition des tables pour permettre la squeletisation de l'API Flickr
  //



  /*Les nouvelles colonne de l'auteur*/
include_spip('base/serial');
$GLOBALS['tables_principales']['spip_auteurs']['field']['flickr_nsid'] = "TINYTEXT DEFAULT NULL";
$GLOBALS['tables_principales']['spip_auteurs']['field']['flickr_token'] = "TINYTEXT DEFAULT NULL";

//======================================================================

// La table pour une liste de photos
//La version du schema de table

$GLOBALS['FpipR_versions']['spip_fpipr_photos'] = '0.6';

$GLOBALS['FpipR_tables']['spip_fpipr_photos_field'] = array(
															"id_photo"  => "bigint(21) NOT NULL",
															"user_id" => "varchar(100)", //"47058503995@N01" 
															"secret"=> "varchar(100)", //"a123456"
															"server"=> "int NOT NULL", //"2"
															"title"	=> "text DEFAULT '' NOT NULL",
															"ispublic"=> "ENUM ('0','1') NOT NULL",
															"isfriend"=> "ENUM ('0','1') NOT NULL",
															"isfamily"=> "ENUM ('0','1') NOT NULL",
															"originalformat" => "char(4) DEFAULT 'jpg'",
															"license" => "smallint", //http://flickr.com/services/api/flickr.photos.licenses.getInfo.html
															"upload_date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",	
															"taken_date" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															"owner_name" => "text DEFAULT '' NOT NULL",
															"icon_server" => "int NOT NULL",
															"last_update" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															"latitude" => "DOUBLE",
															"longitude" => "DOUBLE",
															"accuracy" => "smallint NOT NULL",
															"rang" => "int",
															"id_photoset" =>  "bigint(21) DEFAULT 0 NOT NULL", //pour le cas ou on cherche les photos dans un set
															"id_group" => "varchar(100)", //"47058503995@N01" 
															"added_date" =>  "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															);

$GLOBALS['FpipR_tables']['spip_fpipr_photos_key'] = array(
														  "PRIMARY KEY" => "id_photo",
														  "KEY" => "user_id",
														  "KEY" => "rang",
														  'KEY' => "id_photoset"
														  );
$GLOBALS['tables_principales']['spip_fpipr_photos'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_photos_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_photos_key']);
//les clefs pour trier, pas vraiment dans la table
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['date_posted'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['date_taken'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['interestingness'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['relevance'];

$GLOBALS['table_des_tables']['flickr_photos_search'] = 'fpipr_photos';


//======================================================================

//Les tables pour les details de photos

$GLOBALS['FpipR_versions']['spip_fpipr_photo_details'] = '0.3';

$GLOBALS['FpipR_tables']['spip_fpipr_photo_details_field'] = array(
																   'id_photo' => 'bigint(21) NOT NULL',
																   'secret' => 'varchar(100)',
																   'server' => 'int NOT NULL',
																   'isfavorite' => "ENUM ('0','1') NOT NULL",
																   'license' => 'smallint',
																   'rotation' => 'smallint',
																   'originalformat' => "char(4) DEFAULT 'jpg'",
																   'user_id' => 'varchar(100)',
																   'owner_username' => "text DEFAULT '' NOT NULL",
																   'owner_realname' => "text DEFAULT '' NOT NULL",
																   'owner_location' => "text DEFAULT '' NOT NULL",
																   'title' => "text DEFAULT '' NOT NULL",
																   'description' => "text DEFAULT '' NOT NULL",
																   'ispublic' => "ENUM ('0','1') NOT NULL",
																   'isfriend' => "ENUM ('0','1') NOT NULL",
																   'isfamily' => "ENUM ('0','1') NOT NULL",
																   'date_posted' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
																   'date_taken' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
																   'date_lastupdate' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
																   'comments' => 'int',
																   "latitude" => "DOUBLE",
																   "longitude" => "DOUBLE",
																   "accuracy" => "smallint NOT NULL",
																   );

$GLOBALS['FpipR_tables']['spip_fpipr_photo_details_key'] = array(
																 "PRIMARY KEY" => "id_photo"
																 );

$GLOBALS['tables_principales']['spip_fpipr_photo_details'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_photo_details_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_photo_details_key']);
$GLOBALS['table_des_tables']['flickr_photos_getinfo'] = 'fpipr_photo_details';


$GLOBALS['FpipR_versions']['spip_fpipr_tags'] = '0.4';
$GLOBALS['FpipR_tables']['spip_fpipr_tags_field'] = array("id_tag" => 'varchar(255) NOT NULL',
														  "author" => 'varchar(100)',
														  "raw" => "text DEFAULT '' NOT NULL",
														  "safe" => "text DEFAULT '' NOT NULL",
														  "id_photo" => 'bigint(21) NOT NULL' //la façon dont on recupere les tags, on a juste une photo par tag.
														  );
$GLOBALS['FpipR_tables']['spip_fpipr_tags_key'] = array("PRIMARY KEY" => "id_tag",
														"KEY id_photo" => "id_photo");


$GLOBALS['tables_principales']['spip_fpipr_tags'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_tags_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_tags_key']);
$GLOBALS['table_des_tables']['flickr_photo_tags'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getlistphoto'] = 'fpipr_tags';

$GLOBALS['FpipR_versions']['spip_fpipr_notes'] = '0.3';
$GLOBALS['FpipR_tables']['spip_fpipr_notes_field'] = array(
														   "id_note" => 'bigint(21) NOT NULL',
														   "id_photo" => 'bigint(21) NOT NULL',
														   'author' => 'varchar(100)',
														   'authorname' => "text DEFAULT '' NOT NULL",
														   'x' => 'float DEFAULT 0',
														   'y' => 'float DEFAULT 0',
														   'width' => 'float DEFAULT 0',
														   'height' => 'float DEFAULT 0',
														   'texte' => "text DEFAULT '' NOT NULL"
														   );
$GLOBALS['FpipR_tables']['spip_fpipr_notes_key'] = array("PRIMARY KEY" => "id_note",
														 "KEY id_photo" => "id_photo");

$GLOBALS['tables_principales']['spip_fpipr_notes'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_notes_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_notes_key']);
$GLOBALS['table_des_tables']['flickr_photo_notes'] = 'fpipr_notes';

$GLOBALS['FpipR_versions']['spip_fpipr_urls'] = '0.2';
$GLOBALS['FpipR_tables']['spip_fpipr_urls_field'] = array(
														  "type" => "VARCHAR(255) NOT NULL",
														  "id_photo" => 'bigint(21) NOT NULL',
														  'url' => "text DEFAULT '' NOT NULL"
														  );
$GLOBALS['FpipR_tables']['spip_fpipr_urls_key'] = array("PRIMARY KEY" => "type",
														"KEY id_photo" => "id_photo");


$GLOBALS['tables_principales']['spip_fpipr_urls'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_urls_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_urls_key']);
$GLOBALS['table_des_tables']['flickr_photo_urls'] = 'fpipr_urls';

//======================================================================
//pour les sets


$GLOBALS['FpipR_versions']['spip_fpipr_photosets'] = '0.3';
$GLOBALS['FpipR_tables']['spip_fpipr_photosets_field'] = array(
															   "id_photoset" => "bigint(21) NOT NULL",
															   "user_id" => 'varchar(100)',
															   'primary_photo' => "bigint(21) NOT NULL",
															   "secret"=> "varchar(100)", //"a123456"
															   "server"=> "int NOT NULL", //"2"
															   'photos' => 'int',
															   "title"	=> "text DEFAULT '' NOT NULL",
															   "description" => "text DEFAULT '' NOT NULL"
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_photosets_key'] = array("PRIMARY KEY" => "id_photoset");


$GLOBALS['tables_principales']['spip_fpipr_photosets'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_photosets_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_photosets_key']);
$GLOBALS['table_des_tables']['flickr_photosets_getlist'] = 'fpipr_photosets';

$GLOBALS['table_des_tables']['flickr_photosets_getphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_groups_pools_getphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getcontactspublicphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_favorites_getpubliclist'] = 'fpipr_photos';

//======================================================================
//pour le contexte


$GLOBALS['FpipR_versions']['spip_fpipr_contextes'] = '0.3';
$GLOBALS['FpipR_tables']['spip_fpipr_contextes_field'] = array(
															   "id_contexte" => "varchar(255) NOT NULL",
															   "title"	=> "text DEFAULT '' NOT NULL",
															   "type" => "ENUM('set','pool') NOT NULL",
															   "id_photo" => 'bigint(21) NOT NULL'
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_contextes_key'] = array("PRIMARY KEY" => "id_contexte",
															"KEY" => 'type');


$GLOBALS['tables_principales']['spip_fpipr_contextes'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_contextes_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_contextes_key']);
$GLOBALS['table_des_tables']['flickr_photos_getallcontexts'] = 'fpipr_contextes';

//======================================================================
//interestingness

$GLOBALS['table_des_tables']['flickr_interestingness_getlist'] = 'fpipr_photos';

//======================================================================
//pour les commentaires



$GLOBALS['FpipR_versions']['spip_fpipr_comments'] = '0.1';
$GLOBALS['FpipR_tables']['spip_fpipr_comments_field'] = array(
															   "id_comment" => "varchar(255) NOT NULL",
															   "user_id" => 'varchar(100)',
															   "authorname" => "text default ''",
															   "date_create"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															   "permalink" => "text DEFAULT '' NOT NULL",
															   "texte" => "text DEFAULT '' NOT NULL",
															   "id_photo" => 'bigint(21) NOT NULL',
															   "id_photoset" => "bigint(21) NOT NULL"
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_comments_key'] = array("PRIMARY KEY" => "id_comment",
															"KEY" => 'id_photo',
															"KEY" => 'id_photoset');


$GLOBALS['tables_principales']['spip_fpipr_comments'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_comments_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_comments_key']);
$GLOBALS['table_des_tables']['flickr_photos_comments_getlist'] = 'fpipr_comments';
$GLOBALS['table_des_tables']['flickr_photosets_comments_getlist'] = 'fpipr_comments';

//======================================================================

function FpipR_creer_tables($method){
  $fct = str_replace('.','_',$method);
  $f=charger_fonction('create_'.$fct, 'FpipR');
  $f();
}

function FpipR_make_table($nom) {
  static $created; 

  $champs = $GLOBALS['FpipR_tables'][$nom.'_field'];
  $cles = $GLOBALS['FpipR_tables'][$nom.'_key'];

  $version_table = lire_meta("FpipR_$nom");

  if($version_table && $version_table != $GLOBALS['FpipR_versions'][$nom]) {
	$version_table = '';
	spip_query("DROP TABLE $nom");
  }
  if(!$created[$nom]) {
	spip_create_table($nom, $champs, $cles, false, false);
  }
  $created[$nom] = true;
  ecrire_meta("FpipR_$nom",$GLOBALS['FpipR_versions'][$nom]);
  ecrire_metas();
}

function FpipR_fill_table($method,$arguments){
  //Faire le query API flickr
  $fct = str_replace('.','_',$method);
  $f=charger_fonction($fct, 'FpipR');
  $f($arguments);
}

//======================================================================


function FpipR_create_flickr_photos_search_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_photos_search_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_search(
								 $arguments['per_page'],$arguments['page'],
								 $arguments['user_id'], $arguments['tags'], $arguments['tag_mode'],
								 $arguments['text'], $arguments['min_upload_date'],
								 $arguments['max_upload_date'], $arguments['min_taken_date'],
								 $arguments['max_taken_date'], $arguments['license'],
								 $arguments['sort'], $arguments['privacy_filter'],
								 $arguments['extras'],
								 $arguments['bbox'],$arguments['accuracy'],
								 $arguments['auth_token']);
  FpipR_fill_photos_table($photos->photos);
}

function FpipR_fill_photos_table($photos,$add='') {
  $cnt = 0;
  $query = "DELETE FROM spip_fpipr_photos";
  spip_query($query);
  $col = '(id_photo,user_id,secret,server,title,ispublic,isfriend,isfamily,originalformat,license,upload_date,taken_date,owner_name,icon_server,last_update,longitude,latitude,accuracy,rang,added_date';

  if($add)
	foreach($add as $name=>$val) {
	  $col.=",$name";
	  $vals .= ','._q($val);
	}
  foreach($photos as $photo) {
	$ownername = $photo->ownername?$photo->ownername:$photo->username;
	$vals = "("._q($photo->id).','._q($photo->owner).','._q($photo->secret).','._q($photo->server).','._q($photo->title).','._q($photo->idpublic).','._q($photo->isfriend).','._q($photo->isfamily).','._q($photo->originalformat).','._q($photo->license).','._q(date('Y-m-d H:i:s',$photo->dateupload+0)).','._q($photo->datetaken).','._q($photo->ownername).','._q($photo->iconserver).','._q(date('Y-m-d H:i:s',$photo->lastupdate+0)).','._q($photo->longitude).','._q($photo->latitude).','._q($photo->accuracy).','.$cnt++.','._q(date('Y-m-d H:i:s',$photo->dateadded+0));
	if($add)
	  foreach($add as $name=>$val) {
		$vals .= ','._q($val);
	  }
	spip_abstract_insert('spip_fpipr_photos',
						 $col.')',
						 $vals.")");
  }
}

//======================================================================

function FpipR_create_flickr_photos_getinfo_dist() {
  FpipR_make_table('spip_fpipr_photo_details');
  FpipR_make_table('spip_fpipr_tags');
  FpipR_make_table('spip_fpipr_notes');
  FpipR_make_table('spip_fpipr_urls');
}

function FpipR_flickr_photos_getinfo_dist($arguments) {
  include_spip('inc/flickr_api');
  $details = flickr_photos_getInfo($arguments['id_photo'],$arguments['secret']);
  $id_photo = intval($details->id);
  if($id_photo) {
	//on vide les tables
	$query = "DELETE FROM spip_fpipr_photo_details";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_tags";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_notes";
	spip_query($query);
	$query = "DELETE FROM spip_fpipr_urls";
	spip_query($query);
	//on insere la ligne unique de detail
	spip_abstract_insert('spip_fpipr_photo_details',
						 '(id_photo,secret,server,isfavorite,license,rotation,originalformat,user_id,owner_username,owner_realname,owner_location,title,description,ispublic,isfriend,isfamily,date_posted,date_taken,date_lastupdate,comments,latitude,longitude,accuracy)',						   
						 '('._q($details->id).','._q($details->secret).','._q($details->server).','._q($details->isfavorite).','._q($details->license).','._q($details->rotation).','._q($details->originalformat).','._q($details->owner_nsid).','._q($details->owner_username).','._q($details->owner_realname).','._q($details->owner_location).','._q($details->title).','._q($details->description).','._q($details->visibility_ispublic).','._q($details->visibility_isfriend).','._q($details->visibility_isfamily).','._q(date('Y-m-d H:i:s',$details->date_posted+0)).','._q($details->date_taken).','._q(date('Y-m-d H:i:s',$details->date_lastupdate+0)).','._q($details->comments).','._q($details->location_latitude).','._q($details->location_longitude).','._q($details->location_accuracy).')'
						 );	  
	//on insere les tags
	foreach($details->tags as $tag) {
	  spip_abstract_insert('spip_fpipr_tags',
						   '(id_tag,author,raw,safe,id_photo)',
						   '('._q($tag->id).','._q($tag->author).','._q($tag->raw).','._q($tag->safe).','._q($id_photo).')'
						   );
	}
	//on insere les notes
	foreach($details->notes as $n) {
	  spip_abstract_insert('spip_fpipr_notes',
						   '(id_note,id_photo,author,authorname,x,y,width,height,texte)',
						   '('._q($n['id']).','._q($id_photo).','._q($n['author']).','._q($n['authorname']).','._q($n['x']).','._q($n['y']).','._q($n['w']).','._q($n['h']).','._q($n['_content']).')'
						   );
	}
	//on insere les urls
	foreach($details->urls as $k=>$u) {
	  spip_abstract_insert('spip_fpipr_urls',
						   '(type,id_photo,url)',
						   '('._q($k).','._q($id_photo).','._q($u).')'
						   );
	}
  }
}

//======================================================================


function FpipR_create_flickr_photosets_getList_dist() {
  FpipR_make_table('spip_fpipr_photosets');
}

function FpipR_flickr_photosets_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  //on vide les tables
  $query = "DELETE FROM spip_fpipr_photosets";
  spip_query($query);
  
  $photosets = flickr_photosets_getList($arguments['user_id']);
  foreach($photosets as $set) {
	spip_abstract_insert('spip_fpipr_photosets',
						 '(id_photoset,user_id,primary_photo,secret,server,photos,title,description)',
						 '('._q($set->id).','._q($set->owner).','._q($set->primary).','._q($set->secret).','._q($set->server).','._q($set->photos).','._q($set->title).','._q($set->description).')'
						 );	
  }
}

//======================================================================

function FpipR_create_flickr_photosets_getphotos_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_photosets_getphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photosets_getPhotos($arguments['id_photoset'],
									   $arguments['extras'],
									   $arguments['per_page'],
									   $arguments['page'],
									   $arguments['privacy_filter']);
  FpipR_fill_photos_table($photos->photos,array(
										'id_photoset' => $arguments['id_photoset']
										));
}


//======================================================================

function FpipR_create_flickr_photos_getallcontexts_dist() {
  FpipR_make_table('spip_fpipr_contextes');
}

function FpipR_flickr_photos_getallcontexts_dist($arguments) {
  include_spip('inc/flickr_api');
  $query = "DELETE FROM spip_fpipr_contextes";
  spip_query($query);
  $id_photo = $arguments['id_photo'];
  $contextes = flickr_photos_getAllContexts($id_photo);
  foreach($contextes as $type => $cont) {
	if(($type == 'set' || $type == 'pool') && is_array($cont)) 
	  foreach ($cont as $c) {
		spip_abstract_insert('spip_fpipr_contextes',
							 '(id_contexte,title,type,id_photo)',
							 '('._q($c['id']).','._q($c['title']).','._q($type).','._q($id_photo).')'
							 );  
	  }
  } 
}


//======================================================================

function FpipR_create_flickr_interestingness_getlist_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_interestingness_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_interestingness_getList($arguments['date'],
										   $arguments['extras'],
										   $arguments['per_page'],
										   $arguments['page']);
  FpipR_fill_photos_table($photos->photos);
}

//======================================================================

function FpipR_create_flickr_groups_pools_getphotos_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_groups_pools_getphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_groups_pools_getPhotos($arguments['id_group'],
										   $arguments['tags'],
										   $arguments['user_id'],
										   $arguments['extras'],
										   $arguments['per_page'],
										   $arguments['page']);
  FpipR_fill_photos_table($photos->photos,array('id_group'=>$arguments['id_group']));
}

//======================================================================
function FpipR_create_flickr_tags_getlistphoto_dist() {
  FpipR_make_table('spip_fpipr_tags');
}

function FpipR_flickr_tags_getlistphoto_dist($arguments) {
  include_spip('inc/flickr_api');
  $tags = flickr_tags_getListPhoto($arguments['id_photo']);
  foreach($tags as $tag) {
	spip_abstract_insert('spip_fpipr_tags',
						 '(id_tag,author,raw,safe,id_photo)',
						 '('._q($tag->id).','._q($tag->author).','._q($tag->raw).','._q($tag->safe).','._q($arguments['id_photo']).')'
						 );
  }
}
//======================================================================

function FpipR_create_flickr_photos_getcontactspublicphotos_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_photos_getcontactspublicphotos_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_photos_getContactsPublicPhotos($arguments['nsid'],
										   $arguments['count'],
										   $arguments['just_friends'],
										   $arguments['single_photo'],
										   $arguments['include_self'],
										   $arguments['extras']);
  FpipR_fill_photos_table($photos->photos);
}


//======================================================================
function FpipR_create_flickr_favorites_getPublicList_dist() {
  FpipR_make_table('spip_fpipr_photos');
}

function FpipR_flickr_favorites_getPublicList_dist($arguments) {
  include_spip('inc/flickr_api');
  $photos = flickr_favorites_getPublicList($arguments['nsid'],
										   $arguments['extras'],
										   $arguments['per_page'],
										   $arguments['page']);
  FpipR_fill_photos_table($photos->photos);
}

//======================================================================

function FpipR_fill_comments_table($comments) {
  
  $query = "DELETE FROM spip_fpipr_comments";
  spip_query($query);
  $comments = $comments['comments'];
  $photo_id = _q($comments['photo_id']);
  $photoset_id = _q($comments['photoset_id']);
  foreach($comments['comment'] as $com) {
	spip_abstract_insert('spip_fpipr_comments',
						 "(id_comment,user_id,authorname,date_create,permalink,texte,id_photo,id_photoset)",
		
						 '('._q($com['id']).','._q($com['author']).','._q($com['authorname']).','._q(date('Y-m-d H:i:s',$com['date_create'])).','._q($com['permalink']).','._q($com['_content']).','.$photo_id.','.$photoset_id.')'
						 );
  }
}

function FpipR_create_flickr_photos_comments_getlist_dist() {
  FpipR_make_table('spip_fpipr_comments');
}

function FpipR_flickr_photos_comments_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $comments = flickr_photos_comments_getList($arguments['id_photo']);
  FpipR_fill_comments_table($comments);
}

function FpipR_create_flickr_photosets_comments_getlist_dist() {
  FpipR_make_table('spip_fpipr_comments');
}

function FpipR_flickr_photosets_comments_getlist_dist($arguments) {
  include_spip('inc/flickr_api');
  $comments = flickr_photosets_comments_getList($arguments['id_photoset']);
  FpipR_fill_comments_table($comments);
}

//======================================================================
?>
