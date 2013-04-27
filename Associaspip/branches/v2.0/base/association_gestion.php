<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
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
	if ($ok)
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

// v0.50 (Associaspip 1.9.1) :
//@r12523 on les table(champs) : asso_adherents@maj_64 asso_categories(id_categorie, valeur, libelle, duree, cotisation, commentaire, maj) asso_comptes(id_compte, recette, depense, justification, imputation, journal, id_journal, maj) asso_financiers@maj_21 asso_profil(id_profil, nom, numero, rue, cp, ville, telephone, siret, declaration, prefet, president, maj, mail) asso_livres(id_livre, valeur, libelle, maj) asso_activites(id_activite, id_evenement, nom, id_adherent, accompagne, inscrits, date, telephone, adresse, email, commentaire, montant, date_paiement, statute, maj) asso_ventes(id_vente, article, code, acheteur, quantite, date_vente, date_envoi, don, prix_vente, frais_envoi, commentaire)
function association_maj_0($version, $meta, $table){
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

// v0.20? (Associaspip 1.9.0)
$GLOBALS['association_maj'][20] = array(
//<r12523
	// les livres de comptes ? ancienne table des financiers ? (table supprimee par v0.60)
	array('sql_create','spip_asso_livres',
		$association_tables_principales['spip_asso_livres']['field'],
	    $association_tables_principales['spip_asso_livres']['key'],
	    TRUE), // champs : id_livre, valeur, libelle
	// initialisation du livre des comptes (donnees plus inserees des v0.40)
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('cotisation', 'Cotisations')"),
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('vente', 'Ventes')"),
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('don', 'Dons')"),
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('achat', 'Achats')"),
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('divers', 'Divers')"),
	array('sql_insert', 'spip_asso_livres', "(valeur, libelle)", "('activite', 'Activit�s')"),
);

// v0.30 (Associaspip 1.9.1) : champ autorisation de publication, liste de l'equipe dirigeante, etc.
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

$GLOBALS['association_maj'][30] = array(
//<r12524
	// asso_financiers devient asso_banques
	array('sql_alter', "TABLE spip_asso_financiers RENAME TO spip_asso_banques"),
	// et sa cle change en consequence
	array('sql_alter', "TABLE spip_asso_banques ADD id_banque INT NOT NULL "),
	array('sql_update', 'spip_asso_banques', array('id_banque' => 'id_financier'), 1),
	array('sql_alter', "TABLE spip_asso_banques DROP id_financier"),
	// et on ajoute une entree caisses
	array('sql_insert', 'spip_asso_financiers', "(code)", "('caisse')"),
	// et on ajoute un champ date
	array('sql_alter', "TABLE spip_asso_banques ADD date DATE NOT NULL "),
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
	array('sql_alter',"TABLE `spip_asso_comptes` ADD `valide` TEXT NOT NULL ")
);

// v0.50 (Associaspip 1.9.1)
$GLOBALS['association_maj'][50] = array(
//<r12524
	array('sql_alter',"TABLE spip_asso_profil ADD indexation TEXT NOT NULL "),
	array('sql_alter',"TABLE spip_asso_activites ADD membres TEXT NOT NULL "),
	array('sql_alter',"TABLE spip_asso_activites ADD non_membres TEXT NOT NULL "),
);

// v0.60 (Associaspip 1.9.2)
$GLOBALS['association_maj'][60] = array(
//@r12530
	// Passage au plugin "CFG"...
	array('sql_insertq', 'spip_meta', array('nom'=>'association', 'valeur'=>serialize(sql_allfetsel('*','spip_profil')) )),  // les entrees de asso_profil sont serialisees par "CFG" dans meta.nom=association ...requete avec "sql_allfetsel()" a verifier...
	array('sql_drop_table', 'spip_asso_profil'), // ...et asso_profil  ne sert plus...
//@r13839
	array('sql_drop_table', 'spip_asso_livres'),
	array('sql_create','spip_asso_ressources',
		$association_tables_principales['spip_asso_ressources']['field'],
	    $association_tables_principales['spip_asso_ressources']['key'],
	    TRUE), // champs : id_ressource, code, intitule, date_acquisition, id_achat, pu, statut, commentaire, maj
	// et une nouvelle table pour gerer la gestion de leurs prets apparait
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
	array('sql_update', 'spip_asso_plan', array('id_plan' => 'id_financier'), 1),
	array('sql_alter', "TABLE spip_asso_plan DROP id_financier"),
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
	// suppression de asso_profil.nom=indexation
	array('sql_delete', 'spip_asso_profil', "nom=indexation"),
);

// v0.61 (Associaspip 1.9.2) : r16181 annonce 0.70 !
$GLOBALS['association_maj'][62] = array(
//@r16181
	// comme dit r38258 : il faut migrer les donnees avant de detruire les champs (r16186+r16315) ou la table (r38192) //!\ mais quand est-ce que Associaspip y exporte ses donnees ?!?
//@r16186
	// asso_adherents perd des champs : passage initie par r16181 a "Inscription 2"
	array('sql_alter', "TABLE spip_asso_adherents DROP nom"),
	array('sql_alter', "TABLE spip_asso_adherents DROP prenom"),
	array('sql_alter', "TABLE spip_asso_adherents DROP sexe"),
	array('sql_alter', "TABLE spip_asso_adherents DROP fonction"),
	array('sql_alter', "TABLE spip_asso_adherents DROP email"),
	array('sql_alter', "TABLE spip_asso_adherents DROP numero"),
	array('sql_alter', "TABLE spip_asso_adherents DROP rue"),
	array('sql_alter', "TABLE spip_asso_adherents DROP cp"),
	array('sql_alter', "TABLE spip_asso_adherents DROP ville"),
	array('sql_alter', "TABLE spip_asso_adherents DROP telephone"),
	array('sql_alter', "TABLE spip_asso_adherents DROP portable"),
	array('sql_alter', "TABLE spip_asso_adherents DROP montant"),
	array('sql_alter', "TABLE spip_asso_adherents DROP relance"),
	array('sql_alter', "TABLE spip_asso_adherents DROP divers"),
	array('sql_alter', "TABLE spip_asso_adherents DROP remarques"),
	array('sql_alter', "TABLE spip_asso_adherents DROP vignette"),
	array('sql_alter', "TABLE spip_asso_adherents DROP id_auteur"),
	array('sql_alter', "TABLE spip_asso_adherents DROP naissance"),
	array('sql_alter', "TABLE spip_asso_adherents DROP profession"),
	array('sql_alter', "TABLE spip_asso_adherents DROP societe"),
	array('sql_alter', "TABLE spip_asso_adherents DROP identifiant"),
	array('sql_alter', "TABLE spip_asso_adherents DROP passe"),
	array('sql_alter', "TABLE spip_asso_adherents DROP creation"),
	array('sql_alter', "TABLE spip_asso_adherents DROP secteur"),
	array('sql_alter', "TABLE spip_asso_adherents DROP publication"),
	// asso_adherents.statut devient asso_adherents.statut_relance
	array('sql_alter', "TABLE spip_asso_adherents ADD statut_relance TEXT NOT NULL"),
	array('sql_update', 'spip_asso_adherents', array('statut_relance' => 'statut'), "statut<>''"),
	array('sql_alter', "TABLE spip_asso_adherents DROP statut"),
	// asso_activites.accompagne se decompose en asso_activites.membres + asso_activites.non_membres
	array('sql_alter', "TABLE spip_asso_activites DROP accompagne"),
	array('sql_alter', "TABLE spip_asso_activites ADD membres TEXT NOT NULL"),
	array('sql_alter', "TABLE spip_asso_activites ADD non_membres TEXT NOT NULL"),
//@r16249
	array('sql_alter',"TABLE spip_asso_adherents ADD id_auteur INT NOT NULL "),
//@r16315
	array('sql_alter', "TABLE spip_asso_adherents DROP montant"),
	array('sql_alter', "TABLE spip_asso_adherents DROP date"),
	array('sql_alter', "TABLE spip_asso_adherents DROP categorie"),
	array('sql_alter', "TABLE spip_asso_adherents DROP statut_relance"),
//@r18150
	array('sql_alter',"TABLE spip_asso_plan ADD actif TEXT NOT NULL "),
);

// v0.63 (Associaspip 1.9.2)
$GLOBALS['association_maj'][63] = array(
//@r18423
	// Passage au plugin "Inscription 2"
	array('sql_drop_table', 'spip_asso_adherents'), // (suppression effective dans r20002 et) on utilise spip_auteurs_elargis jusqu'a la resurection en r37532 //!\ I2 en s'installant etend bien tous les auteurs spip... mais cf. note sur maj_r16181
//@r20002
	array('sql_alter',"TABLE spip_asso_ventes ADD id_acheteur BIGINT NOT NULL"),
);

function association_maj_37532(){
	if (_ASSOCIATION_AUTEURS_ELARGIS == 'spip_auteurs_elargis') { // v0.64 : le plugin "Inscription 2" est la...
		sql_alter("TABLE spip_auteurs_elargis ADD validite DATE NOT NULL default '0000-00-00'");
		sql_alter("TABLE spip_auteurs_elargis ADD montant FLOAT NOT NULL default '0'"); // quelque chose a avoir avec r16315 ?
		sql_alter("TABLE spip_auteurs_elargis ADD date DATE NOT NULL default '0000-00-00' ");
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
	// asso_adherents reloaded
	array('sql_create','spip_asso_adherents',
		$association_tables_principales['spip_asso_adherents']['field'],
	    $association_tables_principales['spip_asso_adherents']['key'],
	    FALSE), // re-creation (cf. maj_20002) avec les champs : id_adherent, nom, prenom, sexe, fonction, email, validite, numero, rue, cp, ville, telephone, portable, montant, date, statut, relance, divers, remarques, vignette, id_auteur, id_asso, categorie, naissance, profession, societe, identifiant, passe, creation, maj, utilisateur1, utilisateur2, utilisateur3, utilisateur4, secteur, publication, statut_interne, commentaire
//@r37???
	// Simulation provisoire
	array('sql_alter', "TABLE spip_asso_adherents ADD commentaire TEXT NOT NULL DEFAULT '' "), // ex remarques ?
	array('sql_alter', "TABLE spip_asso_adherents ADD statut_interne TEXT NOT NULL DEFAULT '' "), // ex relance ?
);

// v0.65 (Associaspip 1.9.2/2.0)
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
// v0.65 (Associaspip 1.9.2/2.0)
$GLOBALS['association_maj'][38192] = array(
//@r38190
	// Utilisation de asso_metas ! Exit le plugin "CFG"
	array('association_maj_38190'),
);

// v0.65 (Associaspip 2.0)
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

// v0.65 (Associaspip 2.0)
$GLOBALS['association_maj'][38578] = array(
	array('sql_alter', 'TABLE spip_asso_metas RENAME TO spip_association_metas'),
);

// v0.65 (Associaspip 2.0)
$GLOBALS['association_maj'][39702] = array(
	// on rajoute asso_comptes.valide
	array('sql_alter', "TABLE spip_asso_comptes ADD valide TEXT DEFAULT 'oui' "),
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