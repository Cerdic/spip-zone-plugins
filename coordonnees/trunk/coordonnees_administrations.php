<?php
/**
 * Plugin Coordonnees pour Spip 3
 * Licence GPL (c) 2010 - Marcimat / Ateliers CYM
 */

function coordonnees_upgrade($nom_meta_base_version, $version_cible){


	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_adresses')),
		array('maj_tables', array('spip_adresses_liens')),
		array('maj_tables', array('spip_numeros')),
		array('maj_tables', array('spip_numeros_liens')),
		array('maj_tables', array('spip_emails')),
		array('maj_tables', array('spip_emails_liens')),
		array('ecrire_meta', 'coordonnees', serialize(array('objets'=>array('auteur'))))
	);
	$maj['1.1'] = array(
		array('sql_update', array(array("voie" => "CONCAT(numero, ' ', voie)"),array("numero IS NOT NULL", "numero <> ''"))),
		array('sql_alter', "TABLE spip_adresses DROP COLUMN numero")
	);

	$maj['1.2'] = array(
		array('sql_alter', 'TABLE spip_adresses CHANGE type_adresse titre varchar(255) not null default ""'),
		array('sql_alter', 'TABLE spip_numeros CHANGE type_numero titre varchar(255) not null default ""'),
		array('sql_alter', 'TABLE spip_emails CHANGE type_email titre varchar(255) not null default ""')
	);

	// On passe les pays en code ISO, beaucoup plus g�n�riques que les ids SQL
	$maj['1.3'] = array(
		array('sql_alter', 'TABLE spip_adresses ADD pays_code varchar(2) not null default ""'),
		array('coordonnees_upgrade_1_3'),
		array('sql_alter', 'TABLE spip_adresses DROP pays'),
		array('sql_alter', 'TABLE spip_adresses CHANGE pays_code pays varchar(2) not null default ""'),
	);

	// On avait supprimer les types, mais ils reviennent en force mais dans les LIENS
	$maj['1.4'] = array(
			array('sql_alter', 'TABLE spip_adresses_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_adresses_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_adresses_liens ADD PRIMARY KEY (id_adresse, id_objet, objet, type)'),
			array('sql_alter', 'TABLE spip_numeros_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_numeros_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_numeros_liens ADD PRIMARY KEY (id_numero, id_objet, objet, type)'),
			array('sql_alter', 'TABLE spip_emails_liens ADD type varchar(25) not null default ""'),
			array('sql_alter', 'TABLE spip_emails_liens DROP PRIMARY KEY'),
			array('sql_alter', 'TABLE spip_emails_liens ADD PRIMARY KEY (id_email, id_objet, objet, type)'),

			);
	// mettre les auteurs par defaut comme objet �coordonnable�
	$maj['1.5'] = array(
		array('ecrire_meta','coordonnees', serialize(array('objets'=>array('auteur'))))
	);

	// ajout du champs region a la table adresses
	$maj['1.6'] = array(
		array('maj_tables', array('spip_adresses')),
	);

	// migration de certaines valeurs pour pouvoir faire fonctionner les selecteurs pendant l'edition
	//!\ comme on n'est pas certain de tous les migrer il y a donc rupture de compatibilite ? :-S
	$maj['1.7'] = array(
		array('sql_updateq', "spip_adresses_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'work'), "LOWER(type) LIKE 'pro%'"),
		array('sql_updateq', "spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'"),
		array('sql_updateq', "spip_adresses_liens", array('type'=>'home'), "LOWER(type) LIKE 'dom%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'home'), "LOWER(type) LIKE 'perso%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'cel%'"),
		array('sql_updateq', "spip_numeros_liens", array('type'=>'cell'), "LOWER(type) LIKE 'mob%'"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


function coordonnees_upgrade_1_3()
{
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
}


function coordonnees_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_adresses");
	sql_drop_table("spip_adresses_liens");
	sql_drop_table("spip_numeros");
	sql_drop_table("spip_numeros_liens");
	sql_drop_table("spip_emails");
	sql_drop_table("spip_emails_liens");

	effacer_meta('coordonnees');
	effacer_meta($nom_meta_base_version);
}

?>
