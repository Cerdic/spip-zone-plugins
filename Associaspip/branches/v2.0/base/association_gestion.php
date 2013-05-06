<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com
	* Version pour SPIP 2: Emmanuel Saint-James
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*
	**/

if (!defined("_ECRIRE_INC_VERSION")) return;

global $association_tables_principales, $association_tables_auxiliaires;
include_spip('base/association');
include_spip('base/abstract_sql');

// A chaque modif de la base SQL ou ses conventions (raccourcis etc)
// le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
// http://zone.spip.org/trac/spip-zone/timeline
// Ce numero est fourni automatiquement par la fonction spip_plugin_install
// lors de l'appel des fonctions de ce fichier.

// desinstatllation
function association_vider_tables($nom_meta, $table) {
	global $association_tables_principales, $association_tables_auxiliaires;
	$ok = TRUE;
	foreach($association_tables_principales as $nom => $desc)
		$ok &= sql_drop_table($nom);
	foreach($association_tables_auxiliaires as $nom => $desc)
		$ok &= sql_drop_table($nom);
	if ($ok) // on log depuis r34297
		spip_log("plugin association correctement desinstalle");
	else
		spip_log("plugin association partiellement desinstalle");
	effacer_meta($nom_meta, $table);
}

// MAJ des tables de la base SQL
// Retourne 0 si ok, le dernier numero de MAJ ok sinon
function association_upgrade($meta, $courante, $table='meta') {

	// Compatibilite: le nom de la meta donnant le numero de version
	// n'etait pas std puis est parti dans une autre table puis encore une autre
	if (!isset($GLOBALS['association_metas']['base_version'])) {
		lire_metas('asso_metas');
		if (isset($GLOBALS['asso_metas']['base_version'])) {
			$n = $GLOBALS['asso_metas']['base_version'];
		} elseif (isset($GLOBALS['meta']['association_base_version'])) {
			$n = $GLOBALS['meta']['association_base_version'];
		} else
			$n = 0;
		$GLOBALS['association_metas']['base_version'] = $n;
	} else
		$n = $GLOBALS['association_metas']['base_version'];

	// Upgrade proprement dit
	effacer_meta('association_base_version');
	spip_log("association upgrade: $table $meta = $n =>> $courante");
	if (!$n)
		return association_maj_0($courante, $meta, $table);
	else {
		// compatibilite avec les numeros de version non entiers (repris de r13971)
		$installee = ($n > 1) ? $n : ($n * 100);
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante > $installee) {
			include_spip('base/association');
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur'];
			// signaler que les dernieres MAJ sont a refaire
			if ($n)
				ecrire_meta($meta, $n-1, $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

// v0.30 (Associaspip 1.9.0) :
//@r12523 on les table(champs) : asso_adherents@maj_64 asso_categories(id_categorie, valeur, libelle, duree, cotisation, commentaire, maj) asso_comptes(id_compte, recette, depense, justification, imputation, journal, id_journal, maj) asso_financiers@maj_21 asso_profil(id_profil, nom, numero, rue, cp, ville, telephone, siret, declaration, prefet, president, maj, mail) asso_livres@maj_30 asso_ventes(id_vente, article, code, acheteur, quantite, date_vente, date_envoi, don, prix_vente, frais_envoi, commentaire)
function association_maj_0($version, $meta, $table) {
	global $association_tables_principales, $association_tables_auxiliaires;
	$ok = TRUE;
	foreach($association_tables_principales as $nom => $desc)
		$ok &= sql_create($nom, $desc['field'], $desc['key'], TRUE, FALSE);
	foreach($association_tables_auxiliaires as $nom => $desc)
		$ok &= sql_create($nom, $desc['field'], $desc['key'], FALSE, FALSE);
	if ($ok) {
		ecrire_meta($meta, $version, NULL, $table);
		return 0;
	} else {
		ecrire_meta($meta, 0, NULL, $table);
		return 1;
	}
}

// Changer le nom de la colonne PRIMARY KEY
// ce champ ne *doit*pas*etre*reference*de*FOREIGN*KEY* ailleurs (cas non pris en compte)
// et cette fonction doit etre appelee comme derniere modification sur la table (avec SQLite on cree les autres champs aussi...)
function association_change_pk($table, $row_old, $row_new, $row_int='') {
	include_spip('inc/install');
	$sgbd = analyse_fichier_connection(_FILE_CONNECT);
	switch ($sgbd[4]) { // le 4e argument de spip_connect_db() --appele dans le /config/connect.php de l'installation contient le "type/moteur" de SGBD : c'est le nom du fichier de portage .php defini /ecrire/req
		case 'mysql' :
			$ok = sql_alter("TABLE spip_$table CHANGE $row_old $row_new $row_int".'INT NOT NULL AUTO_INCREMENT');
			break;
		case 'pg' :
			if ($row_old==$row_new) { // simple changement de type ?
				$ok = sql_alter("TABLE spip_$table ALTER $row_old TYPE $row_int".'SERIAL');
			} else {
				$ok = sql_alter("TABLE spip_$table RENAME COLUMN $row_old TO $row_new"); // il est cependant recommande d'utiliser la methode ANSI http://wiki.postgresql.org/wiki/FAQ#How_do_you_change_a_column.27s_data_type.3F
			}
			break;
		case 'sqlite2' :
		case 'sqlite3' :
			if ($row_old!=$row_new) { // on n'aura pas "Error: duplicate column name: $row_new"
				$ok = sql_alter("TABLE spip_$table ADD $row_new INT NOT NULL"); // ALTER TABLE limite, or AUTOINCREMENT ne peut s'utiliser avec la cle primaire http://www.sqlite.org/lang_altertable.html
				$ok &= sql_update("spip_$table", array($row_new=>$row_old) ); // copier les donnees de l'ancienne colonne a la nouvelle
				global $association_tables_principales;
				$ok &= sql_alter("TABLE spip_$table RENAME TO spip_temp$table"); // ALTER TABLE limite au point de ne pouvoir DROPer de colonne ; mais sait renommer la table, ce qu'on fait... http://www.sqlite.org/lang_altertable.html
				$ok &= sql_create($association_tables_principales["spip_$table"]['field'], $association_tables_principales["spip_$table"]['key'], TRUE); // ...puis on recree la table proprement (avec "INTEGER PRIMARY KEY AUTOINCREMENT" --utiliser Int ou un autre provque "Error: AUTOINCREMENT is only allowed on an INTEGER PRIMARY KEY" et quand AutoIncrement n'est pas le dernier de la liste provoque 'Error: near "autoincremen": syntax error')
#				$ok &= sql_query("INSERT INTO spip_$table SELECT ". implode(', ', array_keys($association_tables_principales["spip_$table"]['field']) ) ." FROM spip_temp$table"); // ...puis on reimporte les donnees http://www.sqlite.org/faq.html#q11 (methode directe peu portable)
				$ok &= sql_insertq_multi("spip_$table", sql_allfetsel(implode(', ', array_keys($association_tables_principales["spip_$table"]['field']) ), "spip_temp$table") ); // ...puis on reimporte les donnees http://www.sqlite.org/faq.html#q11 (methode portable utilisant l'API SQL mais risque de debordement de memoire avec sql_fetchall...)
				if ($ok)
					$ok = sql_drop_table("spip_temp$table"); // ...et enfin on supprime la table de transition http://www.sqlite.org/faq.html#q11
			}
			break;
		default :
			$ok = sql_alter("TABLE spip_$table ADD $row_new $row_int".'INT NOT NULL'); // ajouter la nouvelle colonne : elle doit etre non nullable (pour etre une cle primaire) et de type entiere (pour etre candidate a l'auto-incrementation)
			if ($ok) { // creation reussie
				$ok = sql_update("spip_$table", array($row_new=>$row_old), 1); // migrer l'ancienne colonne vers la nouvelle
				if ($ok) { // migration reussie
					$ok = sql_alter("TABLE spip_$table DROP $row_old"); // supprimer l'ancienne colonne
					if ($ok) // on a donc supprime la cle primaire par consequent
						sql_alter("TABLE spip_$table ADD PRIMARY KEY($row_new)"); // declarer la nouvelle colonne comme cle primaire
				}
			}
	}
	return $ok;
}

// v0.30 (Associaspip 1.9.1)
$GLOBALS['association_maj'][21] = array(
//<r12523
	// champ autorisation de publication d'adherent
	array('sql_alter',"TABLE spip_asso_adherents ADD publication text NOT NULL"),
	// nouvelle table des financiers
	array('sql_create','spip_asso_financiers',
		$association_tables_principales['spip_asso_financiers']['field'],
	    $association_tables_principales['spip_asso_financiers']['key'],
	    TRUE), // champs : id_financier, code, intitule, reference, solde, commentaire, maj
	// statut de bienfaiteur pour les adherents
	array('sql_create','spip_asso_bienfaiteurs',
		$association_tables_principales['spip_asso_bienfaiteurs']['field'],
	    $association_tables_principales['spip_asso_bienfaiteurs']['key'],
	    TRUE), // champs : id_don, date_don, bienfaiteur, id_adherent, argent, colis, contrepartie, commentaire, maj
);

// v0.30 (Associaspip 1.9.1)
$GLOBALS['association_maj'][30] = array(
//<r12524
	// asso_financiers devient asso_banques
	array('sql_alter', "TABLE spip_asso_financiers RENAME TO spip_asso_banques"),
	// et sa cle change en consequence
	array('association_change_pk', 'asso_banques', 'id_financier', 'id_banque'),
	// et on ajoute une entree caisses
	array('sql_insert', 'spip_asso_banques', "(code)", "('caisse')"),
	// et on ajoute un champ date
	array('sql_alter', "TABLE spip_asso_banques ADD \"date\" DATE NOT NULL"), //!\ 'date' fait partir des mots reserves du SQL... https://dev.mysql.com/doc/refman/4.1/en/reserved-words.html https://dev.mysql.com/doc/refman/4.1/en/server-sql-mode.html#sqlmode_ansi_quotes
//<r12523
	// les livres de comptes ? (table supprimee par r13839)
	array('sql_create','spip_asso_livres',
		array(
			'id_livre' => "TINYINT NOT NULL",
			'valeur' => "TEXT NOT NULL",
			'libelle' => "TEXT NOT NULL",
			'maj' => "TIMESTAMP NOT NULL",
		),
	    array(
			'PRIMARY KEY' => "id_livre",
	    ),
	    TRUE),
	// initialisation du livre des comptes (donnees plus inserees des v0.40)
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('cotisation', 'Cotisations'), ('vente', 'Ventes'), ('don', 'Dons'), ('achat', 'Achats'), ('divers', 'Divers'), ('activite', 'Activités')"),
//@r13971
	// asso_profil est enrichi
	array('sql_alter', "TABLE spip_asso_profil ADD dons TEXT NOT NULL DEFAULT 'oui'"),
	array('sql_alter', "TABLE spip_asso_profil ADD ventes TEXT NOT NULL DEFAULT 'oui'"),
	array('sql_alter', "TABLE spip_asso_profil ADD comptes TEXT NOT NULL DEFAULT 'oui'"),
//@r15981
	// asso_bienfaiteurs devient asso_dons
	array('sql_alter', "TABLE spip_asso_bienfaiteurs RENAME TO spip_asso_dons"),
);

// v0.40 (Associaspip 1.9.1)
$GLOBALS['association_maj'][40] = array(
//<r12524
	// validation des ecritures comptables
	array('sql_alter',"TABLE spip_asso_comptes ADD valide TEXT NOT NULL"),
	// nouvelle table des participations aux activites
	array('sql_create','spip_asso_activites',
		$association_tables_principales['spip_asso_activites']['field'],
	    $association_tables_principales['spip_asso_activites']['key'],
	    TRUE), // champs : id_activite, id_evenement, nom, id_adherent, accompagne, inscrits, date, telephone, adresse, email, commentaire, montant, date_paiement, statut, maj
);

// v0.50 (Associaspip 1.9.1)
$GLOBALS['association_maj'][50] = array(
//<r12524
#	array('sql_alter',"TABLE spip_asso_profil ADD indexation TEXT NOT NULL"), // supprime par r13971 ou r12530
//@r16186
	// asso_activites.accompagne se decompose en asso_activites.membres + asso_activites.non_membres
	array('sql_alter', "TABLE spip_asso_activites ADD membres TEXT NOT NULL"),
	array('sql_alter', "TABLE spip_asso_activites ADD non_membres TEXT NOT NULL"),
	array('sql_update', 'spip_asso_activites', array('membres' => 'accompagne'), "accompagne<>''"),
);

// v0.60 (Associaspip 1.9.2)
$GLOBALS['association_maj'][60] = array(
//@r12530
	// Passage au plugin "CFG"...
	array('sql_insertq', 'spip_meta', array('nom'=>'association', 'valeur'=>serialize(sql_fetsel('*','spip_profil')), ) ), // les entrees de asso_profil sont serialisees par "CFG" dans meta.nom=association
	array('sql_drop_table', 'spip_asso_profil'), // ...et asso_profil ne sert donc plus...
//@r13839
	// suppression de la table des livres
	array('sql_drop_table', 'spip_asso_livres'), // n'a jamais servi...
	// nouvelle table pour les ressources materielles
	array('sql_create','spip_asso_ressources',
		$association_tables_principales['spip_asso_ressources']['field'],
	    $association_tables_principales['spip_asso_ressources']['key'],
	    TRUE), // champs : id_ressource, code, intitule, date_acquisition, id_achat, pu, statut, commentaire, maj
	// et une nouvelle table pour gerer leurs prets apparait
	array('sql_create','spip_asso_prets',
		$association_tables_principales['spip_asso_prets']['field'],
	    $association_tables_principales['spip_asso_prets']['key'],
	    TRUE), // creation, avec les champs : id_pret, date_sortie, duree, date_retour, id_emprunteur, statut, commentaire_sortie, commentaire_retour
);

// v0.61 (Associaspip 1.9.2)
$GLOBALS['association_maj'][61] = array(
//@r13971
	// asso_banques devient asso_plan
	array('sql_alter', "TABLE spip_asso_banques RENAME TO spip_asso_plan"),
	// le champ du solde anterieur est renomme de facon plus parlante
	array('sql_alter', "TABLE spip_asso_plan ADD solde_anterieur FLOAT NOT NULL DEFAULT 0"),
	array('sql_update', 'spip_asso_plan', array('solde_anterieur' => 'solde'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP solde"),
	// le champ de la date anterieure est renomme de facon plus parlante
	array('sql_alter', "TABLE spip_asso_plan ADD date_anterieure DATE NOT NULL DEFAULT '0000-00-00'"),
	array('sql_update', 'spip_asso_plan', array('date_anterieure' => 'date'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP date"),
	// et un nouveau champ de classe comptable
	array('sql_alter', "TABLE spip_asso_plan ADD classe TEXT NOT NULL"),
	// la cle change en consequence
	array('association_change_pk', 'asso_plan', 'id_banque', 'id_plan'),
	// suppression de asso_profil.nom=indexation
#	array('sql_delete', 'spip_asso_profil', "nom=indexation"), //!\ fait par r12530
);

function association_maj_16181() {
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // On utilise "Inscription 2" et sa table "auteurs_elargis" est la...
		// comme dit r38258 : il faut migrer les donnees avant de detruire les champs (r16186) ou la table (r38192)
		$champs = array('id_auteur', 'nom', 'prenom', 'sexe', 'fonction', 'email', 'numero', 'rue', 'cp', 'ville', 'telephone', 'portable', /*'montant',*/ 'relance', 'divers', 'remarques', 'vignette', 'naissance', 'profession', 'societe', 'identifiant', 'passe', 'creation', 'secteur', 'publication'); // champs pris en compte dans r16186... (on met id_auteur en tete par rapport a r16249 plus loin)
		$liste_maj = sql_select(implode(', ', $champs), 'spip_adherents');
		while ($maj = sql_fetsel($liste_maj) ) { //!\ I2 en s'installant reprend bien les auteurs ; il faut songer a completer par les informations sur les adherents
			sql_updateq('spip_auteurs_elargis', $maj, 'id_auteur='.$maj['id_auteur']);
		}
		// asso_adherents perd les champs migres...
		unset($champs[0]); //@r16249 : ...sauf 'id_auteur' pour completer r37532 (d'ou on le place en premier...)
		foreach ($champs as $champ) { //@r16186
			sql_alter("TABLE spip_asso_adherents DROP $champ");
		}
	} elseif (_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 62;
		return;
	} else { // On continue a utiliser la table asso_adherents....
		// asso_adherents.statut devient asso_adherents.statut_relance (nom recherche par I2 si je comprends bien http://zone.spip.org/trac/spip-zone/browser/tags/inscription2_192/base/inscription2_installer.php#L70 ? mais I2 utilise statut_interne d'apres http://zone.spip.org/trac/spip-zone/changeset/16209/_plugins_/_test_/Association/Association_1.9.2/exec/action_cotisations.php#L31 ! bon, pas inclus dans maj_16181...)
		sql_alter("TABLE spip_asso_adherents ADD statut_relance TEXT NOT NULL");
		sql_update('spip_asso_adherents', array('statut_relance' => 'statut'), "statut<>''");
		sql_alter("TABLE spip_asso_adherents DROP statut");
	}
}
// v0.62 (Associaspip 1.9.2)
$GLOBALS['association_maj'][62] = array(
//@r16186+r16199
	// migration vers "Inscription2"
	array('association_maj_16181'),
	// asso_activites.accompagne se decompose en asso_activites.membres + asso_activites.non_membres
	array('sql_alter', "TABLE spip_asso_activites DROP accompagne"), //cf. v0.50
//@r18150
	// possibilite d'avoir des references comptables actives ou non
	array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL"),
);

function association_maj_18423() {
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // On utilise "Inscription 2" et sa table "auteurs_elargis" est la...
		// asso_adherents perd les champs : id_adherent, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4
		sql_drop_table('spip_asso_adherents'); // (suppression effective dans r20002 et) on utilise spip_auteurs_elargis jusqu'a la resurection en r37532
	} elseif (_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 63;
		return;
	}
}
// v0.63 (Associaspip 1.9.2)
$GLOBALS['association_maj'][63] = array(
//@r18423
#	array('association_maj_18423'), //!\ report dans maj_64
//@r20002
	// liaison de asso_ventes avec asso_adherents ou auteurs_elargis
	array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT NOT NULL"),
);

function association_maj_16315() {
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // On utilise "Inscription 2" et sa table "auteurs_elargis" est la...
		// champs manquants dans auteurs_elargis qui provoquaient des disfonctionnement signales sur le forum
		sql_alter("TABLE spip_auteurs_elargis ADD validite DATE NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant FLOAT NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD \"date\" DATE NOT NULL default '0000-00-00'"); //!\ 'date' fait partir des mots reserves du SQL... https://dev.mysql.com/doc/refman/4.1/en/reserved-words.html https://dev.mysql.com/doc/refman/4.1/en/server-sql-mode.html#sqlmode_ansi_quotes
		// comme dit r38258 : il faut migrer les donnees avant de detruire les champs (r16315) ou la table (r38192)
		// on utilise des @sql_... suite au deplacement en maj_64 : ca va hurler chez ceux qui avaient fait la maj_62 avant correction...
		$champs = array('id_auteur', 'montant', 'date', 'categorie'); // champs pris en compte dans r16315... (sauf 'statut_relance' deja supprime dans r16186)
		$liste_maj = @sql_select(implode(', ', $champs), 'spip_adherents');
		while ($maj = sql_fetsel($liste_maj) ) { //!\ I2 en s'installant reprend bien les auteurs ; il faut songer a completer par les informations sur les adherents
			@sql_updateq('spip_auteurs_elargis', $maj, 'id_auteur='.$maj['id_auteur']);
		}
		// asso_adherents perd les champs migres...
		foreach ($champs as $champ) { //@r16315+r18423
			@sql_alter("TABLE spip_asso_adherents DROP $champ");
		}
	} elseif (_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 64;
		return;
	}
}
// v0.64 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][64] = array(
//@r25365
	array('sql_alter',"TABLE spip_asso_prets ADD id_ressource VARCHAR(20) NOT NULL"),
//@r16315+r18423+r34264
	array('association_maj_16315'),
);

function association_maj_37532() {
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // On utilise "Inscription 2" et sa table "auteurs_elargis" est la...
		// asso_adherents reloaded
		sql_create('spip_asso_adherents',
			array(
				'id_adherent' => "BIGINT NOT NULL",
				'nom' => "TEXT NOT NULL",
				'prenom' => "TEXT NOT NULL",
				'sexe' => "TINYTEXT NOT NULL",
				'fonction' => "TEXT NOT NULL",
				'email' => "TINYTEXT NOT NULL",
				'validite' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'numero' => "TEXT NOT NULL",
				'rue' => "TEXT NOT NULL",
				'cp' => "TEXT NOT NULL",
				'ville' => "TEXT NOT NULL",
				'telephone' => "TINYTEXT NOT NULL",
				'portable' => "TINYTEXT NOT NULL",
				'montant' => "TEXT NOT NULL",
				'"date"' => "DATE NOT NULL DEFAULT '0000-00-00'", //!\ usage de nom reserve du SQL...
				'statut_interne' => "TINYTEXT NOT NULL", // statut/statut_relance y etait aussi en attendant que le code soit corrige
				'relance' => "TINYINT NOT NULL DEFAULT 0",
				'divers' => "TEXT NOT NULL",
				'remarques' => "TEXT NOT NULL",
				'vignette' => "TINYTEXT NOT NULL",
				'id_auteur' => "BIGINT NOT NULL DEFAULT 0",
				'id_asso' => "TEXT NOT NULL",
				'categorie' => "TEXT NOT NULL",
				'naissance' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'societe' => "TEXT NOT NULL",
				'identifiant' => "TEXT NOT NULL",
				'passe' => "TEXT NOT NULL",
				'creation' => "DATE NOT NULL DEFAULT '0000-00-00'",
				'maj' => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
				'utilisateur1' => "TEXT NOT NULL",
				'utilisateur2' => "TEXT NOT NULL",
				'utilisateur3' => "TEXT NOT NULL",
				'utilisateur4' => "TEXT NOT NULL",
				'secteur' => "TEXT NOT NULL",
				'publication' => "TEXT NOT NULL",
				'profession' => "TEXT NOT NULL",
				'commentaire' => "TEXT NOT NULL", //+
			),
			array(
				'PRIMARY KEY' => "id_adherent",
			),
		FALSE); // re-creation (cf. maj_20002) avec les champs communs (ci apres) et : id_adherent, id_asso, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4
		$champs_communs = 'nom, prenom, sexe, fonction, email, validite, numero, rue, cp, ville, telephone, portable, montant, date, relance, categorie, divers, remarques, vignette, id_auteur, naissance, profession, societe, identifiant, passe, creation, secteur, publication, statut_interne, commentaire'; // champs pris en compte dans r16186+r16315 (cf maj_16181+maj_16315) +r19708
		$liste_maj = sql_select($champs_communs, 'spip_auteurs_elargis');
		while ($maj = sql_fetsel($liste_maj) ) { // re-import necessaire pour passer en v0.7 qui se passe de I2
			sql_insertq('spip_adherents', $maj);
		}
	} elseif (_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 65;
		return;
	} else { // On continue d'utiliser asso_adherents qu'on met en accord
		// asso_adherents.statut_relance devient asso_adherents.statut_interne (harmonisation avec auteurs_elargis ?)
		sql_alter("TABLE spip_asso_adherents ADD statut_interne TEXT NOT NULL DEFAULT ''");
		sql_update('spip_asso_adherents', array('statut_interne' => 'statut_relance'), 1);
		sql_alter("TABLE spip_asso_adherents DROP statut_relance");
		// ajout de asso_adherents.commentaire (harmonisation avec auteurs_elargis ?)
		sql_alter("TABLE spip_asso_adherents ADD commentaire TEXT NOT NULL DEFAULT ''");
	}
}
// v0.65 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][65] = array(
//@r16315+r18423
	// Optionnalisation du plugin "Inscription2" : on supprime ce qu'il reste de asso_adherents
	array('association_maj_18423'), //!\ deplace de maj_62 suite a correction dans r16315
//@r37532+r37978+r37979
	// Optionnalisation du plugin "Inscription2" : on recree propremment asso_adherents
	array('association_maj_37532'),
);

// v0.70 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][37869] = array(
//@r37869
	// spip_asso_adherents.nom devient spip_asso_adherents.nom_famille
	array('sql_alter', "TABLE spip_asso_adherents ADD nom_famille TEXT NOT NULL"),
	array('sql_update', 'spip_asso_adherents', array('nom_famille' => 'nom'), "nom<>''"),
	array('sql_alter', "TABLE spip_asso_adherents DROP nom"),
);

