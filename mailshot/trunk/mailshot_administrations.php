<?php
/***
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

/**
 * Fichier gérant l'installation et désinstallation du plugin
 *
 * @package SPIP\Mailshot\Installation
**/
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 *
 * Crée les tables SQL du plugin (spip_mailshots, spip_mailshots_destinataires)
 * Importe à l'installation les infoslettres des plugins SPIP Listes & SPIP Lettres
 * 
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
**/
function mailshot_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_mailshots','spip_mailshots_destinataires')),
		array('mailshot_import_from_spiplistes'),
		array('mailshot_import_from_spiplettres'),
		array('mailshot_import_from_clevermail'),
	);

	$maj['0.1.4'] = array(
		array('maj_tables', array('spip_mailshot')),
	);
	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_mailshots_destinataires')),
	);
	$maj['0.2.1'] = array(
		array('sql_alter', 'TABLE spip_mailshot DROP next'),
	);
	$maj['0.3.0'] = array(
		array('sql_alter', 'TABLE spip_mailshot RENAME spip_mailshots'),
		array('sql_alter', 'TABLE spip_mailshot_destinataires RENAME spip_mailshots_destinataires'),
	);
	$maj['0.3.2'] = array(
		array('sql_alter', 'TABLE spip_mailshots_destinataires ADD try tinyint NOT NULL DEFAULT 0'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Importe les lettres du plugin SPIP Listes
 *
 * @return void|null
**/
function mailshot_import_from_spiplistes(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($desc = $trouver_table('spip_courriers')){

		include_spip('inc/charsets');
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

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

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

/**
 * Importe les lettres & abonnés du plugin SPIP Lettres
 *
 * @return void|null
**/
function mailshot_import_from_spiplettres(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_lettres')
	  AND $trouver_table('spip_abonnes_lettres')){

		include_spip('inc/charsets');
		include_spip("action/editer_objet");

		// importer les envois
		sql_alter("TABLE spip_lettres ADD id_mailshot bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(
			"id_lettre,titre as sujet,message_html as html,message_texte as texte,date_fin_envoi as date,date_debut_envoi as date_start",
			'spip_lettres',"id_mailshot=0 AND statut=".sql_quote('envoyee'));
		while ($row = sql_fetch($res)){

			$id_lettre = $row['id_lettre'];
			unset($row['id_lettre']);

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

			$row['id'] = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));
			// compter les envois depuis spip_abonnes_lettres
			$row['total'] = sql_countsel("spip_abonnes_lettres","id_lettre=".intval($id_lettre));
			$row['failed'] = sql_countsel("spip_abonnes_lettres","id_lettre=".intval($id_lettre)." AND statut=".sql_quote('echec'));
			$row['current'] = $row['failed']+sql_countsel("spip_abonnes_lettres","id_lettre=".intval($id_lettre)." AND statut=".sql_quote('envoye'));

			$row['statut'] = (($row['current']==$row['total'])?'end':'cancel');

			if ($id_mailshot = mailshot_import_one($row)){
				sql_updateq("spip_lettres",array('id_mailshot'=>$id_mailshot),"id_lettre=".intval($id_lettre));
				spip_log("import from spip_lettres mailshot $id_mailshot ".var_export($row,true),"mailshot");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// importer le detail des destinataires
		$lettres2mailshot = array();
		sql_alter("TABLE spip_abonnes_lettres ADD imported tinyint(1) NOT NULL DEFAULT 0");
		do {

			$lot = sql_allfetsel("D.*,A.email","spip_abonnes_lettres AS D LEFT JOIN spip_abonnes as A ON D.id_abonne=A.id_abonne","D.imported=0",'','','0,50');
			if (count($lot)){
				$ins = array();
				foreach($lot as $l){

					if (!isset($lettres2mailshot[$l['id_lettre']]))
						$lettres2mailshot[$l['id_lettre']] = sql_getfetsel("id_mailshot","spip_lettres","id_lettre=".intval($l['id_lettre']));

					$statut = 'todo';
					if ($l['statut']=='echec') $statut = 'fail';
					if ($l['statut']=='envoye') $statut = 'sent';

					$email = $l['email'];
					if (!$email)
						$email = (rand(0,1)?'jane':'john').".doe.".$l['id_abonne'].'@example.org';

					$ins[] = array(
						'id_mailshot' => $lettres2mailshot[$l['id_lettre']],
						'email' => $email,
						'date' => date('Y-m-d H:i:s',strtotime($l['maj'])),
						'statut' => $statut,
					);
					sql_updateq("spip_abonnes_lettres",array('imported'=>1),'id_abonne='.intval($l['id_abonne']).' AND id_lettre='.intval($l['id_lettre']));
				}
				if (!sql_insertq_multi('spip_mailshots_destinataires',$ins)){
					foreach ($ins as $i){
						sql_insertq('spip_mailshots_destinataires',$i);
					}
				}
			}
			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;

		} while (count($lot));

		// remettre a jour les compteurs pour etre coherent
		$res = sql_select("id_lettre,id_mailshot",'spip_lettres',"id_mailshot>0");
		while ($row = sql_fetch($res)){

			$id_lettre = $row['id_lettre'];
			$id_mailshot = $row['id_mailshot'];

			$set = array();
			// compter les envois depuis spip_abonnes_lettres
			$set['total'] = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot));
			$set['failed'] = sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot)." AND statut=".sql_quote('fail'));
			$set['current'] = $set['failed']+sql_countsel("spip_mailshots_destinataires","id_mailshot=".intval($id_mailshot)." AND statut=".sql_quote('sent'));
			$set['statut'] = (($set['current']==$set['total'])?'end':'cancel');


			sql_updateq("spip_mailshots",$set,"id_mailshot=".intval($id_mailshot));
			sql_updateq("spip_lettres",array('id_mailshot'=>-$id_mailshot),"id_lettre=".intval($id_lettre));

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// et c'est tout !
		sql_alter("TABLE spip_lettres DROP id_mailshot");
		sql_alter("TABLE spip_abonnes_lettres DROP imported");
	}
}


