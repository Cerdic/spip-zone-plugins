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
 * Fonction de desinstallation
 * On supprime :
 * -* la table spip_tradlangs
 * -* la table spip_tradlang_modules
 * -* les éléments de spip_versions concernant l'obet tradlang
 * -* les éléments de spip_versions_fragments concernant l'obet tradlang
 * @param unknown_type $nom_meta_base_version
 */
function tradlang_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tradlangs");
	sql_drop_table("spip_tradlang_modules");
	sql_delete('spip_versions','objet='.sql_quote('tradlang'));
	sql_delete('spip_versions_fragments','objet='.sql_quote('tradlang'));
	effacer_meta($nom_meta_base_version);
}
?>