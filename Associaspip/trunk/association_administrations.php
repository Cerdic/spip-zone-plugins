<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('base/association');
include_spip('base/abstract_sql');

/**
 * Fonction de desinstallation du plugin Associaspip
 *
 * @param string $nom_meta_base_version
 *   Nom de la meta informant de la version du schema de donnees du plugin
 * @param string $table_metas
 *   Nom de la table contenant les metas du plugin installe dans SPIP
 * @return void
**/
function association_vider_tables($nom_meta_base_version, $table_metas='association_metas') {
	$ok = TRUE; // va servir de temoin de la procedure
	foreach(array( // les tables de prefixe "spip_asso_" du plugin
		'activites',
		'categories',
		'comptes',
		'destination',
		'destination_op',
		'dons',
		'exercices',
		'fonctions',
		'groupes',
		'membres',
		'plan',
		'prets',
		'ressources',
		'ventes',
		) as $table) { // On efface les tables du plugin en consignant le resultat
		if (sql_drop_table("spip_asso_$table"))
			spip_log("Associaspip supprime la table '$table' ", 'associaspip');
		else {
			spip_log("Associaspip n'a pu supprimer la table '$table' ", 'associaspip');
			$ok = FALSE;
		}
	}
	if ($ok) { // tout s'est bien passe
		effacer_meta($nom_meta_base_version, $table_metas); // on efface la meta $GLOBALS['association_metas']['base_version'] pour que SPIP (en fait sa fonction qui gere la desinstallation) ne voie plus le plugin et confirme sa desinstallation
		sql_drop_table("spip_$table_metas"); // on efface enfin la table elle-meme devenue inutile
		spip_log("Associaspip supprime la table '$table_metas' (version:$nom_meta_base_version)", 'associaspip'); // et on consigne cela
	} else {
		spip_log("Associaspip conserve la table '$table_metas' (version:$nom_meta_base_version)", 'associaspip'); // on l'inclu dans la liste des tables non supprimees
	}
}

