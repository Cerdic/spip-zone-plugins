<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by * 
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('public/criteres_motspartout'); //d�finition du critere branchemot
include_spip('inc/grouper_mots_motspartout'); // pour faire fonctionner le critere branchemot
//(on passe pas par composer car on n'y faisait que que cet include

global $tables_jointures,$tables_auxiliaires,$exceptions_des_jointures;


$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));	
if (!$tables_installees){
  $tables_installees=array("articles"=>true,"rubriques"=>true,"breves"=>true,"syndic"=>true);
//  ecrire_meta('MotsPartout:tables_installees',serialize($tables_installees));
//  ecrire_metas();
 }
	
//foreach($tables_installees as $chose => $m) { $choses[]= $chose; }
$choses=array_keys($tables_installees); //YOANN 
global $choses_possibles;
//include(_DIR_PLUGIN_MOTSPARTOUT."/mots_partout_choses.php");
   
foreach ($choses as $chose){
  $id_chose = $choses_possibles[$chose]['id_chose'];
  $table_principale = $choses_possibles[$chose]['table_principale'];
	

  $spip_mots_choses[$chose] = array(
							"id_mot"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
							$id_chose	=> "BIGINT (21) DEFAULT '0' NOT NULL");

  $spip_mots_choses_key[$chose] = array(
								"PRIMARY KEY"	=> "$id_chose, id_mot",
								"KEY id_mot"	=> "id_mot");

  $table_des_tables['mots_'.$chose]='mots_'.$chose;
  $tables_auxiliaires['spip_mots_'.$chose] = array(
												   'field' => &$spip_mots_choses[$chose],
												   'key' => &$spip_mots_choses_key[$chose]);

	if (!in_array('mots_'.$chose,$tables_jointures['spip_'.$chose]))
		$tables_jointures['spip_'.$chose][]= 'mots_'.$chose;
	if (!in_array('mots',$tables_jointures['spip_'.$chose]))
		$tables_jointures['spip_'.$chose][]= 'mots';
	if (!in_array('mots_'.$chose,$tables_jointures['spip_mots']))
		$tables_jointures['spip_mots'][]= 'mots_'.$chose;

}
?>