<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function mailsubscribers_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip("inc/mailsubscribers");

	$maj['create'] = array(
		array('maj_tables', array('spip_mailsubscribers')),
		array('mailsubscribers_import_from_spiplistes'),
		array('mailsubscribers_import_from_mesabonnes'),
	);


	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function mailsubscribers_import_from_spiplistes(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table('spip_auteurs_elargis')
		AND isset($desc['field']['spip_listes_format'])
	  AND $trouver_table('spip_listes')){

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rows = sql_allfetsel("id_liste,titre","spip_listes");
		$listes = array();
		foreach ($rows as $row){
			$listes[$row['id_liste']] = mailsubscribers_normaliser_nom_liste($row['id_liste']."-".strtolower($row['titre']));
		}


		include_spip("action/editer_objet");
		sql_alter("TABLE spip_auteurs_elargis ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('A.id_auteur,A.email,A.nom,E.spip_listes_format','spip_auteurs as A JOIN spip_auteurs_elargis AS E ON E.id_auteur=A.id_auteur',"imported=0");
		while ($row = sql_fetch($res)){
			$email = $row['email'];
			$set['statut'] = ($row['spip_listes_format']=="non"?'refuse':'valide');
			$set['nom'] = $row['nom'];

			$ll = sql_allfetsel("id_liste","spip_auteurs_listes","id_auteur=".intval($row['id_auteur']));
			if (count($ll)){
				$set['listes'] = array();
				while ($l = array_shift($ll))
					$set['listes'][] = $listes[$l['id_liste']];
				$set['listes'] = implode(',',$set['listes']);
			}
			mailsubscriber_import_one($email,$set);
			sql_updateq("spip_auteurs_elargis",array('imported'=>1),"id_auteur=".intval($row['id_auteur']));
			spip_log("import from spip_listes $email ".var_export($set,true),"mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter("TABLE spip_auteurs_elargis DROP imported");
	}
}


function mailsubscribers_import_from_mesabonnes(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_mesabonnes')){

		include_spip("inc/mailsubscribers");


		include_spip("action/editer_objet");
		sql_alter("TABLE spip_mesabonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_abonne,email,nom,date_modif as date,statut','spip_mesabonnes',"imported=0");
		while ($row = sql_fetch($res)){
			$email = $row['email'];

			$set = array(
				'nom' => $row['nom'],
				'date' => $row['date'],
				'statut' => $row['statut'],
			);
			if ($set['statut']=='0') $set['statut'] = 'prepa';  // precaution
			if ($set['statut']=='publie') $set['statut'] = 'valide';
			mailsubscriber_import_one($email,$set);

			sql_updateq("spip_mesabonnes",array('imported'=>1),"id_abonne=".intval($row['id_abonne']));
			spip_log("import from mesabonnes $email ".var_export($set,true),"mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter("TABLE spip_mesabonnes DROP imported");
	}
}


function mailsubscriber_import_one($email,$set){
	$GLOBALS['instituermailsubscriber_status'] = false;
	if ($id = sql_getfetsel("id_mailsubscriber","spip_mailsubscribers","email=".sql_quote($email))){
		objet_modifier("mailsubscriber",$id,$set);
		return $id;
	}
	else {
		$set['email'] = $email;
		$id = objet_inserer("mailsubscriber",0,$set);
		objet_modifier("mailsubscriber",$id,$set); // double detente
		return $id;
	}
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailsubscribers_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailsubscribers");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('mailsubscriber')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('mailsubscriber')));
	sql_delete("spip_forum",                 sql_in("objet", array('mailsubscriber')));

	effacer_meta($nom_meta_base_version);
}

?>