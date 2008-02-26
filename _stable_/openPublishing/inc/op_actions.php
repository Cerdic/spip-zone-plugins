<?php

include_spip('base/abstract_sql');

/* API plugin open-publishing
*/

	// verification de l'existance de la base de donnee
	function op_verifier_base() {
		if (!op_verifier_auteurs()) return false;
		if (!op_verifier_config()) return false;
		if (!op_verifier_rubriques()) return false;
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

	function op_verifier_upgrade() {
		if (op_get_version() != '0.4') return true;
		return false;
	}

	function op_upgrade_base() {
		// on recupere la version courante
		$version_old = op_get_version();
		// cas du passage 0.2.x => 0.4
		if ( ($version_old == '0.2.2') || ($version_old == '0.2')) {
			// on ajoute ce qui faut dans les bases existantes
			$req = "
			ALTER TABLE `spip_op_config` ADD (
			`tagmachine` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`motclefs` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`statut` ENUM('publie','prop', 'prepa') DEFAULT 'prop' NOT NULL,
			`min_len` INTEGER DEFAULT 3,
			`champ_surtitre` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_soustitre` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_descriptif` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_chapo` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_ps` ENUM('oui','non') DEFAULT 'non' NOT NULL
			);
			";
			
			spip_query($req);
			spip_query('UPDATE `spip_op_config` SET `version` = "0.4" WHERE `id_config` = 1 LIMIT 1');
		}
		// cas du passage 0.3 => 0.4
		if ($version_old == '0.3') {

			// supprimé l'auteur anonymous
			// récupérer l'id du dernier auteur
			// mettre à jour l'auto-increment
			// remettre l'auteur anonymous
			// ALTER TABLE spip_auteurs AUTO_INCREMENT=$id_auteur


			// nouvelles options : longueur minimal du titre
			$req = "
			ALTER TABLE `spip_op_config` ADD (
			`min_len` INTEGER DEFAULT 3,
			`champ_surtitre` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_soustitre` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_descriptif` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_chapo` ENUM('oui','non') DEFAULT 'non' NOT NULL,
			`champ_ps` ENUM('oui','non') DEFAULT 'non' NOT NULL
			);
			";
			spip_query($req);
			spip_query('UPDATE `spip_op_config` SET `version` = "0.4" WHERE `id_config` = 1 LIMIT 1');
		}
		return true;
	}

	function op_installer_base() {
	
	
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();

		include_spip('inc/meta');
		ecrire_meta('indy_version', '0.4');
		ecrire_metas();

		op_insert_first_config();
	}

	function op_insert_first_config() {
		include_spip('base/abstract_sql');

		spip_abstract_insert('spip_op_config', "(id_config, version)", "(
		" . intval($id_config) .",
		'0.4'
		)");
	}

	// Création de l'utilisateur anonymous
	function op_user_anonymous() {
		$req = "
		INSERT INTO `spip_auteurs` ( `nom` , `bio` , `email` , `nom_site` , `url_site` , `login` , `pass` , `low_sec` , `statut` , `maj` , `pgp` , `htpass` , `en_ligne` , `imessage` , `messagerie` , `alea_actuel` , `alea_futur` , `prefs` , `cookie_oubli` , `source` , `lang` , `idx` , `url_propre` , `extra` )
		VALUES ('anonymous', '', '', '', '', '', '', '', '', NOW( ) , '', '', '0000-00-00 00:00:00', '', '', '', '', '', '', 'spip', '', '', '', '');
			";
		spip_query($req);
	}

	// Supression de l'utilisateur anonymous
	function op_deluser_anonymous() {
		$req = "DELETE FROM `spip_auteurs` WHERE `nom` = `anonymous` LIMIT 1;
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