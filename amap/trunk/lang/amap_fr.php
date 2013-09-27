<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/amap/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action ?',
	'action_modifier' => 'Modifier',
	'action_supprimer' => 'Supprimer',
	'adherent' => 'Adhérent',
	'adherent_sans_type_panier_sans_type_adherent' => 'Vos adhérents n’ont pas encore de type de panier affecté ou de type d’adhérent.',
	'adherents_jour' => 'Adhérents du jour',
	'adhesion' => 'Adhésion (ex:2008)',
	'adhesion_auteur' => 'Adhésion :',
	'amapiens_explication' => 'Sur cette page, vous trouverez la liste des amapiens de votre association. En cliquant sur leur nom, vous retournerez sur la page de l’auteur. Tenir à jour les informations de chaqu’un est obligatoire au moment du changement de saison.',
	'attention' => 'ATTENTION !',
	'attention_modifications' => '<p>Vous venez d’activer le plugin AMAP. Ce dernier vient de créer une nouvelle rubrique "Agenda de la saison" avec deux sous-rubriques "Distribution" et "Évènements", ainsi que la rubrique "Archives".</p>
								<ol>
									<li>Avant de poursuivre, veuillez renseigner toutes les dates de votre saison grâce au plugin agenda.</li>
									<li> Cette opération devra être effectuée avant chaque début de nouvelles saisons.</li>
									<li>À la fin de chaque saison, vous prendrez soin de mettre à jour la liste des amapiens, ainsi que l’archivage dans la rubrique appropriée, des dates de la saison passée. Ceci pour le bon fonctionnement de ce plugin.</li>
									<li>Mettre à jour les <b>type d’adhérent</b>, <b>adhésion</b> et <b>type de panier</b> sur la page de chaque auteur sinon aucun panier ne pourra lui être attribué.</li>
								</ol>
									<p><b>LE NON RESPECT DE CES QUELQUES PRINCIPES ENTRAINERA UN DYSFONCTIONNEMENT DU PLUGIN AMAP</b></p>',
	'aucun_panier_pour_vous' => 'Nous n’avons aucun panier pour vous.',
	'aucun_panier_produit_par_vous' => 'Vous n’avez produit aucun panier.',
	'autorise_envoie_email_explication' => 'L’envoie d’email est utilisé pour la mise à disposition des paniers et la reprise. "Non" est conseillé pour de grosses AMAP. À la place, nous vous proposons une interface de gestion.',
	'autorise_envoie_email_label' => 'Voulez-vous envoyé des mails ?',
	'avant_le' => 'avant le',

	// C
	'configurer_amap' => 'Configuration du plugin AMAP',
	'confirmation_envoi' => 'Votre mise à disposition du panier du @date_distribution@ est confimée, nous vous en remercions.',
	'contenu_panier' => 'Contenu du panier',
	'contenu_panier_explication' => 'Vous pouvez rédiger votre contenu de la même façon que dans SPIP.',
	'creer_paniers_pour_nom' => 'Créer des paniers pour @nom@.',

	// D
	'date' => 'Date',
	'date_distribution' => 'Date de la distribution',
	'date_livraison' => 'Date de la livraison',
	'depuis_le' => 'depuis le',
	'disponible' => 'Disponible',
	'distribution_paniers' => 'Distribution des paniers du @nb@',
	'distribution_paniers_mois' => 'Distribution des paniers du @date_debut@ au @date_fin@',

	// E
	'enregistrement' => 'Enregistrement',
	'enregistrement_livraison' => 'Enregistrement d’une livraison',
	'enregistrement_livraison_explication' => 'Vous pouvez via ce formulaire enregistrer les contenus des livraisons. Seulement les dates passées seront visibles.',
	'enregistrement_paniers' => 'Enregistrement des paniers',
	'enregistrement_paniers_explication' => 'Ce formulaire vous permet d’enregistrer tous les paniers d’un adhérent en une seule fois.',
	'enregistrement_responsable' => 'Enregistrement d’un responsable',
	'enregistrement_responsable_explication' => 'Ce formulaire vous permet de gérer les responsables de chaque distribution.',
	'envoyer' => 'Envoyer',
	'envoyez_email_non' => 'Non, ne pas envoyer d’e-mail aux adhérents.',
	'envoyez_email_oui' => 'Oui, envoyer des e-mails aux adhérents.',

	// G
	'gestion_amap' => 'Gestion AMAP',
	'grand' => 'Grand',

	// I
	'impression' => 'Impression',
	'impression_donnees' => 'Impression de données',
	'impression_explication' => 'Seulement les dates contenant au minimum un panier sont cliquables et ouvrent une nouvelle fenêtre.',
	'impression_paniers_fonction_date' => 'Impression des paniers en fonction d’une date :',
	'impression_paniers_fonction_mois' => 'Impression des paniers en fonction d’un mois :',
	'impression_responsables_fonction_mois' => 'Impression des responsables en fonction d’un mois :',
	'information_amap' => 'Information AMAP',

	// L
	'les_livraisons' => 'Les livraisons',
	'les_livraisons_effectuees' => 'Les livraisons déjà effectuées',
	'les_paniers_dispo' => 'Les paniers disponibles de @nom@',
	'les_responsabilites' => 'Les @nb@ responsabilités de',
	'liste_amapiens' => 'Liste des amapiens',
	'liste_amapiens_enregistres' => 'Liste des amapiens enregistrés',
	'liste_livraisons' => 'Liste des livraisons',
	'liste_paniers' => 'Liste des paniers',
	'liste_paniers_distribuer_le' => 'Liste des paniers à distribuer le',
	'liste_paniers_vendu' => 'Liste des paniers vendus',
	'liste_responsables' => 'Liste des responsables',
	'livraison' => 'Livraison',
	'livraison_enregistre_explication' => 'Vous devez avoir déjà distribué des paniers pour pouvoir enregistrer via ce formulaire des contenus de livraison.',
	'livraison_explication' => 'Sur cette page, vous trouverez la liste des livraisons que vous avez déjà effectuées. Ce tableau est affichable sur le site via un article en mettant le code &lt;liste|livraisons&gt;.',

	// M
	'manque_fpdf_imprimer' => 'Il vous manque le plugin "fpdf" pour pouvoir imprimer vos listes de paniers.',
	'mettre_disposition' => 'Mettre à disposition',
	'mettre_disposition_explication' => 'Via cette page, vous retrouvez vos paniers. Vous pouvez aussi les mettre à disposition (c’est à dire que si vous ne pouvez pas venir, vous pouvez l’échanger ou le donner). Cliquez sur le lien "mettre à disposition" et suivez la procédure.',
	'mettre_disposition_interface' => 'Un tableau est accessible pour les récupérés ensuite. Le contenu de ce tableau est visible sur la page <a href="@url@"><b>suivante</b></a>.',
	'mettre_disposition_mail' => 'Un e-mail sera envoyé à tous les adhérents.',
	'mini_doc' => 'Mini documentation',

	// N
	'nom' => 'NOM',
	'non' => 'Non',

	// O
	'oui' => 'Oui',

	// P
	'panier' => 'Panier',
	'panier_adherent' => 'Les paniers de @nom@',
	'panier_deja_vendu' => 'Vous avez déjà vendu 1 panier',
	'panier_dispo' => 'Panier disponible le @date_distribution@',
	'panier_dispo_auteur' => 'Bonjour,
		<br />Je mets à disposition le panier du @date_distribution@
		<br />@nom_adherent@',
	'panier_dispo_auteur_mail' => 'Bonjour,
