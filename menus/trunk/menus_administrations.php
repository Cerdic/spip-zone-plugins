<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');

// Installation et mise à jour
function menus_upgrade($nom_meta_version_base, $version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('creer_base'),
		array('ecrire_config', 'menus/entrees_masquees', array('rubriques', 'groupe_mots', 'mapage', 'deconnecter', 'secteurlangue')),
	);
	
	$maj['0.5.0'] = array(
		array('sql_alter', "TABLE spip_menus ADD COLUMN css tinytext DEFAULT '' NOT NULL"),
	);
	
	$maj['0.5.1'] = array(
		array('sql_updateq', 'spip_menus_entrees', array('type_entree'=>'rubriques_completes'), 'type_entree = '.sql_quote('rubriques')),
	);
	$maj['0.5.2'] = array(
		array('menus_fusionne_critere_tri_inverse'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_version_base, $version_cible, $maj);
}

function menus_fusionne_critere_tri_inverse(){

	$res = sql_select("*","spip_menus_entrees","","","id_menus_entree");
	while ($row = sql_fetch($res)){
		#var_dump($row);
		$params = unserialize($row['parametres']);

		$change = false;
		if (isset($params['tri_num_inverse'])
			AND $params['tri_num_inverse']=='oui'
		  AND strlen($params['tri_num'])){
			$params['tri_num'] = "!".$params['tri_num'];
			$change = true;
			unset($params['tri_num_inverse']);
		}
		if (isset($params['tri_alpha_inverse'])
			AND $params['tri_alpha_inverse']=='oui'
		  AND strlen($params['tri_alpha'])){
			$params['tri_alpha'] = "!".$params['tri_alpha'];
			$change = true;
			unset($params['tri_alpha_inverse']);
		}
		if (isset($params['tri_num_articles_inverse'])
			AND $params['tri_num_articles_inverse']=='oui'
		  AND strlen($params['tri_num_articles'])){
			$params['tri_num_articles'] = "!".$params['tri_num_articles'];
			$change = true;
			unset($params['tri_num_articles_inverse']);
		}
		if (isset($params['tri_alpha_articles_inverse'])
			AND $params['tri_alpha_articles_inverse']=='oui'
		  AND strlen($params['tri_alpha_articles'])){
			$params['tri_alpha_articles'] = "!".$params['tri_alpha_articles'];
			$change = true;
			unset($params['tri_alpha_articles_inverse']);
		}

		if ($change){
			$params = serialize($params);
			sql_updateq("spip_menus_entrees",array('parametres'=>$params),"id_menus_entree=".intval($row['id_menus_entree']));
			#var_dump($params);
		}

	}
}

// Désinstallation
function menus_vider_tables($nom_meta_version_base){
	include_spip('base/abstract_sql');
	
	// On efface les tables du plugin
	sql_drop_table('spip_menus');
	sql_drop_table('spip_menus_entrees');
		
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	// On efface la config
	effacer_meta('menus');
}

?>
