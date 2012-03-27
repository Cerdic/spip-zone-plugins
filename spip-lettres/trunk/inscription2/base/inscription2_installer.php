<?php
/**
 * Plugin Inscription2 pour SPIP
 * Licence GPL v3
 *
 */

include_spip('inc/meta');
include_spip('cfg_options');

/**
 * Fonction d'installation et de mise à jour du plugin
 * @return
 */
function inscription2_upgrade($nom_meta_base_version,$version_cible){
	spip_log('INSCRIPTION 2 : installation','inscription2');
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
	$verifier_tables = charger_fonction('inscription2_verifier_tables','inc');
	
	//On force le fait d accepter les visiteurs
	$accepter_visiteurs = $GLOBALS['meta']['accepter_visiteurs'];
	if($accepter_visiteurs != 'oui'){
		ecrire_meta("accepter_visiteurs", "oui");
	}

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	//insertion des infos par defaut
	$inscription2_meta = $GLOBALS['meta']['inscription2'];

	//Certaines montées de version ont oublié de corriger la meta de I2
	//si ce n'est pas un array alors il faut reconfigurer la meta
	if (isset($inscription2_meta) && !is_array(unserialize($inscription2_meta))) {
		spip_log("INSCRIPTION 2 : effacer la meta inscription2 et relancer l'install","inscription2");
		echo "La configuration du plugin Inscription 2 a &eacute;t&eacute; effac&eacute;e.<br />";
		effacer_meta('inscription2');
		$current_version = 0.0;
	}

	//Si c est une nouvelle installation toute fraiche
	if ($current_version==0.0){
		spip_log('INSCRIPTION2 : installation neuve');
		if(!$inscription2_meta){
			$i2_configuration_initiale = charger_fonction('i2_configuration_initiale','inc');
			$i2_configuration_initiale();
		}
		
		// Creation de la table et des champs
		$verifier_tables();
	
		//inserer les auteurs qui existent deja dans la table spip_auteurs en non pas dans la table elargis
		$s = sql_select("a.id_auteur","spip_auteurs a left join spip_auteurs_elargis b on a.id_auteur=b.id_auteur","b.id_auteur is null");
		while($q = sql_fetch($s)){
			sql_insertq("spip_auteurs_elargis",array('id_auteur' => $q['id_auteur']));
		}

		/** Inscription 2 (0.70)
		 * Les pays sont maintenant pris dans le plugin Geographie
		 * On ne les installe si le plugin n'est pas actif,
		 * pour ne pas en etre dependant.
		 */
		i2_installer_pays();

		echo "Inscription2 installe @ ".$version_cible;
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}
	if ($current_version<0.63){
		// Suppression du champs id et on remet la primary key sur id_auteur...
		sql_alter("TABLE spip_auteurs_elargis DROP id");
		sql_alter("TABLE spip_auteurs_elargis DROP INDEX id_auteur");
		sql_alter("TABLE spip_auteurs_elargis ADD PRIMARY KEY (id_auteur)");
		echo "Inscription2 update @ 0.63 : On supprime le champs id pour privilegier id_auteur<br />";
		ecrire_meta($nom_meta_base_version,$current_version=0.63);
	}

	if ($current_version<0.71){	
		/*
		 * Reinstaller les pays de Geographie
		 * pour ne pas etre dependant de ce plugin
		 */
		i2_installer_pays();
		$verifier_tables();
		echo "Inscription2 update @ 0.71 : installation de la table pays de geographie<br />";
		ecrire_meta($nom_meta_base_version,$current_version=0.71);
	}
	if ($current_version<0.72){
		i2_installer_pays();
		$verifier_tables();
		echo "Inscription2 update @ 0.72 : Modification de la table spip_pays (Neerlandais)<br />";
		ecrire_meta($nom_meta_base_version,$current_version=0.72);
	}
	if ($current_version<0.73){	
		i2_installer_pays();
		$verifier_tables();
		echo "Inscription2 update @ 0.73 : Une erreur de la table spip_pays (Neerlandais)<br />";
		ecrire_meta($nom_meta_base_version,$current_version=0.73);
	}
}


/**
 * Fonction de suppession du plugin
 *
 * Supprime les donnees de la table spip_auteurs_elargis
 * Supprime la table si plus nécessaire
 * Supprime la table des pays si nécessaire
 */
function inscription2_vider_tables($nom_meta_base_version) {
	$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());

	//supprime la table spip_auteurs_elargis
	if (is_array(lire_config('inscription2'))){
		$clef_passee = array();
		$desc = sql_showtable('spip_auteurs_elargis','', '', true);
		foreach(lire_config('inscription2',array()) as $cle => $val){
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $clef);
			if(!in_array($cle,$clef_passee)){
				if(isset($desc['field'][$cle]) and !in_array($cle,$exceptions_des_champs_auteurs_elargis)){
					spip_log("INSCRIPTION 2 : suppression de $cle","inscription2");
					$a = sql_alter('TABLE spip_auteurs_elargis DROP COLUMN '.$cle);
					$desc['field'][$cle]='';
				}
				$clef_passee[] = $cle;
			}
		}
	}
	if (!lire_config('plugin/SPIPLISTES')){
		sql_drop_table('spip_auteurs_elargis');
		spip_log("INSCRIPTION 2 : suppression de la table spip_auteurs_elargis");
	}
	if(!lire_config('spip_geo_base_version')
	and !defined('_DIR_PLUGIN_GEOGRAPHIE')){
		sql_drop_table('spip_geo_pays');
		spip_log("INSCRIPTION 2 : suppression de la table spip_geo");
	}
	effacer_meta('inscription2');
	effacer_meta($nom_meta_base_version);
}


// reinstaller la table de pays
function i2_installer_pays() {
	spip_log('INSCRIPTION2 : i2_installer_pays');
	if (!defined('_DIR_PLUGIN_GEOGRAPHIE')) {
		// 1) suppression de la table existante
		// pour redemarrer les insert a zero
		$descpays = sql_showtable('spip_geo_pays', '', false);
		if(isset($descpays['field'])){
			spip_log('suppression de la table spip_geo_pays');
			sql_drop_table("spip_geo_pays");
		}
		// 2) recreation de la table
		include_spip('base/create');
		creer_base();
		// 3) installation des entrees
		// importer les pays
		include_spip('imports/pays');
		include_spip('inc/charset');
		spip_log('Insertion des pays dans spip_geo_pays');
		foreach($GLOBALS['liste_pays'] as $k=>$p)
			sql_insertq('spip_geo_pays',array('id_pays'=>$k,'nom'=>unicode2charset(html2unicode($p))));
	}
}

?>