<?php
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