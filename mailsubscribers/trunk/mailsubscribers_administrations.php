<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation du plugin et de mise à jour.
 **/
function mailsubscribers_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip("inc/mailsubscribers");

	$maj['create'] = array(
		array('maj_tables', array('spip_mailsubscribers','spip_mailsubscribinglists', 'spip_mailsubscriptions')),
		array('mailsubscribers_import_from_spiplistes'),
		array('mailsubscribers_import_from_mesabonnes'),
		array('mailsubscribers_import_from_spiplettres'),
		array('mailsubscribers_import_from_clevermail'),
	);

	$maj['0.3.0'] = array(
		array('sql_alter', "TABLE spip_mailsubscribers ADD invite_email_from text NOT NULL DEFAULT '' "),
		array('sql_alter', "TABLE spip_mailsubscribers ADD invite_email_text text NOT NULL DEFAULT '' "),
	);

	$maj['1.0.0'] = array(
		array('maj_tables', array('spip_mailsubscribinglists', 'spip_mailsubscriptions')),
		array('mailsubscribers_migrate_mailsubscribinglists'),
		array('sql_alter','TABLE spip_mailsubscribers DROP imported'),
		array('maj_tables', array('spip_mailsubscribers')),
		array('sql_alter','TABLE spip_mailsubscribers DROP listes'),
	);
	$maj['1.1.0'] = array(
		array('maj_tables', array('spip_mailsubscribinglists', 'spip_mailsubscriptions')),
		array('sql_alter','TABLE spip_mailsubscriptions DROP PRIMARY KEY'),
		array('sql_alter','TABLE spip_mailsubscriptions ADD PRIMARY KEY (id_mailsubscriber,id_mailsubscribinglist,id_segment)'),
	);
	$maj['1.1.1'] = array(
		array('maj_tables', array('spip_mailsubscribinglists')),
	);
	$maj['1.1.2'] = array(
		array('maj_tables', array('spip_mailsubscribinglists')),
	);
	$maj['1.1.3'] = array(
		array('sql_alter','TABLE spip_mailsubscribinglists DROP anonyme'),
		array('maj_tables', array('spip_mailsubscribinglists')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Migrer les listes et les subscription vers les tables spip_mailsubscribinglists/spip_mailsubscriptions
 */
function mailsubscribers_migrate_mailsubscribinglists() {
	include_spip("inc/mailsubscribers");
	$listes = mailsubscribers_old_listes_from_config();
	$today = date('Y-m-d H:i:s');

	$remap = array();
	foreach ($listes as $identifiant => $liste) {
		if (!$row = sql_fetsel('*', 'spip_mailsubscribinglists', 'identifiant=' . sql_quote($identifiant))) {
			$id_mailsubscribinglist = sql_insertq('spip_mailsubscribinglists', array('identifiant' => $identifiant));
		} else {
			$id_mailsubscribinglist = $row['id_mailsubscribinglist'];
		}
		if ($id_mailsubscribinglist) {
			$set = array(
				'titre' => $liste['titre'],
				'statut' => (($liste['status'] == 'open') ? 'ouverte' : 'fermee'),
				'date' => $today,
			);
			$remap["newsletter::$identifiant"] = $id_mailsubscribinglist;
			if (!$row or $row['statut'] !== $set['statut'] or $row['titre'] !== $set['titre']) {
				sql_updateq('spip_mailsubscribinglists', $set, 'id_mailsubscribinglist=' . intval($id_mailsubscribinglist));
			}
		}
	}

	// $remap nous donne la correspondance newsletter::xx => id_mailsubscribinglist
	// on bascule tous les id_mailsubscriber qui ne sont pas deja dans spip_mailsubscriptions
	sql_alter("TABLE spip_mailsubscribers ADD imported tinyint NOT NULL DEFAULT 0");
	$where = 'listes like ' . sql_quote('%newsletter::%') . ' AND statut IN (\'prop\',\'valide\',\'refuse\')' . ' AND imported=0';
	$n = sql_countsel('spip_mailsubscribers', $where);
	spip_log("mailsubscribers_migrate_mailsubscribinglists: $n restant",'maj');
	do {
		$all = sql_allfetsel('*', 'spip_mailsubscribers', $where, '', 'id_mailsubscriber', '0,100');
		foreach ($all as $a) {
			$ins = array();
			$listes = explode(',', $a['listes']);
			$listes = array_map('trim', $listes);
			$listes = array_unique($listes);
			foreach ($listes as $l) {
				if (isset($remap[$l])) {
					$ins[] = array(
						'id_mailsubscriber' => $a['id_mailsubscriber'],
						'id_mailsubscribinglist' => $remap[$l],
						'statut' => $a['statut'],
					);
				}
			}
			sql_insertq_multi('spip_mailsubscriptions', $ins);
			sql_updateq("spip_mailsubscribers", array('imported' => 1), "id_mailsubscriber=" . intval($a['id_mailsubscriber']));

			if (time() >= _TIME_OUT) {
				return;
			}
		}
	} while (count($all));

}

/**
 * Renvoi les listes de diffusion disponibles avec leur status
 * (open,close,?) stockees en configuration (ancienne structure de donnees)
 *
 * @return array
 *   array
 *     id : identifiant
 *     titre : titre de la liste
 *     status : status de la liste
 */
function mailsubscribers_old_listes_from_config() {
	$filtrer_status = $filtrer_category = false;

	$listes = array();
	// d'abord les listes connues en config
	if (!function_exists('lire_config')) {
		include_spip('inc/config');
	}
	if ($known_lists = lire_config("mailsubscribers/lists", array())
		AND is_array($known_lists)
		AND count($known_lists)
	) {

		foreach ($known_lists as $kl) {
			$id = $kl['id'];
			if ($id = mailsubscribers_filtre_liste($id)) {
				$status = ($kl['status'] == 'open' ? 'open' : 'close');
				$listes[$id] = array(
					'id' => $id,
					'titre' => $kl['titre'],
					'status' => $status
				);
			}
		}
	}

	// puis trouver toutes les listes qui existent en base et non connues en config
	$rows = sql_allfetsel("DISTINCT listes", "spip_mailsubscribers", "statut!=" . sql_quote('poubelle'));
	foreach ($rows as $row) {
		$ll = explode(",", $row['listes']);
		foreach ($ll as $l) {
			if ($id = $l and $id = mailsubscribers_filtre_liste($l)) {
				if (!isset($listes[$id])) {
					$listes[$id] = array('id' => $id, 'titre' => $id, 'status' => 'close');
				}
			}
		}
	}

	return $listes;
}

/**
 * Importer les donnees depuis SPIP Listes
 */
function mailsubscribers_import_from_spiplistes() {
	$trouver_table = charger_fonction("trouver_table", "base");
	if ($desc = $trouver_table('spip_auteurs_elargis')
		AND isset($desc['field']['spip_listes_format'])
		AND $trouver_table('spip_listes')
	) {

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rows = sql_allfetsel("id_liste as id,titre,descriptif,date", "spip_listes");
		$listes = mailsubscribers_importer_listes($rows);

		include_spip("action/editer_objet");
		sql_alter("TABLE spip_auteurs_elargis ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('A.id_auteur,A.email,A.nom,E.spip_listes_format',
			'spip_auteurs as A JOIN spip_auteurs_elargis AS E ON E.id_auteur=A.id_auteur', "imported=0");
		while ($row = sql_fetch($res)) {
			$email = $row['email'];
			$set = array();
			$set['statut'] = ($row['spip_listes_format'] == "non" ? 'refuse' : 'prop');
			$set['nom'] = $row['nom'];

			$ll = sql_allfetsel("id_liste,statut", "spip_auteurs_listes", "id_auteur=" . intval($row['id_auteur']));
			if (count($ll)) {
				$set['subscriptions'] = array();
				while ($l = array_shift($ll)) {
					$set['subscriptions'][] = array(
						'id_mailsubscribinglist'=>$listes[$l['id_liste']],
						'statut' => str_replace(array('a_valider','valide'), array('prop','valide'), $l['statut']),
					);
				}
			}
			mailsubscriber_import_one($email, $set);
			sql_updateq("spip_auteurs_elargis", array('imported' => 1), "id_auteur=" . intval($row['id_auteur']));
			spip_log("import from spip_listes $email " . var_export($set, true), "mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT) {
				return;
			}
		}

		sql_alter("TABLE spip_auteurs_elargis DROP imported");
	}
}

/**
 * Importer les donnees depuis MesAbonnes
 */
function mailsubscribers_import_from_mesabonnes() {
	$trouver_table = charger_fonction("trouver_table", "base");
	if ($trouver_table('spip_mesabonnes')) {

		include_spip("inc/mailsubscribers");

		$rows = array(
			array(
				'id' => 1,
				'titre' => 'Mes abonnes',
				'descriptif' => 'Import depuis Mes abonnes',
			)
		);
		$listes = mailsubscribers_importer_listes($rows);


		include_spip("action/editer_objet");
		sql_alter("TABLE spip_mesabonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_abonne,email,nom,date_modif as date,statut', 'spip_mesabonnes', "imported=0");
		while ($row = sql_fetch($res)) {
			$email = $row['email'];

			$set = array(
				'nom' => $row['nom'],
				'date' => $row['date'],
				'statut' => $row['statut'],
			);
			if ($set['statut'] == '0') {
				$set['statut'] = 'prepa';
			}  // precaution
			if ($set['statut'] == 'publie') {
				$set['statut'] = 'valide';
			}
			$set['subscriptions'] = array(
				array(
					'id_mailsubscribinglist' => reset($listes),
					'statut' => 'valide',
				)
			);
			mailsubscriber_import_one($email, $set);

			sql_updateq("spip_mesabonnes", array('imported' => 1), "id_abonne=" . intval($row['id_abonne']));
			spip_log("import from mesabonnes $email " . var_export($set, true), "mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT) {
				return;
			}
		}
		sql_alter("TABLE spip_mesabonnes DROP imported");
	}
}


/**
 * Importer les donnees depuis SPIP Lettres
 */
function mailsubscribers_import_from_spiplettres() {
	$trouver_table = charger_fonction("trouver_table", "base");
	if ($trouver_table('spip_abonnes')
		AND $trouver_table('spip_desabonnes')
		AND $trouver_table('spip_abonnes_rubriques')
	) {

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rubs = sql_allfetsel("DISTINCT id_rubrique", "spip_abonnes_rubriques", "statut=" . sql_quote('valide'));
		$rubs = array_map('reset', $rubs);
		$listes = array();
		$rows = sql_allfetsel("id_rubrique as id,titre,descriptif", "spip_rubriques", sql_in('id_rubrique', $rubs));
		$listes = mailsubscribers_importer_listes($rows);


		include_spip("action/editer_objet");

		// les abonnes
		sql_alter("TABLE spip_abonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_abonne,email,nom', 'spip_abonnes', "imported=0");
		while ($row = sql_fetch($res)) {
			$email = $row['email'];
			$set = array(
				'nom' => $row['nom'],
				'statut' => 'prop',
			);

			$ll = sql_allfetsel("id_rubrique,statut", "spip_abonnes_rubriques", "id_abonne=" . intval($row['id_abonne']));
			if (count($ll)) {
				$set['subscriptions'] = array();
				while ($l = array_shift($ll)) {
					$set['subscriptions'][] = array(
						'id_mailsubscribinglist' => $listes[$l['id_rubrique']],
						'statut' => str_replace(array('a_valider','valide'), array('prop','valide'), $l['statut']),
					);
				}
			}

			mailsubscriber_import_one($email, $set);
			sql_updateq("spip_abonnes", array('imported' => 1), "id_abonne=" . intval($row['id_abonne']));
			spip_log("import from spip_lettres $email " . var_export($set, true), "mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT) {
				return;
			}
		}

		// les desabonnes
		sql_alter("TABLE spip_desabonnes ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('id_desabonne,email', 'spip_desabonnes', "imported=0");
		while ($row = sql_fetch($res)) {
			$email = $row['email'];
			$set = array(
				'statut' => 'refuse',
			);
			mailsubscriber_import_one($email, $set);
			sql_updateq("spip_desabonnes", array('imported' => 1), "id_desabonne=" . intval($row['id_desabonne']));
			spip_log("import from spip_lettres $email " . var_export($set, true), "mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT) {
				return;
			}
		}

		sql_alter("TABLE spip_abonnes DROP imported");
		sql_alter("TABLE spip_desabonnes DROP imported");
	}
}

/**
 * Importer les donnees depuis CleverMail
 */
function mailsubscribers_import_from_clevermail() {
	$trouver_table = charger_fonction("trouver_table", "base");
	if ($desc = $trouver_table('spip_cm_subscribers')
		AND $trouver_table('spip_cm_lists_subscribers')
		AND $trouver_table('spip_cm_lists')
	) {

		include_spip("inc/mailsubscribers");

		// reperer les listes
		$rows = sql_allfetsel("lst_id as id,lst_name as titre,lst_comment as descriptif", "spip_cm_lists");
		$listes = mailsubscribers_importer_listes($rows);


		include_spip("action/editer_objet");
		sql_alter("TABLE spip_cm_subscribers ADD imported tinyint NOT NULL DEFAULT 0");
		$res = sql_select('sub_id,sub_email AS email', 'spip_cm_subscribers', "imported=0");
		while ($row = sql_fetch($res)) {
			$email = $row['email'];
			$set = array();
			$set['statut'] = 'prop';

			$set['subscriptions'] = array();
			$ll = sql_allfetsel("lst_id", "spip_cm_lists_subscribers", "sub_id=" . intval($row['sub_id']));
			if (count($ll)) {
				while ($l = array_shift($ll)) {
					$set['subscriptions'][] = array(
						'id_mailsubscribinglist' => $listes[$l['lst_id']],
						'statut' => 'valide',
					);
				}
			}
			$ll = sql_allfetsel("lst_id", "spip_cm_pending", "sub_id=" . intval($row['sub_id']));
			if (count($ll)) {
				while ($l = array_shift($ll)) {
					$set['subscriptions'][] = array(
						'id_mailsubscribinglist' => $listes[$l['lst_id']],
						'statut' => 'prop',
					);
				}
			}

			mailsubscriber_import_one($email, $set);
			sql_updateq("spip_cm_subscribers", array('imported' => 1), "sub_id=" . intval($row['sub_id']));
			spip_log("import from clevermail $email " . var_export($set, true), "mailsubscribers");

			// timeout ? on reviendra
			if (time() >= _TIME_OUT) {
				return;
			}
		}

		sql_alter("TABLE spip_cm_subscribers DROP imported");
	}
}

/**
 * Importer un email
 *
 * @param $email
 * @param $set
 * @return bool|int|mixed
 */
function mailsubscriber_import_one($email, $set) {
	if (!$email) {
		return false;
	}
	$subs = array();
	if (isset($set['subscriptions'])){
		$subs = $set['subscriptions'];
		unset($set['subscriptions']);
	}
	$statut = $set['statut'];
	unset($set['statut']);

	if ($id = sql_getfetsel("id_mailsubscriber", "spip_mailsubscribers",
		"email=" . sql_quote($email) . " OR email=" . sql_quote(mailsubscribers_obfusquer_email($email)))
	) {
		$set['email'] = $email; // si mail obfusque
		objet_modifier("mailsubscriber", $id, $set);

	} else {
		$set['email'] = $email;
		$id = objet_inserer("mailsubscriber", 0, $set);
		objet_modifier("mailsubscriber", $id, $set); // double detente
	}

	if ($id){
		$statut = 'refuse'; // par defaut si aucune subscription
		foreach($subs as $sub){
			$sub['id_mailsubscriber'] = $id;
			sql_insertq('spip_mailsubscriptions',$sub);
			if ($sub['statut']=='prop' and $statut!=='valide'){
				$statut = 'prop';
			}
			if ($sub['statut']=='valide'){
				$statut = 'valide';
			}
		}
		sql_updateq('spip_mailsubscribers', array('statut'=>$statut), 'id_mailsubscriber='.intval($id) );
	}
	return $id;
}

/**
 * Importer les listes
 * @param array $listes
 * @return array
 */
function mailsubscribers_importer_listes($listes) {
	$correspondances = array();
	foreach ($listes as $liste) {
		$statut = 'fermee';
		if (isset($liste['statut'])){
			$statut = $liste['statut'];
		}
		$identifiant = mailsubscribers_normaliser_nom_liste($liste['id'].'-'.$liste['titre']);
		if (!$id = sql_getfetsel('id_mailsubscribinglist','spip_mailsubscribinglists','identifiant='.sql_quote($identifiant))){
			$ins = array(
				'titre' => $liste['titre'],
				'descriptif' => isset($liste['descriptif'])?$liste['descriptif']:'',
				'identifiant' => $identifiant,
				'statut' => $statut,
				'date' => isset($liste['date'])?$liste['date']:date('Y-m-d H:i:s'),
			);
			$id = sql_insertq('spip_mailsubscribinglists',$ins);
		}
		$correspondances[$liste['id']] = $id;
	}
	return $correspondances;
}


/**
 * Fonction de désinstallation du plugin.
 **/
function mailsubscribers_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_mailsubscribers");
	sql_drop_table("spip_mailsubscribinglists");
	sql_drop_table("spip_mailsubscriptions");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('mailsubscriber','mailsubscribinglist')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('mailsubscriber','mailsubscribinglist')));
	sql_delete("spip_forum", sql_in("objet", array('mailsubscriber','mailsubscribinglist')));

	effacer_meta('mailsubscribers');
	effacer_meta($nom_meta_base_version);
}
