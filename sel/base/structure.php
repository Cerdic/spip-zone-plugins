<?php
function sel_declarer_tables_interfaces($interface) {
	$interface['table_des_tables']['auteurs_extension'] = 'auteurs_extension';
	$interface['table_des_tables']['annonces'] = 'annonces';
	$interface['table_des_tables']['echanges'] = 'echanges';
	$interface['table_des_tables']['sels'] = 'sels';
	$interface['table_des_tables']['themes'] = 'themes';
	$interface['table_des_tables']['parametres'] = 'parametres';
	
	// syntaxe table_jointure [table source (avec sa clé primaire)][champ] = [table cible (avec clé étrangère)]
	$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_extension';
	$interface['tables_jointures']['spip_sels'][] = 'auteurs_extension';
	$interface['tables_jointures']['spip_auteurs'][] = 'annonces';	
	$interface['tables_jointures']['spip_offreurs'][] = 'echanges';	 // vers le champ "id_offreur" d'une vue spip_offreurs  de spip_auteurs
	$interface['tables_jointures']['spip_demandeurs'][] = 'echanges'; // vers le champ "id_demandeur" d'une vue spip_demandeurs  de spip_auteurs
	$interface['tables_jointures']['spip_valideurs'][] = 'echanges';  // vers le champ "id_valideur" d'une vue spip_valideurs de spip_auteurs
	$interface['tables_jointures']['spip_annonces'][] = 'echanges';	
	$interface['tables_jointures']['spip_themes']['id_theme'] = 'themes_annonces';
	$interface['tables_jointures']['spip_annonces']['id_annonce'] = 'themes_annonces';
	$interface['tables_jointures']['spip_themes'][] = 'echanges';	
	$interface['tables_jointures']['spip_annonces'][] = 'echanges';	// cette clé étrangère pourra être NULL (pas forcément d'annonce de référence à la saisie d'un échange)

	// $interface['table_des_traitements']['NOM_DU_CHAMP']['table'] = _TRAITEMENT_TYPO;
	return $interface;
}



