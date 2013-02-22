<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/amap/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action ?',
	'action_modifier' => 'Modifier',
	'action_supprimer' => 'Supprimer',
	'adherent' => 'Adhérent',
	'adherent_sans_type_panier_sans_type_adherent' => 'Vos adhérents n\'ont pas encore de type de panier affecté ou de type d\'adhérent.',
	'adherents_jour' => 'Adhérents du jour',
	'adhesion' => 'Adhésion (ex:2008)',
	'adhesion_auteur' => 'Adhésion :',
	'amapiens_explication' => 'Sur cette page vous trouverez la liste des amapiens de votre association, en cliquant sur le nom vous retournerez sur la page de l\'auteur. Tenir à jours les informations de chaqu\'un est obligatoire au moment du changement de saison.',
	'attention' => 'ATTENTION !',
	'attention_modifications' => '<p>Vous venez d\'activer le plugin AMAP, ce dernier vient de créer une nouvelle rubrique "Agenda de la saison" avec deux sous-rubriques "Distribution" et "Évènements", ainsi que la rubrique "Archives".</p>
									1. Avant de poursuivre, veuillez renseigner toutes les dates de votre saison grâce au plugin agenda.<br />
									2. Cette opération devra être effectuée avant chaque début de nouvelles saisons.<br />
									3. À la fin de chaque saison, vous prendrez soin de mettre à jour la liste des amapiens, ainsi que l\'archivage dans la rubrique appropriée, des dates de la saison passée, ceci pour le bon fonctionnement de ce plugin.<br />
									4. Mettre à jour les <b>type d\'adhérent</b>, <b>adhésion</b> et <b>type de panier</b> sur la page de chaque auteur sinon aucun panier ne pourra lui être attribué.<br />
									<p><b>LE NON RESPECT DE CES QUELQUES PRINCIPES ENTRAINERA UN DYSFONCTIONNEMENT DU PLUGIN AMAP</b></p>',
	'aucun_panier_pour_nom' => 'Nous n\'avons aucun panier pour @nom@.',
	'aucun_panier_produit_par_nom' => 'Nous n\'avons aucun panier produit par "@nom@".',
	'autorise_envoie_email_explication' => 'L\'envoie d\'email est utiliser pour la mise a disposition des paniers et la reprise. Non est conseiller pour de grosses amap, à la place on vous propose une interface de gestion.',
	'autorise_envoie_email_label' => 'Voulez vous envoyez des mails ?',
	'avant_le' => 'avant le',

	// C
	'configurer_amap' => 'Configuration du plugin AMAP',
	'confirmation_envoi' => 'Votre mise a disposition du panier du @date_distribution@ est confimée, nous vous remercions.',
	'contenu_panier' => 'Contenu du panier',
	'contenu_panier_explication' => 'Vous pouvez rédiger votre contenu de la même façon que dans SPIP.',
	'creer_paniers_pour_nom' => 'Créer des paniers pour @nom@.',

	// D
	'date' => 'Date',
	'date_distribution' => 'Date de la distribution',
	'date_livraison' => 'Date de la livraison',
	'depuis_le' => 'depuis le',
	'distribution_paniers' => 'Distribution des paniers du @nb@',
	'distribution_paniers_mois' => 'Distribution des paniers du @date_debut@ au @date_fin@',

	// E
	'enregistrement' => 'Enregistrement',
	'enregistrement_livraison' => 'Enregistrement d\'une livraison',
	'enregistrement_paniers' => 'Enregistrement des paniers',
	'enregistrement_responsable' => 'Enregistrement d\'un responsable pour les distributions',
	'envoyer' => 'Envoyer',
	'envoyez_email_non' => 'Non, ne pas envoyez de mail au adhérents.',
	'envoyez_email_oui' => 'Oui, envoyez des mails aux adhérents.',

	// G
	'gestion_amap' => 'Gestion AMAP',
	'grand' => 'Grand',

	// I
	'impression' => 'Impression',
	'impression_donnees' => 'Impression de données',
	'impression_explication' => 'Seulement les dates contenant au minimum un panier sont cliquables et ouvrent une nouvelle fenêtre.',
	'impression_paniers_fonction_date' => 'Impression des paniers en fonction d\'une date :',
	'impression_paniers_fonction_mois' => 'Impression des paniers en fonction d\'un mois :',
	'impression_responsables_fonction_mois' => 'Impression des responsables en fonction d\'un mois :',
	'information_amap' => 'Information AMAP',

	// L
	'les_livraisons' => 'Les livraisons',
	'les_livraisons_effectuees' => 'Les livraisons déjà effectuées',
	'les_paniers' => 'Les paniers de @nom@',
	'les_paniers_dispo' => 'Les paniers disponible de @nom@',
	'les_responsabilites' => 'Les responsabilités de @nom@',
	'liste_amapiens' => 'Liste des amapiens',
	'liste_amapiens_enregistres' => 'Liste des amapiens enregistrés',
	'liste_livraisons' => 'Liste des livraisons',
	'liste_paniers' => 'Liste des paniers',
	'liste_paniers_distribuer_le' => 'Liste des paniers à distribuer le',
	'liste_paniers_vendu_par' => 'Liste des paniers produit par @nom@',
	'liste_responsables' => 'Liste des responsables',
	'livraison' => 'Livraison',
	'livraison_enregistre_explication' => 'Vous devez avoir déjà distribuer des paniers pour pouvoir enregistré via ce formulaire des contenus de livraison.',
	'livraison_explication' => 'Sur cette page vous trouverez la liste des livraison que vous avez déjà effectuer. Ce tableau est affichable sur le site via un article en mettant le code &lt;liste|livraison&gt;.',

	// M
	'manque_fpdf_imprimer' => 'Il vous manque le plugins "fpdf" pour pouvoir imprimer vos listes de paniers.',
	'mettre_disposition' => 'Mettre à disposition',
	'mettre_disposition_explication' => 'Via cette page vous retrouvez vos paniers mais vous pouvez aussi les mettre a disposition, c\'est à dire que si vous ne pouvez pas venir vous pouvez l\'échanger ou le donner. Cliquer sur le lien "mettre à disposition" et suivez la procédure.',
	'mettre_disposition_interface' => 'Un tableau est accessible pour les récupérés ensuite, le contenu de ce tableau est visible sur la page <a href="@url@"><b>ici</b></a>.',
	'mettre_disposition_mail' => 'Un mail sera envoyez à tous les adhérents.',
	'mini_doc' => 'Mini documentation',

	// N
	'nom' => 'NOM',

	// P
	'panier' => 'Panier',
	'panier_deja_vendu' => 'Vous avez déjà vendu 1 panier',
	'panier_dispo' => 'Panier disponible le @date_distribution@',
	'panier_dispo_auteur' => 'Bonjour,
		<br />Je mets à disposition le panier du @date_distribution@
		<br />@nom_adherent@',
	'panier_dispo_auteur_mail' => 'Bonjour,
