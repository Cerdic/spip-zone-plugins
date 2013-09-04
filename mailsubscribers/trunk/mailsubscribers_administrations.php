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
		array('mailsubscribers_import_from_spiplettres'),
		array('mailsubscribers_import_from_clevermail'),
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
			$set = array();
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
		mailsubscribers_finaliser_listes();
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
		mailsubscribers_finaliser_listes();
		sql_alter("TABLE spip_mesabonnes DROP imported");
	}
}


function mailsubscribers_import_from_spiplettres(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_abonnes')
	  AND $trouver_table('spip_desabonnes')
	  AND $trouver_table('spip_abonnes_rubriques')
	){

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rubs = sql_allfetsel("DISTINCT id_rubrique","spip_abonnes_rubriques","statut=".sql_quote('valide'));
		$rubs = array_map('reset',$rubs);
		$listes = array();
		$rows = sql_allfetsel("id_rubrique,titre","spip_rubriques",sql_in('id_rubrique',$rubs));
		foreach ($rows as $row){
			$listes[$row['id_rubrique']] = mailsubscribers_normaliser_nom_liste($row['id_rubrique']."-".strtolower($row['titre']));
		}


		include_spip("action/editer_objet");

		// les abonnes
		sql_alter("TABLE spip_abonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_abonne,email,nom','spip_abonnes',"imported=0");
		while ($row = sql_fetch($res)){
			$email = $row['email'];
			$set = array(
				'nom' => $row['nom'],
				'statut' => 'valide',
			);

			$ll = sql_allfetsel("id_rubrique","spip_abonnes_rubriques","id_abonne=".intval($row['id_abonne'])." AND statut=".sql_quote('valide'));
			if (count($ll)){
				$set['listes'] = array();
				while ($l = array_shift($ll))
					$set['listes'][] = $listes[$l['id_rubrique']];
				$set['listes'] = implode(',',$set['listes']);
			}
			else
				$set['statut'] = 'prepa'; // aucune liste ? pas un vrai abonne en fait !
			mailsubscriber_import_one($email,$set);
			sql_updateq("spip_abonnes",array('imported'=>1),"id_abonne=".intval($row['id_abonne']));
			spip_log("import from spip_lettres $email ".var_export($set,true),"mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// les desabonnes
		sql_alter("TABLE spip_desabonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_desabonne,email','spip_desabonnes',"imported=0");
		while ($row = sql_fetch($res)){
			$email = $row['email'];
			$set = array(
				'statut' => 'refuse',
			);
			mailsubscriber_import_one($email,$set);
			sql_updateq("spip_desabonnes",array('imported'=>1),"id_desabonne=".intval($row['id_desabonne']));
			spip_log("import from spip_lettres $email ".var_export($set,true),"mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		mailsubscribers_finaliser_listes();
		sql_alter("TABLE spip_abonnes DROP imported");
		sql_alter("TABLE spip_desabonnes DROP imported");
	}
}


function mailsubscribers_import_from_clevermail(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table('spip_cm_subscribers')
	  AND $trouver_table('spip_cm_lists_subscribers')
	  AND $trouver_table('spip_cm_lists')
	  ){

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rows = sql_allfetsel("lst_id,lst_name","spip_cm_lists");
		$listes = array();
		foreach ($rows as $row){
			$listes[$row['lst_id']] = mailsubscribers_normaliser_nom_liste($row['lst_id']."-".strtolower($row['lst_name']));
		}


		include_spip("action/editer_objet");
		sql_alter("TABLE spip_cm_subscribers ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('sub_id,sub_email AS email','spip_cm_subscribers',"imported=0");
		while ($row = sql_fetch($res)){
			$email = $row['email'];
			$set = array();
			$set['statut'] = 'valide';

			$ll = sql_allfetsel("lst_id","spip_cm_lists_subscribers","sub_id=".intval($row['sub_id']));
			if (count($ll)){
				$set['listes'] = array();
				while ($l = array_shift($ll))
					$set['listes'][] = $listes[$l['lst_id']];
				$set['listes'] = implode(',',$set['listes']);
			}
			else {
				// un abonnement suspendu est passe en md5(email)@example.com
				if (strpos($email,'@example.com')!==false){
					$set['statut'] = 'refuse';
					$email = str_replace("@example.com","@example.org",$email);
				}
				else
					$set['statut'] = 'prepa';
			}
			mailsubscriber_import_one($email,$set);
			sql_updateq("spip_cm_subscribers",array('imported'=>1),"sub_id=".intval($row['sub_id']));
			spip_log("import from clevermail $email ".var_export($set,true),"mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		mailsubscribers_finaliser_listes();
		sql_alter("TABLE spip_cm_subscribers DROP imported");
	}
}


function mailsubscriber_import_one($email,$set){
	if (!$email) return false;
	$GLOBALS['notification_instituermailsubscriber_status'] = false;
	if ($id = sql_getfetsel("id_mailsubscriber","spip_mailsubscribers","email=".sql_quote($email)." OR email=".sql_quote(mailsubscribers_obfusquer_email($email)))){
		$set['email'] = $email; // si mail obfusque
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


function mailsubscribers_finaliser_listes(){
	include_spip("inc/mailsubscribers");
	$listes = mailsubscribers_listes();
	$l = array();
	foreach ($listes as $k => $v){
		$l[] = array(
			'id' => $v['id'],
			'titre' => $v['titre'],
			'status' => in_array($v['status'],array('open','?'))?'open':'close',
		);
	}
	include_spip('inc/config');
	ecrire_config("mailsubscribers/lists",$l);
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

	effacer_meta('mailsubscribers');
	effacer_meta($nom_meta_base_version);
}

?>
