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

// Definition des tables temporaires pour permettre la squeletisation de l'API Flickr
//

/*Les nouvelles colonne de l'auteur*/
include_spip('base/serial');
$GLOBALS['tables_principales']['spip_auteurs']['field']['flickr_nsid'] = "TINYTEXT DEFAULT NULL";
$GLOBALS['tables_principales']['spip_auteurs']['field']['flickr_token'] = "TINYTEXT DEFAULT NULL";


// Boucle XML
$GLOBALS['spip_fpipr_photos_field'] = array(
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
	"last_update" => "INT",
	"latitude" => "DOUBLE",
	"longitude" => "DOUBLE",
	"accuracy" => "smallint NOT NULL",
	"rang" => "int"
);

$GLOBALS['spip_fpipr_photos_key'] = array(
	"PRIMARY KEY" => "id_photo",
	"KEY" => "owner",
	"KEY" => "rang"
);

$GLOBALS['tables_principales']['spip_fpipr_photos'] =
	array('field' => &$GLOBALS['spip_fpipr_photos_field'], 'key' => &$GLOBALS['spip_fpipr_photos_key']);
//les clefs pour trier, pas vraiment dans la table
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['date_posted'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['date_taken'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['interestingness'];
$GLOBALS['tables_principales']['spip_fpipr_photos']['field']['relevance'];

//TODO vraiment pas sur de ce qu'il faut mettre la??
$GLOBALS['table_des_tables']['flickr_photos_search'] = 'fpipr_photos';

function FpipR_creer_tables_temporaires($method){
	//TODO, elle n'est plus temporaire, verifier qu'on ne l'a pas deja cree.
	static $ok=NULL;
	if ($ok==NULL){
		$ok=true;
		switch($method) {
		  case 'flickr.photos.search':
			$nom = 'spip_fpipr_photos';
			break;
		  default:
		  return;
		}
		$field_n = $nom.'_field';
		$key_n = $nom.'_key';
		$champs = $GLOBALS[$field_n];
		$cles = $GLOBALS[$key_n];
	spip_create_table($nom, $champs, $cles, false, false);		
	}
}

function FpipR_fill_table($method,$arguments){
  include_spip('inc/flickr_api');
  //Faire le query API flickr
  switch($method){
	case 'flickr.photos.search':
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
	  $not_id = '';
	  $cnt = 0;
	  foreach($photos->photos as $photo) {
		$query = "REPLACE INTO spip_fpipr_photos (id_photo,user_id,secret,server,title,ispublic,isfriend,isfamily,originalformat,license,upload_date,taken_date,owner_name,icon_server,last_update,longitude,latitude,accuracy,rang)";
		$query .= " VALUES (".intval($photo->id).','.spip_abstract_quote($photo->owner).','.spip_abstract_quote($photo->secret).','.intval($photo->server).','.spip_abstract_quote($photo->title).','.intval($photo->idpublic).','.intval($photo->isfriend).','.intval($photo->isfamily).','.spip_abstract_quote($photo->originalformat).','.intval($photo->license).','.spip_abstract_quote(date('Y-m-d H:i:s',$photo->dateupload)).','.spip_abstract_quote($photo->datetaken).','.spip_abstract_quote($photo->ownername).','.intval($photo->iconserver).','.intval($photo->lastupdate).','.floatval($photo->longitude).','.floatval($photo->latitude).','.intval($photo->accuracy).','.$cnt++.")";
		spip_query($query);
		$not_id .= ','.intval($photo->id);
	  }
	  $query = "DELETE FROM spip_fpipr_photos";
	  if($not_id) {
		  $not_id = substr($not_id,1);
		  $query .= " WHERE id_photo NOT IN ($not_id)";
	  }
	  spip_query($query);
	  break;
	default: 
	  return;
  }
}

?>
