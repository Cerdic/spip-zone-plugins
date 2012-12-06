<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	//Inclusion des fichiers
	include_spip('inc/meta');

	//Installation / MAJ / Désinstallation du plugin
	function spipimmo_install($action)
	{
		switch ($action)
		{
			case 'test':
				include_spip('base/abstract_sql');

				$desc1=sql_showtable("spip_annonces", true);
				$desc2=sql_showtable("spip_documents_annonces", true);
				$desc3=sql_showtable("spip_types_offres", true);

				return ((isset($desc1['field']['id_annonce'])) and (isset($desc2['field']['id_document'])) and (isset($desc3['field']['id_type_offre'])) and $GLOBALS['meta']['spipimmo_version']==_SPIPIMMO_VERSION);

				break;

			//Installation ou mises à jour des tables des tables
			case 'install':
				$desc1=sql_showtable("spip_annonces", true);
				$desc2=sql_showtable("spip_documents_annonces", true);
				$desc3=sql_showtable("spip_types_offres", true);

				//Installation
				if(!isset($desc1['field']['id_annonce']))
				{
					spipimmo_base_creer();
				}

				$desc1=sql_showtable("spip_annonces", true);
				$desc2=sql_showtable("spip_documents_annonces", true);
				$desc3=sql_showtable("spip_types_offres", true);

				//MAJ =>1.0 à 2.0 et plus
				if(!isset($desc3['field']['id_type_offre']) or ($GLOBALS['meta']['spipimmo_version']<_SPIPIMMO_VERSION) or (!isset($GLOBALS['meta']['spipimmo_version'])))
				{
					include_spip('base/spipimmo_upgrade');
					spipimmo_upgrade();
				}
				break;

			//Desctruction des tables
			case 'uninstall':
				spipimmo_vider_tables();
				break;
		}
	}

	//Installation de la base
	function spipimmo_base_creer()
	{
		//Vérification mise à jour ou installation
		include_spip('base/create');
		include_spip('base/spipimmo');
		creer_base();

		//On remplit la table `spip_types_offres` si nécessaire {possibilité de mettre le code ailleurs?}
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Appartement"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Maison / Villa"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Terrain"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Commerce"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Boutique"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Bureau / Local commercial"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Bureaux"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Parking"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Local"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Immeuble"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Hangar"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"H&ocirc;tel particulier"));
		sql_insertq("spip_types_offres", array("libelle_offre"=>"Divers"));

		//Création répertoire vignette
		if(!file_exists(_DIR_IMG . 'cache100xXXX'))
		{
			mkdir(_DIR_IMG . 'cache100xXXX');
		}

		//Sauvegarde de la version dans les métas
		ecrire_meta('spipimmo_version', _SPIPIMMO_VERSION);
	}


	//Suppressimer spipimmo
	function spipimmo_vider_tables()
	{
		include_spip('base/abstract_sql');

		//Supression des tables
		sql_drop_table("spip_annonces, spip_documents_annonces, spip_types_offres", true);

		//Suprression des documents
		$handle=opendir(_DIR_IMG);
		while($fichier=readdir($handle))
		{
			if(ereg("^immo[0-9]*-" . "[0-9]*.[a-zA-Z]*$", $fichier))
			{
				unlink(_DIR_IMG . $fichier);
			}
		}
		closedir($handle);

		if(file_exists(_SPIPIMMO_REP_VIGNETTES))
		{
			$handle=opendir(_SPIPIMMO_REP_VIGNETTES);
			while($fichier=readdir($handle))
			{
				if(ereg("^immo[0-9]*-" . "[0-9]*.[a-zA-Z]*$", $fichier))
				{
					unlink(_SPIPIMMO_REP_VIGNETTES . $fichier);
				}
			}
			closedir($handle);
		}

		//Suppression meta
		effacer_meta('spipimmo_version');
	}
?>
