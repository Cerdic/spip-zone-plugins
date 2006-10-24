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

// Boucle XML
$xml_field = array(
"id_photo"  => "bigint(21) NOT NULL",
"owner" => "varchar(100)", //"47058503995@N01" 
"secret"=> "varchar(100)", //"a123456"
"server"=> "int NOT NULL" //"2"
"title"	=> "text DEFAULT '' NOT NULL",
"ispublic"=> "ENUM (0,1) NOT NULL",
"isfriend"=> "ENUM (0,1) NOT NULL",
"isfamily"=> "ENUM (0,1) NOT NULL"
);
$xml_key = array(
	"PRIMARY KEY" => "id_photo",
	"KEY" => "owner",
	"KEY" => "ispublic",
	"KEY" => "isfriend",
	"KEY" => "isfamily"
);

$GLOBALS['tables_principales']['spip_fpipr_photos'] =
	array('field' => &$xml_field, 'key' => &$xml_key);
//TODO vraiment pas sur de ce qu'il faut mettre la??
$GLOBALS['table_des_tables']['flickr_photos_search'] = 'fpipr_photos';

function FpipR_creer_tables_temporaires($method){
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
		$champs = $GLOBALS['tables_principales'][$nom]['field'];
		$cles = $GLOBALS['tables_principales'][$nom]['key'];
		spip_create_table($nom, $champs, $cles, true, true);		
	}
}

function FpipR_fill_table($method,$arguments){
  include_spip('inc/flickr_api');
  //Faire le query API flickr
  //TODO remplire la table
}
?>