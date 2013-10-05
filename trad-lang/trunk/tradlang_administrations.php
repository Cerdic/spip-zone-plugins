<?php
/**
 * Plugin Tradlang
 * Licence GPL (c) 2009-2012
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function tradlang_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	$maj = array();
	$maj['create'] = array(
		array('creer_base'),
		array('maj_tables',array('spip_auteurs')),
		array('tradlang_import_ancien_tradlang',true),
		array('tradlang_maj_modules',true)
	);
	$maj['0.3.1'] = array(
		array('sql_alter',"TABLE spip_tradlang CHANGE status status VARCHAR(16) NOT NULL DEFAULT 'OK'")
	);
	$maj['0.3.2'] = array(
		array('sql_alter',"TABLE spip_tradlang_modules CHANGE nom_mod nom_mod VARCHAR(32) NOT NULL"),
		array('sql_alter',"TABLE spip_tradlang_modules CHANGE lang_prefix lang_prefix VARCHAR(32) NOT NULL")
	);
	$maj['0.3.3'] = array(
		array('sql_alter',"TABLE spip_tradlang CHANGE status statut VARCHAR(16) NOT NULL default 'OK'"),
	);
	$maj['0.3.4'] = array(
		array('sql_alter',"TABLE spip_tradlang ADD id_tradlang_module bigint(21) DEFAULT '0' NOT NULL"),
		array('tradlang_maj_id_tradlang_modules',true)
	);
	$maj['0.3.5'] = array(
		array('maj_tables',array('spip_tradlang')),
		array('tradlang_maj_tradlang_titre',true)
	);
	$maj['0.3.6'] = array(
		array('maj_tables',array('spip_tradlang'))
	);
	$maj['0.3.7'] = array(
		array('tradlang_maj_traducteurs','true')
	);
	$maj['0.3.8'] = array(
		array('maj_tables',array('spip_tradlang_modules'))
	);
	$maj['0.3.9'] = array(
		array('sql_alter',"TABLE spip_tradlang_modules DROP INDEX nom_mod"),
		array('sql_alter',"TABLE spip_tradlang_modules CHANGE nom_mod nom_mod text DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_tradlang_modules ADD INDEX `nom_mod` ( `nom_mod` ( 255 ) )")
	);
	$maj['0.4.0'] = array(
		array('maj_tables',array('spip_auteurs'))
	);
	$maj['0.4.1'] = array(
		array('sql_alter',"TABLE spip_tradlang DROP maj"),
		array('sql_alter',"TABLE spip_tradlang CHANGE ts maj timestamp(14) NOT NULL"),
	);
	$maj['0.4.2'] = array(
		array('sql_alter',"TABLE spip_tradlang RENAME spip_tradlangs")
	);
	$maj['0.4.3'] = array(
		array('maj_tables',array('spip_auteurs'))
	);
	$maj['0.4.4'] = array(
		array('maj_tables',array('spip_tradlang_modules'))
	);
	$maj['0.4.5'] = array(
		array('sql_alter',"TABLE spip_tradlangs ADD INDEX id_tradlang_module (id_tradlang_module)"),
	);
	$maj['0.4.6'] = array(
		array('sql_alter',"TABLE spip_tradlangs ADD INDEX statut (statut)"),
	);
	$maj['0.5.0'] = array(
		array('creer_base'),
		array('tradlang_maj_bilans')
	);
	$maj['0.5.1'] = array(
		array('tradlang_maj_attic')
	);
	$maj['0.5.2'] = array(
		array('maj_tables',array('spip_tradlang_modules'))
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction d'import de l'ancien tradlang 
 * Ne devrait être utile que sur spip.net mais sais t on jamais
 */
