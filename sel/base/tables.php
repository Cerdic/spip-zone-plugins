<?php

/* D�claration des tables et jointures :
sel_declarer_tables_interfaces -> SPIP 2
sel_declarer_tables_principales -> SPIP 2
sel_declarer_tables_objets_sql -> SPIP 3
sel_declarer_tables_auxiliaires -> SPIP 2 et 3
*/

function sel_declarer_tables_interfaces($interface) {
// DECLARATION TABLES POUR SPIP 2

	// D�claration de nommage des tables
	// ---------------------------------
	
	$interface['table_des_tables']['sels'] = 'sels';
	$interface['table_des_tables']['organisations'] = 'organisations';
	$interface['table_des_tables']['correspondances'] = 'correspondances';	
	$interface['table_des_tables']['annonces'] = 'annonces';
	$interface['table_des_tables']['echanges'] = 'echanges';
	$interface['table_des_tables']['themes'] = 'themes';
	$interface['table_des_tables']['parametres'] = 'parametres';

	// D�claration des relations 1 - N
	// -------------------------------		
	// syntaxe table_jointure [table source (avec sa cl� primaire)][champ] = [table cible (avec cl� �trang�re)]
	$interface['tables_jointures']['spip_auteurs'][] = 'annonces';	
	$interface['tables_jointures']['spip_offreurs'][] = 'echanges';	 // vers le champ "id_offreur" d'une vue spip_offreurs  de spip_auteurs
	$interface['tables_jointures']['spip_demandeurs'][] = 'echanges'; // vers le champ "id_demandeur" d'une vue spip_demandeurs  de spip_auteurs
	$interface['tables_jointures']['spip_valideurs'][] = 'echanges';  // vers le champ "id_valideur" d'une vue spip_valideurs de spip_auteurs
	$interface['tables_jointures']['spip_annonces'][] = 'echanges'; // cette cl� �trang�re pourra �tre NULL (pas forc�ment d'annonce de r�f�rence � la saisie d'un �change)


	// D�claration des relations N - N
	// -------------------------------
	// syntaxe : ['tables_jointures']['table principale (avec sa cl� primaire)'] = 'table_auxiliaire (cl� �trang�re)'.
	$interface['tables_jointures']['spip_auteurs'][] = 'correspondances';
	$interface['tables_jointures']['spip_sels'][] = 'correspondances';		
	$interface['tables_jointures']['spip_organisations'][] = 'correspondances';

	$interface['tables_jointures']['spip_themes'][] = 'themes_annonces';
	$interface['tables_jointures']['spip_annonces'][] = 'themes_annonces';	
	
	// $interface['table_des_traitements']['NOM_DU_CHAMP']['table'] = _TRAITEMENT_TYPO;
	return $interface;
}