/**
 * Fonction d'installation et de mise-a-jour du plugin Associaspip
 *
 * @param string $meta
 *   Nom de la meta informant de la version du schema de donnees du plugin
 * 'base_version'
 * @param string $courante
 *   Version cible du schema de donnees dans ce plugin (base_version dan plugin.xml)
 * @param string $table
 *   Nom de la table contenant les metas de ce plugin (meta dans plugin.xml)
 * 'association_metas'
 * @return int
 *   Retourne 0 si ok, le dernier numero de MAJ ok sinon
**/
function association_upgrade($meta, $courante, $table='association_metas') {

	// Compatibilite pour la meta donnant le numero de version
	if (!isset($GLOBALS[$table][$meta])) { // Le nom de la meta donnant le numero de version n'etait pas standard puis est parti dans une autre table puis encore une autre
		lire_metas('asso_metas'); //!\ Pour une nouvelle installation, ceci genere des alertes dans prive_spip.log
		if (isset($GLOBALS['asso_metas']['base_version'])) { // [r38190;r38579[:spip_asso_metas
			$n = $GLOBALS['asso_metas']['base_version'];
		} elseif (isset($GLOBALS['meta']['association_base_version'])) { // [;r38190[:spip_meta
			$n = $GLOBALS['meta']['association_base_version'];
		} else
			$n = 0;
		$GLOBALS[$table][$meta] = $n; // et recuperer a la bonne place
	} else // [r38579;[:spip_association_metas (avec le prefixe du plugin pour #CONFIGURER_METAS qui remplace CFG pour le chargement+enregistrement auto de la config)
		$n = $GLOBALS[$table][$meta];

	// Upgrade proprement dit
	effacer_meta('association_base_version', $table);
	spip_log("Associaspip migre $table.$meta : $n =>> $courante", 'associaspip');
	if (!$n) { // Creation de la base
		include_spip('base/create');
		creer_base(); // comme on utilise les pipelines qui vont bien, on se contente simplement de "creer_base();" qui (ne) fait (en fait que) "alterer_base(lister_tables_principales(), lister_tables_auxiliaires(), FALSE);" et voila
		association_change_ai('asso_groupes', 100); //!\ l'index de depart de l'autoincrement de la table doit etre a 100 car les premiers groupes sont reserves aux autorisations
		association_maj_autorisations();
		ecrire_meta($meta, $courante, NULL, $table);
		return 0; // Reussite supposee ! (car "alterer_base" n'a pas de retour)
	} else { // Mise-A-Jour de la base
		$installee = ($n>1) ? $n : ($n*100); // compatibilite avec les numeros de version non entiers (avant Associaspip 2.0 le numero du schema de donnees etait celui de la version du plugin : 0.xx) apparemment repris de r13971
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante>$installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table); // jouer les mises-a-jour dans $GLOBALS['association_maj'] (defini plus loin)
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur']; // on recupere la derniere mise-a-jour reussie
			if ($n) // signaler que les dernieres MAJ sont a refaire
				ecrire_meta($meta, $n-1, '', $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
}

/**
 * Mise a jour de la table asso_groupes avec les groupes gerant les autorisations
 * d'acces (id<100)
 */
function association_maj_autorisations() {
/// initialisations
	$autorisations_nouvelles = array(
#		1, 2, 3, 20, 21, 30, 31, // r59886 avant r67499
		1, 2, 3, 21, 23, 31, 33, // r59886 apres r67499
#		10, 32, 33, // r66289 avant r67499
		10, 30, 35, // r66289 apres r67499
#		11, 12, 40, 41, 50, 51, // r66769 avant r67499
		11, 13, 41, 43, 51, 53, // r66769 apres r67499
#		61, 63, 62, 66, 73, 74, 76, // r67500 avant r67499
		61, 63, 64, 66, 73, 74, 76, // r67500 apres r67499
	); // definir tous les groupes qui doivent exister
	$autorisations_anciennes = sql_allfetsel('id_groupe', 'spip_asso_groupes', 'id_groupe<100'); // recuperer tous les groupes existants
	$autorisations_rajoutees = array(); // servira de temoin des rajouts
/// traitements
	foreach ($autorisations_anciennes as $groupe) { // comparer l'existant au requis :
		$deja = array_search($groupe['id_groupe'], $autorisations_toutes); // verifier qu'une entree requise est bien presente...
		if ($deja!==FALSE) // ...et si c'est le cas, alors...
			unset($autorisations_nouvelles[$deja]); // ...la rayer des entrees a insere
	}
	foreach ($autorisations_nouvelles as $id) { // metre a jour
		$autorisations_rajoutees[] = sql_insertq('spip_asso_groupes', array(
			'nom'=>'', 'affichage'=>0, 'commentaire'=>'',
			'id_groupe'=>$id,
		)); // insertion des absents
	} //!\ Ce n'est pas le plus performant de faire (un peu moins d') une centaine de requetes individuelles quand on peut faire une requete groupee, mais on s'evite de planter le lot a cause d'un...
	spip_log('Associaspip ajoute asso_groupes.id_groupe : '.implode(',',$autorisations_rajoutees), 'associaspip'); // trace des ajouts (leur champ "maj" devrait etre a la date d'insertion ...sauf si c'est edite...)
	$autorisations_ignorees = array_diff($autorisations_nouvelles, $autorisations_rajoutees); // liste des insertions echouees
	if (count($autorisations_ignorees)) // malgre le controle des existants on a eu des doublons ?
		spip_log('Associaspip conserve asso_groupes.id_groupe : '.implode(', ', $autorisations_ignorees), 'associaspip'); // signaler les fautifs

}

/**
 * Changer la valeur de depart l'auto-incrementation d'une table
 *
 * c'est la valeur minimale du compteur : si une valeur plus elevee est en cours
 * elle est conservee ; sinon on partira de celle specifiee a l'insertion suivante
 *
 * @param string $tbl
 *   Nom de la table sans prefixe "spip_"
 * @param int $val
 *   Valeur de depart desiree
 * @param string $col
 *   Nom de la colonne auto-incrementee
 * @return bool $ok
 *   TRUE en cas de succes et FALSE en cas d'echec
**/
function association_change_ai($tbl, $val, $col='') {
	$val = intval($val);
	include_spip('inc/install');
	$sgbd = analyse_fichier_connection(_FILE_CONNECT);
	switch ($sgbd[4]) { // le 4e argument de spip_connect_db() --appele dans le /config/connect.php de l'installation contient le "type/moteur" de SGBD : c'est le nom du fichier de portage .php defini /ecrire/req
		case 'mysql' :
			$ok = sql_query("ALTER TABLE spip_$tbl AUTO_INCREMENT = $val", '', TRUE); // http://stackoverflow.com/questions/1485668/how-to-set-initial-value-auto-increment-in-mysql http://dev.mysql.com/doc/refman/5.0/en/example-auto-increment.html
			break;
		case 'pg' :
			$ok = sql_query("ALTER SEQUENCE spip_$tbl"."_id_seq RESTART WITH $val", '', TRUE); // http://stackoverflow.com/questions/8745051/postgres-manually-alter-sequence http://www.postgresql.org/docs/current/interactive/sql-altersequence.html
			break;
		case 'sqlite2' :
		case 'sqlite3' :
			$ok = sql_query("UPDATE SQLITE_SEQUENCE SET seq = $val WHERE name = 'spip_$tbl'", '', TRUE); // http://stackoverflow.com/questions/692856/set-start-value-for-autoincrement-in-sqlite http://sqlite.org/fileformat2.html#seqtab
			break;
		default :
			if ($tbl && !sql_countsel("spip_$tbl", "$col>=$val") ) { // il n'existe pas de ligne plus loin
				$ok = sql_insertq("spip_$tbl", array($col=>$val) ); // on insere la ligne a cette valeur : cela devra y positionner le compteur
				$ok &= sql_delete("spip_$tbl", "$col=$val"); // on supprime cette ligne : le compteur ne bouge normalement pas
			} // sinon on ne fait rien
			break;
	}
	return $ok;
}

/**
 * Changer le nom de la colonne PRIMARY KEY
 *
 * ce champ ne *doit*pas*etre*reference*de*FOREIGN*KEY* ailleurs (cas non pris en compte)
 * et cette fonction doit etre appelee comme derniere modification sur la table (avec SQLite on cree les autres champs aussi...)
 *
 * @param string $table
 *   Nom de la table sans prefixe "spip_"
 * @param string $row_old
 *   Nom de l'ancienne colonne
 * Cette colonne doit etre une cle primaire : cette verification n'est pas faite !
 * @param string $row_new
 *   Nom de la nouvelle colonne
 * @param string $row_int
 *   Vaut : 'SMALL' (16 bits) | '' (32 bits par defaut) | 'BIG' (64 bits)
 * Attention : eviter 'TINY' (8 bits) ou 'MEDIUM' (32 bits) qui sont pas portables !
 * @return bool $ok
 *   FALSE en cas d'erreur, TRUE sinon
**/
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
				if ($ok) {
					$ok = sql_alter("TABLE spip_$table RENAME TO spip_temp$table"); // ALTER TABLE limite au point de ne pouvoir DROPer de colonne ; mais sait renommer la table, ce qu'on fait... http://www.sqlite.org/lang_altertable.html
					$ok &= maj_tables("spip_$table"); // ...puis on recree la table proprement (avec "INTEGER PRIMARY KEY AUTOINCREMENT" --utiliser Int ou un autre provque "Error: AUTOINCREMENT is only allowed on an INTEGER PRIMARY KEY" et quand AutoIncrement n'est pas le dernier de la liste provoque 'Error: near "autoincremen": syntax error')
#					sql_query("INSERT INTO spip_$table SELECT ". implode(', ', array_keys($GLOBALS['tables_principales']["spip_$table"]['field']) ) ." FROM spip_temp$table"); // ...puis on reimporte les donnees http://www.sqlite.org/faq.html#q11 (methode directe peu portable)
					$ok &= sql_insertq_multi("spip_$table", sql_allfetsel(implode(', ', array_keys($GLOBALS['tables_principales']["spip_$table"]['field']) ), "spip_temp$table") ); // ...puis on reimporte les donnees http://www.sqlite.org/faq.html#q11 (methode portable utilisant l'API SQL mais risque de debordement de memoire avec sql_fetchall...)
					if ($ok)
						sql_drop_table("spip_temp$table"); // ...et enfin on supprime la table de transition http://www.sqlite.org/faq.html#q11
				}
			}
			break;
		default :
			$ok = sql_alter("TABLE spip_$table ADD $row_new $row_int".'INT NOT NULL'); // ajouter la nouvelle colonne : elle doit etre non nullable (pour etre une cle primaire) et de type entiere (pour etre candidate a l'auto-incrementation)
			if ($ok) { // creation reussie
				$ok = sql_update("spip_$table", array($row_new=>$row_old), 1); // migrer l'ancienne colonne vers la nouvelle
				if ($ok) { // migration reussie
					$ok = sql_alter("TABLE spip_$table DROP $row_old"); // supprimer l'ancienne colonne
					if ($ok) // on a donc supprime la cle primaire par consequent
						$ok = sql_alter("TABLE spip_$table ADD PRIMARY KEY($row_new)"); // declarer la nouvelle colonne comme cle primaire
				}
			}
			break;
	}
	return $ok;
}

/**
 * Liste des mises-a-jour
 *
 * A chaque modif de la base SQL ou ses conventions (raccourcis etc)
 * le fichier plugin.xml doit indiquer le numero de depot qui l'implemente sur
 * http://zone.spip.org/trac/spip-zone/timeline
 * Ce numero est fourni automatiquement par la fonction spip_plugin_install()
 * lors de l'appel de la fonction association_upgrade()
 *
 * Chaque mise-a-jour numero=>array() est elle-meme une liste d'actions successives,
 * chaque action consistant en un appel de fonction suivi de ses parametres :
 * array('la_fonction', 'param1', ...),
 * nota : Et oui, comme le 3eme argument de maj_plugin() present dans base/upgrade de SPIP 3.0 ;-)
 */

