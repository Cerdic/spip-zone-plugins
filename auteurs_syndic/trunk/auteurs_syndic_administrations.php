<?php
/**
 * Plugin auteurs_syndic
 * Ajouter des auteurs aux sites syndiqués
 * 
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2010/2012 - Distribue sous licence GNU/GPL
 * 
 * Installation / Mise à jour et désinstallation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour
 * 
 * @param string $nom_meta_base_version
 * @param float $version_cible
 */
function auteurs_syndic_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	$maj['create'] = array();
	
	$maj['0.2.0'] = array(
		array('auteur_syndic_update_3')
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * 
 * Fonction de désinstallation
 * @param string $nom_meta_base_version
 */
function auteurs_syndic_vider_tables($nom_meta_base_version){
	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);
}

/**
 * Fonction de mise à jour par rapport à l'ancienne table de liens
 */
function auteur_syndic_update_3(){
	$desc = sql_showtable('spip_auteurs_syndic', true, $connect);
	if (is_array($desc['field'])) {
		$liens_auteur = sql_select('*','spip_auteurs_syndic');
		while($lien = sql_fetch($liens_auteur)){
			sql_insertq('spip_auteurs_liens',array('id_auteur'=>$lien['id_auteur'],'objet'=>'site','id_objet'=>$lien['id_syndic']));
		}
	}
}
?>