function tradlang_import_ancien_tradlang($affiche=false){
	/**
	 * On insère les modules
	 */
	$modules = sql_select('*','trad_lang','', array('module'));
	while($module=sql_fetch($modules)){
		$id_module = sql_insertq('spip_tradlang_modules',array('module'=>$module['module'],'nom_mod' =>$module['module']));
		/**
		 * On insére les anciens tradlang
		 */
		//$docs = array_map('reset',sql_allfetsel('id_document','spip_documents',"statut='0'",'','',"0,100"));
		$strings = sql_allfetsel('id,module,lang,str,comm,status,traducteur,ts,md5,orig,date_modif','trad_lang',"module=".sql_quote($module['module']). " AND orig!='2'",'','',"0,100");
		$count = 0;
		while (count($strings)){
			foreach($strings as $id => $string){
				$string['titre'] = $string['id'].' : '.$string['module'].' - '.$string['lang'];
				if(!$string['md5'])
					$string['md5'] = md5($string['str']);
				$string['langue_choisie'] = 'non';
				$string['id_tradlang_module'] = $id_module;
				$string['statut'] = $string['status'] ? $string['status'] : 'OK';
				$string['maj'] = $string['ts'];
				unset($string['ts']);
				unset($string['status']);
				sql_insertq('spip_tradlangs',$string);
				sql_updateq('trad_lang',array('orig' => 2),'str='.sql_quote($string['str']).' AND lang='.sql_quote($string['lang']));
			}
			if ($affiche) echo " .";
			$count = $count+count($strings);
			spip_log($count,'tradlang');
			$strings = sql_allfetsel('id,module,lang,str,comm,status,traducteur,ts,md5,orig,date_modif','trad_lang',"module=".sql_quote($module['module']). " AND orig!='2'",'','',"0,100");
		}
	}
}
function tradlang_maj_id_tradlang_modules($affiche = false){
	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlangs',"id_tradlang_module='0'",'','',"0,100"));
	while (count($strings)){
		foreach($strings as $id_tradlang){
			$module = sql_getfetsel('module','spip_tradlangs','id_tradlang='.intval($id_tradlang));
			$id_tradlang_module = sql_getfetsel('id_tradlang_module','spip_tradlang_modules','module='.sql_quote($module));
			sql_updateq('spip_tradlangs',array('id_tradlang_module' => $id_tradlang_module),'id_tradlang='.intval($id_tradlang));
		}
		if ($affiche) echo " .";
	  	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlangs',"id_tradlang_module='0'",'','',"0,100"));
	}
}

function tradlang_maj_tradlang_titre($affiche = false){
	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlangs',"titre=''",'','',"0,500"));
	while (count($strings)){
		spip_log(count($strings),'maj');
		foreach($strings as $id_tradlang){
			$tradlang = sql_fetsel('*','spip_tradlangs','id_tradlang='.intval($id_tradlang));
			$titre = $tradlang['id'].' : '.$tradlang['module'].' - '.$tradlang['lang'];
			sql_updateq('spip_tradlangs',array('titre' => $titre),'id_tradlang='.intval($id_tradlang));
		}
		if ($affiche) echo " .";
	  	$strings = array_map('reset',sql_allfetsel('id_tradlang','spip_tradlangs',"titre=''",'','',"0,500"));
	}
}

function tradlang_maj_modules($affiche=false){
	$tradlang_verifier_langue_base = charger_fonction('tradlang_verifier_langue_base','inc');
	/**
	 * On update les modules
	 */
	$modules = sql_select('*','spip_tradlang_modules','module NOT LIKE "attic%" AND module !='.sql_quote('attic'));
	
	while($module = sql_fetch($modules)){
		spip_log($module['module'],'tradlang');
		if ($affiche) echo " .";
		$langues = sql_select('lang','spip_tradlangs','id_tradlang_module='.intval($module['id_tradlang_module']).' AND lang!='.sql_quote($module['lang_mere']), array('lang'));
		while($lang = sql_fetch($langues)){
			$modifs = $tradlang_verifier_langue_base($module['module'],$lang['lang']);
		}
	}
}

/**
 * On remet les traducteurs des locutions 
 */
function tradlang_maj_traducteurs($affiche=false){
	$chaines_traducteurs = sql_select('*','trad_lang','status = "" AND traducteur != ""');
	while($traduction = sql_fetch($chaines_traducteurs)){
		sql_updateq('spip_tradlangs',array('traducteur'=>$traduction['traducteur']),'module = '.sql_quote($traduction['module']).' AND id='.sql_quote($traduction['id']).' AND lang='.sql_quote($traduction['lang']));
	}
	
	$chaines_traducteurs_modif = sql_select('*','trad_lang','status = "MODIF" AND traducteur != ""');
	while($traduction = sql_fetch($chaines_traducteurs_modif)){
		sql_updateq('spip_tradlangs',array('traducteur'=>$traduction['traducteur']),'module = '.sql_quote($traduction['module']).' AND id='.sql_quote($traduction['id']).' AND lang='.sql_quote($traduction['lang']));
	}
}

/**
 * On crée les bilans de chaque langue de chaque module
 */
