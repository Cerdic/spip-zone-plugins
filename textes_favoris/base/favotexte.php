<?php


	/**
	 * Favoris: plugin de gestion des Articles favoris
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
  

$spip_favtextes = array(
						"id_favtxt" => "BIGINT(21) NOT NULL AUTO_INCREMENT",
						"id_auth" => "INT(11) NOT NULL DEFAULT '0'",
						"id_texte" => "INT(11) NOT NULL DEFAULT '0' "
						);
$spip_favtextes_key = array(
						"PRIMARY KEY" => "id_favtxt");
					
	
	$tables_principales['spip_favtextes'] =
		array('field' => &$spip_favtextes, 'key' => &$spip_favtextes_key);
		
		
// Declarer dans la table des tables pour sauvegarde
global $table_des_tables;
$table_des_tables['favtextes']  = 'spip_favtextes';

//boucle
function boucle_SPIP_FAVTEXTES_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_favtextes";  

			
	        return calculer_boucle($id_boucle, $boucles); 
	}


?>