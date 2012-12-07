<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	//Inclusion des fichiers
	include_spip('inc/meta');

	//MAJ e la base
	function spipimmo_upgrade()
	{
		include_spip('base/create');
		include_spip('base/spipimmo');
		creer_base();
		//Remplacement des "oui" et "non" par " 0" et "1"
		$tabOuiNon[""]=0;
		$tabOuiNon["Non"]=0;
		$tabOuiNon["Oui"]=1;
		$resListeAnnonce=sql_select("`id_annonce`, `publier`, `ascenseur`, `prestige`, `acces_handi`, `piscine`, `date_offre`, `date_modification`",  "spip_annonces");
		$nbAnnonce=sql_count($resListeAnnonce);
		for($i=0; $i<$nbAnnonce; $i++)
		{
			$enrAnnonce=sql_fetch($resListeAnnonce);

			//Remplacement du format des dates
			$tabDateOffre=split("/",$enrAnnonce["date_offre"]);
			$tabDateModif=split("/",$enrAnnonce["date_modification"]);

			//Update des annonces
			sql_update("spip_annonces",
									array("acces_handi"=> $tabOuiNon[$enrAnnonce["acces_handi"]],
											"prestige"=> $tabOuiNon[$enrAnnonce["prestige"]],
											"ascenseur"=> $tabOuiNon[$enrAnnonce["ascenseur"]],
											"piscine"=> $tabOuiNon[$enrAnnonce["piscine"]],
											"publier"=> $tabOuiNon[$enrAnnonce["publier"]],
											"date_modification"=> $tabDateModif[2] . "-" . $tabDateModif[1] . "-" . $tabDateModif[0],
											"date_offre"=>$tabDateOffre[2] . "-" . $tabDateOffre[1] . "-" . $tabDateOffre[0])
								, 'id_annonce=' . $enrAnnonce["id_annonce"]);
		}

		//Table `spip_annonces`
		sql_alter("TABLE `spip_annonces` MODIFY `id_annonce` int(50) AUTO_INCREMENT");
		sql_alter("TABLE `spip_annonces` MODIFY `publier` tinyint(1)");
		sql_alter("TABLE `spip_annonces` MODIFY `type_offre` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `vente_location` varchar(8)");
		sql_alter("TABLE `spip_annonces` MODIFY `n_mandat` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `type_mandat` varchar(50)");
		sql_alter("TABLE `spip_annonces` MODIFY `date_offre` date");
		sql_alter("TABLE `spip_annonces` MODIFY `date_modification` date");
		sql_alter("TABLE `spip_annonces` MODIFY `date_disponibilite` date");
		sql_alter("TABLE `spip_annonces` MODIFY `negociateur` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `prix_loyer` int(30)");
		sql_alter("TABLE `spip_annonces` MODIFY `honoraires` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `travaux` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `charges` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `depot_garantie` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `taxe_habitation` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `taxe_fonciere` int(10)");
		sql_alter("TABLE `spip_annonces` MODIFY `adr_bien_1` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `adr_bien_2` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `cp_bien` int(5)");
		sql_alter("TABLE `spip_annonces` MODIFY `ville_bien` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `cp_internet` int(5)");
		sql_alter("TABLE `spip_annonces` MODIFY `ville_internet` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `quartier` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `residence` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `transport` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `proximite` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `secteur` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `categorie` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_pieces` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_chambres` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `surf_habit` int(6)");
		sql_alter("TABLE `spip_annonces` MODIFY `surf_carrez` int(6)");
		sql_alter("TABLE `spip_annonces` MODIFY `surf_sejour` int(6)");
		sql_alter("TABLE `spip_annonces` MODIFY `surf_terrain` int(6)");
		sql_alter("TABLE `spip_annonces` MODIFY `etage` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `code_etage` int(6)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_etage` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `annee_cons` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `type_cuisine` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_wc` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_sdb` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_sde` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_park_int` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_park_ext` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_garages` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `type_soussol` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_caves` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `type_chauf` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `nat_chauf` varchar(255)");
		sql_alter("TABLE `spip_annonces` MODIFY `ascenseur` int(2)");
		sql_alter("TABLE `spip_annonces` MODIFY `balcon` int(4)");
		sql_alter("TABLE `spip_annonces` MODIFY `terrasse` int(5)");
		sql_alter("TABLE `spip_annonces` MODIFY `piscine` bool");
		sql_alter("TABLE `spip_annonces` MODIFY `acces_handi` bool");
		sql_alter("TABLE `spip_annonces` MODIFY `nb_murs_mit` int(1)");
		sql_alter("TABLE `spip_annonces` MODIFY `facade_terrain` int(3)");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_annonce_fr` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_annonce_uk` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_annonce_sp` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_annonce_de` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_annonce_it` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `texte_mailing` longtext");
		sql_alter("TABLE `spip_annonces` MODIFY `prestige` bool");
		sql_alter("TABLE `spip_annonces` DROP `id_date`");
		sql_alter("TABLE `spip_annonces` ADD COLUMN `DPE` varchar(3)");

		//Table `spip_documents_annonces`
		sql_alter("TABLE `spip_documents_annonces` MODIFY `id_document` int(11) AUTO_INCREMENT");
		sql_alter("TABLE `spip_documents_annonces` MODIFY `numero_dossier` int(10)");
		sql_alter("TABLE `spip_documents_annonces` MODIFY `fichier` varchar(255)");
		sql_alter("TABLE `spip_documents_annonces` MODIFY `taille` int(11)");
		sql_alter("TABLE `spip_documents_annonces` ADD `type` bool");
		sql_update('spip_documents_annonces', array('type'=>'1'));

		//On remplit la table `spip_types_offres` si nécessaire
		$resTypesOffres=sql_select("*", "spip_types_offres");
		$nbTypesOffres=sql_count($resTypesOffres);
		if($nbTypesOffres==0)
		{
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
		}

		//Suppression de mauvais documents
		$handle=opendir(_DIR_IMG);
		while($fichier=readdir($handle))
		{
			if(ereg("^o[0-9]*-" . "[0-9]*.[a-zA-Z]*$", $fichier))
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
				if(ereg("^o[0-9]*-" . "[0-9]*.[a-zA-Z]*$", $fichier))
				{
					unlink(_SPIPIMMO_REP_VIGNETTES . $fichier);
				}
			}
			closedir($handle);
		}

		//Le nouveau chemin des images
		$resDocument=sql_select("*", "spip_documents_annonces", "fichier LIKE '../%'");
		$nbDocument=sql_count($resDocument);

		for($i=0; $i<$nbDocument; $i++)
		{
			$enrDocument=sql_fetch($resDocument);
			sql_updateq("spip_documents_annonces", array("fichier"=>substr($enrDocument['fichier'], 3)), "id_document=" . $enrDocument['id_document']);
		}

		//Les metas
		effacer_meta('version_spipimmo');
		ecrire_meta('spipimmo_version', _SPIPIMMO_VERSION);
	}
?>
