<?php

function ajouter_des_jours($date,$nb){
	if(!intval($nb))
		return;

	return date('Y-m-d H:i:s',mktime(0, 0, 0, date("m") , date("d") + $nb, date("Y")));
}

function add_date($givendate,$day=0,$mth=0,$yr=0) 
{
      $cd = strtotime($givendate);
      $newdate = date('Y-m-d h:i:s', mktime(date('h',$cd), date('i',$cd), date('s',$cd), date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
      return $newdate;
}

?>