Je mets à disposition le panier du @date_distribution@. Pour le récupérer, suivez le lien suivant @lien@
@panier_dispo_plus@
@nom_adherent@',
	'panier_dispo_interface' => 'Panier disponible',
	'panier_dispo_plus' => 'Des infos à donner en plus (elles seront ajoutées dans l’e-mail envoyé avant votre nom)',
	'panier_disposition' => 'Il y a 1 panier à disposition',
	'panier_distribuer' => 'panier à distribuer',
	'panier_explication' => 'Sur cette page, vous trouverez la liste des paniers que vous devez distribuer. Vous pouvez les modifier ou les supprimer. Une fois la date passée, vous ne les verez automatiquement plus.',
	'panier_explication_email' => 'Toutes modifications entraînent un envoi d’e-mail en masse à tous les adhérents et producteurs de l’association.',
	'panier_explication_interface' => 'Aucun e-mail n’est envoyé. Il est ajouté à l’interface de disponibilité.',
	'panier_liste' => 'Liste des paniers',
	'panier_livraison' => 'Contenu d’un panier',
	'panier_recupere' => 'Panier du @date_distribution@ récupéré',
	'panier_recupere_auteur' => 'Je récupère le panier du @date_distribution@ mis à disposition par @nom_adherent@',
	'panier_recupere_auteur_mail' => 'Bonjour,
