<?php
/**
* Plugin Association
*
* Copyright (c) 2007
* Bernard Blazin & François de Montlivault
* http://www.plugandspip.com
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt.
*
**/

function association_verifier_base() {
	$version_base = 0.50; // version actuelle
	$current_version = 0.0;

	if ( (!isset($GLOBALS['meta']['asso_base_version']) )
		|| (($current_version = $GLOBALS['meta']['asso_base_version'])!=$version_base)) {

		include_spip('base/association');

		if ($current_version==0.0) {
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			spip_query("INSERT INTO spip_asso_profil (nom) VALUES ('')");
			ecrire_meta('asso_base_version',$current_version=$version_base);
		}

		if ($current_version<0.21) {
			spip_query("ALTER TABLE spip_asso_adherents ADD publication TEXT NOT NULL");
			spip_query("CREATE TABLE spip_asso_financiers(id_financier INT NOT NULL AUTO_INCREMENT, code TEXT NOT NULL, reference TEXT NOT NULL, solde FLOAT NOT NULL DEFAULT 0, commentaire TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_financier) )");
			spip_query("CREATE TABLE spip_asso_bienfaiteurs(id_don INT NOT NULL AUTO_INCREMENT, date_don DATE NOT NULL, bienfaiteur TEXT NOT NULL, id_adherent INT NOT NULL, argent TINYTEXT NOT NULL, colis TEXT NOT NULL, valeur TEXT NOT NULL, contrepartie TINYTEXT NOT NULL, commentaire TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_don) )");
			ecrire_meta('asso_base_version',$current_version=0.21);
		}

		if ($current_version<0.30) {
			spip_query("RENAME TABLE spip_asso_financiers TO spip_asso_banques");
			spip_query("ALTER TABLE spip_asso_banques CHANGE id_financier id_banque INT NOT NULL AUTO_INCREMENT");
			spip_query("INSERT INTO spip_asso_banques(code) VALUES ('caisse')");
			spip_query("ALTER TABLE spip_asso_banques ADD `date` DATE NOT NULL");
			spip_query("ALTER TABLE spip_asso_profil ADD dons TEXT NOT NULL DEFAULT 'oui', ADD ventes TEXT NOT NULL DEFAULT 'oui', ADD comptes TEXT NOT NULL DEFAULT 'oui'");
			spip_query("RENAME TABLE spip_asso_bienfaiteurs TO spip_asso_dons");
			spip_query("CREATE TABLE spip_asso_livres (id_livre TINYINT NOT NULL AUTO_INCREMENT, valeur TEXT NOT NULL, libelle TEXT NOT NULL, maj TIMESTAMP NOT NULL, PRIMARY KEY (id_livre) )");
			spip_query("INSERT INTO spip_asso_livres (valeur, libelle) VALUES ('cotisation', 'Cotisations'), ('vente', 'Ventes'), ('don', 'Dons'), ('achat', 'Achats'), ('divers', 'Divers'), ('activite', 'Activités')");
			spip_query("ALTER TABLE spip_asso_profil ADD dons TEXT NOT NULL, ADD ventes TEXT NOT NULL, ADD comptes TEXT NOT NULL, ADD activites TEXT NOT NULL");
			spip_query("UPDATE spip_asso_profil SET dons='oui', ventes='oui', comptes='oui' WHERE id_profil=1");
			ecrire_meta('asso_base_version',$current_version=0.30);
		}

		if ($current_version<0.40) {
			spip_query("ALTER TABLE spip_asso_comptes ADD valide TEXT NOT NULL");
			spip_query("CREATE TABLE spip_asso_activites(id_activite BIGINT NOT NULL AUTO_INCREMENT, id_evenement BIGINT NOT NULL, nom TEXT NOT NULL, id_adherent BIGINT NOT NULL, accompagne TEXT NOT NULL, inscrits BIGINT NOT NULL DEFAULT '0', `date` DATE NOT NULL DEFAULT '0000-00-00', telephone TEXT NOT NULL, adresse TEXT NOT NULL, email TEXT NOT NULL, commentaire TEXT NOT NULL, montant FLOAT NOT NULL DEFAULT '0', date_paiement DATE NOT NULL DEFAULT '0000-00-00', statut TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_activite) )");
			ecrire_meta('asso_base_version',$current_version=0.40);
		}

		if ($current_version<0.50){
			spip_query("ALTER TABLE spip_asso_profil ADD indexation TEXT NOT NULL");
			spip_query("ALTER TABLE spip_asso_activites CHANGE accompagne membres TEXT NOT NULL, ADD non_membres TEXT NOT NULL");
			ecrire_meta('asso_base_version',$current_version=0.50);
		}

		ecrire_metas();
	}

/*
	if (isset($GLOBALS['meta']['INDEX_elements_objet'])) {
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
		if (!isset($INDEX_elements_objet['spip_evenements'])) {
			$INDEX_elements_objet['spip_evenements'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_objet_associes'])) {
		$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
		if (!isset($INDEX_objet_associes['spip_articles']['spip_evenements'])) {
			$INDEX_objet_associes['spip_articles']['spip_evenements'] = 1;
			ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_elements_associes'])) {
		$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
		if (!isset($INDEX_elements_associes['spip_evenements'])) {
			$INDEX_elements_associes['spip_evenements'] = array('titre'=>2,'descriptif'=>1);
			ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
			ecrire_metas();
		}
	}
*/

}

function asso_install() {
	association_verifier_base();
}

/function asso_uninstall() {
	include_spip('base/association');
	include_spip('base/abstract_sql');
	spip_query("DROP TABLE spip_asso_adherents");
	spip_query("DROP TABLE spip_asso_activites");
	spip_query("DROP TABLE spip_asso_categories");
	spip_query("DROP TABLE spip_asso_comptes");
	spip_query("DROP TABLE spip_asso_dons");
	spip_query("DROP TABLE spip_asso_plan");
	spip_query("DROP TABLE spip_asso_prets");
	spip_query("DROP TABLE spip_asso_ressources");
	spip_query("DROP TABLE spip_asso_ventes");
	effacer_meta('asso_base_version');
	effacer_meta('association');
	ecrire_metas();
}

?>