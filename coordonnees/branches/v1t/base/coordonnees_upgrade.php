<?php
/**
 * Plugin Coordonnees pour Spip 2.1
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function coordonnees_upgrade($nom_meta_base_version, $version_cible){
	include_spip('inc/meta');


	/**
	 *
	 *  11/01/2009 : ajout table spip_emails, version 1.0.1
	 *
	 */

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	if ($current_version=="0.0") {
		include_spip('base/create');
		creer_base();
		// mettre les auteurs par defaut comme objet «coordonnable»
		ecrire_meta('coordonnees', serialize(array('objets'=>array('auteur'))));
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

	// On utilise plus le champ "numero" qui sera inclu dans la "voie"
	if (version_compare($current_version, "1.1", "<")) {
		$ok = true;

		// on ajoute le contenu du champ "numero" au champ "voie"
		sql_update("spip_adresses",
			array("voie" => "CONCAT(numero, ' ', voie)"), //!\ PostgreSQL, Sqlite, MySQL apres "SET sql_mode='PIPES_AS_CONCAT';" (sinon interprete comme le double-pipe comme OR), Oracle : "numero||' '||voie;" Nota1: L'equivalent SqlServer est "numero+' '+voie;" Nota2: Oracle connait CONCAT mais que ne prend que deux arguments donc "CONCAT(numero,CONCAT(' ',voie));" Bref: ce n'est pas portable !!!
			array("numero IS NOT NULL", "numero <> ''"));

		if ($ok){
			// on supprime le champ "numero"
			sql_alter("TABLE spip_adresses DROP COLUMN numero");

			spip_log('Tables coordonnées correctement passsées en version 1.1','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.1");
		}
		else return false;
	}

	// On supprime les "type" en les transformant en vrai "titre" libres
	if (version_compare($current_version, "1.2", "<")) {
		$ok = true;

		// On renomme les champs "type_truc" en "titre" tout simplement + on les allonge
		$ok &= sql_alter("TABLE spip_adresses ADD titre VARCHAR(255) NOT NULL DEFAULT ''");
		$ok &= sql_update('spip_adresses', array('titre' => 'type_adresse') );
		$ok &= sql_alter("TABLE spip_numeros ADD titre VARCHAR(255) NOT NULL DEFAULT ''");
		$ok &= sql_update('spip_numeros', array('titre' => 'type_numero') );
		$ok &= sql_alter("TABLE spip_emails ADD titre VARCHAR(255) NOT NULL DEFAULT ''");
		$ok &= sql_update('spip_emails', array('titre' => 'type_email') );

		if ($ok){
			sql_alter("TABLE spip_adresses DROP COLUMN type_adresse");
			sql_alter("TABLE spip_numeros DROP COLUMN type_numero");
			sql_alter("TABLE spip_emails DROP COLUMN type_email");
			spip_log('Tables coordonnées correctement passsées en version 1.2','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version='1.2');
		}
		else return false;
	}

	// On passe les pays en code ISO, beaucoup plus génériques que les ids SQL
	if (version_compare($current_version, "1.3", "<")) {
		if ( test_plugin_actif('pays') ) { // cas normal si on a mis a jour regulierement
			$ok = true;

			// On ajoute un champ pour le code car il faut les deux champs pour la transistion
			$ok &= sql_alter("TABLE spip_adresses ADD pays_code VARCHAR(2) NOT NULL DEFAULT ''");

			// On parcourt les adresses pour remplir le code du pays
			$adresses = sql_allfetsel('id_adresse,pays', 'spip_adresses');
			if ($adresses and is_array($adresses)){
				foreach ($adresses as $adresse){
					$ok &= sql_update(
					'spip_adresses',
					array('pays_code' => '(SELECT code FROM spip_pays WHERE id_pays='.intval($adresse['pays']).')'),
					'id_adresse='.intval($adresse['id_adresse'])
					);
				}
			}

			// On supprime l'ancien
			$ok &= sql_alter('TABLE spip_adresses DROP pays');

			// On change le nom du nouveau
			$ok &= sql_alter("TABLE spip_adresses ADD pays VARCHAR(3) NOT NULL DEFAULT '' ");
			$ok &= sql_update('spip_adresses', array('pays'=> 'pays_code') );
			$ok &= sql_alter('TABLE spip_adresses DROP pays_code');

			if ($ok){
				spip_log('Tables coordonnées correctement passsées en version 1.3','coordonnees');
				ecrire_meta($nom_meta_base_version, $current_version="1.3");
			}
			else return false;
		} else { // on a saute directement a la v1.5 du plugin et donc on n'est pas passe par la case dependance au plugin "Pays"...
			spip_log('Tables coordonnées non passsées en version 1.3 car plugin Pays absent !','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.2b");
			return true;
		}
	}

	// On avait supprimer les types, mais ils reviennent en force mais dans les LIENS
	if (version_compare($current_version, "1.4", "<")) {
		$ok = true;

		// On ajoute un champ "type" plus petit que l'ancien (car vrai type donc généralement juste un mot)
		$ok &= sql_alter('TABLE spip_adresses_liens ADD type VARCHAR(25) not null default ""');
		$ok &= sql_alter('TABLE spip_adresses_liens DROP PRIMARY KEY');
		$ok &= sql_alter('TABLE spip_adresses_liens ADD PRIMARY KEY (id_adresse, id_objet, objet, type)');

		$ok &= sql_alter('TABLE spip_numeros_liens ADD type VARCHAR(25) not null default ""');
		$ok &= sql_alter('TABLE spip_numeros_liens DROP PRIMARY KEY');
		$ok &= sql_alter('TABLE spip_numeros_liens ADD PRIMARY KEY (id_numero, id_objet, objet, type)');

		$ok &= sql_alter('TABLE spip_emails_liens ADD type VARCHAR(25) not null default ""');
		$ok &= sql_alter('TABLE spip_emails_liens DROP PRIMARY KEY');
		$ok &= sql_alter('TABLE spip_emails_liens ADD PRIMARY KEY (id_email, id_objet, objet, type)');

#		if ($ok){
			spip_log('Tables coordonnées correctement passsées en version 1.4','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.4");
#		}
#		else return false;
	}

	if (version_compare($current_version, "1.5", "<")) {
		// mettre les auteurs par defaut comme objet «coordonnable»
		ecrire_meta('coordonnees', serialize(array('objets'=>array('auteur'))));
		ecrire_meta($nom_meta_base_version, $current_version="1.5");
	}

	// prise en compte des regions/territoires/departements dans les adresses
	if (version_compare($current_version, "1.6", "<")) {
		$ok = true;

		include_spip('base/upgrade');
		maj_tables('spip_adresses'); //=		$ok &= sql_alter("TABLE spip_adresses ADD region VARCHAR(40) DEFAULT '' NOT NULL");

		if ($ok){ // "maj_tables" ne renvoit rien... mais le retour de "sql_alter" n'est pas pertinent (sauf en cas d'indisponibilite du serveur ou il renvoit FALSE)
			spip_log('Tables coordonnées correctement passsées en version 1.6','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.6");
		}
		else return false;
	}

	// migration de certaines valeurs pour pouvoir faire fonctionner les selecteurs pendant l'edition
	//!\ comme on n'est pas certain de tous les migrer il y a donc rupture de compatibilite ? :-S
	if (version_compare($current_version, "1.7", "<")) {
		$ok = true;
		// transformer les "pro"* en "work" pour pouvoir faire fonctionner les selecteurs pendant l'edition
		$ok &= sql_updateq("spip_adresses_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'");
		$ok &= sql_updateq("spip_numeros_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'");
		// transformer les "perso"* en "home" pour pouvoir faire fonctionner les selecteurs pendant l'edition
		$ok &= sql_updateq("spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'");
		$ok &= sql_updateq("spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'dom%'");
		$ok &= sql_updateq("spip_numeros_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'");
		// transformer les "mobi"* en "cell" pour pouvoir faire fonctionner les selecteurs pendant l'edition
		$ok &= sql_updateq("spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'cel%'");
		$ok &= sql_updateq("spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'mob%'");

//		if ($ok){ // il n'est pas dit que toutes ces "update"s passent :-/
			spip_log('Tables coordonnées correctement passsées en version 1.7','coordonnees');
			ecrire_meta($nom_meta_base_version, $current_version="1.7");
//		}
//		else return false;
	}

	// Les pages web deviennent des coordonnees
	if (version_compare($current_version, "1.8", "<")) {
		$ok = true;

		include_spip('base/create');
		maj_tables('spip_syndic_liens'); //=		$ok &= sql_create("spip_syndic_liens", array('id_syndic'=>"BIGINT NOT NULL DEFAULT 0", 'id_objet'=>"BIGINT NOT NULL DEFAULT 0", 'objet'=>"VARCHAR(25) NOT NULL", 'type'=>"VARCHAR(25) NOT NULL DEFAULT ''", ), array('PRIMARY KEY'=>"id_syndic, id_objet, objet, type", 'KEY id_syndic'=>"id_syndic", false, false ) );

		if ($ok){ // ni "sql_create" ni "maj_tables" ne renvoient rien :-/
			ecrire_meta($nom_meta_base_version, $current_version="1.8");
		}
		else return false;
	}

	// On distingue les formats des types d'usage
	if (version_compare($current_version, "1.9", "<")) {
		$ok = true;

		include_spip('base/upgrade');
		maj_tables('spip_emails'); //=		$ok &= sql_alter("TABLE spip_emails ADD format VARCHAR(9) DEFAULT '' NOT NULL");

		if ($ok){ // "maj_tables" ne renvoit rien... mais le retour de "sql_alter" n'est pas pertinent (sauf en cas d'indisponibilite du serveur ou il renvoit FALSE)
			ecrire_meta($nom_meta_base_version, $current_version="1.9");
		}
		else return false;
	}

}

function coordonnees_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");
	sql_drop_table("spip_syndic_liens"); // peut etru utilise par d'autres plugins non ?

	effacer_meta('coordonnees');
	effacer_meta($nom_meta_base_version);
}

?>