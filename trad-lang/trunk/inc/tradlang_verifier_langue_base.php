<?php
/**
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 * 
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
	
	$trad_langue_mere_id = $trad_langue_cible_id = array();
	
	/**
	 * On crée deux tableaux: 
	 * -* l'ensemble des id de la langue mère non supprimés 
	 * -* l'ensemble des id de la langue cible non supprimés
	 */
	$trad_langue_meres = sql_allfetsel('id','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere).' AND statut !="attic"');
	foreach($trad_langue_meres as $trad_langue_mere){
		$trad_langue_mere_id[] = $trad_langue_mere['id']; 
	}
	$trad_langue_cibles  = sql_allfetsel('id','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue).' AND statut !="attic"');
	foreach($trad_langue_cibles as $trad_langue_cible){
		$trad_langue_cible_id[] = $trad_langue_cible['id']; 
	}

	$inserees = $supprimees = 0;
	/**
	 * $diff1 est l'ensemble des chaines manquantes dans la langue cible
	 * et donc à insérer
	 * 
	 * On met dans un tableau les chaines en question si on a au moins un résultat
	 */
	$diff1 = array_diff($trad_langue_mere_id, $trad_langue_cible_id);
	if(count($diff1) > 0)
		$diff1_array = sql_allfetsel('*','spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere).' AND '.sql_in('id',$diff1));
	
	/**
	 * $diff2 est l'ensemble des chaines en trop dans la langue fille
	 * et donc à supprimer
	 */
	$diff2 = array_diff($trad_langue_cible_id,$trad_langue_mere_id);
	
	/**
	 * Si on a des éléments dans les diffs, on applique les modifications
	 */
	if((count($diff1)>0) OR (count($diff2)>0)){
		if(isset($diff1_array) && is_array($diff1_array)){
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
		/**
		 * On donne le statut attic aux chaînes en trop
		 * On incrémente le nombre de chaînes supprimées
		 */
		if(count($diff2) > 0){
			foreach($diff2 as $key => $id){
				sql_updateq('spip_tradlangs',array('statut' => 'attic'),'id='.sql_quote($id).' AND lang='.sql_quote($langue).' AND module='.sql_quote($module));
				$supprimees++;
			}
		}
	}else
		return array('0','0');
	include_spip('inc/invalideur');
	suivre_invalideur('1');
	return array($inserees,$supprimees);
}
?>