// v0.30 (Associaspip 1.9.1)
$GLOBALS['association_maj'][21] = array(
//<r12523
	// champ autorisation de publication d'adherent
	array('maj_tables','spip_asso_adherents'), // + champ : publication
	// nouvelle table des financiers
	array('maj_tables','spip_asso_financiers'), // champs : id_financier, code, intitule, reference, solde, commentaire, maj
	// statut de bienfaiteur pour les adherents
	array('maj_tables','spip_asso_bienfaiteurs'), // champs : id_don, date_don, bienfaiteur, id_adherent, argent, colis, contrepartie, commentaire, maj
);

$GLOBALS['association_maj'][30] = array(
//<r12524
	// asso_financiers devient asso_banques
	array('sql_alter', "TABLE spip_asso_financiers RENAME TO spip_asso_banques"),
	// et sa cle change en consequence
	array('association_change_pk', 'asso_banques', 'id_financier', 'id_banque'),
	// et on ajoute une entree caisses
	array('sql_insert', 'spip_asso_banques', "(code)", "('caisse')"),
	// et on ajoute un champ date
	array('sql_alter', "TABLE spip_asso_banques ADD \"date\" DATE NOT NULL "), //!\ 'date' fait partir des mots reserves du SQL... https://dev.mysql.com/doc/refman/4.1/en/reserved-words.html https://dev.mysql.com/doc/refman/4.1/en/server-sql-mode.html#sqlmode_ansi_quotes
//<r12523
	// modules comptables ? (table supprimee par r13839 en maj_60)
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
	TRUE, FALSE),
	// initialisation (donnees plus inserees des maj_40 : activation des modules dans le profil...)
	array('sql_insertq_multi', 'spip_asso_livres', array(
		array('valeur'=>"cotisation", 'libelle'=>"Cotisations"),
		array('valeur'=>"vente", 'libelle'=>"Ventes"),
		array('valeur'=>"don", 'libelle'=>"Dons"),
		array('valeur'=>"achat", 'libelle'=>"Achats"),
		array('valeur'=>"divers", 'libelle'=>"Divers"),
		array('valeur'=>"activité", 'libelle'=>"Activités"),
	) ),
//@r13971
	// asso_profil est enrichi
	array('sql_alter', "TABLE spip_asso_profil ADD dons TEXT NOT NULL DEFAULT 'oui' "),
	array('sql_alter', "TABLE spip_asso_profil ADD ventes TEXT NOT NULL DEFAULT 'oui' "),
	array('sql_alter', "TABLE spip_asso_profil ADD comptes TEXT NOT NULL DEFAULT 'oui' "),
//@r15981
	// asso_bienfaiteurs devient asso_dons
	array('sql_alter', "TABLE spip_asso_bienfaiteurs RENAME TO spip_asso_dons"),
);

// v0.40 (Associaspip 1.9.1)
$GLOBALS['association_maj'][40] = array(
//<r12524
	array('maj_tables','spip_asso_comptes'), // +champ : valide
	array('maj_tables','spip_asso_activites'), // champs : id_activite, id_evenement, nom, id_adherent, accompagne, inscrits, date, telephone, adresse, email, commentaire, montant, date_paiement, statut, maj
);

// v0.50 (Associaspip 1.9.1)
$GLOBALS['association_maj'][50] = array(
//<r12524
#	array('sql_alter',"TABLE spip_asso_profil ADD indexation TEXT NOT NULL "), // supprime par r13971 ou r12530
//@r16186
	// asso_activites.accompagne se decompose en asso_activites.membres + asso_activites.non_membres
	array('sql_alter', "TABLE spip_asso_activites ADD membres TEXT NOT NULL"),
	array('sql_alter', "TABLE spip_asso_activites ADD non_membres TEXT NOT NULL"),
	array('sql_update', 'spip_asso_activites', array('membres' => 'accompagne'), "accompagne<>''"),
	array('sql_alter', "TABLE spip_asso_activites DROP accompagne"),
	array('spip_log', "maj_50", 'associaspip'),
);

function association_maj_12530() {
	sql_insertq('spip_meta', array(
		'nom' => 'association',
		'valeur' => serialize(sql_fetsel('*','spip_asso_profil')),
	)); // les entrees de asso_profil sont serialisees par "CFG" dans meta.nom=association
	sql_drop_table('spip_asso_profil'); // ...et asso_profil ne sert donc plus...
}
// v0.60 (Associaspip 1.9.2)
$GLOBALS['association_maj'][60] = array(
//@r12530
	// Passage au plugin "CFG"...
	array('association_maj_12530'), // migration de la table asso_profil dans un tableau dans meta
//@r13839
	// suppression de la table des livres
	array('sql_drop_table', 'spip_asso_livres'), // n'a jamais servi...
	// nouvelle table pour les ressources materielles
	array('maj_tables','spip_asso_ressources'), // champs : id_ressource, code, intitule, date_acquisition, id_achat, pu, statut, commentaire, maj
	// et nouvelle table pour en gerer les locations
	array('maj_tables','spip_asso_prets'), // creation, avec les champs : id_pret, date_sortie, duree, date_retour, id_emprunteur, statut, commentaire_sortie, commentaire_retour
);

