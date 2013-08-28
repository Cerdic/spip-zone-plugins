<?php
/**
 * Plugin Inscription3 pour SPIP
 * © 2007-2013 - cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Fonctions d'installation et de désinstallation du plugin
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction d'installation et de mise à jour du plugin
 * @return
 */
function inscription3_upgrade($nom_meta_base_version,$version_cible){
	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());
	
	/**
	 *  A t on une meta d'installation déjà?
	 */
	$inscription3_meta = isset($GLOBALS['meta']['inscription3']) ? $GLOBALS['meta']['inscription3'] : false;

	/**
	 * Certaines montées de version ont oublié de corriger la meta de I2
	 * si ce n'est pas un array alors il faut supprimer la meta pour la réinstaller
	 */
	if ($inscription3_meta && !is_array(@unserialize($inscription3_meta)))
		effacer_meta('inscription3');

	/**
	 * Inscription2 semble installé, on tranfère sa configuration vers inscription3
	 */
	if(isset($GLOBALS['meta']['inscription2']) && is_array(@unserialize($GLOBALS['meta']['inscription2']))){
		ecrire_meta('inscription3', $GLOBALS['meta']['inscription2']);
		$inscription3_meta = $inscription2_meta;
		inscription3_transfert_infos_auteurs();
		effacer_meta('inscription2');
	}
	
	include_spip('inc/cextras');
	include_spip('base/inscription3');

	$maj = array();
	
	$maj['create'] = array(
		array('i3_installer_pays','')
	);
	if(!is_array($inscription3_meta)){
		$maj['create'][] = array('ecrire_meta','inscription3',serialize(array(
						'nom_fiche_mod' => 'on',
						'nom_fiche_table' => 'on',
						'email_fiche_mod' => 'on',
						'email_fiche_table' => 'on',
						'pass_fiche_mod' => 'on',
						'bio_fiche_mod' => 'on',
						'login_fiche_mod' => 'on',
						'nom_site_fiche_mod' => 'on',
						'url_site_fiche_mod' => 'on',
						'nom_fiche_mod' => 'on',
						'statut_nouveau' => '6forum',
						'statut_interne' => ''
					)));
	}

	cextras_api_upgrade(inscription3_declarer_champs_extras(), $maj['create']);

	$maj['3.0.2'] = array(
		array('i3_installer_pays',array()),
	);

	include_spip('base/upgrade');
    maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de suppession du plugin
 *
 * Supprime la méta de configuration d'inscription3
 * Supprime la table des pays si nécessaire
 * 
 * @param string $nom_meta_base_version Le nom de la méta d'installation
 */
function inscription3_vider_tables($nom_meta_base_version) {
	effacer_meta('inscription3');
	if (!defined('_DIR_PLUGIN_GEOGRAPHIE'))
		sql_drop_table("spip_geo_pays");
	effacer_meta($nom_meta_base_version);
}


/**
 * Installe ou réinstalle la table des pays
 * Dans tous les cas cette fonction réinstallera le contenu de la table spip_geo_pays
 */
function i3_installer_pays() {
	if (!defined('_DIR_PLUGIN_GEOGRAPHIE')) {
		include_spip('inc/charsets');
		// 1) suppression de la table existante
		// pour redemarrer les insert a zero
		$descpays = sql_showtable('spip_geo_pays', '', false);
		if(isset($descpays['field'])){
			sql_drop_table("spip_geo_pays");
		}
		// 2) recreation de la table
		creer_base();
		if(($descpays = sql_showtable('spip_geo_pays', '', false)) && isset($descpays['field'])){
			// 3) installation des entrees
			// importer les pays
			include_spip('imports/pays');
			foreach($GLOBALS['liste_pays'] as $k=>$p)
				sql_insertq('spip_geo_pays',array('id_pays'=>$k,'code_iso' => $p['code_iso'],'nom'=>unicode2charset(html2unicode($p['nom']))));
		}
	}
	return true;
}

/**
 * Transfère les données de la tables auteurs_elargis vers spip_auteurs
 * @return unknown_type
 */
function inscription3_transfert_infos_auteurs(){
	include_spip('inc/config');
	$config = lire_config('inscription3',array());
	$exceptions_des_champs_auteurs_elargis = pipeline('i3_exceptions_des_champs_auteurs_elargis',array());

	/**
	 * On récupère un array $champs des champs qui doivent être dans la table
	 */
	$champs = array();
	$champs[] = 'id_auteur';
	if (is_array($config)){
		foreach($config as $clef=>$val){
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$champs)
				AND !in_array($cle,$exceptions_des_champs_auteurs_elargis) 
				AND !preg_match(",(categories|zone|newsletter).*$,", $cle) 
				AND ($val == 'on')
			)
				$champs[] = $cle;
		}
	}

	$desc_auteurs_elargis = sql_showtable('spip_auteurs_elargis', '', false);
	if(isset($desc_auteurs_elargis['field'])){
		$champs = array_intersect($desc_auteurs_elargis,$champs);
		$auteurs = sql_select($champs,'spip_auteurs_elargis');
		while($auteur = sql_fetch($auteurs)){
			$id_auteur = $auteur['id_auteur'];
			unset($auteur['id_auteur']);
			sql_updateq('spip_auteurs',$auteur,"id_auteur=$id_auteur");
		}
	}
	return;
}
?>