Je mets à disposition le panier du @date_distribution@, pour le récupérer suiver le lien suivant @lien@
@panier_dispo_plus@
@nom_adherent@',
	'panier_dispo_interface' => 'Panier disponible',
	'panier_dispo_plus' => 'Des infos à donner en plus (elles seront rajoutées dans le courriel envoyé avant votre nom)',
	'panier_disposition' => 'Il y a 1 panier à disposition',
	'panier_distribuer' => 'panier à distribuer',
	'panier_explication' => 'Sur cette page vous trouverez la liste des paniers que vous devez distribuer, ils sont modifiable et supprimable. Une fois la date passez, vous ne les verez automatiquement plus.',
	'panier_explication_email' => 'Toutes modifications entraîne un envoie de mail en masse à tous les adhérents et les producteurs de l\'association.',
	'panier_explication_interface' => 'Aucun mail n\'est envoyé, il est rajouté sur l\'interface de disponibilité.',
	'panier_liste' => 'Liste des paniers',
	'panier_livraison' => 'Contenu d\'un panier',
	'panier_recupere' => 'Panier du @date_distribution@ récupéré',
	'panier_recupere_auteur' => 'Je récupère le panier du @date_distribution@ mis à disposition par @nom_adherent@',
	'panier_recupere_auteur_mail' => 'Bonjour,
