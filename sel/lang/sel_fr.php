<?php

// Ce fichier remplit la double fonction de fournir toutes les chaînes de langues
// pour Spip 2, tous objets confondus (sel_fr.php : préfixe du plugin),
// et les chaines de l'objet éditorial SEL pour spip3 (sel_fr.php : nom de l'objet déclaré)

	 if (!defined('_ECRIRE_INC_VERSION')) return;
     $GLOBALS[$GLOBALS['idx_lang']] = array(
	 
		//0,1,2...
		'0nouveau'	=> 'profil non certifié',
		'1utilisateur_ok'=>'profil certifié',				
		'2utilisateur_ko'=>'profil refusé',
		'3admin_local'=>'administrateur local',
		'4admin_general'=>'administrateur général',
	 
	 
		//A
		'acces'					=> 'Niveau d\'accès',
		'acces_insuffisant_explication' => 'Vous ne disposez pas du niveau d\'accès nécessaire pour consulter ces informations',
		'admin_moderer_utilisateurs'=>'Certifier - refuser des utilisateurs',
		'admin_voir_utilisateurs'=>'Voir et exporter la liste des utilisateurs',
		'adresse_annonce'			=> 'Adresse de cette annonce',
		'adresse_annonce_explications'=>'Si cette adresse est différente de celle qui figure sur votre fiche, merci de la préciser ci-dessous.',
		'ajout_sel'				=> 'Ajouter un sel',
		'avatar'				=> 'Avatar',
	 
		//B
		'bienvenue'				=> 'Bienvenue',
		'bienvenue_catalogue'	=> 'Bienvenue sur le catalogue !',
		'bienvenue_catalogue_explications'=> 'Cet espace permet :',
		'bienvenue_explications_1'=>'de s\'inscrire comme untilisateur, de gérer ses annonces',
		'bienvenue_explications_2'=>'de consulter les annonces, les SEL',
		'bienvenue_explications_3'=>'de localiser vos voisins sur la carte, pour faire des échanges',
		'bienvenue_explications_4'=>'enregistrer et suivre les échanges comptabilisés, consulter son solde',
		'bienvenue_explications_5'=>'consulter les correspondants locaux',
		'bienvenue_explications_6'=>'suivre les relations des utilisateurs, représantant des ateliers / instances intersel...',

		//C
		'certifier_ko'					=> 'Refuser',
		'certifier_ok'					=> 'Certifier',
		'certifier_oko'					=> 'En attente',
		'connexion'						=> 'Connexion',
		'connectez_vous'				=> 'Vos accès sont maintenant créés. Utilisez-les pour vous connecter au catalogue, en saisissant login et mot de passe en haut de cette page',
		'contacts_annonce'				=> 'Contact',
		'consulter_catalogue des annonces'	=> 'Consulter le catalogue',
		'consulter_correspondants'		=> 'Consulter la liste des correspondants',
		'poster_annonce'				=> 'Poster une annonce',
		'poster_annonce_explications'	=> 'Vous pouvez créer une nouvelle annonce dans le catalogue. Il peut s\'agir d\'une offre, une demande ou un partage, concernant des objets, des savoirs ou des services. 
		Tout service ou savoir proposés collectivement (sous forme de stages par exemple) peuvent être programmés à date fixe, ou rester à la demande, en dates libre.',
		'saisir echange'				=> 'Saisir un nouvel échange',
		'correspondance'				=> 'Correspondance locale',
		'correspondance_multiple_sel_organisation1'=> 'Si vous êtes correspondant au titre de tous les SELs indiqués (si vous en avez indiqué plusieurs) cochez cette case.',
		'correspondance_multiple_sel_organisation2'=> 'Sinon, précisez ci-dessous le ou les SEL :',
		'correspondant_auteur_organisations' => "Correspondant-e des ateliers / organisations suivant-e-s :",
		'creer_nouvel_utilisateur'=>'Créer votre accès utilisateur',
		'creer_nouvel_utilisateur_explications'=>'Si vous disposez d\'un accès, connectez-vous directement avec votre login et votre mot de passe.
		Si vous l\'avez perdu, vous pouvez utiliser le lien <strong>mot de passe oublié ?</strong> pour le réinitialiser.
		Dans le cas contraire, remplissez la fiche ci-dessous. Après validation, vous pourrez choisir un avatar.',
		'creer_nouvel_utilisateur2'		=> 'Ajouter votre avatar',
		'creer_nouvel_utilisateur2_explications' => 'Félicitations, votre compte a été correctement créé et vous permet dès à présent de consulter les annonces. Ci dessous, le récapitulatif de votre fiche.<br />Vous pouvez maintenant ajouter votre avatar en joignant à votre fiche une image qui vous représente.',
	
		//D
		'deconnexion'		=> 'Déconnexion',

	
		// E
		'echanges_collectifs_organisation_annonce' => 'Organisation des échanges collectifs',
		'echanges_collectifs_organisation_annonce_explications'=>'Saisissez ici tous les aspects pratiques de l\'organisation collective',
		'entree_acces'	=> 'Niveau d\'accès au catalogue',
		'entree_adresse1' => 'Votre adresse (numéro de voie, voie...)',
		'entree_adresse1_annonce' => 'Adresse pour cette annonce',
		'entree_adresse1_2' => 'Adresse (numéro de voie, voie...)',
		'entree_adresse2' => 'Votre adresse complémentaire (bâtiment, résidence, lieu-dit...)',
		'entree_adresse2_2' => 'Adresse complémentaire (bâtiment, résidence, lieu-dit...)',
		'entree_code_postal' => 'Votre code postal',
		'entree_code_postal_2' => 'Code postal',
		'entree_commentaires' => 'Commentaires',
		'entree_demande'		=> 'Demande',
		'entree_description_annonce'=>'Description de votre annonce',
		'entree_description_annonce2'=>'Description de l\'annonce',
		'entree_direction_echange_annonce'=>'Direction de l\'annonce',
		'entree_email_referent_annonce'=>'E-mail',
		'entree_hebergement_annonce'=>'Hébergement',
		'entree_lon'		=>'Longitude',
		'entree_lat'		=>'Latitude',
		'entree_nature_annonce'=> 'Nature de l\'annonce : Objet, service, savoir',
		'entree_nom_referent_annonce'=> 'Nom du référent pour cette annonce',
		'entree_nombre_personnes_annonce'=>'Nombre de personnes maximum',
		'entree_nom1' 	=> "Votre nom",
		'entree_nom2' 	=> "Nom",
		'entree_objet'	=> 'Objet',
		'entree_obligatoire' => '[Obligatoire]',
		'entree_offre'			=> 'Offre',
		'entree_partage'		=> 'Partage',
		'entree_pays'	 => 	'Votre pays',
		'entree_pays_2'	 => 	'Pays',
		'entree_prenom1' 	=> 'Votre prénom',
		'entree_prenom2' 	=> 'prénom',
		'entree_repas_annonce'=>'Repas',
		'entree_savoir'		=> 'savoir',
		'entree_sel'		=> 'SEL d\'appartenance',
		'entree_service'	=> 'Service',
		'entree_tel1' => 'Votre téléphone',
		'entree_tel1_referent_annonce'=>'Téléphone du référent',
		'entree_tel2_referent_annonce'=>'Autre téléphone',
		'entree_tel1_2' => 'Téléphone',
		'entree_tel2' => 'Votre 2e téléphone',
		'entree_tel2_2' => '2e Téléphone',
		'entree_titre_annonce'=> 'Titre de l\'annonce',
		'entree_titre_annonce2'=> 'Titre de votre annonce',
		'entre_total_fiduc_annonce'=>'Total demandé en monnaie fiduciaire',
		'entree_total_unite_annonce_obligatoire'=>'Nombre d\'unité demandé',
		'entree_si_unite_heure_annonce'=>'Par heure',
		'entree_transport_annonce'=>'Comment venir : transports',
		'entree_ville' => "Votre ville",
		'entree_ville_2' => "Ville",
		'explication_choixsel' => 'Indiquez parmi le JEU / les SELs le ou les groupes dans le/lesquels vous vous trouvez. Débutez la saisie dans le champ, celui-ci sera auto-complété. Sélectionnez alors le SEL parmi les propositions, sans rien modifier. Si vous devez indiquer d\'autres SELs, cliquez sur le bouton "Ajouter un SEL" et recommencez.',
		'explications_email_valide'=>'Ce mail doit être valide, il vous servira notamment à réinitialiser votre mot de passe si vous le perdez et à récupérer votre accès au catalogue',
		'exporter'			=> 'Exporter au format CSV',
		
		//F
		'fiche'			=> 'Fiche',
		'fiche_utilisateur'=>'Fiche utilisateur',
		'frais_annonce_explications'=>'Les montants en monnaie fiduciaire correspondent à des participations à des frais réels. Ceux-ci sont détaillés en nature : location de matériel extérieur, nourriture, par exemple. Indiquer les totaux uniquement (nombres entiers). Si le montant en euros est différent de 0, le champ des précisions devient obligatoire.',
		'form_login'	=> 'Login ou e-mail',
		'form_passe'		=> 'Mot de passe',
		'formerr'		=> 'Votre saisie contient des erreurs !',
		'formerr_oblig'	=> 'Cette information est obligatoire',
		'formerr_email_existe'=>'L\'email que vous avez saisi existe déjà dans la base, vous devez déjà disposer d\'une connexion. Merci d\'utiliser de préférence la fonction "Mot de passe oublié" afin de retrouver vos accès',
		'formerr_email_format'=>'Le format de votre email n\'est pas correct. Veuillez saisir une adresse mail contenant un@ et un .',
		'frais_annonce'		=> 'Frais engagés',
		
		//G
		'geocodage_echec'	=> 'Le geocodage n\'a pu etre effectue pour la raison suivante : ',
		'geocodage_zero_resultat'=>'L\'adresse que vous avez saisie ne permet pas de vous localiser sur la carte',
		'gestion_annonces'	=> 'Gestion des annonces',
		'gestion_inscriptions'=> 'Gestion des inscriptions',
		'gestion_echanges'	=> 'Gestion des échanges',
		
		//I
		'identite'			=> 'Identité',
		'imprimer'			=> 'Imprimer - format PDF',
		'info_coordonnees' => 'Coordonnées',
		'info_admin_statuer_webmestre' => "Donner à cet administrateur les droits de webmestre, co-Admnistrateur global du catalogue",
		'inscrit_depuis'	=> 'inscrit depuis le : ',
		
		//J
		'jeu'				=> 'JEU',
		'jyvais'			=> 'J\'y vais !',
		
		
		//L
		'localisation'		=>	'Localisation',
		'localisez'			=> 'Localisez les nouveaux inscrits',
		'localite'			=> 'Localité',
		'login'				=> 'Login',
	
		
		//M
		'membre_auteur_sel' => 'Membre du / des SEL(s) suivant(s) :',
		'mes_annonces'		=> 'Mes annonces',
		'modifier_donnees'	=> 'Modifier mes données',
		'modifier_statut'	=> 'Modifier le statut',
		
		
		//N
		'nom'				=> 'Nom',
		'nouvelles_annonces'=>'Nouvelles annonces',
		'nouveaux_inscrits'	=> 'Nouveaux inscrits',

		//O

		
		//P
		'pays_albanie'=>'Albanie',
		'pays_algerie'=>'Algérie',
		'pays_allemagne'=>'Allemagne',
		'pays_andorre'=>'Andorre',
		'pays_armenie'=>'Arménie',
		'pays_autriche'=>'Autriche',
		'pays_azerbaidjan'=>'Azerbaïdjan',
		'pays_belgique'=>'Belgique',
		'pays_benin'=>'Bénin',
		'pays_bosnie_herzegovine'=>'Bosnie Herzégovine',		
		'pays_bulgarie'=>'Bilgarie',
		'pays_burkina_faso'=>'Burkina Faso',
		'pays_burundi'=>'Burundi',
		'pays_cameroun'=>'Cameroun',
		'pays_canada'=>'Canada',
		'pays_centrafique'=>'Centrafrique',
		'pays_chypre'=>'Chypre',
		'pays_comores'=>'Comores',
		'pays_cote_divoire'=>'Côte d\'Ivoire',
		'pays_croatie'=>'Croatie',
		'pays_danemark'=>'Danemark',
		'pays_djibouti'=>'Djibouti',
		'pays_espagne'=>'Espagne',
		'pays_estonie'=>'Estonie',
		'pays_finlande'=>'Finlande',
		'pays_france'=>'France',
		'pays_gabon'=>'Gabon',
		'pays_georgie'=>'Georgie',
		'pays_grece'=>'Grèce',
		'pays_guinee'=>'Guinée',
		'pays_guinee_equatoriale'=>'Guinée Équatoriale',
		'pays_haiti'=>'Haïti',
		'pays_hongrie'=>'Hongrie',
		'pays_irlande'=>'Irlande',
		'pays_islande'=>'Islande',
		'pays_italie'	=> 'Italie',
		'pays_jersey'=>'Jersey',
		'pays_kazakhstan'=>'Kazakhstan',
		'pays_kosovo'=>'Kosovo',
		'pays_lettonie'=>'Lettonie',
		'pays_liban'=>'Liban',
		'pays_liechtenstein' => 'Liechtenstein',
		'pays_lituanie'	=>'Lituanie',
		'pays_luxembourg'=>'Luxembourg',
		'pays_macedoine'=>'Macedoine',
		'pays_madagascar'=>'Madagascar',
		'pays_mali'		=> 'Mali',
		'pays_malte'	=> 'Malte',
		'pays_maroc'	=> 'Maroc',
		'pays_maurice'	=> 'Maurice',
		'pays_moldavie'	=> 'Moldavie',
		'pays_monaco'	=> 'Monaco',
		'pays_montenegro'=> 'Monténégro',
			/*
				<option value='[ne]'><:sel:pays_niger:></option>
				<option value='[no]'><:sel:pays_norvege:></option>
				<option value='[nl]'><:sel:pays_pays_bas:></option>
				<option value='[pl]'><:sel:pays_pologne:></option>
				<option value='[pt]'><:sel:pays_portugal:></option>
				<option value='[cd]'><:sel:pays_republique_democratique_du_congo:></option>
				<option value='[cg]'><:sel:pays_republique_du_congo:></option>
				<option value='[cz]'><:sel:pays_republique_tcheque:></option>
				<option value='[ro]'><:sel:pays_roumanie:></option>
				<option value='[gb]'><:sel:pays_royaume_uni:></option>
				<option value='[rw]'><:sel:pays_rwanda:></option>
				<option value='[sn]'><:sel:pays_senegal:></option>
				<option value='[rs]'><:sel:pays_serbie:></option>
				<option value='[sc]'><:sel:pays_seychelles:></option>
				<option value='[sk]'><:sel:pays_slovaquie:></option>
				<option value='[si]'><:sel:pays_slovenie:></option>
				<option value='[se]'><:sel:pays_suede:></option>
				<option value='[ch]'><:sel:pays_suisse:></option>
				<option value='[td]'><:sel:pays_tchad:></option>
				<option value='[tg]'><:sel:pays_togo:></option>
				<option value='[tn]'><:sel:pays_tunisie:></option>
				<option value='[ua]'><:sel:pays_ukraine:></option>
		*/
		'prenom'		=> 'Prénom',
		'pour'			=> 'pour : ',
		
		//Q
		'que_voulez_vous_faire'=>'Que voulez-vous faire ?',
		//R
		'resume_annonce'		=> 'Résumé',
		
		//S
		'seljeu'			=> 'SEL / JEU',
		'suppr_sel'			=> 'Supprimer un sel',
		
		//T
		'telecharger_nouvel_avatar'	=> 'Télécharger un nouvel avatar',	
		
		//U
		'utilisateurs'			=>'Utilisateurs',
		
		//V
		'validite_annonce'	=> 'Période de validité de l\'annonce',
		'validite_annonce_explications'=>'Toute annonce se voit attribuer une durée de validité par défaut, à partir du jour de sa saisie. Vous pouvez néanmoins lui attribuer vous-même un créneau, en lui associant une date de votre choix
		à l\'aide du premier champ date de début, et s\'il s\'agit de la programmation d\'un échange collectif (un stage par exemple), ajoutez-lui une date de fin de façon à ce que l\'annonce soit considéré comme étant "à dates fixes".',
		'visites'			=> 'Visites depuis le lancement du catalogue',
		'voir_fiche'		=> 'Voir la fiche',
		'vous_etes_correspondant'=>'Vous êtes correspondant-e au titre de votre / vos SEL(s), cliquez ici :'
		
     );
?>