function sel_declarer_tables_principales($tables_principales) {

/* Cette fonction se compose de 3 parties :
 1- déclaration de structure de table,
 2- déclaration des clés primaires et index 
 - retour d'un résultat sous forme de tableau qui contient les 2 premières déclarations,
 avec éventuellement jointures N-N d'une table sur elle-même, exemple :
 $tables_principales['spip_trucs']['join'] =
      array("id_auteur" => "id_auteur",
            "id_traducteur" => "id_auteur");
où id_traducteur = 1 autre champ de la table auteur, pointant vers un id_auteur de la même table
*/

// 1 - STRUCTURE DES TABLES
// ------------------------

	$spip_auteurs_extension = array(
		"id_auteur_extension" => "BIGINT(21) NOT NULL",
		"id_auteur" => "BIGINT(21) NOT NULL",
		"id_sel"  =>  "BIGINT(21) DEFAULT NULL",
		"num_adhesion_sel"   => "VARCHAR(10) DEFAULT NULL",
		"cotiz_euros1_sel" => "INT(10) DEFAULT NULL",
		"cotiz_euros_sel"   => "INT(10) DEFAULT NULL",
		"recup_katalog_sel" => "VARCHAR(10) DEFAULT NULL", // "internet", "permanence" ou "poste"
		"etat_compte" => "VARCHAR(20) DEFAULT NULL", // "actif" ou "inactif"
		"etat_compte_depuis"   => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", // = format datetime 2011-03-14 22:20:33
		"adresse1"  => "VARCHAR(70) NOT NULL DEFAULT ''",
		"adresse2" => "VARCHAR(70) DEFAULT NULL",
		"code_postal" => "VARCHAR(15) NOT NULL DEFAULT ''",
		"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"pays" => "VARCHAR(150) NOT NULL DEFAULT 'France'",
		"tel1" => "VARCHAR(15) NOT NULL DEFAULT ''",
		"tel2" => "VARCHAR(15) DEFAULT NULL",
		"statut_katalog" => "SMALLINT(3) UNSIGNED NOT NULL DEFAULT '1'", // 1= utilisateur non certifié, 2= certifié, 3=admin local, 4=admin global
		"moyen_echange" => "VARCHAR(5) NOT NULL DEFAULT '0'", // sel, jeu, les2
		"si_correspondance" => "SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0'", // 0 = n'est pas correspondant local, le numéro de département sinon
		"couverture_correspondance" => "TEXT DEFAULT NULL" // précision sur la couverture géographique (si plusieurs départements par exemple)
	);

	$spip_sels = array(
		"id_sel" => "BIGINT(21) NOT NULL",
		"nom" => "BIGINT(21) NOT NULL",
		"id_sel"  =>  "BIGINT(21) DEFAULT NULL",
		"num_adhesion_sel"   => "VARCHAR(10) DEFAULT NULL",
		"etat_compte" => "VARCHAR(20) DEFAULT NULL", // "actif" ou "inactif"
		"etat_compte_depuis"   => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", //= format datetime 2011-03-14 22:20:33
		"adresse1"  => "VARCHAR(70) NOT NULL DEFAULT ''",
		"adresse2" => "VARCHAR(70) DEFAULT NULL",
		"code_postal" => "VARCHAR(15) NOT NULL DEFAULT ''",
		"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"pays" => "VARCHAR(150) NOT NULL DEFAULT 'France'",
		"tel1" => "VARCHAR(25) NOT NULL DEFAULT ''",
		"tel2" => "VARCHAR(25) DEFAULT NULL",
		"email" => "VARCHAR(255) DEFAULT NULL",
		"nom_unite" => "VARCHAR(255) DEFAULT NULL", // basilic, piaf, bouchon...
		"credit_ouverture" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // chiffre : valeur à créditer à tout ouverture de comte. prévoir question : à prélever sur le compte d'un SEL ou corne d'abondance ?
		"validation_echange" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // permettre la validation implicite d'un échange saisi par un membre ? si oui, nombre de jours laissé avant validation automatique, 0 sinon 
		"cotisation_unite" => "VARCHAR(25) NOT NULL DEFAULT ''", // combinaison de 3 chaînes :
		/*
		1. existe-t-il une cotisation en unités ?
		- sans, forfait, pourcnt
		2. sur les comptes actifs ou tous les comptes ?
		- tous, actf (tous les comptes, les comptes actifs uniquement)
		3. A quelle fréquence ?
		- 12 (tous les mois), 4 (une fois par trimestre), 2 (toues les 6 mois, 2 fois / an), 1 (une foisl'année)
		Exemple de chaîne générée : pourcntactf01, ou forfaittous01
		4. A prévoir en plus : sur les comptes positifs uniquement
		*/	
		"cotisation_montant_taux" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'" // un chiffre à prélever, à prendre en valeur ou en pourcentage

		/*
		"cotisation_derniere_date" => ""
		"cloture_compte" => ""
		*/ 
	);

	$spip_annonces = array(
		"id_annonce" => "BIGINT(21) NOT NULL",
		"titre" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"statut" => "VARCHAR(30) NOT NULL DEFAULT 'offre'", // offre, demande, partage
		"nature" => "VARCHAR(30) NOT NULL DEFAULT 'service'", // service, savoir (=stage), objet
		"id_auteur" => "BIGINT(21) NOT NULL", // auteur de l'annonce
		"date_saisie" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", //= format datetime 2011-03-14 22:20:33
		"description" => "TEXT NOT NULL",
		"nombre_personnes" => "SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",	
		"date_debut" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",  // date de début, pour événement à date fixe ou libre = format timestamp 2011-03-14 22:20:33
		"date_fin" => "DATETIME DEFAULT NULL",   // date de fin spécifiée uniquement pour les offres à dates fixes = format timestamp 2011-03-14 22:20:33
		"adresse1"  => "VARCHAR(70) NOT NULL DEFAULT ''",
		"adresse2" => "VARCHAR(70) DEFAULT NULL",
		"code_postal" => "VARCHAR(15) NOT NULL DEFAULT ''",
		"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"pays" => "VARCHAR(150) NOT NULL DEFAULT 'France'",
		"adresse2" => "VARCHAR(70) DEFAULT NULL",
		"transport"  => "TEXT DEFAULT NULL",
		"repas"  => "TEXT DEFAULT NULL",
		"hebergement"  => "TEXT DEFAULT NULL",
		"nom_referent" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"tel1_referent" => "VARCHAR(25) DEFAULT NULL",
		"tel2_referent" => "VARCHAR(25) DEFAULT NULL",
		"email_referent" => "VARCHAR(255) DEFAULT NULL",
		"nom_autre" => "VARCHAR(255) DEFAULT NULL",
		"tel1_autre" => "VARCHAR(25) DEFAULT NULL",
		"tel2_autre" => "VARCHAR(25) DEFAULT NULL",
		"email_autre" => "VARCHAR(255) DEFAULT NULL",
		"total_unite" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
		"si_unite_heure" => "SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0'",
		"total_euros" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
		"justificatif_euros" => "VARCHAR(255) DEFAULT NULL" // ce champ devra être rempli si des frais en euros sont indiqués
	);
		
		
	$spip_echanges = array(
		"id_echange" => "BIGINT(21) NOT NULL",
		"id_offreur" => "BIGINT(21) DEFAULT NULL", // vers id_offreur (= id_auteur) de la vue sur spip_auteurs
		"offreur_ext"  => "VARCHAR(255) DEFAULT NULL",
		"offreur_sel_ext"   => "VARCHAR(255) DEFAULT NULL",
		"titre" => "VARCHAR(150) NOT NULL DEFAULT ''",
		"date_echange"  => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", // = format datetime 2011-03-14 22:20:33
		"saisi_par" => "VARCHAR(150) NOT NULL DEFAULT 'offreur'", // 'offreur', 'demandeur' ou 'automatique'
		"date_saisie" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //  = format timestamp 2011-03-14 22:20:33
		"nature"   => "VARCHAR(20) NOT NULL DEFAULT 'echange'",  // 'echange', 'cotizunite', 'cloturecompte'
		"id_demandeur"  =>  "BIGINT(21) DEFAULT NULL", // vers id_demandeur (= id_auteur) de la vue sur spip_auteurs
		"demandeur_ext"  =>  "VARCHAR(255) DEFAULT NULL",
		"demandeur_sel_ext" => "VARCHAR(255) DEFAULT NULL",
		"valeur" => "INT(10) NOT NULL DEFAULT '0'",
		"date_validation"  => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"valide_par" => "BIGINT(21) DEFAULT NULL", // vers id_auteur
		"commentaires"  => "TEXT DEFAULT NULL"

	);

	$spip_themes = array(
		"id_theme" => "BIGINT(21) NOT NULL",
		"id_parent" => "BIGINT(21) NOT NULL DEFAULT '0'", // 0 si theme racine
		"id_secteur" => "BIGINT(21) NOT NULL",
		"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"descriptif" => "TEXT DEFAULT NULL",
	);

	$spip_parametres = array(
		"id_parametre" => "BIGINT(21) NOT NULL",
		"cible" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"liste_diffusion" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"expediteur" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"www" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"signature" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"acces_inscription" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"pagination" => "INT(3) UNSIGNED NOT NULL DEFAULT '10'",
	);
	
// 2 - CLES	
// --------

	$spip_auteurs_extension_key = array(
		"PRIMARY KEY" => "id_auteur_extension",
		"KEY" => "id_auteur",
		"KEY" => "si_correspondance",
		"KEY" => "num_adhesion_sel"
	);

	$spip_sels_key = array(
		"PRIMARY KEY" => "id_sel",
		"KEY" => "nom, ville"
	);

	$spip_annonces_key = array(
		"PRIMARY KEY" => "id_annonce",
		"KEY" => "titre",
		"KEY" => "id_auteur",
		"KEY" => "date_debut"
	);
	
	$spip_echanges_key = array(
		"PRIMARY KEY" => "id_echange",
		"KEY" => "id_offreur",
		"KEY" => "id_demandeur"
	);

	$spip_themes_key = array(
		"PRIMARY KEY" => "id_theme",
		"KEY" => "id_parent"
	);

	$spip_parametres_key = array(
		"PRIMARY KEY" => "id_parametre"
	);
	
// 3 - RETOUR RESULTAT
// -------------------

	$tables_principales['spip_auteurs_extension'] =
	array('field' => &$spip_auteurs_extension, 'key' => &$spip_auteurs_extension_key);

	$tables_principales['spip_sels'] =
	array('field' => &$spip_sels, 'key' => &$spip_sels_key);	

	$tables_principales['spip_annonces'] =
	array('field' => &$spip_annonces, 'key' => &$spip_annonces_key);	
	
	$tables_principales['spip_echanges'] =
	array('field' => &$spip_echanges, 'key' => &$spip_echanges_key);	

	$tables_principales['spip_themes'] =
	array('field' => &$spip_themes, 'key' => &$spip_themes_key);

	$tables_principales['spip_parametres'] =
	array('field' => &$spip_parametres, 'key' => &$spip_parametres_key);

	return $tables_principales;		
}

function sel_declarer_tables_auxiliaires($tables_auxiliaires){
	$spip_themes_annonces = array(
		"id_theme" 	=> "BIGINT(21) NOT NULL",
		"id_annonce" 	=> "BIGINT(21) NOT NULL"
	);
	
	$spip_themes_annonces_key = array(
		"PRIMARY KEY" 	=> "id_theme, id_annonce",
		"KEY id_annonce" => "id_annonce"
	);
	
	$tables_auxiliaires['spip_themes_annonces'] = array(
		'field' => &$spip_themes_annonces,
		'key' => &$spip_themes_annonces_key
	);
	
	return $tables_auxiliaires;
}
?>