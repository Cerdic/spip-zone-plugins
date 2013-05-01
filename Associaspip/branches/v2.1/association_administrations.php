<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

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
function association_vider_tables($nom_meta, $table){
	global $association_tables_principales, $association_tables_auxiliaires;
	$ok = TRUE;
	foreach($association_tables_principales as $nom => $desc)
		$ok &= sql_drop_table($nom);
	foreach($association_tables_auxiliaires as $nom => $desc)
		$ok &= sql_drop_table($nom);
	if ($ok)
		spip_log("Associaspip correctement desinstalle");
	else
		spip_log("Associaspip partiellement desinstalle");
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
		} else $n = 0;
		$GLOBALS['association_metas']['base_version'] = $n;
	} else
		$n = $GLOBALS['association_metas']['base_version'];

	// Upgrade proprement dit
	effacer_meta('association_base_version');
	spip_log("association upgrade $table.$meta: $n =>> $courante");
	if (!$n) {
		include_spip('base/create');
		alterer_base($GLOBALS['association_tables_principales'], $GLOBALS['association_tables_auxiliaires'], FALSE);
		ecrire_meta($meta, $courante, NULL, $table);
		return 0; // Reussite supposee ! (car "alterer_base" n'a pas de retour)
	} else {
		// compatibilite avec les numeros de version non entiers (repris de r13971)
		$installee = ($n > 1) ? $n : ($n * 100);
		$GLOBALS['association_maj_erreur'] = 0;
		if ($courante > $installee) {
			include_spip('base/upgrade');
			$n = maj_while($installee, $courante, $GLOBALS['association_maj'], $meta, $table);
			$n = $n ? $n[0] : $GLOBALS['association_maj_erreur'];
			// signaler que les dernieres MAJ sont a refaire
			if ($n)
				ecrire_meta($meta, $n-1, '', $table);
		}
		return $GLOBALS['association_maj_erreur'];
	}
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
	array('sql_alter', "TABLE spip_asso_banques ADD id_banque INT NOT NULL "),
	array('sql_update', 'spip_asso_banques', array('id_banque' => 'id_financier'), 1),
	array('sql_alter', "TABLE spip_asso_banques DROP id_financier"),
	array('sql_alter', "TABLE spip_asso_banques ADD PRIMARY KEY(id_banque) "),
	array('sql_alter', "TABLE spip_asso_banques MODIFY id_banque INT NOT NULL AUTO_INCREMENT "), //!\ non-portable...
	// et on ajoute une entree caisses
	array('sql_insert', 'spip_asso_banques', "(code)", "('caisse')"),
	// et on ajoute un champ date
	array('sql_alter', "TABLE spip_asso_banques ADD \"date\" DATE NOT NULL "), //!\ 'date' fait partir des mots reserves du SQL... https://dev.mysql.com/doc/refman/4.1/en/reserved-words.html https://dev.mysql.com/doc/refman/4.1/en/server-sql-mode.html#sqlmode_ansi_quotes