function sel_declarer_tables_principales($tables_principales) {
// TABLES PRINCIPALES - OBJETS POUR SPIP 2

/* Cette fonction se compose de 3 parties :
 1- d�claration de structure de table,
 2- d�claration des cl�s primaires et index 
 - retour d'un r�sultat sous forme de tableau qui contient les 2 premi�res d�clarations,
 avec �ventuellement jointures N-N d'une table sur elle-m�me, exemple :
 $tables_principales['spip_trucs']['join'] =
      array("id_auteur" => "id_auteur",
            "id_traducteur" => "id_auteur");
o� id_traducteur = 1 autre champ de la table auteur, pointant vers un id_auteur de la m�me table
*/

// 1 - STRUCTURE DES TABLES
// ------------------------

	$spip_sels = array(
		"id_sel" => "BIGINT(21) NOT NULL",
		"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"id_sel"  =>  "BIGINT(21) DEFAULT NULL",
		"num_adhesion_sel"   => "VARCHAR(10) DEFAULT NULL",
		"etat_compte" => "VARCHAR(20) DEFAULT NULL", // "actif" ou "inactif"
		"etat_compte_depuis"   => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //= format datetime 2011-03-14 22:20:33
		"adresse1"  => "TINYTEXT NOT NULL",
		"adresse2" => "TINYTEXT DEFAULT NULL",
		"code_postal" => "VARCHAR(20) NOT NULL DEFAULT ''",
		"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"pays" => "VARCHAR(150) NOT NULL DEFAULT 'France'",
		"lon" => "DECIMAL(20,16) DEFAULT NULL",
		"lat" => "DECIMAL(20,16) DEFAULT NULL",
		"tel1" => "VARCHAR(50) NOT NULL DEFAULT ''",
		"tel2" => "VARCHAR(50) DEFAULT NULL",
		"email" => "TINYTEXT DEFAULT NULL",
		"site" => "TINYTEXT DEFAULT NULL",		
		"nom_unite" => "VARCHAR(255) DEFAULT NULL", // basilic, piaf, bouchon...
		"credit_ouverture" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // chiffre : valeur � cr�diter � tout ouverture de comte. pr�voir question : � pr�lever sur le compte d'un SEL ou corne d'abondance ?
		"validation_echange" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // permettre la validation implicite d'un �change saisi par un membre ? si oui, nombre de jours laiss� avant validation automatique, 0 sinon 
		"cotisation_unite" => "VARCHAR(30) NOT NULL DEFAULT ''", // combinaison de 3 cha�nes :
		/*
		1. existe-t-il une cotisation en unit�s ?
		- sans, forfait, pourcnt
		2. sur les comptes actifs ou tous les comptes ?
		- tous, actf (tous les comptes, les comptes actifs uniquement)
		3. A quelle fr�quence ?
		- 12 (tous les mois), 4 (une fois par trimestre), 2 (toues les 6 mois, 2 fois / an), 1 (une foisl'ann�e)
		Exemple de cha�ne g�n�r�e : pourcntactf01, ou forfaittous01
		4. A pr�voir en plus : sur les comptes positifs uniquement
		*/	
		"cotisation_montant_taux" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'" // un chiffre � pr�lever, � prendre en valeur ou en pourcentage

		/*
		"cotisation_derniere_date" => ""
		"cloture_compte" => ""
		*/ 
	);

	$spip_annonces = array(
		"id_annonce" => "BIGINT(21) NOT NULL",
		"titre" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"direction_echange" => "VARCHAR(10) NOT NULL DEFAULT 'offre'", // offre, demande, partage
		"nature" => "VARCHAR(10) NOT NULL DEFAULT 'service'", // service, savoir (=stage), bien
		"id_auteur" => "BIGINT(21) NOT NULL", // auteur de l'annonce
		"date_saisie" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", //= format datetime 2011-03-14 22:20:33
		"description" => "TEXT NOT NULL",
		"nombre_personnes" => "SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",	
		"date_debut" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",  // date de d�but, pour �v�nement � date fixe ou libre = format timestamp 2011-03-14 22:20:33
		"date_fin" => "DATETIME DEFAULT NULL",   // date de fin sp�cifi�e uniquement pour les offres � dates fixes = format timestamp 2011-03-14 22:20:33
		"adresse1"  => "TINYTEXT NOT NULL",
		"adresse2" => "TINYTEXT DEFAULT NULL",
		"code_postal" => "VARCHAR(20) NOT NULL DEFAULT ''",
		"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"pays" => "VARCHAR(150) NOT NULL DEFAULT ''",
		"transport"  => "TEXT DEFAULT NULL",
		"repas"  => "TEXT DEFAULT NULL",
		"hebergement"  => "TEXT DEFAULT NULL",
		"nom_referent" => "VARCHAR(255) DEFAULT NULL", // si l'auteur n'est pas le r�f�rent
		"tel1_referent" => "VARCHAR(50) DEFAULT NULL",
		"tel2_referent" => "VARCHAR(50) DEFAULT NULL",
		"email_referent" => "TINYTEXT DEFAULT NULL",
		"statut" => "VARCHAR(20) NOT NULL DEFAULT ''", // pour l'�ventuelle gestion d'une mod�ration a priori des annonces
		// liste des statuts : 0nouvelle, 1annonce_ok, 2annonce_ko, ?? 3perime ?? ce dernier statut pourrait �tre calcul� automatiquement � partir des dates
		"total_unite" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
		"si_unite_heure" => "SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0'",
		"total_fiduc" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
		"justification_fiduc" => "TINYTEXT DEFAULT NULL", // ce champ devra �tre rempli si des frais en euros sont indiqu�s
		"commentaires"  => "TEXT DEFAULT NULL"
	);
		
		
	$spip_echanges = array(
		"id_echange" => "BIGINT(21) NOT NULL",
		"id_offreur" => "BIGINT(21) DEFAULT NULL", // vers id_offreur (= id_auteur) de la vue sur spip_auteurs
		"offreur_ext"  => "VARCHAR(255) DEFAULT NULL",
		"offreur_sel_ext"   => "VARCHAR(255) DEFAULT NULL",
		"titre" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"date_echange"  => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // = format datetime 2011-03-14 22:20:33
		"saisi_par" => "VARCHAR(150) NOT NULL DEFAULT 'offreur'", // 'offreur', 'demandeur' ou 'automatique'
		"date_saisie" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //  = format timestamp 2011-03-14 22:20:33
		"nature"   => "VARCHAR(20) NOT NULL DEFAULT 'echange'",  // 'echange', 'cotizunite', 'cloturecompte'
		"id_demandeur"  =>  "BIGINT(21) DEFAULT NULL", // vers id_demandeur (= id_auteur) de la vue sur spip_auteurs
		"demandeur_ext"  =>  "VARCHAR(255) DEFAULT NULL",
		"demandeur_sel_ext" => "VARCHAR(255) DEFAULT NULL",
		"valeur" => "INT(10) NOT NULL DEFAULT '0'",
		"date_validation"  => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
		"id_valideur" => "BIGINT(21) DEFAULT NULL", // vers id_auteur
		"commentaires"  => "TEXT DEFAULT NULL"
	);

	$spip_themes = array(
		"id_theme" => "BIGINT(21) NOT NULL",
		"id_parent" => "BIGINT(21) NOT NULL DEFAULT '0'", // 0 si theme racine
		"id_secteur" => "BIGINT(21) NOT NULL",
		"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"descriptif" => "TEXT DEFAULT NULL"
	);

	$spip_organisations = array(
		"id_organisation" => "BIGINT(21) NOT NULL",
		"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
		"description" => "TEXT DEFAULT NULL"
	);	

	
// Cette table a un statut interm�diaire entre principale et auxiliaire.
// Comme table auxiliaire, elle d�crit :
// - une relation d'appartenance N/N (binaire) auteur / sel ou sel / organisation (adh�sion du SEL � un intersel, participation � un atelier Selidaire, etc...)
// - une relation de correspondace N/N (ternaire) d'un adh�rent pour son SEL vis � vis d'une organisation (RDS, etc...) : donc auteur / sel / organisation
// Elle sert de table principale pour toutes les informations relatives � l'adh�sion individuelle � un SEL (auteur / sel) : num�ro et date d'adh�sion, mode de r�cup�ration de catalogue, etc...

	
	$spip_correspondances = array(
		"id_correspondance" => "BIGINT(21) NOT NULL",
		"id_auteur" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
		"id_sel" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
		"num_adhesion"   => "VARCHAR(10) NOT NULL DEFAULT ''",
		"cotisation_fiduc1" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // date de premi�re cotisation
		"cotisation_fiduc" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // date de derni�re cotisation
		"recupere_catalogue" => "VARCHAR(10) NOT NULL DEFAULT ''", //permanence, internet, poste
		"etat_compte" => "VARCHAR(10) NOT NULL DEFAULT ''", //actif / inactif
		"etat_compte_depuis" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //actif / inactif
		"id_organisation" 	=> "BIGINT(21) NOT NULL DEFAULT '0'"
	);

	
	$spip_parametres = array(
		"id_parametre" => "BIGINT(21) NOT NULL",
		"cible" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"liste_diffusion" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"expediteur" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"www" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"signature" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"acces_inscription" => "VARCHAR(100) NOT NULL DEFAULT ''",
		"pagination" => "INT(3) UNSIGNED NOT NULL DEFAULT '10'"
	);
	
// 2 - CLES	
// --------

	$spip_sels_key = array(
		"PRIMARY KEY" => "id_sel",
		"KEY" => "nom"
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

	$spip_organisations_key = array(
		"PRIMARY KEY" => "id_organisation",
	);

	$spip_correspondances_key = array(
		"PRIMARY KEY" 	=> "id_correspondance"
	);	
	
	$spip_parametres_key = array(
		"PRIMARY KEY" => "id_parametre"
	);
	
// 3 - RETOUR RESULTAT
// -------------------

	$tables_principales['spip_auteurs']['field']['prenom'] = "TINYTEXT NOT NULL";
	$tables_principales['spip_auteurs']['field']['acces'] = "VARCHAR(15) NOT NULL DEFAULT '0nouveau'";
	// Ce champ acc�s permettra de ne pas s'appuyer sur les statuts Spip pour la gestion
	// Usage : Qd le form de cr�ation / modif d'auteur est rempli depuis l'espace public, on envoit tojours :
	// statut = r�dacteur (envoi cach�) et acces = 0nouveau pour la cr�ation. En modif, on donne acc�s au menu d�roulant uniquement aux admins locaux et g�n�raux
	// liste des acces : 0nouveau, 1utilisateur_ok, 2utilisateur_ko, 3admin_local, 4admin_general
	$tables_principales['spip_auteurs']['field']['adresse1'] = "TINYTEXT NOT NULL";
	$tables_principales['spip_auteurs']['field']['adresse2'] = "TINYTEXT DEFAULT NULL";
	$tables_principales['spip_auteurs']['field']['code_postal'] = "VARCHAR(20) NOT NULL DEFAULT ''";
	$tables_principales['spip_auteurs']['field']['ville'] = "VARCHAR(255) NOT NULL DEFAULT ''";
	$tables_principales['spip_auteurs']['field']['pays'] = "VARCHAR(255) NOT NULL DEFAULT ''";
	$tables_principales['spip_auteurs']['field']['lon'] = "DECIMAL(20,16) DEFAULT NULL";
	$tables_principales['spip_auteurs']['field']['lat'] = "DECIMAL(20,16) DEFAULT NULL";	
	$tables_principales['spip_auteurs']['field']['tel1'] = "VARCHAR(50) NOT NULL DEFAULT ''";
	$tables_principales['spip_auteurs']['field']['tel2'] = "VARCHAR(50) DEFAULT NULL";
	$tables_principales['spip_auteurs']['field']['commentaires'] = "TEXT DEFAULT NULL";

	
	$tables_principales['spip_sels'] =
	array('field' => &$spip_sels, 'key' => &$spip_sels_key);	

	$tables_principales['spip_annonces'] =
	array('field' => &$spip_annonces, 'key' => &$spip_annonces_key);	
	
	$tables_principales['spip_echanges'] =
	array('field' => &$spip_echanges, 'key' => &$spip_echanges_key);	

	$tables_principales['spip_themes'] =
	array('field' => &$spip_themes, 'key' => &$spip_themes_key);

	$tables_principales['spip_organisations'] =
	array('field' => &$spip_organisations, 'key' => &$spip_organisations_key);

	$tables_principales['spip_correspondances'] =
	array('field' => &$spip_correspondances, 'key' => &$spip_correspondances_key);	
	
	$tables_principales['spip_parametres'] =
	array('field' => &$spip_parametres, 'key' => &$spip_parametres_key);

	return $tables_principales;		
}


function sel_declarer_tables_objets_sql($tables){
// TABLES PRINCIPALES - OBJETS POUR SPIP 3

	$tables['spip_auteurs']['field']['prenom'] = "TINYTEXT NOT NULL";
	$tables['spip_auteurs']['field']['acces'] = "VARCHAR(15) NOT NULL DEFAULT '0nouveau'";
	// Ce champ acc�s permettra de ne pas s'appuyer sur les statuts Spip pour la gestion
	// Usage : Qd le form de cr�ation / modif d'auteur est rempli depuis l'espace public, on envoit tojours :
	// statut = r�dacteur (envoi cach�) et acces = 0attente pour la cr�ation. En modif, on donne acc�s au menu d�roulant uniquement aux admins locaux et g�n�raux
	// liste des acces : 0nouveau, 1utilisateur_ok, 2utilisateur_ko, 3admin_local, 4admin_general
	// Liste des statuts spip correspondants :'6forum','1comite','6forum','1comite','0minirezo'
		
	$tables['spip_auteurs']['field']['adresse1'] = "TINYTEXT NOT NULL";
	$tables['spip_auteurs']['field']['adresse2'] = "TINYTEXT DEFAULT NULL";
	$tables['spip_auteurs']['field']['code_postal'] = "VARCHAR(20) NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['field']['ville'] = "VARCHAR(255) NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['field']['pays'] = "VARCHAR(255) NOT NULL DEFAULT ''";	
	$tables['spip_auteurs']['field']['tel1'] = "VARCHAR(50) NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['field']['tel2'] = "VARCHAR(50) NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['field']['lon'] = "DECIMAL(20,16) DEFAULT NULL";
	$tables['spip_auteurs']['field']['lat'] = "DECIMAL(20,16) DEFAULT NULL";
	$tables['spip_auteurs']['field']['commentaires'] = "TEXT DEFAULT NULL";
	$tables['spip_auteurs']['field']['inscription'] = "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'"; // date premi�re inscription
	
	
    $tables['spip_sels'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_sel" => "BIGINT(21) NOT NULL",
			"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"num_adhesion"   => "VARCHAR(10) DEFAULT NULL",
			"etat_compte" => "VARCHAR(20) DEFAULT NULL", // "actif" ou "inactif"
			"etat_compte_depuis"   => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //= format datetime 2011-03-14 22:20:33
			"adresse1"  => "TINYTEXT NOT NULL",
			"adresse2" => "TINYTEXT DEFAULT NULL",
			"code_postal" => "VARCHAR(20) NOT NULL DEFAULT ''",
			"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"pays" => "VARCHAR(150) NOT NULL DEFAULT 'France'",
			"lon" => "DECIMAL(20,16) DEFAULT NULL",
			"lat" => "DECIMAL(20,16) DEFAULT NULL",	
			"tel1" => "VARCHAR(50) NOT NULL DEFAULT ''",
			"tel2" => "VARCHAR(50) DEFAULT NULL",
			"email" => "TINYTEXT DEFAULT NULL",
			"site" => "TINYTEXT DEFAULT NULL",	
			"nom_unite" => "VARCHAR(255) DEFAULT NULL", // basilic, piaf, bouchon...
			"credit_ouverture" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // chiffre : valeur � cr�diter � tout ouverture de comte. pr�voir question : � pr�lever sur le compte d'un SEL ou corne d'abondance ?
			"validation_echange" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'", // permettre la validation implicite d'un �change saisi par un membre ? si oui, nombre de jours laiss� avant validation automatique, 0 sinon 
			"cotisation_unite" => "VARCHAR(30) NOT NULL DEFAULT ''", // combinaison de 3 cha�nes
			"cotisation_montant_taux" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'" // un chiffre � pr�lever, � prendre en valeur ou en pourcentage
			/*
			cotisation_unite : combinaison de 3 cha�nes :
			1. existe-t-il une cotisation en unit�s ?
			- sans, forfait, pourcnt
			2. sur les comptes actifs ou tous les comptes ?
			- tous, actf (tous les comptes, les comptes actifs uniquement)
			3. A quelle fr�quence ?
			- 12 (tous les mois), 4 (une fois par trimestre), 2 (toues les 6 mois, 2 fois / an), 1 (une foisl'ann�e)
			Exemple de cha�ne g�n�r�e : pourcntactf01, ou forfaittous01
			4. A pr�voir en plus : sur les comptes positifs uniquement
			*/	
        ),
        'key' => array(
			"PRIMARY KEY" => "id_sel",
			"KEY" => "nom"
        ),
		'rechercher_champs' => array(
            'nom' => 8, 'ville' => 2, 'nom_unite' => 2
		),
    );
	
	
    $tables['spip_annonces'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_annonce" => "BIGINT(21) NOT NULL",
			"titre" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"direction_echange" => "VARCHAR(10) NOT NULL DEFAULT 'offre'", // offre, demande, partage
			"nature" => "VARCHAR(10) NOT NULL DEFAULT 'service'", // service, savoir (=stage), bien
			"id_auteur" => "BIGINT(21) NOT NULL", // auteur de l'annonce
			"date_saisie" => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP", //= format datetime 2011-03-14 22:20:33
			"description" => "TEXT NOT NULL",
			"nombre_personnes" => "SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0'",	
			"date_debut" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",  // date de d�but, pour �v�nement � date fixe ou libre = format timestamp 2011-03-14 22:20:33
			"date_fin" => "DATETIME DEFAULT NULL",   // date de fin sp�cifi�e uniquement pour les offres � dates fixes = format timestamp 2011-03-14 22:20:33
			"adresse1"  => "TINYTEXT NOT NULL",
			"adresse2" => "TINYTEXT DEFAULT NULL",
			"code_postal" => "VARCHAR(20) NOT NULL DEFAULT ''",
			"ville" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"pays" => "VARCHAR(150) NOT NULL DEFAULT ''",
			"transport"  => "TEXT DEFAULT NULL",
			"repas"  => "TEXT DEFAULT NULL",
			"hebergement"  => "TEXT DEFAULT NULL",
			"nom_referent" => "VARCHAR(255) DEFAULT NULL", // si l'auteur n'est pas le r�f�rent
			"tel1_referent" => "VARCHAR(50) DEFAULT NULL",
			"tel2_referent" => "VARCHAR(50) DEFAULT NULL",
			"email_referent" => "TINYTEXT DEFAULT NULL",
			"statut" => "VARCHAR(20) NOT NULL DEFAULT ''", // pour l'�ventuelle gestion d'une mod�ration a priori des annonces
			// liste des statuts : 0nouvelle, 1annonce_ok, 2annonce_ko, ?? 3perime ?? ce dernier statut pourrait �tre calcul� automatiquement � partir des dates
			"total_unite" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
			"si_unite_heure" => "SMALLINT(3) UNSIGNED NOT NULL DEFAULT '0'",
			"total_fiduc" => "INT(5) UNSIGNED NOT NULL DEFAULT '0'",
			"justification_fiduc" => "TINYTEXT DEFAULT NULL", // ce champ devra �tre rempli si des frais en euros sont indiqu�s
			"commentaires"  => "TEXT DEFAULT NULL"
        ),
        'key' => array(
			"PRIMARY KEY" => "id_annonce",
			"KEY" => "titre",
			"KEY" => "id_auteur",
			"KEY" => "date_debut"
        ),
		'rechercher_champs' => array(
            'titre' => 8, 'nature' => 1, 'direction_echange' => 1, 'description' => 1, 'ville' => 1, 
		),
    );
	

    $tables['spip_echanges'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_echange" => "BIGINT(21) NOT NULL",
			"id_offreur" => "BIGINT(21) DEFAULT NULL", // vers id_offreur (= id_auteur) de la vue sur spip_auteurs
			"offreur_ext"  => "VARCHAR(255) DEFAULT NULL",
			"offreur_sel_ext"   => "VARCHAR(255) DEFAULT NULL",
			"titre" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"date_echange"  => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // = format datetime 2011-03-14 22:20:33
			"saisi_par" => "VARCHAR(150) NOT NULL DEFAULT 'offreur'", // 'offreur', 'demandeur' ou 'automatique'
			"date_saisie" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //  = format timestamp 2011-03-14 22:20:33
			"nature"   => "VARCHAR(20) NOT NULL DEFAULT 'echange'",  // 'echange', 'cotizunite', 'cloturecompte'
			"id_demandeur"  =>  "BIGINT(21) DEFAULT NULL", // vers id_demandeur (= id_auteur) de la vue sur spip_auteurs
			"demandeur_ext"  =>  "VARCHAR(255) DEFAULT NULL",
			"demandeur_sel_ext" => "VARCHAR(255) DEFAULT NULL",
			"valeur" => "INT(10) NOT NULL DEFAULT '0'",
			"date_validation"  => "TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP",
			"id_valideur" => "BIGINT(21) DEFAULT NULL", // vers id_auteur
			"commentaires"  => "TEXT DEFAULT NULL"
        ),
        'key' => array(
			"PRIMARY KEY" => "id_echange",
			"KEY" => "id_offreur",
			"KEY" => "id_demandeur"
        ),
    );
	
    $tables['spip_themes'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_theme" => "BIGINT(21) NOT NULL",
			"id_parent" => "BIGINT(21) NOT NULL DEFAULT '0'", // 0 si theme racine
			"id_secteur" => "BIGINT(21) NOT NULL",
			"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"descriptif" => "TEXT DEFAULT NULL"
        ),
        'key' => array(
			"PRIMARY KEY" => "id_theme",
			"KEY" => "id_parent"
        ),
    );	
 
