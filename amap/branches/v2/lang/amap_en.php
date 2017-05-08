<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/amap?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action ?',
	'action_modifier' => 'Modify',
	'action_supprimer' => 'Delete',
	'adherent' => 'Member',
	'adherent_sans_type_panier_sans_type_adherent' => 'Vos adhérents n\'ont pas encore de type de panier affecté ou de type d\'adhérent.', # NEW
	'adherents_jour' => 'Adhérents du jour', # NEW
	'adhesion' => 'Membership (ie:2008)',
	'adhesion_auteur' => 'Membership :',
	'administrateur' => 'Administrator',
	'amapiens_explication' => 'Sur cette page vous trouverez la liste des amapiens de votre association, en cliquant sur le nom vous retournerez sur la page de l\'auteur. Tenir à jours les informations de chaqu\'un est obligatoire au moment du changement de saison.', # NEW
	'attention' => 'WARNING !',
	'attention_modifications' => '<p>You have just activated the CSA (Community Supported Agriculture) plugin. It created a new section "Season agenda" which includes two sub-sections "Delivery" and "Events". It also created the section "Archives".</p>
									1. Before continuing, please fill up all the dates of the season via the plugin agenda.<br />
									2. This has to be done before each new season begins.<br />

									3. At the end of each season, you have to update the CSA members list and archive into the appropriate sections, the dates of last season, this is needed for a proper functioning of this plugin.<br />

									4. Update the basket type and the subscriptions on each author page, or no basket  delivery will be attributed.<br />
									<p><strong>NON RESPECT OF THOSE RULES WILL RESULT IN THE PLUGIN DYSFUNCTION</strong></p>',
	'aucun_panier_pour_nom' => 'There is no basket for @nom@.',
	'aucun_panier_produit_par_nom' => 'There is no basket made by @nom@.',
	'autorise_envoie_email_explication' => 'L\'envoie de email est utiliser pour la mise a disposition des paniers et la reprise. Non est conseiller pour de grosses amap, à la place on vous propose une interphase de gestion.', # NEW
	'autorise_envoie_email_label' => 'Voulez vous envoyez des mails ?', # NEW
	'avant_le' => 'before',

	// C
	'configurer_amap' => 'Configuration du plugin AMAP', # NEW
	'confirmation_envoi' => 'The availability of your basket for @date_distribution@ is confirmed, thanks.',
	'contenu_panier' => 'Basket content',
	'contenu_panier_explication' => 'You can edit content the same way as in SPIP.',
	'creer_paniers_pour_nom' => 'Create baskets for @nom@.',

	// D
	'date' => 'Date', # NEW
	'date_distribution' => 'Giving date',
	'date_livraison' => 'Delivery date',
	'depuis_le' => 'since',
	'distribution_paniers' => 'Basket delivery of @nb@',
	'distribution_paniers_mois' => 'Distribution des paniers du @date_debut@ au @date_fin@', # NEW

	// E
	'enregistrement' => 'Registration',
	'enregistrement_livraison' => 'Enregistrement d\'une livraison', # NEW
	'enregistrement_paniers' => 'Enregistrement des paniers', # NEW
	'enregistrement_responsable' => 'Enregistrement d\'un responsable pour les distributions', # NEW
	'envoyer' => 'Send',
	'envoyez_email_non' => 'Non, ne pas envoyez de mail au adhérents.', # NEW
	'envoyez_email_oui' => 'Oui, envoyez des mails aux adhérents.', # NEW

	// G
	'gestion_amap' => 'CSA administration',
	'grand' => 'Big',

	// I
	'impression' => 'Print',
	'impression_donnees' => 'Print data',
	'impression_explication' => 'Only the dates with at least 1 basket are clickable and open a new window.',
	'impression_paniers_fonction_date' => 'Impression des paniers en fonction d\'une date :', # NEW
	'impression_paniers_fonction_mois' => 'Impression des paniers en fonction d\'un mois :', # NEW
	'impression_responsables_fonction_date' => 'Impression des responsables en fonction d\'un mois :', # NEW
	'information_amap' => 'CSA information',

	// L
	'les_livraisons' => 'Deliveries',
	'les_livraisons_effectuees' => 'Past deliveries',
	'les_paniers' => 'The basket of @nom@',
	'les_paniers_dispo' => 'Les paniers disponible de @nom@', # NEW
	'les_responsabilites' => 'Les responsabilités de @nom@', # NEW
	'liste_amapiens' => 'CSA members list',
	'liste_amapiens_enregistres' => 'registered CSA members list',
	'liste_livraisons' => 'Deliveries list',
	'liste_paniers' => 'Baskets list',
	'liste_paniers_distribuer_le' => 'Basket list to deliver on',
	'liste_paniers_vendu_par' => 'Baskets list produced by @nom@',
	'liste_responsables' => 'Liste des responsables', # NEW
	'livraison' => 'Delivery',
	'livraison_explication' => 'Sur cette page vous trouverez la liste des livraison que vous avez déjà effectuer, le contenu de ce tableau est visible sur la page <a href="@url@"><b>ici</b></a>.', # NEW

	// M
	'manque_fpdf_imprimer' => 'The plugin "fpdf" is missing to print your baskets lists.',
	'mettre_disposition' => 'Place at disposal',
	'mettre_disposition_explication' => 'Via cette page vous retrouvez vos paniers mais vous pouvez aussi les mettre a disposition, c\'est à dire que si vous ne pouvez pas venir vous pouvez l\'échanger ou le donner. Cliquer sur le lien "mettre à disposition" et suivez la procédure.', # NEW
	'mettre_disposition_interface' => 'Un tableau est accessible pour les récupérés ensuite, le contenu de ce tableau est visible sur la page <a href="@url@"><b>ici</b></a>.', # NEW
	'mettre_disposition_mail' => 'Un mail sera envoyez à tous les adhérents.', # NEW
	'mini_doc' => 'Short documentation',

	// N
	'nom' => 'NAME',

	// P
	'panier' => 'Basket',
	'panier_deja_vendu' => 'You have sold already 1 basket',
	'panier_dispo' => 'Basket available the @date_distribution@',
	'panier_dispo_auteur' => 'Hello,
		<br />The basket of @date_distribution@ is at disposal
		<br />@nom_adherent@',
	'panier_dispo_auteur_mail' => 'Hello,
