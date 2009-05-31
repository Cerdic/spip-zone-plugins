<?php

function critere_pim_agenda_actif_dist($idb, &$boucles, $crit){
	$not = $crit->not;
	$boucle = &$boucles[$idb];
  $type = $boucle->type_requete;
  $nom = $table_des_tables[$type];
  if ($boucle->id_table=='auteurs'){
  	if (isset($GLOBALS['meta']['pim_agenda_auteurs_actifs']))
			$auteurs_agenda_actif = unserialize($GLOBALS['meta']['pim_agenda_auteurs_actifs']);
		else
			$auteurs_agenda_actif = array();
		$c = "calcul_mysql_in('$type.id_auteur', implode(',',isset(\$GLOBALS['meta']['pim_agenda_auteurs_actifs'])?unserialize(\$GLOBALS['meta']['pim_agenda_auteurs_actifs']):array()),'$not')";
		$boucle->where[]= "$c";
  }
}

?>