// Une organisation est un groupe exterieur avec lequel les s�listes et les sels locaux sont en relation (adh�sion ou correspondance).
// Ce peut �tre : un intersel r�gional, un atelier selidaire, RdS, etc...

    $tables['spip_organisations'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_organisation" => "BIGINT(21) NOT NULL",
			"nom" => "VARCHAR(255) NOT NULL DEFAULT ''",
			"description" => "TEXT DEFAULT NULL"
        ),
        'key' => array(
			"PRIMARY KEY" => "id_organisation"
        ),
    );

// Cette table a un statut interm�diaire entre principale et auxiliaire.
// Comme table auxiliaire, elle d�crit :
// - une relation d'appartenance N/N (binaire) auteur / sel ou sel / organisation (adh�sion du SEL � un intersel, participation � un atelier Selidaire, etc...)
// - une relation de correspondace N/N (ternaire) d'un adh�rent pour son SEL vis � vis d'une organisation (RDS, etc...) : donc auteur / sel / organisation
// Elle sert de table principale pour toutes les informations relatives � l'adh�sion individuelle � un SEL (auteur / sel) : num�ro et date d'adh�sion, mode de r�cup�ration de catalogue, etc...

	
	$tables['spip_correspondances'] = array(
        'principale' => "oui",
        'field'=> array(
			"id_correspondance" => "BIGINT(21) NOT NULL",
			"id_auteur" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
			"id_sel" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
			"num_adhesion"   => "VARCHAR(10) NOT NULL DEFAULT ''",
			"cotisation_fiduc1" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // date de premi�re cotisation
			"cotisation_fiduc" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", // date de derni�re cotisation
			"recupere_catalogue" => "VARCHAR(10) NOT NULL DEFAULT ''", //permanence, internet, poste
			"etat_compte" => "VARCHAR(10) NOT NULL DEFAULT ''", //actif / inactif
			"etat_compte_depuis" => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'", //actif / inactif
			"id_organisation" 	=> "BIGINT(21) NOT NULL DEFAULT '0'"
		),
        'key' => array(
			"PRIMARY KEY" 	=> "id_correspondance"
        ),
	);

	
	$tables['spip_parametres'] = array(
	    'principale' => "oui",
        'field'=> array(
			"id_parametre" => "BIGINT(21) NOT NULL",
			"cible" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"liste_diffusion" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"expediteur" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"www" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"signature" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"acces_inscription" => "VARCHAR(100) NOT NULL DEFAULT ''",
			"pagination" => "INT(3) UNSIGNED NOT NULL DEFAULT '10'"
		),
        'key' => array(
			"PRIMARY KEY" => "id_parametre"
        ),		
	);	
	
	return $tables;

}



function sel_declarer_tables_auxiliaires($tables_auxiliaires){
// TABLES AUXILIAIRES POUR SPIP 2 ET 3

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