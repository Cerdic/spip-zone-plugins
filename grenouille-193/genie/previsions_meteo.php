<?php

include_spip('inc/grenouille');
	
/**
 * genie_previsions_meteo
 *
 * Tache de fond pour mettre a jour les previsions meteo
 *
 * @author Pierre Basson
 **/
 
function genie_previsions_meteo($t) {
	
	$aujourdhui = date("Y-m-d 00:00:00");
	
	/*toutes les heures on declenche la revision voir ("H")-1 */
	$heureprecedent = date("Y-m-d H:00:00", mktime(date("H")-1, 0, 0, date("m") , date("d"), date("Y")));
	$pourmaj = sql_fetsel('*','spip_meteo','maj', '', 'maj ASC',1);
	$pour_maj = $pourmaj['maj'];
	$id_meteo=$pourmaj['id_meteo'];
	$statut=$pourmaj['statut'];
	 
	if (($pour_maj>$heureprecedent)&&($statut!='')){
		 //spip_log("METEO pas besoin de mise ˆ jour car id_meteo = $id_meteo et $pour_maj > $heureprecedent");
	}else{
		  spip_log("METEO on declenche la revision car $pour_maj < $heureprecedent");
	  
	sql_delete('spip_previsions','date<'. sql_quote($aujourdhui));
	$jours = 7;
	$villes = sql_select('*', 'spip_meteo');
	while ($arr = @sql_fetch($villes)) {
		$code = $arr['code'];
		$id_meteo = $arr['id_meteo'];
		$url = "http://xoap.weather.com/weather/local/".$code."?cc=*&unit=s&dayf=".$jours;
		$xml = meteo_lire_xml($url,true,"day d=.*",array("hi","low","part p=\"d\"","part p=\"n\""));
		if ($xml) {
			sql_update('spip_meteo', array('statut'=>sql_quote("publie"),'maj'=>'NOW()'),
				'id_meteo=' . sql_quote($id_meteo));
			for($i=0; $i<$jours; $i++) {
			   $tmp = preg_split("/<\/?icon>/",$xml["part p=\"d\""][$i]);
			   $xml["icon"][$i] = $tmp[1];
			}
			for($i=0; $i<$jours; $i++) {
				$set = array();
				$date = strftime("%Y-%m-%d 12:00:00", time() + $i * 24 * 3600);
				if ($xml["hi"][$i] != "N/A") {
					$set['maxima'] = meteo_convertir_fahrenheit_celsius($xml["hi"][$i]);
				}
				if ($xml["low"][$i] != "N/A") {
					$set['minima'] = meteo_convertir_fahrenheit_celsius($xml["low"][$i]);
				}
				if ($xml["icon"][$i] != 48) {
					$set['id_temps'] = $xml["icon"][$i];
				}

				if ($id_prevision = sql_getfetsel('id_prevision', 'spip_previsions', 
					array("date=". sql_quote($date), "id_meteo=". sql_quote($id_meteo),"maj"))) {
						sql_updateq('spip_previsions', $set,  
							array(
							'id_prevision='. sql_quote($id_prevision), 
							'id_meteo='. sql_quote($id_meteo),
							'maj='.'NOW()'));
				} else {
					isset($set['maxima']) || $set['maxima']='NA';
					isset($set['minima']) || $set['minima']='NA';
					isset($set['id_temps']) || $set['minima']=48;
					$set['id_meteo'] = $id_meteo;
					$set['date'] = $date;
					$set['maj'] = 'NOW()';
					sql_insertq('spip_previsions', $set);

				}
			}
		} else {
			sql_updateq('spip_meteo', 
				array("statut"=>"en_erreur", "maj"=>"NOW()"),
				"id_meteo=". sql_quote($id_meteo));
		}
	}
	return true;
}
}

?>
