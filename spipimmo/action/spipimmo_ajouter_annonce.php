<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function action_spipimmo_ajouter_annonce()
	{
		$tabAnnonce=array(
				'publier'=>_request('publier_offre'),
				'type_offre'=>_request('type_offre'),
				'vente_location'=>_request('vente_location'),
				'n_mandat'=>_request('numero_mandat'),
				'type_mandat'=>_request('type_mandat'),
				'date_offre'=>date('Y-m-d'),
				'date_modification'=>date('Y-m-d'),
				'date_disponibilite'=>_request('annee_dispo') . '-' . _request('mois_dispo') . '-' . _request('jour_dispo'),
				'negociateur'=>_request('negociateur'),
				'prix_loyer'=>_request('prix_loyer'),
				'honoraires'=>_request('honoraire'),
				'travaux'=>_request('travaux'),
				'charges'=>_request('charge'),
				'depot_garantie'=>_request('depot_garantie'),
				'taxe_habitation'=>_request('taxe_habitation'),
				'taxe_fonciere'=>_request('taxe_fonciere'),
				'adr_bien_1'=>_request('adresse_1'),
				'adr_bien_2'=>_request('adresse_2'),
				'cp_bien'=>_request('code_postal'),
				'ville_bien'=>_request('ville'),
				'cp_internet'=>_request('code_postal_internet'),
				'ville_internet'=>_request('ville_internet'),
				'quartier'=>_request('quartier'),
				'residence'=>_request('residence'),
				'transport'=>_request('transport'),
				'proximite'=>_request('proximite'),
				'secteur'=>_request('secteur'),
				'categorie'=>_request('categorie'),
				'nb_pieces'=>_request('nombre_piece'),
				'nb_chambres'=>_request('nombre_chambre'),
				'surf_habit'=>_request('surface_habitable'),
				'surf_carrez'=>_request('surface_carre'),
				'surf_sejour'=>_request('surface_sejour'),
				'surf_terrain'=>_request('surface_terrain'),
				'etage'=>_request('etage'),
				'code_etage'=>_request('code_etage'),
				'nb_etage'=>_request('nombre_etage'),
				'annee_cons'=>_request('annee_construction'),
				'type_cuisine'=>_request('type_cuisine'),
				'nb_wc'=>_request('nombre_wc'),
				'nb_sdb'=>_request('nombre_bain'),
				'nb_sde'=>_request('nombre_eau'),
				'nb_park_int'=>_request('nombre_parking_interieur'),
				'nb_park_ext'=>_request('nombre_parking_exterieur'),
				'nb_garages'=>_request('nombre_garage'),
				'type_soussol'=>_request('type_sous_sol'),
				'nb_caves'=>_request('nombre_cave'),
				'type_chauf'=>_request('type_chauffage'),
				'nat_chauf'=>_request('nature_chauffage'),
				'ascenseur'=>_request('ascenseur'),
				'balcon'=>_request('balcon'),
				'terrasse'=>_request('terrasse'),
				'piscine'=>_request('piscine'),
				'acces_handi'=>_request('acces_handicape'),
				'nb_murs_mit'=>_request('nombre_mur'),
				'facade_terrain'=>_request('facade'),
				'texte_annonce_fr'=>_request('texte_francais'),
				'texte_annonce_uk'=> _request('texte_anglais'),
				'texte_annonce_sp'=>_request('texte_espagnol'),
				'texte_annonce_it'=>_request('texte_italien'),
				'texte_annonce_de'=>_request('texte_allemand'),
				'texte_mailing'=>_request('texte_mailing'),
				'prestige'=>_request('prestige'),
				'DPE'=>_request('dpe')
			);

		// Requête d'insertion
		$resInsertionAnnonce=sql_insertq("spip_annonces", $tabAnnonce);

		if($resInsertionAnnonce)
		{
			redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=ajouter_document&id=' . $resInsertionAnnonce);
		}
		else
		{
			redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=spipimmo&ajout=0');
		}
	}
?>