function tradlang_maj_bilans($affiche=false){
	$modules = sql_select('id_tradlang_module,module,lang_mere','spip_tradlang_modules');
	
	/**
	 * On passe d'abord les modules un par un
	 * On récupère $total qui est le total des chaines de la langue mère
	 */
	while($module = sql_fetch($modules)){
		/**
		 * Si on n'est pas dans un module type attic
		 */
		if(substr($module['module'],0,5) != 'attic'){
			$total = sql_countsel('spip_tradlangs','module='.sql_quote($module['module']).' AND lang='.sql_quote($module['lang_mere']));
			$langues_base = sql_select('lang','spip_tradlangs','module='.sql_quote($module['module']),'lang');
			/**
			 * On passe ensuite chaque langue de ce module en revue
			 * On insère une entrée pour chaque langue de chaque module
			 */
			while($langue = sql_fetch($langues_base)){
				$lang = $langue['lang'];
				$chaines_ok = sql_countsel('spip_tradlangs','module='.sql_quote($module['module']).' AND lang='.sql_quote($lang).' AND statut="OK"');
				$chaines_relire = sql_countsel('spip_tradlangs','module='.sql_quote($module['module']).' AND lang='.sql_quote($lang).' AND statut="RELIRE"');
				$chaines_modif = sql_countsel('spip_tradlangs','module='.sql_quote($module['module']).' AND lang='.sql_quote($lang).' AND statut="MODIF"');
				$chaines_new = sql_countsel('spip_tradlangs','module='.sql_quote($module['module']).' AND lang='.sql_quote($lang).' AND statut="NEW"');
				$infos_bilan = array(
									'id_tradlang_module' => $module['id_tradlang_module'],
									'module' => $module['module'],
									'lang' => $lang,
									'chaines_total' => $total,
									'chaines_ok' => $chaines_ok,
									'chaines_relire' => $chaines_relire,
									'chaines_modif' => $chaines_modif,
									'chaines_new' => $chaines_new
								);
				sql_insertq('spip_tradlangs_bilans',$infos_bilan);
			}
		}
	}
}

function tradlang_maj_attic($affiche=false){
	/**
	 * Dans un premier temps, on supprimer les attics qui ont un statut NEW,
	 * il ne serviront jamaiscar même récupérés, ils ne sont pas traduit
	 */
	sql_delete('spip_tradlangs','module LIKE "attic%" AND statut="NEW"');
	$select_attic_id_module = sql_select('*','spip_tradlangs','module LIKE "attic%"','id_tradlang_module');
	while($id_module = sql_fetch($select_attic_id_module)){
		$module = sql_getfetsel('module','spip_tradlang_modules','id_tradlang_module='.intval($id_module['id_tradlang_module']));
		if($module){
			$attics_module = sql_select('id_tradlang,id,module,lang','spip_tradlangs','id_tradlang_module='.intval($id_module['id_tradlang_module']).' AND module LIKE "attic%"');
			while($id_tradlang = sql_fetch($attics_module)){
				if(!sql_getfetsel('id_tradlang','spip_tradlangs','id='.sql_quote($id_tradlang['id']).' AND module='.sql_quote($id_tradlang['module']).' AND lang='.sql_quote($id_tradlang['lang'])))
					sql_updateq('spip_tradlangs',array('statut'=>'attic','module'=>$module),'id_tradlang='.intval($id_tradlang['id_tradlang']));
				else
					sql_delete('spip_tradlangs','id_tradlang='.intval($id_tradlang['id_tradlang']));
			}
		}
	}
	sql_delete('spip_tradlang_modules','module LIKE "attic%"');
}
/**
 * Fonction de desinstallation
 * On supprime :
 * -* la table spip_tradlangs
 * -* la table spip_tradlang_modules
 * -* la table spip_tradlangs_bilans
 * -* les éléments de spip_versions concernant l'obet tradlang
 * -* les éléments de spip_versions_fragments concernant l'obet tradlang
 * @param unknown_type $nom_meta_base_version
 */
function tradlang_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tradlangs");
	sql_drop_table("spip_tradlang_modules");
	sql_drop_table("spip_tradlangs_bilans");
	sql_delete('spip_versions','objet='.sql_quote('tradlang'));
	sql_delete('spip_versions_fragments','objet='.sql_quote('tradlang'));
	effacer_meta($nom_meta_base_version);
}
?>