// v0.61 (Associaspip 1.9.2)
$GLOBALS['association_maj'][61] = array(
//@r13971
	// asso_banques devient asso_plan
	array('sql_alter', "TABLE spip_asso_banques RENAME TO spip_asso_plan"),
	// la cle change en consequence
	array('association_change_pk', 'asso_plan', 'id_banque', 'id_plan'),
	// et de nouveaux champs apparaissent
	array('maj_tables', 'spip_asso_plan'), // +champs : solde_anterieur date_anterieure classe
	// le champ du solde anterieur est renomme de facon plus parlante
	array('sql_update', 'spip_asso_plan', array('solde_anterieur' => 'solde'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP solde"),
	// le champ de la date anterieure est renomme de facon plus parlante
	array('sql_update', 'spip_asso_plan', array('date_anterieure' => 'date'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP date"),
);

// Avec Associaspip 2.1 le support de "Inscription2" est abandonne... La constante
// '_ASSOCIATION_INSCRIPTION2' n'est donc plus définie dans association_options.php
// (elle vaut donc FALSE --en fait elle est NULL car unset...) Du coup, il faut
// le faire ici pour ne pas fausser les migrations qui en dependent, mais en
// preferant une variable (locale au script et) du meme nom :
$_ASSOCIATION_INSCRIPTION2 = ($GLOBALS['association_metas']['base_version']>0.6 AND $GLOBALS['association_metas']['base_version']<0.7); // pour etre strict c'aurait du etre 0.62 a 0.64 ?

// Avec Associaspip 2.1 le support de "Inscription2" est abandonne... La constante
// '_ASSOCIATION_AUTEURS_ELARGIS' n'est donc plus définie dans association_options.php
// Il faut donc le faire ici pour ne pas fausser les migrations qui en dependent,
// mais en preferant une variable (locale au script et) de meme nom pour un
// test plus allege :
$_ASSOCIATION_AUTEURS_ELARGIS = ($_ASSOCIATION_INSCRIPTION2 AND @sql_select('id_auteur', 'spip_auteurs_elargis', '', '', '', 1) ); // $GLOBALS['meta']['inscription2'] au lieu du sql_select ?

function association_maj_16181() {
	if ($_ASSOCIATION_AUTEURS_ELARGIS) { // "Inscription 2" et sa table "auteurs_elargis" sont la...
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
	} elseif ($_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
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
	if ($_ASSOCIATION_AUTEURS_ELARGIS) { // "Inscription 2" et sa table "auteurs_elargis" sont la...
		// asso_adherents perd les champs : id_adherent, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4
		sql_drop_table('spip_asso_adherents'); // (suppression effective dans r20002 et) on utilise spip_auteurs_elargis jusqu'a la resurection en r37532
	} elseif ($_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 63;
		return;
	}
}
// v0.63 (Associaspip 1.9.2)
$GLOBALS['association_maj'][63] = array(
//@r20002+r37869
	// liaison de asso_ventes avec asso_adherents ou auteurs_elargis
	array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT NOT NULL"),
);

function association_maj_16315() {
	if ($_ASSOCIATION_AUTEURS_ELARGIS) { // "Inscription 2" et sa table "auteurs_elargis" sont la...
		// champs manquants dans auteurs_elargis
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
	} elseif ($_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
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
	if ($_ASSOCIATION_AUTEURS_ELARGIS) { // "Inscription 2" et sa table "auteurs_elargis" sont la...
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
		while ($maj = sql_fetsel($liste_maj) ) { // re-import necessaire pour se passer de I2 des v0.7
			sql_insertq('spip_adherents', $maj);
		} //= sql_insertq_multi('spip_adherents', sql_allfetsel($champs_communs, 'spip_auteurs_elargis') ); // attention a ce que allfetsel() ne fasse pas depasser la taille memoire allouee a PHP si trop grand nombre de membres...
	} elseif ($_ASSOCIATION_INSCRIPTION2) { // On utilise "Inscription 2" ...mais la table auteurs_elargis est absente...
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
// v0.65 (Associaspip 1.9.2/2.0) : r37532+r38091+r37827
$GLOBALS['association_maj'][65] = array(
//@r16315+r18423
	// Optionnalisation du plugin "Inscription2" : on supprime ce qu'il reste de asso_adherents
	array('association_maj_18423'), //!\ deplace de maj_62 suite a correction dans r16315
//@r37532+r37978+r37979
	// Optionnalisation du plugin "Inscription2" : on recree propremment asso_adherents
	array('association_maj_37532'),
);

// v0.70 (Associaspip 2.0) : annonce par r16181 !
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
		$GLOBALS['tables_auxiliaires']['spip_association_metas']['field'],
		$GLOBALS['tables_auxiliaires']['spip_association_metas']['key'],
		FALSE, FALSE)) {
		include _DIR_PLUGINS . 'cfg/inc/cfg.php'; // Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus
		if (is_array($c = lire_config('association'))) {
			// recopie des vieilles meta
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
// v0.80 (Associaspip 2.0)
$GLOBALS['association_maj'][38192] = array(
//@r38109+r38190
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

// v1.00 (Associaspip 2.0)
$GLOBALS['association_maj'][39702] = array(
	// on rajoute asso_comptes.valide
	array('sql_alter', "TABLE spip_asso_comptes ADD valide TEXT DEFAULT 'oui'"),
	// on rajoute asso_comptes.maj
	array('sql_alter', "TABLE spip_asso_comptes ADD maj TIMESTAMP"),
);

// v1.00 (Associaspip 2.0)
$GLOBALS['association_maj'][42024] = array(
	// (d'apres r51766): on renomme asso_comptes.valide en asso_comptes.vu
	array('sql_alter', "TABLE spip_asso_comptes ADD vu BOOLEAN DEFAULT 0"),
	array('sql_update', 'spip_asso_comptes', array('vu' => 1), "valide='oui'"),
	array('sql_alter', "TABLE spip_asso_comptes DROP valide"),
);

// Associaspip 2.1.0 : comptabilite analytique (gestion de destinations comptables)
$GLOBALS['association_maj'][46392] = array(
	// on elimine le champ mal nomme dans r43909 (doit etre "direction" et non "destination")
	array('sql_alter', "TABLE spip_asso_plan DROP destination"),
	// et on refait la modif correctement
	array('maj_tables', 'spip_asso_plan'), // direction
	// tables listant les destinations comptables
	array('sql_create', 'spip_asso_destination',
		$GLOBALS['tables_principales']['spip_asso_destination']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination']['key'],
	TRUE, FALSE),
	// table liant les destinations aux operations
	array('sql_create', 'spip_asso_destination_op',
		$GLOBALS['tables_principales']['spip_asso_destination_op']['field'],
		$GLOBALS['tables_principales']['spip_asso_destination_op']['key'],
	FALSE, FALSE),
);

// simplification de la structure en eliminant les champs spip_asso_plan.reference et spip_asso_ventes.don
// spip_asso_plan.actif devient spip_asso_plan.active booleen
function association_maj_46779() {
	$rows = sql_select("id_plan, reference, commentaire", 'spip_asso_plan', "reference <> ''");
	while ($row = sql_fetch($rows)) { // avant d'eliminer reference de la table spip_asso_plan, on recopie sa valeur (si non vide) dans le champ commentaires
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['reference']:$row['reference'];
		sql_updateq('spip_asso_plan',
			array('commentaire' => $commentaire),
			"id_plan=".$row['id_plan']);
	}
	sql_alter("TABLE spip_asso_plan DROP reference");

	maj_tables('spip_asso_plan'); // active type_op
	// modification du type de direction, on ajoute une troisieme valeur a l'enumeration, on renomme direction en type_op essentiellement
	sql_update('spip_asso_plan', array('type_op' => 'direction'));
	sql_alter("TABLE spip_asso_plan DROP direction");
	// transforme actif en booleen plutot que texte oui/non, et renomme pour la meme raison en active
	sql_update('spip_asso_plan', array('active' => 0), "actif='non'");
	sql_alter("TABLE spip_asso_plan DROP actif");

	// avant d'eliminer don de la table spip_asso_ventes, on recopie sa valeur(si non nul) dans le champ commentaires
	$rows = sql_select("id_vente, don, commentaire", 'spip_asso_ventes', "don <> ''");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['don']:$row['don'];
		sql_updateq('spip_asso_ventes',
			array('commentaire' => $commentaire),
			"id_vente=".$row['id_vente']);
	}
	sql_alter("TABLE spip_asso_ventes DROP don");

}
$GLOBALS['association_maj'][46779] = array(
	array('association_maj_46779')
);

// suppression id_asso de la table spip_asso_membres
function association_maj_47144() {
	$rows = sql_select("id_auteur, id_asso, commentaire", 'spip_asso_membres', "id_asso <> '' AND id_asso <> 0"); // avant d'eliminer id_asso de la table spip_asso_membres, on recopie sa valeur (si non vide et non egal a 0) dans le champ commentaires
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - Ref. Int. ".$row['id_asso']:"Ref. Int. ".$row['id_asso'];
		sql_updateq('spip_asso_membres',
			array('commentaire' => $commentaire),
			"id_auteur=".$row['id_auteur']);
	}
	sql_alter("TABLE spip_asso_membres DROP id_asso");
}
$GLOBALS['association_maj'][47144] = array(
	array('association_maj_47144')
);
unset($GLOBALS['association_maj'][47144]); // finalement on garde le champ id_asso, on n'effectue donc pas la maj_47144

// revert de la 47144 pour ceux qui l'aurait effectue avant qu'elle ne soit supprimee
function association_maj_47501() {
	// on verifie si le champ id_asso existe dans la table spip_asso_membre, si oui, rien a faire, la 47144 n'a pas ete effectuee
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table_membres = $trouver_table('spip_asso_membres');
	if (!$table_membres['field']['id_asso']) { // pas de champ id_asso, il faut le restaurer
		sql_alter("TABLE spip_asso_membres ADD id_asso TEXT NOT NULL ");

		$rows = sql_select("id_auteur, commentaire", 'spip_asso_membres', "commentaire LIKE '% - Ref. Int. %' OR commentaire LIKE 'Ref. Int. %'"); // on va voir dans commentaire si on trouve un champ qui ressemble a ce que la 47144 a sauvegarde
		while ($row = sql_fetch($rows)) { // restaurer le contenu du champ id_asso
			if (preg_match('/^(.*?)( - )?Ref\. Int\. (.*)$/', $row['commentaire'], $matches)) {
				$commentaire = $matches[1];
				$id_asso = $matches[3];
				sql_updateq('spip_asso_membres',
					array('commentaire' => $commentaire, 'id_asso' => $id_asso),
					"id_auteur=".$row['id_auteur']);
			}
		}
	}
}
$GLOBALS['association_maj'][47501] = array(
	array('association_maj_47501')
);

// eliminer le champ id_achat de la table ressources car il est inutile et non utilise : rien a sauvegarder
$GLOBALS['association_maj'][47731] = array(
	array('sql_alter', "TABLE spip_asso_ressources DROP id_achat"),
);

// mise a jour integrant l'utilisation du plugin Coordonnees
function association_maj_48001() {
	$effectuer_maj = FALSE;

	// cette partie du code s'execute au premier chargement, on n'a pas encore interroge l'utilisateur sur ce qu'il veut faire de ses donnees si il en a ou il n'a pas voulu faire la maj
	if (!_request('valider_association_maj_coordonnees')) {
		// on commence par verifier si des informations de la table spip_asso_membres sont potentiellement transferable vers les tables de coordonnees
		$adresse = sql_countsel('spip_asso_membres', "adresse <> '' OR code_postal <> '' OR ville <> ''");
		$telephone = sql_countsel('spip_asso_membres', "telephone <> '' OR mobile <> ''");

		if (! ($adresse OR $telephone)) { // si on n'a pas de donnees a sauvegarder, on fait la mise a jour sans poser de question
		  spip_log("pas d'adresse ni de telephone, adoption tacite de Coordonnees");
			$effectuer_maj = TRUE;
		} else { // on a des donnees, demander a l'utilisateur ce qu'il veut en faire
			echo '<form method="post" action="">';
			echo '<fieldset><p>'._T('asso:maj_coordonnees_intro').'</p>';
			// on commence par determiner si le plugin Coordonnees est installe
			include_spip('inc/plugin');
			$liste_plugins = liste_plugin_actifs();
			$plugin_coordonnees_actif = isset($liste_plugins['COORDONNEES']);
			if (!$plugin_coordonnees_actif) {// Le plugin coordonnees n'est pas actif
				echo '<p>'._T('asso:maj_coordonnees_plugin_inactif').'</p>';
			} else { // le plugin coordonnees est actif
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="ignorer">'._T('asso:maj_coordonnees_ignorer').'</input><br/>';
				echo '<input type="radio" name="association_maj_coordonnees_traitement_data" value="merge" checked="checked">'._T('asso:maj_coordonnees_merge').'</input>';
				echo "\n<input type='hidden' name='association_maj_adresses' value='$adresse' />";
				echo "\n<input type='hidden' name='association_maj_telephones' value='$telephone' />";
			}
			echo '<p><input type="submit" name="valider_association_maj_coordonnees" value="'._T('asso:effectuer_la_maj').'"/></p>';
			echo '<p>'._T('asso:maj_coordonnees_notes').'</p></fieldset>';
			echo '</form>';
		}
	} else { // l'utilisateur veut effectuer la maj, on controle si il y a des precision quand a l'ecrasement de donnees existentes
			$choix_donnees = _request('association_maj_coordonnees_traitement_data');
			if ($choix_donnees=='merge') { // on integre les donnees d'association dans Coordonnees
				include_spip('action/editer_numero');
				include_spip('action/editer_adresse');
				include_spip('inc/modifier');

				// pre-remplissage pour les fonctions insert_numero et insert_adresse de Coordonnees
				$liens = array('objet' => 'auteur');
				$telephone = array('titre' => 'telephone');
				$mobile = array('titre' => 'mobile');
				$spip_table_numero = table_objet_sql('numero');
				$id_table_numero = id_table_objet('numero');
				$spip_table_adresse = table_objet_sql('adresse');
				$id_table_adresse = id_table_objet('adresse');

				// On recupere les coordonnees utiles
				$coordonnees_membres = sql_select('id_auteur, adresse AS voie, code_postal, ville, telephone, mobile', 'spip_asso_membres', "adresse <> '' OR mobile <> '' OR code_postal <> '' OR ville <> '' OR telephone <> ''");
				while ($data = sql_fetch($coordonnees_membres)) {
					$liens['id_objet'] = $data['id_auteur'];
					unset($data['id_auteur']);
					if ($telephone['numero'] = $data['telephone']) { // si on a un numero de telephone
						if ($id_numero =  insert_numero($liens)) {
							sql_updateq($spip_table_numero, $telephone, "$id_table_numero=$id_numero");
						}
					}
					unset($data['telephone']);
					if ($mobile['numero'] = $data['mobile']) { // si on a un numero de moblie
						if ($id_numero = insert_numero($liens)) {
							sql_updateq($spip_table_numero, $mobile, "$id_table_numero=$id_numero");
						}
					}
					unset($data['mobile']);
					if ($data['voie'] OR $data['code_postal'] OR $data['ville']) { // si on a une adresse, meme partielle
						if ($id_adresse = insert_adresse($liens)) {
							sql_updateq($spip_table_adresse, $data, "$id_table_adresse=$id_adresse");
						}
					}
				}
				echo "\n<fieldset>", intval(_request('association_maj_adresses')), _T('asso:maj_coordonnees_adresses_inserees'),
				  '<br/>', intval(_request('association_maj_telephones')), _T('asso:maj_coordonnees_numeros_inseres'), "\n</fieldset>";
			}

			$effectuer_maj = TRUE;
	}

	// on effectue si besoin la mise a jour
	if ($effectuer_maj) { // on supprime les champs de la table spip_asso_membres, ils ont deja ete sauvegarde dans les tables de Coordonnees si besoin
		sql_alter("TABLE spip_asso_membres DROP telephone");
		sql_alter("TABLE spip_asso_membres DROP mobile");
		sql_alter("TABLE spip_asso_membres DROP adresse");
		sql_alter("TABLE spip_asso_membres DROP code_postal");
		sql_alter("TABLE spip_asso_membres DROP ville");
		sql_alter("TABLE spip_asso_membres DROP email");
	} else { // la mise a jour n'est pas effectuee : on le signale dans les maj_erreur pour y revenir au prochain chargement de la page de gestion des plugins
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 48001;
	}
}
$GLOBALS['association_maj'][48001] = array(
	array('association_maj_48001')
);

// passer l'horodatage des modifications de ligne en TIMESTAMP automatique
$GLOBALS['association_maj'][48225] = array(
	array ('sql_alter', "TABLE spip_asso_categories CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_plan CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_prets CHANGE maj maj TIMESTAMP"),
	array ('sql_alter', "TABLE spip_asso_activites CHANGE maj maj TIMESTAMP"),
);

// cette mise a jour introduit un controle sur l'activation des modules de gestions des dons,
// ventes, prets, activites subordonnes a l'activation de la gestion comptable.
// la fonction de mise a jour desactive donc d'eventuels modules actifs si la gestion
// comptable n'est pas activee
function association_maj_48466() {
	include_spip('inc/association_comptabilite');
	/* on verifie la validite du plan comptable existant */
	if ($GLOBALS['association_metas']['comptes'] && !association_valider_plan_comptable()) {
		ecrire_meta('comptes', '', 'oui', 'association_metas');
		echo '<p>'._T('asso:maj_desactive_gestion_comptable').'</p>';
	}

	$desactivation = FALSE;
	if (!$GLOBALS['association_metas']['comptes']) {
		if ($GLOBALS['association_metas']['dons']) { ecrire_meta('dons', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['ventes']) { ecrire_meta('ventes', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['prets']) { ecrire_meta('prets', '', 'oui', 'association_metas'); $desactivation = TRUE; }
		if ($GLOBALS['association_metas']['activites']) { ecrire_meta('activites', '', 'oui', 'association_metas'); $desactivation = TRUE; }
	}

	// si on a desactive des modules, on le signale par un message
	if ($desactivation) echo '<p>'._T('asso:maj_desactive_modules').'</p>';

	// on en profite pour effacer des metas qui ne servent plus
	effacer_meta('comptes_stricts', 'association_metas');
	effacer_meta('indexation', 'association_metas');
}
$GLOBALS['association_maj'][48466] = array(
	array('association_maj_48466')
);

// ajout de la date d'adhesion du membre
$GLOBALS['association_maj'][51602] = array(
	array('sql_alter', "TABLE spip_asso_membres ADD date_adhesion DATE "),
);
unset($GLOBALS['association_maj'][51682]); // finalement non...

// Ces champs de configuration n'etant plus geres par defaut,
// les passer en personalises pour ceux qui les utilisent
$GLOBALS['association_maj'][52476] = array(
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_siret'" ), "nom='siret' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='siret' AND valeur=''" ),
	array('sql_update', 'spip_association_metas', array('nom' => "'meta_utilisateur_n_tva'" ), "nom='tva' AND valeur<>''" ),
	array('sql_delete', 'spip_association_metas', "nom='tva' AND valeur=''" ),
);

// mise a jour introduisant les groupes
function association_maj_53901() {
	sql_create('spip_asso_groupes',
		$GLOBALS['tables_principales']['spip_asso_groupes']['field'],
		$GLOBALS['tables_principales']['spip_asso_groupes']['key'],
	TRUE, FALSE);
	association_change_ai('asso_groupes', 100, 'id_groupe');
	sql_create('spip_asso_fonctions',
		$GLOBALS['tables_principales']['spip_asso_fonctions']['field'],
		$GLOBALS['tables_principales']['spip_asso_fonctions']['key'],
	FALSE, FALSE);

	$liste_membres_bureau = sql_select("id_auteur, fonction" ,"spip_asso_membres", "fonction <> ''"); // si on a des membres avec une fonction definie, on recupere tout...
	if (sql_count($liste_membres_bureau )) { // ...et on les met dans un groupe appele "Bureau"
		$id_groupe = sql_insertq("spip_asso_groupes", array('nom' => 'Bureau', 'affichage' => '1')); // on cree un groupe "Bureau"...
		while ($membre_bureau = sql_fetch($liste_membres_bureau)) { // ...et on y insere tous les membres qui avaient une fonction
			sql_insertq("spip_asso_fonctions", array(
				'id_groupe' => $id_groupe,
				'id_auteur' => $membre_bureau['id_auteur'],
				'fonction'  => $membre_bureau['fonction'],
			));
		}
	}

	sql_alter("TABLE spip_asso_membres DROP fonction"); // on supprime le champ fonction de la table spip_asso_membres car cela est maintenat gere dans spip_asso_fonctions
}
$GLOBALS['association_maj'][53901] = array(
	array('association_maj_53901')
);

// Creation de la table 'exercices' permettant de gerer la comptabilite en exercice comptable
// sur une 'annee civile', une 'annee scolaire', ou sur des periodes donnees
$GLOBALS['association_maj'][55177] = array(
	array('sql_create','spip_asso_exercices',
		$GLOBALS['tables_principales']['spip_asso_exercices']['field'],
		$GLOBALS['tables_principales']['spip_asso_exercices']['key'],
	TRUE, FALSE),
);

// Changer les champs FLOAT (ou parfois TEXT...) en DECIMAL
// correction de r57429 (etourderie: 2 decimales et non 4), et rajout de deux champs confirmes (spip_asso_dons)
$GLOBALS['association_maj'][57896] = array(
	array ('sql_alter', "TABLE spip_asso_categories CHANGE cotisation cotisation DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE prix_vente prix_vente DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ventes CHANGE frais_envoi frais_envoi DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE recette recette DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_comptes CHANGE depense depense DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_plan CHANGE solde_anterieur solde_anterieur DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE recette recette DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_destination_op CHANGE depense depense DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE pu pu DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_activites CHANGE montant montant DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE argent argent DECIMAL(19,2) NOT NULL"),
	array ('sql_alter', "TABLE spip_asso_dons CHANGE valeur valeur DECIMAL(19,2) NOT NULL"),
);

// Revue de la gestion des ressources et prets (debut)
$GLOBALS['association_maj'][58825] = array(
	array ('sql_alter', "TABLE spip_asso_prets DROP statut"), // ce champ ne sert pas, donc...
	array ('sql_alter', "TABLE spip_asso_prets CHANGE date_sortie date_sortie DATETIME NOT NULL"), // permettre une gestion plus fine (duree inferieure a la journee)
	array ('sql_alter', "TABLE spip_asso_prets CHANGE date_retour date_retour DATETIME NOT NULL"), // permettre une gestion plus fine (duree inferieure a la journee)
	array('sql_update', 'spip_asso_ressources', array('statut' => 1), "statut='ok'"), // nouveau statut numerique gerant simultanement les quantites
	array('sql_update', 'spip_asso_ressources', array('statut' => 0), "statut='reserve'"), // nouveau statut numerique gerant simultanement les quantites
	array('sql_update', 'spip_asso_ressources', array('statut' => -1), "statut='suspendu'"), // nouveau statut numerique gerant simultanement les quantites
/* Ne pas convertir le champ si on a des statuts personnalises... le code prevoit la compatibilite ascendante (sauf ajout de fonctionnalite incompatible) */
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE statut statut TINYTEXT NULL"), // changement temporaire pour rendre le champ nullable
	array('sql_update', 'spip_asso_ressources', array('statut' => NULL), "statut='sorti'"), // nouveau statut numerique gerant simultanement les quantites
	array ('sql_alter', "TABLE spip_asso_ressources CHANGE statut statut TINYINT NULL DEFAULT 1"), // nouvelle gestion numerique
);

// Revue de la gestion des ressources et prets (suite)
$GLOBALS['association_maj'][58824] = array(
	array('maj_tables', 'spip_asso_prets'), // prix_unitaire : comme pour asso_ventes.prix_vente) et asso_activites.montant on garde le cout de base facture car asso_ressources.pu peut changer par la suite
);

// Revue de la gestion des ressources et prets (fin)
$GLOBALS['association_maj'][58825] = array(
// on reprend ici les requetes erronnees de maj-57780 ("bienfaiteur" y est malencontreusement+logiquement nomme "donateur")
	// En liant le nom du bienfaiteur avec l'ID membre avant d'enregistrer, il faut penser a defaire cela a chaque edition pour eviter de se retrouver avec [un nom->membreXX] qui devient [[un nom->mebreXX]->membreXX] au moment de reediter. Il semble plus simple de ne pas transformer la saisie a stocker mais seulement l'affichage avec la nouvelle fonction association_formater_idnom($id,$nom) Du coup il faut quand meme retablir les champs pour ne pas reproduire a l'affichage le souci qu'on avait a l'edition...
	array('sql_update', 'spip_asso_dons', array('bienfaiteur' => "SUBSTR(bienfaiteur,2, INSTR(bienfaiteur,'->membre')-1)"), "bienfaiteur LIKE '[%->membre%]'"), // SUBSTR est compris par la plupart meme s'il y a d'autres appelations comme SUBSTRING (SQL Server et mySQL). INSTR (pour lequel Oracle accepte deux parametres optionnels de plus que mySQL) ou POSITION ou PARTINDEX ou CHARINDEX ou LOCATE ... pfff. peut-etre vaut-il mieux le faire en PHP pour etre certain d'etre independant de l'implementation SQL ?!? ou tenter l'approche par REPLACE("dans quelle chaine","sous-chaine a trouver","sous-chaine de remplacement") qui est commun a beaucoup de SGBD_SQL (mais pas dans la norme de 92 non plus si j'ai bonne memoire) ? faut voir...
	array('maj_tables', 'spip_asso_ressources'), // prix_acquisition (cout total pour mieux evaluer l'amortissement) ud (unite des durees de location)
	array('sql_update', 'spip_asso_prets AS a_p INNER JOIN spip_asso_ressources AS a_r ON a_p.id_ressource=a_r.id_ressource', array('prix_unitaire'=>'pu' ), "prix_unitaire=0"), // mettre a jour avec les tarifs actuels...
);

// normalisation des tables.champs :
//- bien distinguer par des prefixes : date, prix, etc.
$GLOBALS['association_maj'][58894] = array(
// activites
	array('maj_tables', 'spip_asso_activites'), // date_inscription (plus parlant que le mot reserve)
	array('sql_update', 'spip_asso_activites', array('date'=>'date_inscription') ),
	array('sql_alter', "TABLE spip_asso_activites DROP date "),
);

// introduction des autorisations: 1,2,3,20,21,30,31.
$GLOBALS['association_maj'][59886] = array(
	array('association_change_ai', 'asso_groupes', 100),
	array('association_maj_autorisations'),
);

// Correction de l'erreur aux niveau de l'auto-increment des id_groupe presente pour les nouvelles installations effectuees entre la r53901  et r60035
function association_maj_60038() {
	association_change_ai('asso_groupes', 100); // reset de l'auto-increment meme s'il y a deja des groupes d'ID >100 car ca ne pose pas de probleme

	$query = sql_select('id_groupe', 'spip_asso_groupes', "id_groupe<100 AND nom<>''", '', 'id_groupe'); // on verifie qu'on a des groupes d'ID <100 avec un nom <>''
	if (sql_count($query)) {
		$max_id = sql_getfetsel('MAX(id_groupe)', 'spip_asso_groupes'); // on recupere l'ID du dernier groupe cree
		$max_id = ($max_id<100)?100:$max_id;
		while ($data=sql_fetch($query)) {
			sql_updateq('spip_asso_groupes', array('id_groupe'=>($data['id_groupe']+$max_id)), 'id_groupe='.$data['id_groupe']); // on ajoute $max_id_ a l'ID des groupes selectionnes
			sql_updateq('spip_asso_fonctions', array('id_groupe'=>($data['id_groupe']+$max_id)), 'id_groupe='.$data['id_groupe']); // on fait pareil dans la table des liaisons
		}
	}

	// on verifie qu'il ne faille pas creer des groupes pour les autorisations apres avoir bouge ceux existants
	association_maj_autorisations();
}
$GLOBALS['association_maj'][60038] = array(
	array('association_maj_60038')
);

// ajout de la caution a la gestion des ressources
$GLOBALS['association_maj'][62712] = array(
	array('maj_tables', 'spip_asso_ressources'), // prix_caution
	array('maj_tables', 'spip_asso_prets'), // date_caution0 date_caution1 date_reservation prix_caution
);

// ajout de nouvelles autorisations: 10,32,33.
$GLOBALS['association_maj'][66289] = array(
	array('association_maj_autorisations'),
);

// normalisation des tables.champs :
//- renommer le champ "commentaires" en "commentaire" pour homogeniser les traitement et l'affichage
//- homogeniser le nommage de la cle secondaire sur les membres/auteurs pour ne plus s'arracher les cheveux
$GLOBALS['association_maj'][66346] = array(
// categories
	array('maj_tables', 'spip_asso_categories'), // commentaire
	array('sql_update', 'spip_asso_categories', array('commentaire'=>'commentaires') ),
	array('sql_alter', "TABLE spip_asso_categories DROP commentaires"),
// groupes
	array('maj_tables', 'spip_asso_groupes'), // commentaire
	array('sql_update', 'spip_asso_groupes', array('commentaire'=>'commentaires') ),
	array('sql_alter', "TABLE spip_asso_groupes DROP commentaires"),
// dons
	array('maj_tables', 'spip_asso_dons'), // id_auteur
	array('sql_update', 'spip_asso_dons', array('id_auteur'=>'id_adherent') ),
	array('sql_alter', "TABLE spip_asso_dons DROP id_adherent"),
// activites
	array('maj_tables', 'spip_asso_activites'), // id_auteur
	array('sql_update', 'spip_asso_activites', array('id_auteur'=>'id_adherent') ),
	array('sql_alter', "TABLE spip_asso_activites DROP id_adherent"),
// prets
	array('maj_tables', 'spip_asso_prets'), // id_auteur
	array('sql_update', 'spip_asso_prets', array('id_auteur'=>'id_emprunteur') ),
	array('sql_alter', "TABLE spip_asso_prets DROP id_emprunteur"),
);

// ajout de nouvelles autorisations: 11,12,40,41,50,51.
$GLOBALS['association_maj'][66769] = array(
	array('association_change_ai', 'asso_groupes', 100),
	array('association_maj_autorisations'),
);

// normalisation des tables.champs :
//- bien distinguer par des prefixes : date, prix, etc.
//- homogeniser le nommage de cle secondaire (id_<objet>) pour etre moins ambigu
//- homogeniser l'appelation du champ de nom alternatif pour simplifier le code
$GLOBALS['association_maj'][66804] = array(
// categories
	array('maj_tables', 'spip_asso_categories'), // prix_cotisation
	array('sql_update', 'spip_asso_categories', array('prix_cotisation'=>'cotisation') ),
	array('sql_alter', "TABLE spip_asso_categories DROP cotisation"),
// membres
	array('maj_tables', 'spip_asso_membres'), // id_categorie date_validite
	array('sql_update', 'spip_asso_membres', array('date_validite'=>'validite') ),
	array('sql_alter', "TABLE spip_asso_membres DROP validite"),
	array('sql_update', 'spip_asso_membres', array('id_categorie'=>'categorie') ),
#	array('sql_alter', "TABLE spip_asso_membres DROP categorie"),
// activites
	array('maj_tables', 'spip_asso_activites'), // quantite prix_activite
	array('sql_update', 'spip_asso_activites', array('quantite'=>'inscrits') ),
	array('sql_alter', "TABLE spip_asso_activites DROP inscrits"),
	array('sql_alter', "TABLE spip_asso_activites ADD prix_activite DECIMAL(19,2) NOT NULL DEFAULT 0"),
	array('sql_update', 'spip_asso_activites', array('prix_activite'=>'montant') ),
	array('sql_alter', "TABLE spip_asso_activites DROP montant"),
// exercices
	array('maj_tables', 'spip_asso_exercices'), // date_debut date_fin
	array('sql_update', 'spip_asso_exercices', array('date_debut'=>'debut') ),
	array('sql_alter', "TABLE spip_asso_exercices DROP debut"),
	array('sql_update', 'spip_asso_exercices', array('date_fin'=>'fin') ),
	array('sql_alter', "TABLE spip_asso_exercices DROP fin"),
// ventes
	array('maj_tables', 'spip_asso_ventes'), // id_auteur nom
	array('sql_update', 'spip_asso_ventes', array('id_auteur'=>'id_acheteur') ),
	array('sql_alter', "TABLE spip_asso_ventes DROP id_acheteur"),
	array('sql_update', 'spip_asso_ventes', array('nom'=>'acheteur') ),
	array('sql_alter', "TABLE spip_asso_ventes DROP acheteur"),
// dons
	array('maj_tables', 'spip_asso_dons'), // nom
	array('sql_update', 'spip_asso_dons', array('nom'=>'bienfaiteur') ),
	array('sql_alter', "TABLE spip_asso_dons DROP bienfaiteur"),
);

// normalisation des tables.champs :
//- bien distinguer par des prefixes : date, prix, etc.
$GLOBALS['association_maj'][66942] = array(
// journaux comptables
	array('maj_tables', 'spip_asso_comptes'), // date_operation
	array('sql_update', 'spip_asso_comptes', array('date_operation'=>'date') ),
	array('sql_alter', "TABLE spip_asso_comptes DROP date"),
);

// reorganisation des autorisations
$GLOBALS['association_maj'][67499] = array(
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>13), 'id_groupe=12' ), // lister livres de comptes et etats comptables
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>43), 'id_groupe=41' ), // lister dons
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>41), 'id_groupe=40' ), // editer dons
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>53), 'id_groupe=51' ), // lister ventes
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>51), 'id_groupe=50' ), // editer ventes
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>66), 'id_groupe=63' ), // lister prets
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>64), 'id_groupe=62' ), // editer prets
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>63), 'id_groupe=61' ), // lister ressources
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>61), 'id_groupe=60' ), // editer ressources
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>23), 'id_groupe=21' ), // voir profil association
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>21), 'id_groupe=20' ), // editer profil association
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>35), 'id_groupe=33' ), // relancer membres
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>33), 'id_groupe=31' ), // voir profils membres
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>31), 'id_groupe=30' ), // editer membres
	array('sql_update', 'spip_asso_fonctions', array('id_groupe'=>30), 'id_groupe=32' ), // synchroniser membres
);

