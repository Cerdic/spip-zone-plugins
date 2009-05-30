<?php


	/**
	 * Favoris: plugin Photos SPIP 2.0
	 *
	 * Copyright (c) 2009
	 * Bernard Blazin
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * 
	 *  
	 **/

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_principales;
//global $tables_auxiliaires;
  

$spip_photos = array(
						"id_photo" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"nom_photo" => "TINYTEXT NOT NULL",
						"nom_vignette" => "TINYTEXT NOT NULL",
						"dateheure" => "DATETIME",
						"id_auteur"  => "BIGINT(21) NOT NULL",
						"alt_photo" => "TINYTEXT NOT NULL"
						
						);
$spip_photos_key = array(
						"PRIMARY KEY" => "id_photo");
					
	
	$tables_principales['spip_photos'] =
		array('field' => &$spip_photos, 'key' => &$spip_photos_key);
		
		
// Declarer dans la table des tables pour sauvegarde
global $table_des_tables;
$table_des_tables['photos']  = 'spip_photos';

//boucle
function boucle_SPIP_PHOTOS_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_photos";  

			
	        return calculer_boucle($id_boucle, $boucles); 
	}


?>