// Recopie des metas geree par CFG dans la table asso_meta
function association_maj_38190() {
	if (sql_create('spip_asso_metas',
		array('nom'=>"VARCHAR(255) NOT NULL", 'valeur'=>"TEXT DEFAULT ''", 'impt'=>"ENUM('non', 'oui') DEFAULT 'oui' NOT NULL", 'maj'=>"TIMESTAMP"),
		array('PRIMARY KEY'=>'nom'),
		FALSE, FALSE)) {
		// Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus
		include _DIR_PLUGINS . 'cfg/inc/cfg.php';
		if (is_array($c = lire_config('association'))) {
			foreach($c as $k => $v) {
				ecrire_meta($k, $v, 'oui', 'asso_metas');
			}
			// effacer les vieilles meta
			effacer_meta('association');
			effacer_meta('asso_base_version');
			effacer_meta('association_base_version');
		}
	} else
		spip_log("maj_38190: echec de  la creation de spip_asso_metas");
}
// v0.80 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][38192] = array(
//@r38190
	// Utilisation de asso_metas ! Exit le plugin "CFG"
	array('association_maj_38190'),
);

// v1.00 (Associaspip 2.0)
$GLOBALS['association_maj'][38258] = array(
	// spip_asso_adherents devient spip_asso_membres
	array('sql_alter', "TABLE spip_asso_adherents RENAME TO spip_asso_membres"), // a noter que asso_adherents n'etait plus utilise depuis r20002+r20034 ! puis est revenu en r37532 dans l'idee de pouvoir suppleer auteurs_elargis !
	// ...et la cle de asso_membres change...
	array('sql_alter', "TABLE spip_asso_membres DROP id_adherent"), // plus utilise depuis r20076
	array('sql_alter', "TABLE spip_asso_adherents  ADD PRIMARY KEY (id_auteur)"), // ce champ est NOT NULL et unique (c'est en fait une cle etrangere auteurs.id_auteur)
	// asso_adherents.numero et asso_adherents.rue fusionnent en asso_membres.adresse
	array('sql_alter', "TABLE spip_asso_membres ADD adresse TEXT NOT NULL"),
	array('sql_update', 'spip_asso_membres', array('adresse' => "CONCAT(numero,CONCAT(', ',rue))") ), // l'ordre inverse est possible aussi, et dans les deux cas on peut ne pas avoir de virgule ou alors un autre symbole :-S
#	array('sql_alter', "TABLE spip_asso_membres DROP numero"), // garder pour ceux qui veulent refaire la requete a leur sauce
#	array('sql_alter', "TABLE spip_asso_membres DROP rue"), // garder pour ceux qui veulent refaire la requete a leur sauce
	// spip_asso_adherents.cp devient spip_asso_membres.code_postal
	array('sql_alter', "TABLE spip_asso_membres ADD code_postal TEXT NOT NULL"),
	array('sql_update', 'spip_asso_membres', array('code_postal' => 'cp'), "cp<>''"),
	array('sql_alter', "TABLE spip_asso_membres DROP cp"),
	// spip_asso_adherents.portable devient spip_asso_membres.mobile
	array('sql_alter', "TABLE spip_asso_membres ADD mobile TINYTEXT NOT NULL"),
	array('sql_update', 'spip_asso_membres', array('mobile' => 'portable'), "portable<>''"),
	array('sql_alter', "TABLE spip_asso_membres DROP portable"),
	// beaucoup sont supprimes : on garde pour ceux qui les utilisent
#	array('sql_alter', "TABLE spip_asso_membres DROP montant"),
#	array('sql_alter', "TABLE spip_asso_membres DROP date"),
#	array('sql_alter', "TABLE spip_asso_membres DROP statut"), // doublonne avec auteurs.statut
#	array('sql_alter', "TABLE spip_asso_membres DROP relance"), // nouveau champ statut_interne ?
#	array('sql_alter', "TABLE spip_asso_membres DROP divers"),
#	array('sql_alter', "TABLE spip_asso_membres DROP remarques"), // nouveau champ commentaire ?
#	array('sql_alter', "TABLE spip_asso_membres DROP vignette"),
#	array('sql_alter', "TABLE spip_asso_membres DROP naissance"),
#	array('sql_alter', "TABLE spip_asso_membres DROP profession"),
#	array('sql_alter', "TABLE spip_asso_membres DROP societe"),
#	array('sql_alter', "TABLE spip_asso_membres DROP identifiant"), // doublonne avec auteurs.login
#	array('sql_alter', "TABLE spip_asso_membres DROP passe"), // doublonnne avec auteurs.pass
#	array('sql_alter', "TABLE spip_asso_membres DROP creation"),
#	array('sql_alter', "TABLE spip_asso_membres DROP maj"),
#	array('sql_alter', "TABLE spip_asso_membres DROP secteur"),
#	array('sql_alter', "TABLE spip_asso_membres DROP utilisateur1"), // cf. r19708
#	array('sql_alter', "TABLE spip_asso_membres DROP utilisateur2"), // cf. r19708
#	array('sql_alter', "TABLE spip_asso_membres DROP utilisateur3"), // cf. r19708
#	array('sql_alter', "TABLE spip_asso_membres DROP utilisateur4"), // cf. r19708
);

// v1.00 (Associaspip 2.0)
$GLOBALS['association_maj'][38578] = array(
	array('sql_alter', 'TABLE spip_asso_metas RENAME TO spip_association_metas'),
);

// v0.65 (Associaspip 2.0)
$GLOBALS['association_maj'][39702] = array(
	// on rajoute asso_comptes.valide
	array('sql_alter', "TABLE spip_asso_comptes ADD valide TEXT DEFAULT 'oui'"),
	// on rajoute asso_comptes.maj
	array('sql_alter', "TABLE spip_asso_comptes ADD maj TIMESTAMP"),
);

// v1.0.0 (Associaspip 2.0)
$GLOBALS['association_maj'][42024] = array(
	// (d'apres r51766): on renomme asso_comptes.valide en asso_comptes.vu
	array('sql_alter', "TABLE spip_asso_comptes ADD vu BOOLEAN DEFAULT 0"),
	array('sql_update', 'spip_asso_comptes', array('vu' => 1), "valide='oui'"),
	array('sql_alter', "TABLE spip_asso_comptes DROP valide"),
);


?>