Je récupère le panier du @date_distribution@ produit par @nom_producteur@
@nom_adherent@',
	'panier_vous_bien_attribuer' => 'Le panier vous a bien été attribué',
	'paniers_deja_vendu' => 'Vous avez déjà vendu @nb@ paniers',
	'paniers_disponibles' => 'Paniers disponibles',
	'paniers_disposition' => 'IL y a @nb@ paniers à disposition',
	'paniers_distribuer' => 'paniers à distribuer',
	'pas_article_agenda' => 'Vous n’avez pas créé d’article avec l’agenda.',
	'pas_connecte_ou_reconnu' => 'Vous n’êtes pas connecté ou vous n’avez pas été reconnu.',
	'pas_date_distributions' => 'Pas de date de distribution renseignée.',
	'pas_paniers' => 'Vous ne disposez d’aucun panier durant cette saison. En effet, aucun contrat vous concernant n’est actuellement en cours',
	'pas_paniers_disponible' => 'Nous n’avons pas de paniers disponibles à vous proposer actuellement.',
	'pas_producteur_amap' => 'Vous n’avez pas de producteur dans votre amap.',
	'pas_responsable_distribution' => 'Vous n’êtes pas responsable pour les distributions de la saison en cours.',
	'pas_responsable_pour_vous' => 'Vous n’êtes pas responsable de distribution.',
	'pas_statuts_nom' => '@nom@ n’a pas de statut actuellemment. Veuillez éditer son profil pour corriger son statut.',
	'petit' => 'Petit',
	'pour_le' => 'pour le',
	'producteur' => 'Producteur',
	'producteurs' => 'Producteurs',

	// Q
	'qui_recupere_panier_disponible' => 'Qui récupère le panier disponible ?',

	// R
	'recupere_panier' => 'Récupérer le panier',
	'responsables' => 'Responsables',
	'responsables_distribution_paniers_mois' => 'Responsables des distributions du @date_debut@ au @date_fin@',
	'responsables_explication' => 'Sur cette page, vous trouverez la liste des responsables avec la date de distribution à laquelle ils ont été associés.',
	'reste_panier_distribuer' => 'Il nous reste encore 1 panier à distribuer',
	'reste_panier_recuperer' => 'Il vous reste encore 1 panier à recupérer',
	'reste_paniers_distribuer' => 'Il nous reste encore @nb@ paniers à distribuer',
	'reste_paniers_recuperer' => 'Il vous reste encore @nb@ paniers à recupérer',
	'retour_auteur' => 'Retour sur la page auteur de @nom@',

	// S
	'signature' => 'Signature',

	// T
	'table_vide_aucun_enregistrement' => 'Cette table est actuellement vide : Elle ne contient aucun enregistrement.',
	'type_adherent' => 'Type d’adhérent',
	'type_adherent_auteur' => 'Type d’adhérent :',
	'type_panier' => 'Type de panier',
	'type_panier_auteur' => 'Type de panier :',

	// U
	'utiliser_entete_colone_tri' => 'Utiliser les entêtes de colonne pour classer les amapiens (en noir l’ordre de tri actif et en vert les ordres disponibles).',

	// V
	'visiteur' => 'Visiteur',
	'vos_paniers' => 'Vos @nb@ paniers',
	'vos_paniers_vendu' => 'Vos @nb@ paniers vendus',
	'vos_responsabilites' => 'Vos @nb@ responsabilités',
	'votre_compte_amap' => 'Votre compte AMAP',
	'votre_panier' => 'Votre panier',
	'votre_panier_vendu' => 'Votre panier vendu',
	'votre_responsabilite' => 'Votre responsabilité',
	'vous_etes_responsable_distribution' => 'Vous êtes responsable pour la distribution suivante :',
	'vous_etes_responsable_distributions' => 'Vous êtes responsable pour les distributions suivantes :'
);

?>