/**
 * Importe les lettres du plugin Clevermail
 *
 * @return void|null
**/
function mailshot_import_from_clevermail(){
	$trouver_table = charger_fonction("trouver_table","base");
	if ($trouver_table('spip_cm_posts')
	  AND $trouver_table('spip_cm_posts_done')) {
		spip_log('Import des lettres clevermail', 'mailshot');

		include_spip("inc/charsets");
		include_spip("action/editer_objet");

		// Importer les lettres
		// ajout d'un champ le temps de l'import. Évite d'attraper 2 fois une même lettre et de reprendre sur timeout.
		sql_alter("TABLE spip_cm_posts ADD id_mailshot bigint(21) NOT NULL DEFAULT 0");
		$res = sql_select(array(
			"C.pst_id",
			"C.pst_subject AS sujet",
			"C.pst_html AS html",
			"C.pst_text AS texte",
			"C.pst_date_create AS date",
			"C.pst_date_sent AS date_start",
			),
			'spip_cm_posts AS C',
			"C.id_mailshot=0");
		while ($row = sql_fetch($res)){

			$pst_id = $row['pst_id'];
			unset($row['pst_id']);

			// Tant qu'à faire, remplacer des mauvais restes
			$row['html']  = str_replace('@@NOM_LETTRE@@', $row['sujet'], $row['html']);
			$row['texte'] = str_replace('@@NOM_LETTRE@@', $row['sujet'], $row['texte']);

			if ($GLOBALS['meta']['charset']=='utf-8'){
				if (!is_utf8($row['sujet'])) $row['sujet'] = importer_charset($row['sujet'] ,'iso-8859-15');
				if (!is_utf8($row['html']))  $row['html']  = importer_charset($row['html']  ,'iso-8859-15');
				if (!is_utf8($row['texte'])) $row['texte'] = importer_charset($row['texte'] ,'iso-8859-15');
			}

			$row['id'] = md5(serialize(array('sujet'=>&$row['sujet'],'html'=>&$row['html'],'texte'=>&$row['texte'])));

			// compter les envois depuis spip_abonnes_lettres
			$row['total'] = sql_countsel("spip_cm_posts_done","pst_id=".intval($pst_id));
			$row['failed'] = 0; # pas l'impression qu'on puisse savoir ça dans Clevermail
			$row['current'] = $row['total'];

			$row['statut'] = (($row['current']==$row['total'])?'end':'cancel');

			// corriger les dates actuellement en time
			$row['date']       = date('Y-m-d H:i:s', $row['date']);
			$row['date_start'] = date('Y-m-d H:i:s', $row['date_start']);

			// inserer la lettre dans mailshot
			if ($id_mailshot = mailshot_import_one($row)){
				sql_updateq("spip_cm_posts", array('id_mailshot'=>$id_mailshot), "pst_id=".intval($pst_id));
				spip_log("import from spip_cm_posts mailshot $id_mailshot ".var_export($row,true),"mailshot");
			}

			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;
		}

		// Importer le détail des destinataires
		$cm_posts2mailshot = array();
		sql_alter("TABLE spip_cm_posts_done ADD imported tinyint(1) NOT NULL DEFAULT 0");
		do {

			$lot = sql_allfetsel(
				array("D.*", "A.sub_email AS email"),
				"spip_cm_posts_done AS D LEFT JOIN spip_cm_subscribers as A ON D.sub_id=A.sub_id",
				"D.imported=0",'','','0,50');

			if (count($lot)){
				$ins = array();
				foreach($lot as $l){

					if (!isset($cm_posts2mailshot[$l['pst_id']]))
						$cm_posts2mailshot[$l['pst_id']] = sql_fetsel(
							"id_mailshot, pst_date_sent",
							"spip_cm_posts",
							"pst_id=".intval($l['pst_id']));

					$statut = 'sent';

					$email = $l['email'];
					if (!$email)
						$email = (rand(0,1)?'jane':'john').".doe.".$l['id_abonne'].'@example.org';

					$ins[] = array(
						'id_mailshot' => $cm_posts2mailshot[$l['pst_id']]['id_mailshot'],
						'email' => $email,
						'date' => date('Y-m-d H:i:s', $cm_posts2mailshot[$l['pst_id']]['pst_date_sent']),
						'statut' => $statut,
					);
					sql_updateq("spip_cm_posts_done",
						array('imported'=>1),
						'sub_id='.intval($l['sub_id']).' AND pst_id='.intval($l['pst_id']));
				}
				if (!sql_insertq_multi('spip_mailshots_destinataires',$ins)){
					foreach ($ins as $i){
						sql_insertq('spip_mailshots_destinataires',$i);
					}
				}
			}
			// timeout ? on reviendra
			if (time() >= _TIME_OUT)
				return;

		} while (count($lot));


		// C'est fini !
		sql_alter("TABLE spip_cm_posts DROP id_mailshot");
		sql_alter("TABLE spip_cm_posts_done DROP imported");
	}

}



/**
 * Insère un nouvel envoi en base
 *
 * @param array $set
 *     Couples de données à enregistrer
 * @return int
 *     Identifiant de l'envoi
**/
function mailshot_import_one($set){
	$id = objet_inserer("mailshot",0,$set);
	objet_modifier("mailshot",$id,$set); // double detente
	return $id;
}


/**
 * Désinstallation du plugin
 *
 * Supprime les tables SQL du plugin (spip_mailshots, spip_mailshots_destinataires)
 * 
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
**/
function mailshot_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailshots");
	sql_drop_table("spip_mailshots_destinataires");

	effacer_meta($nom_meta_base_version);
}

?>