The basket of @date_distribution@ is at disposal, please follow this link @lien@ to get it
@panier_dispo_plus@
@nom_adherent@',
	'panier_dispo_plus' => 'Extra infos to give (It will be added in the mail sent before your name)',
	'panier_disposition' => 'Vous avez mis 1 panier à disposition', # NEW
	'panier_distribuer' => 'basket to deliver',
	'panier_explication' => 'Sur cette page vous trouverez la liste des paniers que vous devez distribuer, ils sont modifiable et supprimable. Toutes modifications entraîne un envoie de mail en masse à tous les adhérents et les producteurs de l\'association.', # NEW
	'panier_liste' => 'Baskets list',
	'panier_livraison' => 'Basket content',
	'panier_recupere' => 'Transaction of the @date_distribution@ basket done',
	'panier_recupere_auteur' => 'Je récupère le panier du @date_distribution@ mis à disposition par @nom_adherent@', # NEW
	'panier_recupere_auteur_mail' => 'Hello,
I will get the basket of the @date_distribution@ produced by @nom_producteur@
@nom_adherent@',
	'paniers_deja_vendu' => 'You have sold @nb@ basket(s)',
	'paniers_disponibles' => 'Paniers disponibles', # NEW
	'paniers_disposition' => 'Vous avez mis @nb@ paniers à disposition', # NEW
	'paniers_distribuer' => 'baskets to deliver',
	'pas_connecte_ou_reconnu' => 'You are not logged in or not identified.',
	'pas_date_distributions' => 'No distribution date are filled in.',
	'pas_paniers' => 'You do not have any basket for this season, no contract has been done concerning you',
	'pas_producteur_amap' => 'ou do not have any producer in your CSA.',
	'pas_responsable_distribution' => 'Vous êtes pas responsable pour les distributions de la saison en cours.', # NEW
	'pas_responsable_pour_nom' => '@nom@ est pas responsable de distribution.', # NEW
	'pas_statuts_nom' => '@nom@ n\'a pas de statuts actuellemment, éditer son profil pour corriger le manque.', # NEW
	'petit' => 'Small',
	'producteur' => 'Producer',
	'producteurs' => 'Producers',

	// Q
	'qui_recupere_panier_disponible' => 'Who wants to get the available basket ?',

	// R
	'recupere_panier' => 'Récupéré le panier', # NEW
	'responsables' => 'Responsables', # NEW
	'responsables_distribution_paniers_mois' => 'Responsables des distributions pour le mois du @date_debut@ au @date_fin@', # NEW
	'responsables_explication' => 'Sur cette page vous trouverez la liste des responsables avec la dates de distribution à laquelle il a été associé. Ce tableau est affichable sur le site via un article en mettant le code <liste|distributions>', # NEW
	'reste_panier_distribuer' => 'We still have 1 basket to deliver',
	'reste_panier_recuperer' => 'You still have 1 basket to get',
	'reste_paniers_distribuer' => 'We still have @nb@ baskets to deliver',
	'reste_paniers_recuperer' => 'You still have @nb@ baskets to get',
	'retour_auteur' => 'Back to the author page @nom@ ',

	// S
	'signature' => 'Signature',

	// T
	'table_vide_aucun_enregistrement' => 'This table is empty :
					<br />It does not contain any registration.',
	'type_adherent' => 'Member type',
	'type_adherent_auteur' => 'Member type :',
	'type_panier' => 'Basket type',
	'type_panier_auteur' => 'Basket type :',

	// U
	'utiliser_entete_colone_tri' => 'Use column header to sort CSA Members (in black active sorting method and in green available ones).',

	// V
	'visiteur' => 'Visitor',
	'vos_paniers' => 'Your baskets',
	'vous_etes_responsable_distribution' => 'Vous êtes responsables pour la distribution suivante :', # NEW
	'vous_etes_responsable_distributions' => 'Vous êtes responsables pour les distributions suivante :' # NEW
);

?>
