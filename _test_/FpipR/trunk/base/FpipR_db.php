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

$GLOBALS['FpipR_versions']['spip_fpipr_photos'] = '0.7';

$GLOBALS['FpipR_tables']['spip_fpipr_photos_field'] = array(
															"id_photo"  => "bigint(21) NOT NULL",
															"user_id" => "varchar(100)", //"47058503995@N01" 
															"farm" => "int NOT NULL",
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


$GLOBALS['FpipR_versions']['spip_fpipr_tags'] = '0.8';
$GLOBALS['FpipR_tables']['spip_fpipr_tags_field'] = array("id_tag" => 'varchar(255) NOT NULL',
														  "author" => 'varchar(100)',
														  "raw" => "text DEFAULT '' NOT NULL",
														  "safe" => "text DEFAULT '' NOT NULL",
														  "id_photo" => 'bigint(21) NOT NULL', //la façon dont on recupere les tags, on a juste une photo par tag.
														  'score' => 'int',
														  'count' => 'int'
														  );
$GLOBALS['FpipR_tables']['spip_fpipr_tags_key'] = array("PRIMARY KEY" => "id_tag",
														"KEY id_photo" => "id_photo");


$GLOBALS['tables_principales']['spip_fpipr_tags'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_tags_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_tags_key']);
$GLOBALS['table_des_tables']['flickr_photo_tags'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getrelated'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getlistphoto'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getlistuser'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getlistuserraw'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_getlistuserpopular'] = 'fpipr_tags';
$GLOBALS['table_des_tables']['flickr_tags_gethotlist'] = 'fpipr_tags';

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


$GLOBALS['FpipR_versions']['spip_fpipr_photosets'] = '0.4';
$GLOBALS['FpipR_tables']['spip_fpipr_photosets_field'] = array(
															   "id_photoset" => "bigint(21) NOT NULL",
															   "user_id" => 'varchar(100)',
															   'primary_photo' => "bigint(21) NOT NULL",
															   "secret"=> "varchar(100)", //"a123456"
															   "server"=> "int NOT NULL", //"2"
															   "farm" => "int NOT NULL",
															   'photos' => 'int',
															   "title"	=> "text DEFAULT '' NOT NULL",
															   "description" => "text DEFAULT '' NOT NULL"
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_photosets_key'] = array("PRIMARY KEY" => "id_photoset");


$GLOBALS['tables_principales']['spip_fpipr_photosets'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_photosets_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_photosets_key']);
$GLOBALS['table_des_tables']['flickr_photosets_getlist'] = 'fpipr_photosets';
$GLOBALS['table_des_tables']['flickr_photosets_getinfo'] = 'fpipr_photosets';

$GLOBALS['table_des_tables']['flickr_photosets_getphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_groups_pools_getphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getcontactspublicphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getcontactsphotos'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_favorites_getpubliclist'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_favorites_getlist'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getnotinset'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getrecent'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getuntagged'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getwithgeodata'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_getwithoutgeodata'] = 'fpipr_photos';
$GLOBALS['table_des_tables']['flickr_photos_recentlyupdated'] = 'fpipr_photos';

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
// pour les groupes

$GLOBALS['FpipR_versions']['spip_fpipr_groups'] = '0.5';
$GLOBALS['FpipR_tables']['spip_fpipr_groups_field'] = array(
															   "id_group" => "varchar(255) NOT NULL",
															   "user_id" => 'varchar(100)', //cas où on recupere les groupes d'un utilisateur
															   "admin" => "ENUM ('0','1')",
															   "eighteenplus" => "ENUM ('0','1')",
															   "iconserver" => "int default 0",
															   "name" => "text default ''",
															   "description" => "text default ''",
															   "members" => "int default 0",
															   "privacy" => "int", //???
															   "throttle_count" => "int",
															   "throttle_mode" => "varchar(100)",
															   "throttle_remaining" => "int",
															   "photos" => 'int'
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_groups_key'] = array("PRIMARY KEY" => "id_group",
															"KEY" => 'user_id');


$GLOBALS['tables_principales']['spip_fpipr_groups'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_groups_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_groups_key']);
$GLOBALS['table_des_tables']['flickr_groups_getinfo'] = 'fpipr_groups';
$GLOBALS['table_des_tables']['flickr_urls_lookupgroup'] = 'fpipr_groups';
$GLOBALS['table_des_tables']['flickr_people_getpublicgroups'] = 'fpipr_groups';
$GLOBALS['table_des_tables']['flickr_groups_pools_getgroups'] = 'fpipr_groups';

//======================================================================
//pour les peoples



$GLOBALS['FpipR_versions']['spip_fpipr_people'] = '0.4';
$GLOBALS['FpipR_tables']['spip_fpipr_people_field'] = array(
															   "user_id" => 'varchar(100)', //cas où on recupere les groupes d'un utilisateur
															   "isadmin" => "ENUM ('0','1')",
															   "ispro" => "ENUM ('0','1')",
															   "iconserver" => "int default 0",
															   "username" => "text default ''",
															   "realname" => "text default ''",
															   "location" => "text default ''",
															   "url_photos" => "text default ''",
															   "url_profile" => "text default ''",
															   "date_firstphoto" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															   "date_taken_firstphoto" => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
															   "photos_count" => "int",
															   "ignored" => "ENUM ('0','1')",
															   "family" => "ENUM ('0','1')",
															   "friend" => "ENUM ('0','1')"
															   );
$GLOBALS['FpipR_tables']['spip_fpipr_people_key'] = array("PRIMARY KEY" => "user_id");


$GLOBALS['tables_principales']['spip_fpipr_people'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_people_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_people_key']);
$GLOBALS['table_des_tables']['flickr_people_getinfo'] = 'fpipr_people';
$GLOBALS['table_des_tables']['flickr_contacts_getpubliclist'] = 'fpipr_people';
$GLOBALS['table_des_tables']['flickr_contacts_getlist'] = 'fpipr_people';
$GLOBALS['table_des_tables']['flickr_urls_lookupuser'] = 'fpipr_people';

//======================================================================
//EXIF

$GLOBALS['FpipR_versions']['spip_fpipr_exif'] = '0.2';
$GLOBALS['FpipR_tables']['spip_fpipr_exif_field'] = array(
														  'id_photo' => 'bigint(21) NOT NULL',
														  'secret' => 'varchar(100)',
														  'server' => 'int NOT NULL',
														  'tagspace' => 'varchar(50)',
														  'tagspaceid' => 'int',
														  'tag' => 'int',
														  'label' => "text DEFAULT ''",
														  'raw' => "text DEFAULT ''",
														  'clean' => "text DEFAULT ''",
														  );
$GLOBALS['FpipR_tables']['spip_fpipr_exif_key'] = array("PRIMARY KEY" => "tagspaceid",
														"PRIMARY KEY" => "tag");
$GLOBALS['tables_principales']['spip_fpipr_exif'] =
  array('field' => &$GLOBALS['FpipR_tables']['spip_fpipr_exif_field'], 'key' => &$GLOBALS['FpipR_tables']['spip_fpipr_exif_key']);
$GLOBALS['table_des_tables']['flickr_photos_getexif'] = 'fpipr_exif';

//======================================================================

function FpipR_creer_tables($method){
  $fct = str_replace('.','_',$method);
  $f=charger_fonction($fct, 'FpipR_create');
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
	spip_abstract_create($nom, $champs, $cles, false, false);
  }
  $created[$nom] = true;
  ecrire_meta("FpipR_$nom",$GLOBALS['FpipR_versions'][$nom]);
  ecrire_metas();
}

function FpipR_fill_table($method,$arguments){
  //Faire le query API flickr
  $fct = str_replace('.','_',$method);
  $f=charger_fonction($fct, 'FpipR_fill');
  return $f($arguments);
}

function FpipR_fill_photos_table($photos,$add='') {
  $cnt = 0;
  $query = "DELETE FROM spip_fpipr_photos";
  spip_query($query);
  $col = '(id_photo,user_id,secret,server,title,ispublic,isfriend,isfamily,originalformat,license,upload_date,taken_date,owner_name,icon_server,last_update,longitude,latitude,accuracy,rang,added_date,farm';

  if($add)
	foreach($add as $name=>$val) {
	  $col.=",$name";
	}
  foreach($photos as $photo) {
	$ownername = $photo->ownername?$photo->ownername:$photo->username;
	$vals = "("._q($photo->id).','._q($photo->owner).','._q($photo->secret).','._q($photo->server).','._q($photo->title).','._q($photo->idpublic).','._q($photo->isfriend).','._q($photo->isfamily).','._q($photo->originalformat).','._q($photo->license).','._q(date('Y-m-d H:i:s',$photo->dateupload+0)).','._q($photo->datetaken).','._q($photo->ownername).','._q($photo->iconserver).','._q(date('Y-m-d H:i:s',$photo->lastupdate+0)).','._q($photo->longitude).','._q($photo->latitude).','._q($photo->accuracy).','.$cnt++.','._q(date('Y-m-d H:i:s',$photo->dateadded+0)).','.intval($photo->farm);
	if($add)
	  foreach($add as $name=>$val) {
		$vals .= ','._q($val);
	  }
	spip_abstract_insert('spip_fpipr_photos',
						 $col.')',
						 $vals.")");
  }
}

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


function FpipR_fill_groups_table($groups,$key='groups',$add='') {
  $query = "DELETE FROM spip_fpipr_groups";
  spip_query($query);			

  if($groups = $groups[$key]) {
	$col = '(id_group,name,admin,eighteenplus,privacy,photos,iconserver';
	if($add)
	  foreach($add as $name=>$val) {
		$col.=",$name";
	  }
	foreach($groups['group'] as $g) {
	  $vals = '('._q($g['nsid']).','._q($g['name']).','._q($g['admin']).','._q($g['eighteenplus']).
		','._q($g['privacy']).','._q($g['photos']).','._q($g['iconserver']);
	  foreach($add as $name=>$val) {
		$vals .= ','._q($val);
	  }
	  spip_abstract_insert('spip_fpipr_groups',
						   $col.')',$vals.')'
						   );
	}
  }	
}
?>
