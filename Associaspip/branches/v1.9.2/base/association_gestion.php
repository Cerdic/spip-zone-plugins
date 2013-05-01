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

// version actuelle du plugin a changer en cas de maj
$GLOBALS['association_version'] = 0.65;

function association_verifier_base() {
	$version_base = $GLOBALS['association_version'];
	$current_version = 0.0;

	if ( (!isset($GLOBALS['meta']['asso_base_version']) )
		|| (($current_version = $GLOBALS['meta']['asso_base_version'])!=$version_base)) {

		include_spip('base/association');

		if ($current_version==0.0) {
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			ecrire_meta('asso_base_version',$current_version=$version_base);
		}

		if ($current_version<0.21) {
			//@r12???
			spip_query("ALTER TABLE spip_asso_adherents ADD publication TEXT NOT NULL");
			spip_query("CREATE TABLE spip_asso_financiers(id_financier INT NOT NULL AUTO_INCREMENT, code TEXT NOT NULL, reference TEXT NOT NULL, solde FLOAT NOT NULL DEFAULT 0, commentaire TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_financier) )"); //!\ portability: AUTO_INCREMENT is MySQL specific...
			spip_query("CREATE TABLE spip_asso_bienfaiteurs(id_don INT NOT NULL AUTO_INCREMENT, date_don DATE NOT NULL, bienfaiteur TEXT NOT NULL, id_adherent INT NOT NULL, argent TINYTEXT NOT NULL, colis TEXT NOT NULL, valeur TEXT NOT NULL, contrepartie TINYTEXT NOT NULL, commentaire TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_don) )"); //!\ portability: AUTO_INCREMENT is MySQL specific...
			//done
			ecrire_meta('asso_base_version',$current_version=0.21);
		}

		if ($current_version<0.30) {
			//@r12???
			spip_query("RENAME TABLE spip_asso_financiers TO spip_asso_banques"); //!\ portability: RENAME TABLE isn't ANSI syntaxe...
			spip_query("ALTER TABLE spip_asso_banques CHANGE id_financier id_banque INT NOT NULL AUTO_INCREMENT"); //!\ portability: AUTO_INCREMENT is MySQL specific... and when using InnoDB, primary key cannont be renamed so if they are referenced by foreign keys...
			spip_query("INSERT INTO spip_asso_banques(code) VALUES ('caisse')");
			spip_query("ALTER TABLE spip_asso_banques ADD `date` DATE NOT NULL"); //!\ portability: back-ticks escaping like `date` is MySQL specific... anyway one should avoid use of reserved words...
			//@r12???
#			spip_query("CREATE TABLE spip_asso_livres (id_livre TINYINT NOT NULL AUTO_INCREMENT, valeur TEXT NOT NULL, libelle TEXT NOT NULL, maj TIMESTAMP NOT NULL, PRIMARY KEY (id_livre) )");
#			spip_query("INSERT INTO spip_asso_livres (valeur, libelle) VALUES ('cotisation', 'Cotisations'), ('vente', 'Ventes'), ('don', 'Dons'), ('achat', 'Achats'), ('divers', 'Divers'), ('activite', 'Activités')");
			//@r13971
			spip_query("ALTER TABLE spip_asso_profil ADD dons TEXT NOT NULL DEFAULT 'oui', ADD ventes TEXT NOT NULL DEFAULT 'oui', ADD comptes TEXT NOT NULL DEFAULT 'oui'");
			//@r15981
			spip_query("RENAME TABLE spip_asso_bienfaiteurs TO spip_asso_dons"); //!\ portability: RENAME TABLE isn't ANSI syntax...
			//@r13971
			spip_query("ALTER TABLE spip_asso_profil ADD dons TEXT NOT NULL, ADD ventes TEXT NOT NULL, ADD comptes TEXT NOT NULL, ADD activites TEXT NOT NULL");
			spip_query("UPDATE spip_asso_profil SET dons='oui', ventes='oui', comptes='oui' WHERE id_profil=1");
			//done
			ecrire_meta('asso_base_version',$current_version=0.30);
		}

		if ($current_version<0.40) {
			//@r12???
			spip_query("ALTER TABLE spip_asso_comptes ADD valide TEXT NOT NULL");
			spip_query("CREATE TABLE spip_asso_activites(id_activite BIGINT NOT NULL AUTO_INCREMENT, id_evenement BIGINT NOT NULL, nom TEXT NOT NULL, id_adherent BIGINT NOT NULL, accompagne TEXT NOT NULL, inscrits BIGINT NOT NULL DEFAULT '0', `date` DATE NOT NULL DEFAULT '0000-00-00', telephone TEXT NOT NULL, adresse TEXT NOT NULL, email TEXT NOT NULL, commentaire TEXT NOT NULL, montant FLOAT NOT NULL DEFAULT '0', date_paiement DATE NOT NULL DEFAULT '0000-00-00', statut TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_activite) )"); //!\ portability: AUTO_INCREMENT is MySQL specific... also, back-ticks escaping like `date` is MySQL specific... anyway one should avoid use of reserved words...
			//done
			ecrire_meta('asso_base_version',$current_version=0.40);
		}

		if ($current_version<0.50) {
			//@r12???
#			spip_query("ALTER TABLE spip_asso_profil ADD indexation TEXT NOT NULL");
			//@r16186
			spip_query("ALTER TABLE spip_asso_activites CHANGE accompagne membres TEXT NOT NULL, ADD non_membres TEXT NOT NULL"); //!\ portability: CHANGE isn't ANSI syntax...
			//done
			ecrire_meta('asso_base_version',$current_version=0.50);
		}

		if ($current_version<0.60) {
			//@r12530
			$infos_profil = spip_query("SELECT nom, numero, rue, cp, ville, telephone, siret, declaration, prefet, president, mail, dons, ventes, comptes, indexation FROM spip_asso_profil"); // on a exclu : id_profil, maj, indexation
			$metas_list = spip_fetch_array($infos_profil); //cf. http://contrib.spip.net/PortageV2-Migrer-un-plugin-vers-SPIP2
			spip_query("INSERT INTO spip_meta (nom, valeur) VALUES ('valeur', ". _q(serialize($metas_list)) .")");
			//@r13839
			spip_query("DROP TABLE spip_asso_profil");
			spip_query("CREATE TABLE spip_asso_ressources(id_ressource INT NOT NULL AUTO_INCREMENT, code TEXT NOT NULL, intitule TEXT NOT NULL, date_acquisition DATE NOT NULL DEFAULT '0000-00-00', id_achat TINYINT NOT NULL DEFAULT '0', pu FLOAT NOT NULL DEFAULT '0', statut TEXT NOT NULL, commentaire TEXT NOT NULL, maj TIMESTAMP, PRIMARY KEY(id_ressource) )"); //!\ portability: AUTO_INCREMENT is MySQL specific...
			spip_query("CREATE TABLE spip_asso_prets(id_pret INT NOT NULL AUTO_INCREMENT, date_sortie DATE NOT NULL DEFAULT '0000-00-00', duree INT NOT NULL DEFAULT '0', date_retour DATE NOT NULL DEFAULT '0000-00-00', id_emprunteur TEXT NOT NULL, statut TEXT NOT NULL, commentaire_sortie TEXT NOT NULL, commentaire_retour TEXT NOT NULL maj TIMESTAMP, PRIMARY KEY(id_pret) )"); //!\ portability: AUTO_INCREMENT is MySQL specific...
			//done
			ecrire_meta('asso_base_version',$current_version=0.60);
		}

		if ($current_version<0.61) {
			//@r13971
			spip_query("RENAME TABLE spip_asso_banques TO spip_asso_plan"); //!\ portability: RENAME TABLE isn't ANSI syntax...
			spip_query("ALTER TABLE spip_asso_plan CHANGE id_banque id_plan INT NOT NULL AUTO_INCREMENT"); //!\ portability: AUTO_INCREMENT is MySQL specific... and when using InnoDB, primary key cannont be renamed so if they are referenced by foreign keys...
			spip_query("ALTER TABLE spip_asso_plan CHANGE solde solde_anterieur FLOAT NOT NULL DEFAULT '0'"); //!\ portability: CHANGE isn't ANSI syntax...
			spip_query("ALTER TABLE spip_asso_plan CHANGE `date` date_anterieure DATE NOT NULL DEFAULT '0000-00-00'"); //!\ portability: CHANGE isn't ANSI syntax...
			spip_query("ALTER TABLE spip_asso_plan ADD classe TEXT NOT NULL");
			//done
			ecrire_meta('asso_base_version',$current_version=0.61);
		}

		if (!spip_query("SELECT * FROM spip_auteurs_elargis") ) {
			echo "Installer les plugins cfg et Inscription2 avant d'installer le plugin Association $current_version!";
			ecrire_metas();
			exit;
		}

		if ($current_version<0.62) {
			//@r16186
			spip_query("ALTER TABLE spip_asso_adherents CHANGE statut statut_relance TEXT NOT NULL"); //!\ portability: CHANGE isn't ANSI syntax...
			//@r16181
			$champs = array('id_auteur', 'nom', 'prenom', 'sexe', 'fonction', 'email', 'numero', 'rue', 'cp', 'ville', 'telephone', 'portable', 'montant', 'relance', 'divers', 'remarques', 'vignette', 'naissance', 'profession', 'societe', 'identifiant', 'passe', 'creation', 'secteur', 'publication', 'statut_relance');
			$liste_maj = spip_query("SELECT ". implode(', ', $champs) ." FROM spip_adherents");
			while ($maj = spip_fetch_array($liste_maj) ) {
				$modifs = array();
				foreach ($champs as $champ) {
					$modifs[] .= "'$champ'=". _q($maj[$champ]);
				}
				spip_query("UPDATE spip_auteurs_elargis SET ". implode(', ', $modifs) ." WHERE id_auteur=".$maj['id_auteur']);
			}
			//@r16249
			unset($champs[0]); //= id_auteur
			//@r16186
			spip_query("ALTER TABLE spip_asso_adherents DROP ". implode(', DROP ', $champs) );
			@spip_query("ALTER TABLE spip_asso_activites DROP accompagne");
			//@r18150
			spip_query("ALTER TABLE spip_asso_plan ADD actif TEXT NOT NULL");
			//done
			ecrire_meta('asso_base_version',$current_version=0.62);
		}

		if ($current_version<0.63) {
			//@r20002
			spip_query("ALTER TABLE spip_asso_ventes ADD id_acheteur BINGINT NOT NULL");
			//done
			ecrire_meta('asso_base_version',$current_version=0.63);
		}

		if ($current_version<0.64) {
			//@r25365
			spip_query("ALTER TABLE spip_asso_prets ADD id_ressource VARCHAR(20) NOT NULL");
			//@r37532
			spip_query("ALTER TABLE spip_auteurs_elargis ADD validite DATE NOT NULL DEFAULT '0000-00-00', ADD montant FLOAT NOT NULL DEFAULT '0', ADD `date` DATE NOT NULL DEFAULT '0000-00-00'"); //!\ portability: back-ticks escaping like `date` is MySQL specific... anyway one should avoid use of reserved words...
			//@r16181
			$champs = array('id_auteur', 'montant', 'date', 'categorie');
			$liste_maj = @spip_query("SELECT ". implode(', ', $champs) ." FROM spip_adherents");
			while ($maj = spip_fetch_array($liste_maj) ) {
				$modifs = array();
				foreach ($champs as $champ) {
					$modifs[] .= "'$champ'=". _q($maj[$champ]);
				}
				@spip_query("UPDATE spip_auteurs_elargis SET ". implode(', ', $modifs) ." WHERE id_auteur=".$maj['id_auteur']);
			}
			//@r16315
			@spip_query("ALTER TABLE spip_asso_adherents DROP ". implode(', DROP ', $champs) );
			//@r18423
			@spip_query("DROP TABLE spip_asso_adherents");
			//done
			ecrire_meta('asso_base_version',$current_version=0.64);
		}

		ecrire_metas();
	}
}

function association_effacer_tables() {
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

function association_install($action) {
	$version_base = $GLOBALS['association_version'];
	switch ($action) {
		case 'test':
			return ( isset($GLOBALS['meta']['asso_base_version'])
				AND ($GLOBALS['meta']['asso_base_version']>=$version_base) );
			break;
		case 'install':
			association_verifier_base();
			break;
		case 'uninstall':
			association_effacer_tables();
			break;
	}
}

?>