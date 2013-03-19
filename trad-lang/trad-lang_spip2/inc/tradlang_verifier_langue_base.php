<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
 * @package SPIP\Tradlang\
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de vérification de la concordance d'une langue x par rapport à la langue mère
 * 
 * @param string $module
 * 		Le nom du module
 * @param string $langue
 * 		La langue à comparer 
 */
function inc_tradlang_verifier_langue_base_dist($module,$langue){
	/**
	 * Quelle est la langue mère
	 */
	$langue_mere = sql_getfetsel('lang_mere','spip_tradlang_modules','module='.sql_quote($module));
	
	/**
	 * On teste et on ajoute ce qu'il y a en trop
	 */
	$trad_langue_mere_id = array();
	$trad_langue_mere = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere));
	while($row_langue_mere = sql_fetch($trad_langue_mere)){
		$trad_langue_mere_id[] = $row_langue_mere['id']; 
	}
	$trad_langue_cible_id = array();
	$trad_langue_cible  = sql_select('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue));
	while($row_langue_cible = sql_fetch($trad_langue_cible)){
		$trad_langue_cible_id[] = $row_langue_cible['id']; 
	}

	/**
	 * $diff1 est l'ensemble des chaines manquantes dans la langue fille
	 * et donc à insérer
	 */
	$diff1 = array_diff($trad_langue_mere_id, $trad_langue_cible_id);
	$diff1_array = sql_allfetsel('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere).' AND '.sql_in('id',$diff1));
	$inserees = 0;
	/**
	 * $diff2 est l'ensemble des chaines en trop dans la langue fille
	 * et donc à supprimer
	 */
	$diff2 = array_diff($trad_langue_cible_id,$trad_langue_mere_id);
	$supprimees = 0;
	if((count($diff1)>0) OR (count($diff2)>0)){
		if(is_array($diff1_array)){
			foreach($diff1_array as $key => $array){
				$array['orig'] = 0;
				$array['lang'] = $langue;
				$array['titre'] = $array['id'].' : '.$array['module'].' - '.$langue;
				$array['statut'] = 'NEW';
				unset($array['maj']);
				unset($array['id_tradlang']);
				unset($array['traducteur']);
				$id_tradlang = sql_insertq('spip_tradlangs',$array);
				$inserees++;
			}
		}

		foreach($diff2 as $key => $id){
			$array['id'] = $module.'_'.$id;
			$array['module'] = 'attic%'.$module;
			$id_tradlang = sql_getfetsel('id_tradlang','spip_tradlangs','id='.sql_quote($id)." AND module=".sql_quote($module)." AND lang=".sql_quote($langue));
			sql_updateq('spip_tradlangs',$array,'id_tradlang='.intval($id_tradlang));
			$supprimees++;
		}
	}else{
		return array('0','0');
	}
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array($inserees,$supprimees);
}
?>