// ajout de nouvelles autorisations: 61,63,64,66,73,74,76.
$GLOBALS['association_maj'][67500] = array(
	array('association_maj_autorisations'),
);

// normalisation des tables.champs :
//- homogenisation du nommage des prix unitaires que multiplient une quantite
$GLOBALS['association_maj'][67570] = array(
// ventes d'articles
	array('maj_tables', 'spip_asso_ventes'), // prix_unitaire
	array('sql_update', 'spip_asso_ventes', array('prix_unitaire'=>'prix_vente') ),
	array('sql_alter', "TABLE spip_asso_ventes DROP prix_vente"),
// participations aux activites
	array('maj_tables', 'spip_asso_activites'), // prix_unitaire
	array('sql_update', 'spip_asso_activites', array('prix_unitaire'=>'prix_activite'), 'quantite=0' ),
	array('sql_update', 'spip_asso_activites', array('prix_unitaire'=>'prix_activite/quantite'), 'quantite<>0' ),
	array('sql_alter', "TABLE spip_asso_activites DROP prix_activite"),
);

function association_maj_71776() {
	if ( sql_countsel('spip_association_metas', "nom='unique_dest' AND valeur='on' ") )
		sql_update('spip_association_metas', array('valeur'=>'1'), "nom='destinations'");
	if ( sql_countsel('spip_association_metas', "nom='destinations' AND valeur='on' ") )
		sql_update('spip_association_metas', array('valeur'=>'2'), "nom='destinations'");
}
$GLOBALS['association_maj'][71780] = array(
	array('association_maj_71776'), // conversion/migration
	array('sql_delete', 'spip_association_metas',  "nom='unique_dest'"), // supprimer l'entree devenue inutile
	array('sql_alter', "TABLE  spip_asso_groupes_liaisons RENAME TO spip_asso_fonctions"),
);

$GLOBALS['association_maj'][72929] = array(
	// liaison des ressources aux mots-cles
	array('maj_tables', 'spip_asso_ressources'), // + champ : id_mot
);


?>