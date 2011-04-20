<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_tradlang_verifier_langue_base_dist($module,$langue){
	/**
	 * Quelle est la langue mère
	 */
	$langue_mere = sql_getfetsel('lang_mere','spip_tradlang_modules','nom_mod='.sql_quote($module));
	
	if(sql_count($trad_langue_mere) != sql_count($trad_langue_cible)){
		spip_log("compte de locutions different entre $langue_mere et $langue");
	}
	
	/**
	 * On teste et on ajoute ce qu'il y a en trop
	 */
	$trad_langue_mere = sql_select('*','spip_tradlang','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere));
	while($row_langue_mere = sql_fetch($trad_langue_mere)){
		$trad_langue_mere_id[] = $row_langue_mere['id']; 
	}
	
	$trad_langue_cible  = sql_select('*','spip_tradlang','module='.sql_quote($module).' AND lang='.sql_quote($langue));
	while($row_langue_cible = sql_fetch($trad_langue_cible)){
		$trad_langue_cible_id[] = $row_langue_cible['id']; 
	}
	
	/**
	 * $diff1 est l'ensemble des chaines manquantes dans la langue fille
	 */
	$diff1 = array_diff($trad_langue_mere_id, $trad_langue_cible_id);
	$diff1_array = sql_allfetsel('*','spip_tradlang','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere).' AND '.sql_in('id',$diff1));
	
	$inserees = 0;
	
	/**
	 * $diff2 est l'ensemble des chaines en trop dans la langue fille
	 */
	$diff2 = array_diff($trad_langue_cible_id,$trad_langue_mere_id);
	
	$supprimees = 0;
	
	if((count($diff1)>0) OR (count($diff2)>0)){
		include_spip('action/editer_tradlang');
		
		foreach($diff1_array as $key => $array){
			$array['orig'] = 0;
			$array['lang'] = $langue;
			$array['statut'] = 'NEW';
			unset($array['ts']);
			unset($array['id_tradlang']);
			
			$id_tradlang = sql_insertq('spip_tradlang');
			spip_log($array);
			tradlang_set($id_tradlang,$array);
			$inserees++;
		}
		spip_log("$inserees insertions");

		foreach($diff2 as $key => $id){
			$array['id'] = $module.'_'.$id;
			$array['module'] = 'attic';
			$id_tradlang = sql_getfetsel('id_tradlang','spip_tradlang','id='.sql_quote($id)." AND module=".sql_quote($module)." AND lang=".sql_quote($langue));
			tradlang_set($id_tradlang,$array);
			$supprimees++;
		}
		spip_log("$supprimees suppressions");		
	}
}
?>