//<r12523
/*
	// les livres de comptes ? (table supprimee par r13839)
	array('sql_create','spip_asso_livres',
		$association_tables_principales['spip_asso_livres']['field'],
	    $association_tables_principales['spip_asso_livres']['key'],
	    TRUE), // champs : id_livre, valeur, libelle
	// initialisation du livre des comptes (donnees plus inserees des v0.40)
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('cotisation', 'Cotisations'), ('vente', 'Ventes'), ('don', 'Dons'), ('achat', 'Achats'), ('divers', 'Divers'), ('activite', 'Activités') "),
*/
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
	// validation des ecritures comptables
	array('sql_alter',"TABLE spip_asso_comptes ADD valide TEXT NOT NULL "),
	// nouvelle table des participations aux activites
	array('sql_create','spip_asso_activites',
		$association_tables_principales['spip_asso_activites']['field'],
	    $association_tables_principales['spip_asso_activites']['key'],
	    TRUE), // champs : id_activite, id_evenement, nom, id_adherent, accompagne, inscrits, date, telephone, adresse, email, commentaire, montant, date_paiement, statut, maj
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
	// la cle change en consequence
	array('sql_alter', "TABLE spip_asso_plan ADD id_plan INT NOT NULL"),
	array('sql_update', 'spip_asso_plan', array('id_plan' => 'id_banque'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP id_banque"),
	array('sql_alter', "TABLE spip_asso_plan ADD PRIMARY KEY(id_plan) "),
	array('sql_alter', "TABLE spip_asso_plan MODIFY id_plan INT NOT NULL AUTO_INCREMENT "), //!\ non-portable...
	// le champ du solde anterieur est renomme de facon plus parlante
	array('sql_alter', "TABLE spip_asso_plan ADD solde_anterieur FLOAT NOT NULL DEFAULT 0"),
	array('sql_update', 'spip_asso_plan', array('solde_anterieur' => 'solde'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP solde"),
	// le champ de la date anterieure est renomme de facon plus parlante
	array('sql_alter', "TABLE spip_asso_plan ADD date_anterieure DATE NOT NULL DEFAULT '0000-00-00' "),
	array('sql_update', 'spip_asso_plan', array('date_anterieure' => 'date'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP date"),
	// et un nouveau champ de classe comptable
	array('sql_alter', "TABLE spip_asso_plan ADD classe TEXT NOT NULL"),
);

function association_maj_16181(){
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // le plugin "Inscription 2" est la...
		// comme dit r38258 : il faut migrer les donnees avant de detruire les champs (r16186) ou la table (r38192)
		$champs = array('id_auteur', 'nom', 'prenom', 'sexe', 'fonction', 'email', 'numero', 'rue', 'cp', 'ville', 'telephone', 'portable', 'montant', 'relance', 'divers', 'remarques', 'vignette', 'naissance', 'profession', 'societe', 'identifiant', 'passe', 'creation', 'secteur', 'publication'); // champs pris en compte dans r16186... (on met id_auteur en tete par rapport a r16249 plus loin)
		$liste_maj = sql_select(implode(', ', $champs), 'spip_adherents');
		while ($maj = sql_fetsel($liste_maj) ) { //!\ I2 en s'installant reprend bien les auteurs ; il faut songer a completer par les informations sur les adherents
			sql_updateq('spip_auteurs_elargis', $maj, 'id_auteur='.$maj['id_auteur']);
		}
		// asso_adherents perd les champs migres...
		unset($champs[0]); //@r16249 : ...sauf 'id_auteur' pour completer r37532 (d'ou on le place en premier...)
		foreach ($champs as $champ) { //@r16186
			sql_alter("TABLE spip_asso_adherents DROP $champ");
		}
	} elseif (_ASSOCIATION_INSCRIPTION2) { // le plugin "Inscription 2" n'est pas la... mais on n'utilise pas sa simulation..?
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 62;
		return;
	}
}
// v0.62 (Associaspip 1.9.2) : r16181 annonce 0.70 !
$GLOBALS['association_maj'][62] = array(
//@r16186
	// asso_adherents.statut devient asso_adherents.statut_relance (nom utilise par I2 si je comprends bien http://zone.spip.org/trac/spip-zone/browser/tags/inscription2_192/base/inscription2_installer.php#L70 ?)
	array('sql_alter', "TABLE spip_asso_adherents ADD statut_relance TEXT NOT NULL"),
	array('sql_update', 'spip_asso_adherents', array('statut_relance' => 'statut'), "statut<>''")
	array('sql_alter', "TABLE spip_asso_adherents DROP statut"),
	// migration vers "Inscription2"
	array('association_maj_16181'),
	// asso_activites.accompagne se decompose en asso_activites.membres + asso_activites.non_membres
	array('sql_alter', "TABLE spip_asso_activites DROP accompagne"), //cf. v0.50
//@r18150
	// possibilite d'avoir des references comptables actives ou non
	array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL "),
);

function association_maj_18423(){
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // le plugin "Inscription 2" est la...
		// asso_adherents perd les champs : id_adherent, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4
		sql_drop_table('spip_asso_adherents'); // (suppression effective dans r20002 et) on utilise spip_auteurs_elargis jusqu'a la resurection en r37532
	} elseif (_ASSOCIATION_INSCRIPTION2) { // le plugin "Inscription 2" n'est pas la... mais on n'utilise pas sa simulation..?
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

function association_maj_37532(){
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // le plugin "Inscription 2" est la...
		// champs manquants dans auteurs_elargis qui provoquaient des disfonctionnement signales sur le forum
		sql_alter("TABLE spip_auteurs_elargis ADD validite DATE NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant FLOAT NOT NULL default '0'");
		sql_alter("TABLE spip_auteurs_elargis ADD date DATE NOT NULL default '0000-00-00' ");
		// comme dit r38258 : il faut migrer les donnees avant de detruire les champs (r16315) ou la table (r38192)
		// on utilise des @sql_... suite au deplacement en maj_64 : ca va hurler chez ceux qui avaient fait la maj_62 avant correction...
		$champs = array('id_auteur', 'montant', 'date', 'categorie'); // champs pris en compte dans r16315... (sauf 'statut_relance' deja supprime dans r16186)
		$liste_maj = @sql_select(implode(', ', $champs), 'spip_adherents');
		while ($maj = sql_fetsel($liste_maj) ) { //!\ I2 en s'installant reprend bien les auteurs ; il faut songer a completer par les informations sur les adherents
			@sql_updateq('spip_auteurs_elargis', $maj, 'id_auteur='.$maj['id_auteur']);
		}
		// asso_adherents perd les champs migres...
		foreach ($champs as $champ) { //@r16315
			@sql_alter("TABLE spip_asso_adherents DROP $champ");
		}
	} elseif (_ASSOCIATION_INSCRIPTION2) { // le plugin "Inscription 2" n'est pas la... mais on n'utilise pas sa simulation..?
		if (!$GLOBALS['association_maj_erreur'])
			$GLOBALS['association_maj_erreur'] = 64;
		return;
	}
}
// v0.64 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][64] = array(
//@r25365
	array('sql_alter',"TABLE spip_asso_prets ADD id_ressource VARCHAR(20) NOT NULL "),
//@r37532
	// Optionnalisation du plugin "Inscription2" qui n'est plus maintenu...
	array('association_maj_37532'),
	array('association_maj_16181'), //!\ deplace de maj_62 suite a correction dans r37532
	// asso_adherents reloaded
	array('@sql_create','spip_asso_adherents',
		$association_tables_principales['spip_asso_adherents']['field'],
	    $association_tables_principales['spip_asso_adherents']['key'],
	    FALSE), // re-creation (cf. maj_20002) avec les champs : id_adherent, nom, prenom, sexe, fonction, email, validite, numero, rue, cp, ville, telephone, portable, montant, date, statut, relance, divers, remarques, vignette, id_auteur, id_asso, categorie, naissance, profession, societe, identifiant, passe, creation, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4, secteur, publication, statut_interne, commentaire
//@r37???
	// asso_adherents.statut_relance devient asso_adherents.statut_interne
	array('sql_alter', "TABLE spip_asso_adherents ADD statut_interne TEXT NOT NULL"),
	array('sql_update', 'spip_asso_adherents', array('statut_interne' => 'statut_relance'), 1),
	array('sql_alter', "TABLE spip_asso_adherents DROP statut_relance"),
);

// v0.70 (Associaspip 2.0)
$GLOBALS['association_maj'][37869] = array(
//@r37869
	// spip_asso_adherents.nom devient spip_asso_adherents.nom_famille
	array('sql_alter', "TABLE spip_asso_adherents ADD nom_famille TEXT NOT NULL "),
	array('sql_update', 'spip_asso_adherents', array('nom_famille' => 'nom'), "nom<>''"),
	array('sql_alter', "TABLE spip_asso_adherents DROP nom"),
);

// Recopie des metas geree par CFG dans la table asso_meta
function association_maj_38190() {
	global $association_tables_auxiliaires;
	if (sql_create('spip_asso_metas',
		$association_tables_auxiliaires['spip_asso_metas']['field'],
		$association_tables_auxiliaires['spip_asso_metas']['key'],
		FALSE, FALSE)) {
		// Il faut charger a la main ses fichiers puisque plugin.xml ne le demande plus
		include _DIR_PLUGINS . 'cfg/inc/cfg.php';
		if (is_array($c = lire_config('association'))) {
			foreach($c as $k => $v) {
				ecrire_meta($k, $v, 'oui', 'association_metas');
			}
			// effacer les vieilles meta
			effacer_meta('association');
			effacer_meta('asso_base_version');
			effacer_meta('association_base_version');
		}
	} else
		spip_log("maj_38190: echec de  la creation de spip_asso_metas");
}
// v0.70 (Associaspip 2.0)
$GLOBALS['association_maj'][38192] = array(
//@r38190
	// Utilisation de asso_metas ! Exit le plugin "CFG"
	array('association_maj_38190'),
);

// v1.00 (Associaspip 2.0)
$GLOBALS['association_maj'][38258] = array(
	// spip_asso_adherents devient spip_asso_membres
	array('sql_alter', "TABLE spip_asso_adherents RENAME TO spip_asso_membres"), // a noter que spip_asso_adherents n'etait plus utilise depuis r20002+r20034 ! puis est revenu en r37532 dans l'idee de pouvoir suppleer spip_auteurs_elargis !
	// ...et la cle de asso_membres change...
	array('sql_alter', "TABLE spip_asso_membres DROP id_adherent"), // plus utilise depuis r20076
	array('sql_alter', "TABLE spip_asso_adherents  ADD PRIMARY KEY (id_auteur) "), // ce champ est NOT NULL et unique (c'est en fait une cle etrangere auteurs.id_auteur)
	// asso_adherents.numero et asso_adherents.rue fusionnent en asso_membres.adresse
	array('sql_alter', "TABLE spip_asso_membres ADD adresse TEXT NOT NULL "),
	array('sql_update', 'spip_asso_membres', array('adresse' => "CONCAT(numero,CONCAT(', ',rue))") ), // l'ordre inverse est possible aussi, et dans les deux cas on peut ne pas avoir de virgule ou alors un autre symbole :-S
#	array('sql_alter', "TABLE spip_asso_membres DROP numero"), // garder pour ceux qui veulent refaire la requete a leur sauce
#	array('sql_alter', "TABLE spip_asso_membres DROP rue"), // garder pour ceux qui veulent refaire la requete a leur sauce
	// spip_asso_adherents.cp devient spip_asso_membres.code_postal
	array('sql_alter', "TABLE spip_asso_membres ADD code_postal TEXT NOT NULL "),
	array('sql_update', 'spip_asso_membres', array('code_postal' => 'cp'), "cp<>''"),
	array('sql_alter', "TABLE spip_asso_membres DROP cp"),
	// spip_asso_adherents.portable devient spip_asso_membres.mobile
	array('sql_alter', "TABLE spip_asso_membres ADD mobile TINYTEXT NOT NULL "),
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
	array('sql_alter', "TABLE spip_asso_comptes ADD valide TEXT DEFAULT 'oui' "),
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

/* cette mise a jour comporte une erreur: sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL default 'credit'"); le champ doit etre nomme direction et non destination */
function association_maj_43909() {
	global $association_tables_principales;

	sql_alter("TABLE spip_asso_plan ADD destination ENUM('credit','debit') NOT NULL DEFAULT 'credit'");
	sql_create('spip_asso_destination',
		$association_tables_principales['spip_asso_destination']['field'],
		$association_tables_principales['spip_asso_destination']['key']);
	sql_create('spip_asso_destination_op',
		$association_tables_principales['spip_asso_destination_op']['field'],
		$association_tables_principales['spip_asso_destination_op']['key']);
}

$GLOBALS['association_maj'][43909] = array(array('association_maj_43909'));
// pour empecher l'execution de code fautif tout en gardant trace
unset($GLOBALS['association_maj'][43909]);

// repare l'erreur commise sur la maj 43909
$GLOBALS['association_maj'][46392] = array(
	// on elimine le champ mal nomme
	array('sql_alter', "TABLE spip_asso_plan DROP destination"),
	// et on refait la modif correctement: ca risque d'entrainer des erreurs SQL mais c'est pas grave
	array('sql_alter', "TABLE spip_asso_plan ADD direction ENUM('credit','debit') NOT NULL default 'credit'"),
	array('sql_create', 'spip_asso_destination',
		$association_tables_principales['spip_asso_destination']['field'],
		$association_tables_principales['spip_asso_destination']['key'],
		TRUE), // champs : id_destination, intitule, commentaire
	array('sql_create', 'spip_asso_destination_op',
		$association_tables_principales['spip_asso_destination_op']['field'],
		$association_tables_principales['spip_asso_destination_op']['key'],
		FALSE), // champs : id_dest_op, id_compte, id_destination, recette, depense
);

function association_maj_46779() {
	global $association_tables_principales;
	/* avant d'eliminer reference de la table spip_asso_plan, on recopie sa valeur(si non null) dans le champ commentaires */
	$rows = sql_select("id_plan, reference, commentaire", 'spip_asso_plan', "reference <> ''");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['reference']:$row['reference'];
		sql_updateq('spip_asso_plan',
			array('commentaire' => $commentaire),
			"id_plan=".$row['id_plan']);
	}
	sql_alter("TABLE spip_asso_plan DROP reference");

	/* modification du type de direction, on ajoute une troisieme valeur a l'enumeration, on renomme direction en type_op essentiellement
	pour des raisons de compatibilite avec les differentes bases de donnees supportees par SPIP (impossible d'utiliser ALTER COLUMN ou MODIFY)*/
	sql_alter("TABLE spip_asso_plan ADD type_op ENUM('credit','debit','multi') NOT NULL default 'multi'");
	sql_update('spip_asso_plan', array('type_op' => 'direction'));
	sql_alter("TABLE spip_asso_plan DROP direction");

	/* transforme actif en booleen plutot que texte oui/non, et renomme pour la meme raison en active */
	sql_alter("TABLE spip_asso_plan ADD active BOOLEAN default 1");
	sql_update('spip_asso_plan', array('active' => 0), "actif='non'");
	sql_alter("TABLE spip_asso_plan DROP actif");

	/* avant d'eliminer don de la table spip_asso_ventes, on recopie sa valeur(si non null) dans le champ commentaires */
	$rows = sql_select("id_vente, don, commentaire", 'spip_asso_ventes', "don <> ''");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - ".$row['don']:$row['don'];
		sql_updateq('spip_asso_ventes',
			array('commentaire' => $commentaire),
			"id_vente=".$row['id_vente']);
	}
	sql_alter("TABLE spip_asso_ventes DROP don");

}
$GLOBALS['association_maj'][46779] = array(array('association_maj_46779'));

function association_maj_47144() {
	global $association_tables_principales;
	/* avant d'eliminer id_asso de la table spip_asso_membres, on recopie sa valeur(si non null et non egal a 0) dans le champ commentaires */
	$rows = sql_select("id_auteur, id_asso, commentaire", 'spip_asso_membres', "id_asso <> '' AND id_asso <> 0");
	while ($row = sql_fetch($rows)) {
		$commentaire = $row['commentaire']?$row['commentaire']." - Ref. Int. ".$row['id_asso']:"Ref. Int. ".$row['id_asso'];
		sql_updateq('spip_asso_membres',
			array('commentaire' => $commentaire),
			"id_auteur=".$row['id_auteur']);
	}
	sql_alter("TABLE spip_asso_membres DROP id_asso");
}
$GLOBALS['association_maj'][47144] = array(array('association_maj_47144'));
// finalement on garde le champ id_asso, on n'effectue donc pas la maj_47144
unset($GLOBALS['association_maj'][47144]);

// revert de la 47144 pour ceux qui l'aurait effectue avant qu'elle ne soit supprimee
function association_maj_47501() {
	global $association_tables_principales;
	/* on verifie si le champ id_asso existe dans la table spip_asso_membre, si oui, rien a faire, la 47144 n'a pas ete effectuee */
	$trouver_table = charger_fonction('trouver_table', 'base');
	$table_membres = $trouver_table('spip_asso_membres');
	if (!$table_membres['field']['id_asso']) { /* pas de champ id_asso, il faut le restaurer */
		sql_alter("TABLE spip_asso_membres ADD id_asso TEXT NOT NULL AFTER id_auteur");

		/* on va voir dans commentaire si on trouve un champ qui ressemble a ce que la 47144 a sauvegarde */
		$rows = sql_select("id_auteur, commentaire", 'spip_asso_membres', "commentaire LIKE '% - Ref. Int. %' OR commentaire LIKE 'Ref. Int. %'");
		while ($row = sql_fetch($rows)) {
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

// eliminer le champ id_achat de la table ressources car il est inutile et non utilise, rien a sauvegarder
$GLOBALS['association_maj'][47731] = array(
	array('sql_alter', "TABLE spip_asso_ressources DROP id_achat"),
);

?>