Je récupère le panier du @date_distribution@ produit par @nom_producteur@
@nom_adherent@',
	'panier_vous_bien_attribuer' => 'Le panier vous a bien été attribuer',
	'paniers_deja_vendu' => 'Vous avez déjà vendu @nb@ panier',
	'paniers_disponibles' => 'Paniers disponibles',
	'paniers_disposition' => 'IL y a @nb@ paniers à disposition',
	'paniers_distribuer' => 'paniers à distribuer',
	'pas_connecte_ou_reconnu' => 'Vous n\'êtes pas connecté ou on ne vous a pas reconnu.',
	'pas_date_distributions' => 'Pas de date de distribution renseigné.',
	'pas_paniers' => 'Vous ne disposez d\'aucun panier durant cette saison, en effet aucun contrat vous concernant n\'est actuellement en cours',
	'pas_paniers_disponible' => 'Nous avons pas de panier disponible à vous proposer actuellement.',
	'pas_producteur_amap' => 'Vous n\'avez pas de producteur dans votre amap.',
	'pas_responsable_distribution' => 'Vous êtes pas responsable pour les distributions de la saison en cours.',
	'pas_responsable_pour_nom' => '@nom@ est pas responsable de distribution.',
	'pas_statuts_nom' => '@nom@ n\'a pas de statuts actuellemment, éditer son profil pour corriger le manque.',
	'petit' => 'Petit',
	'producteur' => 'Producteur',
	'producteurs' => 'Producteurs',

	// Q
	'qui_recupere_panier_disponible' => 'Qui récupère le panier disponible ?',

	// R
	'recupere_panier' => 'Récupéré le panier',
	'responsables' => 'Responsables',
	'responsables_distribution_paniers_mois' => 'Responsables des distributions pour le mois du @date_debut@ au @date_fin@',
	'responsables_explication' => 'Sur cette page vous trouverez la liste des responsables avec la dates de distribution à laquelle il a été associé.',
	'reste_panier_distribuer' => 'Il nous reste encore 1 panier à distribuer',
	'reste_panier_recuperer' => 'Il vous reste encore 1 panier à recupérer',
	'reste_paniers_distribuer' => 'Il nous reste encore @nb@ paniers à distribuer',
	'reste_paniers_recuperer' => 'Il vous reste encore @nb@ paniers à recupérer',
	'retour_auteur' => 'Retour sur la page auteur de @nom@',

	// S
	'signature' => 'Signature',

	// T
	'table_vide_aucun_enregistrement' => 'Cette table est actuellement vide :
										<br />Elle ne contient aucun enregistrement.',
	'type_adherent' => 'Type d\'adhérent',
	'type_adherent_auteur' => 'Type d\'adhérent :',
	'type_panier' => 'Type de panier',
	'type_panier_auteur' => 'Type de panier :',

	// U
	'utiliser_entete_colone_tri' => 'Utiliser les entêtes de colonne pour classer les amapiens (en noir l\'ordre de tri actif et en vert les ordres disponibles).',

	// V
	'visiteur' => 'Visiteur',
	'vos_paniers' => 'Vos paniers',
	'vous_etes_responsable_distribution' => 'Vous êtes responsables pour la distribution suivante :',
	'vous_etes_responsable_distributions' => 'Vous êtes responsables pour les distributions suivante :'
);

?>
