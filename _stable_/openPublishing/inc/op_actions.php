<?php

/* API plugin open-publishing
	set_config_rubrique($i) : ajoute une rubrique dans la liste des rubriques op
	get_auteur_anonymous() : retourne l'id de l'auteur anonymous
	get_rubrique_op() : renvoi un tableau des rubriques op
	op_get_version() : renvoi la version actuelle du plugin
	op_get_agenda() : renvoi le flag agenda
	op_get_rubrique_agenda() : renvoi le num rubrique de l'agenda
	op_set_agenda($i) : maj du flag agenda
	op_set_rubrique_agenda($i) : maj de la rubrique agenda
*/

	function set_config_rubrique($ajout_rubrique) {
		// faire vérification pour voir si la rubrique n'existe pas déjà ...
		// faire vérification pour voir si la rubrique existe dans spip_article ... sinon pas glop
		spip_abstract_insert('spip_op_rubriques', "(id_rubrique,op_rubrique)", "(
		" . intval($id_rubrique) .",
		" . $ajout_rubrique . "
		)");
		
		$retour = "ok";
		return $retour;
	}

	function get_id_anonymous() {
		$result = spip_query("SELECT `id_auteur` FROM `spip_auteurs` WHERE `id_auteur` = 999");
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function op_get_version() {
		$result = spip_query("SELECT `version` FROM `spip_op_config` WHERE `id_config` = 1");
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function op_get_agenda() {
		$result = spip_query("SELECT `agenda` FROM `spip_op_config` WHERE `id_config` = 1");
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function op_get_rubrique_agenda() {
		$result = spip_query("SELECT `rubrique_agenda` FROM `spip_op_config` WHERE `id_config` = 1");
		$row = mysql_fetch_row($result);
		return $row[0];
	}

	function get_rubriques_op() {
		$result = spip_query("SELECT `op_rubrique` FROM `spip_op_rubriques`");
		return $result;
	}

	function op_set_agenda($flag_agenda) {
		$retour = spip_query('UPDATE spip_op_config SET agenda = '.spip_abstract_quote($flag_agenda).' WHERE id_config = 1');
		return $retour;
	}

	function op_set_rubrique_agenda($rub_agenda) {
		$retour = spip_query('UPDATE spip_op_config SET rubrique_agenda = '.spip_abstract_quote($rub_agenda).' WHERE id_config = 1');
		return $retour;
	}

	// Script de verification de l'existance de la base de donnee
	function op_verifier_base() {
		if (!op_verifier_auteurs()) return false;
		if (!op_verifier_config()) return false;
		if (!op_verifier_anonymous()) return false;
		if (!op_verifier_rubriques()) return false;
		return true;
	}

	function op_verifier_anonymous() {
		$result = spip_query("SELECT * FROM `spip_auteurs` WHERE `id_auteur` = 999");

		if (spip_num_rows($result) == 0) return false;
		return true;
	}

	function op_verifier_auteurs() {

		$sql = "SHOW TABLES";
		$result = mysql_query($sql);

		if (!$result) {
   		echo "Erreur DB, impossible de lister les tables\n";
   		echo 'Erreur MySQL : ' . mysql_error();
   		exit;
		}

		while ($row = mysql_fetch_row($result)) {

			if ($row[0]=="spip_op_auteurs") return true;
		}
		
		mysql_free_result($result);
		return false;
	}

	function op_verifier_config() {

		$sql = "SHOW TABLES";
		$result = mysql_query($sql);

		if (!$result) {
   		echo "Erreur DB, impossible de lister les tables\n";
   		echo 'Erreur MySQL : ' . mysql_error();
   		exit;
		}

		while ($row = mysql_fetch_row($result)) {

			if ($row[0]=="spip_op_config") return true;
		}
		
		mysql_free_result($result);
		return false;
	}

	function op_verifier_rubriques() {

		$sql = "SHOW TABLES";
		$result = mysql_query($sql);

		if (!$result) {
   		echo "Erreur DB, impossible de lister les tables\n";
   		echo 'Erreur MySQL : ' . mysql_error();
   		exit;
		}

		while ($row = mysql_fetch_row($result)) {

			if ($row[0]=="spip_op_rubriques") return true;
		}
		
		mysql_free_result($result);
		return false;
	}

	function op_installer_base() {
	
		include_spip('inc/meta');
		ecrire_meta('indy_version', '0.1');
		ecrire_metas();

		if (!op_verifier_auteurs()) {
			$req = "
			CREATE TABLE `spip_op_auteurs` (
			`id_auteur` bigint(21) NOT NULL auto_increment,
			`id_article` bigint(21) DEFAULT '0' NOT NULL,
			`id_real_auteur` bigint(21) DEFAULT '0' NOT NULL,
			`nom` text NOT NULL,
			`email` text NOT NULL,
			`group_name` text NOT NULL,
			`phone` text NOT NULL,
			PRIMARY KEY  (`id_auteur`),
			KEY (`id_article`)
			);		
			";		
			spip_query($req);
		}

		if (!op_verifier_config()) {
			$req = "
			CREATE TABLE `spip_op_config` (
			`id_config` bigint(21) NOT NULL auto_increment,
			`agenda` ENUM('oui','non') DEFAULT 'oui' NOT NULL,
			`documents` ENUM('oui','non') DEFAULT 'oui' NOT NULL,
			`anti_spam` ENUM('oui','non') DEFAULT 'oui' NOT NULL,
			`titre_minus` ENUM('oui','non') DEFAULT 'oui' NOT NULL,
			`rubrique_agenda` bigint(21) NOT NULL,
			`lien_retour` text NOT NULL,
			`lien_retour_abandon` text NOT NULL,
			`id_auteur_op` bigint(21) NOT NULL,
			`message_retour` text NOT NULL,
			`message_retour_abandon` text NOT NULL,
			`version` text NOT NULL,
			PRIMARY KEY  (`id_config`)
			);
			";
			spip_query($req);
			// dans la foulée : créer l'enregistrement par défaut
			op_insert_first_config();
		}

		if (!op_verifier_rubriques()) {
			$req = "
			CREATE TABLE `spip_op_rubriques` (
			`id_rubrique` bigint(21) NOT NULL auto_increment,
			`op_rubrique` bigint(21) DEFAULT '0' NOT NULL,
			PRIMARY KEY (`id_rubrique`)
			);
			";
			spip_query($req);
		}
	}

	function op_insert_first_config() {
		include_spip('base/abstract_sql');

		spip_abstract_insert('spip_op_config', "(id_config, version)", "(
		" . intval($id_config) .",
		'0.2'
		)");
	}
	// Création de l'utilisateur anonymous (id = 999)
	function op_user_anonymous() {
	
		if (!op_verifier_anonymous()) {
			$req = "
			INSERT INTO `spip_auteurs` ( `id_auteur` , `nom` , `bio` , `email` , `nom_site` , `url_site` , `login` , `pass` , `low_sec` , `statut` , `maj` , `pgp` , `htpass` , `en_ligne` , `imessage` , `messagerie` , `alea_actuel` , `alea_futur` , `prefs` , `cookie_oubli` , `source` , `lang` , `idx` , `url_propre` , `extra` )
			VALUES (999 , 'anonymous', '', '', '', '', '', '', '', '', NOW( ) , '', '', '0000-00-00 00:00:00', '', '', '', '', '', '', 'spip', '', '', '', '');
			";
			spip_query($req);
		}
	}

	// Supression de l'utilisateur anonymous
	function op_deluser_anonymous() {
	
		spip_query($req);

		$req = "
		DELETE FROM `spip_auteurs` WHERE `id_auteur` = 999 LIMIT 1;
		";
		spip_query($req);
	}

	// Script  de suppression de la table spip_op_auteurs et spip_op_config. Utilis�sur exec=indy_effacer
	function op_desinstaller_base() {
		$req = "DROP table `spip_op_auteurs`";
		spip_query($req);
		$req = "DROP table `spip_op_config`";
		spip_query($req);
		$req = "DROP table `spip_op_rubriques`";
		spip_query($req);
	}
 
?>
