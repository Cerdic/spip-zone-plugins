<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function mailshot_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_mailshots','spip_mailshots_destinataires')),
		array('mailshot_import_from_spiplistes'),
	);

	$maj['0.1.4'] = array(
		array('maj_tables', array('spip_mailshot')),
	);
	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_mailshot_destinataires')),
	);
	$maj['0.2.1'] = array(
		array('sql_alter', 'TABLE spip_mailshot DROP next'),
	);
	$maj['0.3.0'] = array(
		array('sql_alter', 'TABLE spip_mailshot RENAME spip_mailshots'),
		array('sql_alter', 'TABLE spip_mailshot_destinataires RENAME spip_mailshots_destinataires'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function mailshot_import_from_spiplistes(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table('spip_courriers')){

		include_spip("action/editer_objet");
		sql_alter("TABLE spip_courriers ADD id_mailshot bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(
			"C.id_courrier,C.titre as sujet, C.texte as html, C.message_texte as texte,C.date_fin_envoi as date,C.date_debut_envoi as date_start,C.total_abonnes as total,C.nb_emails_envoyes+C.nb_emails_non_envoyes+C.nb_emails_echec as current,C.nb_emails_non_envoyes+C.nb_emails_echec as failed",
			'spip_courriers AS C',"C.id_mailshot=0 AND C.total_abonnes>0 AND C.type=".sql_quote('nl'));
		while ($row = sql_fetch($res)){

			$id_courrier = $row['id_courrier'];
			unset($row['id_courrier']);

			// nettoyer les vieux hacks de spip-listes
			$row['html'] = preg_replace(",__bLg__[0-9@\.A-Z_-]+__bLg__,","",$row['html']);

			$row['id'] = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));
			$row['statut'] = (($row['current']==$row['total'])?'end':'cancel');

			if ($id_mailshot = mailshot_import_one($row)){
				sql_updateq("spip_courriers",array('id_mailshot'=>$id_mailshot),"id_courrier=".intval($id_courrier));
				spip_log("import from spip_listes mailshot $id_mailshot ".var_export($row,true),"mailshot");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}
		sql_alter("TABLE spip_courriers DROP id_mailshot");
	}
}

function mailshot_import_one($set){
	$id = objet_inserer("mailshot",0,$set);
	objet_modifier("mailshot",$id,$set); // double detente
	return $id;
}


/**
 * Fonction de désinstallation du plugin.
**/
function mailshot_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailshots");
	sql_drop_table("spip_mailshots_destinataires");

	effacer_meta($nom_meta_base_version);
}

?>