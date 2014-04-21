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
 * Fonction de vérification de la concordance des bilans de chaque langue d'un module
 * 
 * @param string $module
 * 		Le nom du module
 * @param string $langue_mere
 * 		La langue mère du module
 */
function inc_tradlang_verifier_bilans_dist($module,$langue_mere,$invalider=true){
	/**
	 * Quelle est le total de la langue mère
	 */
	$total = sql_countsel('spip_tradlangs','module='.sql_quote($module).' AND lang='.sql_quote($langue_mere).' AND statut="OK"');
	/**
	 * Les infos du module
	 */
	$id_tradlang_module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module='.sql_quote($module));
	/**
	 * Les différentes langues du module
	 */
	$langues = sql_allfetsel('lang','spip_tradlangs','id_tradlang_module = '.intval($id_tradlang_module),'lang');
	/**
	 * Vérification de chaque langue
	 */
	foreach($langues as $langue){
		$bilan = false;
		$chaines_ok = sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($langue['lang']).' AND statut="OK"');
		$chaines_relire = sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($langue['lang']).' AND statut="RELIRE"');
		$chaines_modif = sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($langue['lang']).' AND statut="MODIF"');
		$chaines_new = sql_countsel('spip_tradlangs','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($langue['lang']).' AND statut="NEW"');
		$infos_bilan = array(
							'id_tradlang_module' => $id_tradlang_module,
							'module' => $module,
							'lang' => $langue['lang'],
							'chaines_total' => $total,
							'chaines_ok' => $chaines_ok,
							'chaines_relire' => $chaines_relire,
							'chaines_modif' => $chaines_modif,
							'chaines_new' => $chaines_new
						);
		$bilan = sql_getfetsel('id_tradlang_module','spip_tradlangs_bilans','id_tradlang_module='.intval($id_tradlang_module).' AND lang='.sql_quote($langue['lang']));
		if($bilan)
			sql_updateq('spip_tradlangs_bilans',$infos_bilan,'lang='.sql_quote($langue['lang']).' AND module='.sql_quote($module));
		else
			sql_insertq('spip_tradlangs_bilans',$infos_bilan);
	}
	if($invalider){
		include_spip('inc/invalideur');
		suivre_invalideur('1');
	}
}
?>