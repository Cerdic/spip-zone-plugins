<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

# Titres globaux
	'titre_gestion_pour_association' => 'Gestion pour Association',
	'titre_relance' => 'Renouvellement de votre cotisation',
	'titre_menu_gestion_association' => 'Gestion Association',
	'titre_page_config' => 'Configuration du plugin',

# Navigation
	'menu2_titre_association' => 'L\'association',
	'menu2_titre_gestion_membres' => 'Membres',
	'menu2_titre_relances_cotisations' => 'Relances des cotisations',
	'menu2_titre_gestion_dons' => 'Dons',
	'menu2_titre_ventes_asso' => 'Ventes',
	'menu2_titre_gestion_activites' => 'Activit&eacute;s',
	'menu2_titre_livres_comptes' => 'Comptes',
	'menu2_titre_gestion_prets' => 'Pr&ecirc;ts',

# Titres onglets
	'titre_onglet_activite' => 'Gestion des activit&eacute;s',
	'titre_onglet_membres' => 'Gestion des membres',
	'titre_onglet_dons' => 'Gestion des dons',
	'titre_onglet_ventes' => 'Gestion des ventes',
	'titre_onglet_comptes' => 'Gestion comptable',
	'titre_onglet_prets' => 'Gestion des pr&ecirc;ts',

#Configuration
	# Libellé
	'config_libelle_nom' => 'Nom',
	'config_libelle_email' => 'Adresse courriel',
	'config_libelle_adresse' => 'Adresse',
	'config_libelle_rue' => 'Rue',
	'config_libelle_num_rue' => 'N&deg;',
	'config_libelle_ville' => 'Ville',
	'config_libelle_codepostal' => 'Code Postal',
	'config_libelle_telephone' => 'T&eacute;l&eacute;phone',
	'config_libelle_siret' => 'N&deg; SIRET',
	'config_libelle_declaration' => 'N&deg; de d&eacute;claration',
	'config_libelle_prefet' => 'Pr&eacute;fecture ou Sous-pr&eacute;fecture',
	'config_info_asso' => 'Donn&eacute;es de l\'association',
	'config_info_plugin' => 'Options du plugin',
	'config_info_membres' => 'Options de gestion des membres',
	'config_libelle_classe_banques' => 'Classe des comptes financiers',
	'config_libelle_dons'=> 'Gestion des dons et colis',
	'config_libelle_cotisations'=> 'Gestion des cotisations',
	'config_libelle_ventes'=> 'Gestion des ventes associatives',
	'config_libelle_frais_envoi'=> 'frais d\'envoi',
	'config_libelle_comptes'=> 'Gestion comptable',
	'config_libelle_destinations'=> 'Gestion des destinations comptables',
	'config_libelle_activites'=> 'Gestion des inscriptions aux activit&eacute;s (n&eacute;cessite le plugin Agenda)',
	'config_libelle_prets'=> 'Gestion des pr&egrave;ts et ressources',
	'config_libelle_indexation'=> 'Num&eacute;rotation des membres',
	'config_libelle_id_adherent'=>'Automatis&eacute;e',
	'config_libelle_id_asso'=>'Libre',
	'config_libelle_num_pc'=>'R&eacute;f. comptable',
	'config_libelle_num_dc'=>'Dest. comptable',
	'config_libelle_secteurs'=>'Secteurs (s&eacute;par&eacute;s par des virgules)',
	'config_libelle_spip_listes'=> 'Liste de diffusion par d&eacute;faut (n&eacute;cessite le plugin Spip_listes)',
	'config_libelle_import_nom_auteur' => 'Lors de l\'import/cr&eacute;ation d\'un membre depuis la liste des auteurs SPIP, le nom de l\'auteur a le format suivant:',
	'config_libelle_utiliser_champ_id_asso' => 'R&eacute;f&eacute;rence interne <abbr title="Attention, ce champ est purement informatif les membres sont toujours d&eacute;sign&eacute;s et organis&eacute;s par leur id auteur SPIP mais il permet aux associations qui le d&eacute;sirent d\'avoir une r&eacute;f&eacute;rence membre de leur choix et de conserver cette information dans les tables du plugin">(&agrave; caract&egrave;re informatif)</abbr>',
	'config_libelle_gerer_champs_membres' => 'La fiche des membres contient les champs :',

	# Options
	'import_nom_auteur_nom_prenom' => 'Nom Pr&eacute;nom',
	'import_nom_auteur_prenom_nom' => 'Pr&eacute;nom Nom',
	'import_nom_auteur_nom' => 'Nom',

	# Entetes globales
	'entete_id' => 'ID',
	'entete_action' => 'Action',

	# Boutons globaux
	'bouton_retour' => 'Retour',
	'bouton_confirmer' => 'Confirmer',
	'bouton_modifie' => 'Modifier',
	'bouton_ajoute' => 'Ajouter',
	'bouton_envoyer' => 'Envoyer',
	'bouton_soumettre' => 'Soumettre',
	'bouton_supprimer' => 'Supprimer',
	'bouton_impression' => 'Impression',

	'categories_de_cotisations' => 'Cat&eacute;gories de cotisations',
	'toutes_categories_de_cotisations' => 'Toutes les cat&eacute;gories de cotisations',
	'configuration' => 'Configuration',
	'gestion_association' => 'Gestion d\'une Association',
	'gestion_des_banques' => 'Gestion des banques',
	'gestion_de_lassoc' => 'Gestion de l\'association',
	'ID' => 'ID',
	'info_doc' => '<p>Ce plugin vous permet de g&eacute;rer une petite association en ligne.</p> <p>Vous pouvez ainsi  visualiser, ajouter et modifier des membres actifs, lancer des mails de masse pour les relances de cotisations, g&eacute;rer des dons, des ventes associatives, des inscriptions aux activit&eacute;s, des pr&ecirc;ts de mat&eacute;riels et autres ressources, et tenir un livre de comptes.</p>',
	'message_relance' => '
Bonjour,

Votre adh&eacute;sion est arriv&eacute;e &agrave; &eacute;ch&eacute;ance.
Si vous souhaitez continuer l\'aventure en notre compagnie, n\'oubliez pas de reconduire celle-ci.
Vous pouvez nous faire parvenir votre r&egrave;glement &agrave; votre convenance (ch&egrave;que, mandat  ou virement ).

Le bureau de l\'association.

Merci de ne pas r&eacute;pondre directement &agrave; ce message automatique
	',
	'message_adhesion_webmaster' =>'

	',
	'profil_de_lassociation' => 'Profil de l\'association',

# Adherents
 # Titres
	'adherent_titre_action_membres_actifs' => 'Action sur les membres actifs',
	'adherent_titre_modifier_membre' => 'Modifier un membre actif',
	'adherent_titre_ajout_adherent' => 'Ajout d\'adh&eacute;rent',
	'adherent_titre_ajouter_membre_actif' => 'Ajouter des membres actifs',
	'adherent_titre_ajouter_membre' => 'Ajouter un membre',
	'adherent_titre_historique_membre' => 'Historique membre',
	'adherent_titre_fiche_signaletique_id' => 'Fiche signal&eacute;tique #@id@',
	'adherent_titre_historique_cotisations' => 'Historique des cotisations',
	'adherent_titre_historique_activites' => 'Historique des activit&eacute;s',
	'adherent_titre_historique_ventes' => 'Historique des ventes',
	'adherent_titre_historique_dons' => 'Historique des dons',
	'adherent_titre_historique_prets' => 'Historique des pr&ecirc;ts',
	'adherent_titre_liste_actifs' => 'Tous les membres actifs',

	# Libelle
	'adherent_libelle_donnees_adherent' => 'Donn&eacute;es Adh&eacute;rent',
	'adherent_libelle_id_asso' => 'R&eacute;f. int.',
	'adherent_libelle_reference_interne' => 'R&eacute;f&eacute;rence interne',
	'adherent_libelle_numero' => 'Num&eacute;ro',
	'adherent_libelle_id_auteur' => 'ID',
	'adherent_libelle_photo' => 'Photo',
	'adherent_libelle_nom_famille' => 'Nom',
	'adherent_libelle_prenom' => 'Pr&eacute;nom',
	'adherent_libelle_sexe' => 'Civilit&eacute;',
	'adherent_libelle_ville' => 'Ville',
	'adherent_libelle_categorie' => 'Cat&eacute;gorie',
	'adherent_libelle_fonction' => 'Fonction',
	'adherent_libelle_validite' => 'Validit&eacute;',
	'adherent_libelle_date_validite' => 'Date limite de validit&eacute;',
	'adherent_libelle_commentaires' => 'Remarques',
	'adherent_libelle_statut' => 'Statut de cotisation',
	'adherent_libelle_code_postal' => 'Code postal',
	'adherent_libelle_telephone' => "T&eacute;l&eacute;phone",
	'adherent_libelle_mobile' => "Mobile",
	'adherent_libelle_email' => "Mail",
	'adherent_libelle_adresse' => "Adresse",

	'adherent_libelle_statut_ok' => '&Agrave; jour',
	'adherent_libelle_statut_echu' => '&Eacute;chu',
	'adherent_libelle_statut_relance' => 'Relanc&eacute;',
	'adherent_libelle_statut_sorti' => 'D&eacute;sactiv&eacute;',
	'adherent_libelle_statut_prospect' => 'Prospect',


	'adherent_libelle_oui' => 'oui',
	'adherent_libelle_non' => 'non',
	'adherent_libelle_homme' => 'H',
	'adherent_libelle_femme' => 'F',
	'adherent_libelle_masculin' => 'Monsieur',
	'adherent_libelle_feminin' => 'Madame',

	# En-tetes
	'adherent_entete_date' => 'Date',
	'adherent_entete_id' => 'ID',
	'adherent_entete_livre' => 'Livre',
	'adherent_entete_paiement' => 'Paiement',
	'adherent_entete_justification' => 'Justification',
	'adherent_entete_journal' => 'Journal',
	'adherent_entete_activite' => 'Activit&eacute;',
	'adherent_entete_lieu' => 'Lieu',
	'adherent_entete_inscrits' => 'Inscrits',
	'adherent_entete_action' => 'Action',
	'adherent_entete_notes' => 'Notes',
	'adherent_entete_tous' => 'Tous',
	'adherent_entete_supprimer_abrev' => 'Sup.',

	'adherent_entete_statut' => 'Statut',
	'adherent_entete_statut_defaut' => 'Actifs',
	'adherent_entete_statut_ok' => '&Agrave; jour',
	'adherent_entete_statut_echu' => '&Eacute;chu',
	'adherent_entete_statut_relance' => 'Relanc&eacute;s',
	'adherent_entete_statut_sorti' => 'D&eacute;sactiv&eacute;s',
	'adherent_entete_statut_erreur_bank' => 'Paiement refus&eacute;',
	'adherent_entete_statut_prospect' => 'Prospects',
	'adherent_entete_statut_tous' => 'Tous',

	# Categories
	'pas_de_categorie_attribuee' => 'Pas de cat&eacute;gorie attribu&eacute;e',
	'erreur_pas_de_categorie' => 'Aucune cat&eacute;gorie de cotisation d&eacute;finie',

	# Ref. Int.
	'pas_de_reference_interne_attribuee' => 'Pas de r&eacute;f&eacute;rence interne attribu&eacute;e',
	# Bouton
	'adherent_bouton_confirmer' => 'Confirmer',
	'adherent_bouton_modifier' => 'Modifier',
	'adherent_bouton_envoyer' => 'Envoyer',
	'adherent_bouton_modifier_membre' => 'Modifier le membre',
	'adherent_bouton_maj_operation' => 'Mettre &agrave; jour l\'op&eacute;ration',
	'adherent_bouton_maj_inscription' => 'Mettre &agrave; jour l\'inscription',
	'parametres' => 'Param&egrave;tres',

	# Label
	'adherent_label_modifier_visiteur' => 'Modifier le visiteur',
	'adherent_label_envoyer_courrier' => 'Envoyer un courrier',
	'adherent_label_ajouter_cotisation' => 'Ajouter une cotisation',
	'adherent_label_modifier_membre' => 'Modifier membre',
	'adherent_label_voir_membre' => 'Voir le membre',
	'adherent_label_voir_operation' => 'Voir l\'op&eacute;ration comptable',

	# Message
	'suppression_des_adherents' => 'Suppression des adh&eacute;rents',
	'adherent_message_ajout_adherent' => '@prenom@ @nom@ a &eacute;t&eacute; ajout&eacute; dans le fichier',
	'adherent_message_ajout_adherent_suite' => 'et enregistr&eacute; comme visiteur',
	'adherent_message_email_invalide' => 'L\'email n\'est pas valide !',
	'adherent_message_maj_adherent' => 'Les donn&eacute;es de @prenom@ @nom@ ont &eacute;t&eacute; mises &agrave; jour !',
	'adherent_message_confirmer_suppression' => 'Vous vous appr&ecirc;tez &agrave; effacer les membres',
	'adherent_message_suppression_faite' => 'Suppression effectu&eacute;e !',
	'adherent_message_detail_suppression' => 'Les adh&eacute;rents supprim&eacute;s le sont uniquement de la liste des membres de l\'association. Si vous souhaitez supprimer aussi l\'auteur spip, il faut passer par la page de gestion des auteurs.',

	# Liste
	'adherent_liste_legende' => 'En bleu : Relanc&eacute;<br />En rose : A &eacute;ch&eacute;ance<br />En vert : A jour<br />En brun : D&eacute;sactiv&eacute;<br />En jaune paille : Prospect',
	'adherent_liste_nombre' => 'Nombre de membres',
	'adherent_liste_nombre_ok' => 'A jour : ',
	'adherent_liste_nombre_echu' => 'Echus : ',
	'adherent_liste_nombre_relance' => 'Relanc&eacute;s : ',
	'adherent_liste_nombre_prospect' => 'Prospects : ',
	'adherent_liste_nombre_total' => 'TOTAL : ',
	'adherent_liste_total_cotisations' => 'Total des cotisations : @total@ &euro;',

	# synchro adherents/auteurs
	'synchroniser_asso_membres' => 'Synchroniser la listes des membres avec les auteurs SPIP',
	'synchroniser_choix' => 'Cocher le statut des auteurs SPIP a importer dans la liste des membres, vous pouvez cocher plusieurs cases.',
	'synchroniser_note' => 'Notes:<p> Les auteurs jamais connect&eacute;s seront aussi import&eacute;s dans la liste des membres de l\'association(en fonction de la s&eacute;l&eacute;ction que vous faites).</p><p>Si trop d\'auteurs sont import&eacute;s, vous pourrez toujours les supprimer de la liste des membres, cela n\'a aucune incidence sur leur statut d\'auteur SPIP.</p><p>Meme si vous cochez "Tous les auteurs", les auteurs &agrave; la poubelle ne seront pas import&eacute;s comme membres.</p>Par d&eacute;faut, seul les auteurs non pr&eacute;sents dans la liste des membres sont import&eacute;s. La derni&egrave;re case vous permet de forcer l\'insertion de tous les auteurs dans la liste des membres. Cela ne modifiera toutefois pas le statut des membres d&eacute;j&agrave; pr&eacute;sents mais permet de repartir du bon pied quand on activ&eacute;/desactiv&eacute; le plugin tout en modifiant les auteurs SPIP.',
	'synchroniser_tous' => 'Tous les auteurs',
	'synchroniser_visiteurs' => 'Les visiteurs',
	'synchroniser_redacteurs' => 'Les r&eacute;dacteurs',
	'synchroniser_administrateurs' => 'Les administrateurs',
	'synchroniser_forcer' => 'Forcer l\'insertion des auteurs d&eacute;j&agrave; pr&eacute;sents comme membres',
	'pas_de_categorie' => 'Ne pas renseigner ce champ',
	'synchronise_asso_membre_lien' => 'Synchroniser la liste des membres depuis la liste des auteurs',
	'membres_ajoutes' => ' membres ins&eacute;r&eacute;s dans la liste des membres',
	'membre_ajoute' => ' membre ins&eacute;r&eacute; dans la liste des membres',

	# ACTIVITES

	# Titres
	'activite_titre_action_sur_inscriptions' => 'Action sur les inscriptions',
	'activite_titre_mise_a_jour_inscriptions' => 'Mise &agrave; jour des inscriptions',
	'activite_titre_ajouter_inscriptions' => 'Ajouter des inscriptions',
	'activite_titre_toutes_activites' => 'Toutes les activit&eacute;s',
	'activite_titre_inscriptions_activites' => 'Inscriptions aux activit&eacute;s',

 # Sous-titres
	'activite_mise_a_jour_inscription' => 'Mettre &agrave; jour une inscription',
	'activite_ajouter_inscription' => 'Ajouter une inscription',

# Libelle
	'activite_libelle_inscription' => 'Inscription n&deg;',
	'activite_libelle_date' => 'Date',
	'activite_libelle_nomcomplet' => 'Nom complet',
	'activite_libelle_adherent' => 'N&deg; d\'adh&eacute;rent',
	'activite_libelle_invitation' => ' -- Invitation ext&eacute;rieure -- ',
	'activite_libelle_accompagne_de' => 'Je serai accompagn&eacute; de',
	'activite_libelle_membres' => 'Noms des participants membres',
	'activite_libelle_non_membres' => 'Noms des participants non membres',
	'activite_libelle_nombre_inscrit' => 'Nombre total d\'inscrits',
	'activite_libelle_email' => 'Email',
	'activite_libelle_telephone' => 'T&eacute;l&eacute;phone',
	'activite_libelle_adresse_complete' => 'Adresse compl&egrave;te',
	'activite_libelle_montant_inscription' => 'Montant de l\'inscription (en &euro;)',
	'activite_libelle_date_paiement' => 'Date de paiement (AAAA-MM-JJ)',
	'activite_libelle_mode_paiement' => 'Mode de paiement',
	'activite_libelle_statut' => 'Statut',
	'activite_libelle_commentaires' => 'Commentaires',

 # En-tete
	'activite_entete_id' => 'ID',
	'activite_entete_date' => 'Date',
	'activite_entete_heure' => 'Heure',
	'activite_entete_intitule' => 'Intitul&eacute;',
	'activite_entete_lieu' => 'Lieu',
	'activite_entete_action' => 'Action',
	'activite_entete_toutes' => 'Toutes',
	'activite_entete_validees' => 'Valid&eacute;es',
	'activite_entete_nom' => 'Nom',
	'activite_entete_adherent' => 'Adh&eacute;rent',
	'activite_entete_inscrits' => 'Nbre',
	'activite_entete_montant' => 'Montant',
	'activite_entete_commentaire' => 'Commentaire',

 # Bouton
	'activite_bouton_ajouter' => 'Ajouter',
	'activite_bouton_envoyer' => 'Envoyer',
	'activite_bouton_confirmer' => 'Confirmer',
	'activite_bouton_supprimer' => 'Supprimer',
	'activite_bouton_modifier_article' => 'Modifier l\'article',
	'activite_bouton_ajouter_inscription' => 'Ajouter une inscription',
	'activite_bouton_voir_liste_inscriptions' => 'Voir la liste des inscriptions',
 'activite_bouton_maj_inscription' => 'Mettre &agrave; jour l\'inscription',

	# Liste
	'activite_liste_legende' => 'En bleu : Inscription non valid&eacute;e <br /> En vert : Inscription valid&eacute;e',
	'activite_liste_nombre_inscrits' => 'Nombre d\'inscrits : @total@',
	'activite_liste_total_participations' => 'Total des participations : @total@ &euro;',

	# Message
	'activite_justification_compte_inscription' => 'Inscription n&deg; @id_activite@ - @nom@',
	'activite_message_ajout_inscription' => 'L\'inscription de @nom@ a &eacute;t&eacute; enregistr&eacute;e pour un montant de @montant@ &euro;',
	'activite_message_maj_inscription' => 'L\'inscription de @nom@ a &eacute;t&eacute; mise &agrave; jour',
	'activite_message_confirmation_supprimer' => 'Vous vous appr&ecirc;tez &agrave; effacer @nombre@ inscription@pluriel@ !',
	'activite_message_suppression' => 'Suppression effectu&eacute;e !',
	'activite_message_sujet' => 'Inscription activit&eacute;',
	'activite_message_confirmation_inscription'=>'
Bonjour,

Nous venons d\'enregistrer pour vous l\'inscription suivante:

Activit&eacute;: @activite@
Date: @date@
Lieu: @lieu@

De: @nom@
N&deg; d\'adh&eacute;rent: @id_adherent@
Accompagn&eacute; de
	Membres: @membres@
	Non-membres: @non_membres@
Nombre total d\'inscrits: @inscrits@

Cette inscription ne sera d&eacute;finitive qu\'apr&egrave;s v&eacute;rification et dans la mesure o&ugrave;, sauf stipulation contraire, le montant de @montant@ euros nous est parvenu.

Dans cette attente et dans l\'attente de vous retrouver, nous vous adressons nos salutations les meilleures.

L\'&eacute;quipe @nomasso@
	',
	'activite_message_webmaster'=>'
De: @nom@
Activit&eacute;: @activite@
Nombre: @inscrits@
Commentaire: @commentaire@
	',
	'date_du_jour' => 'Nous sommes le '.date('d/m/Y'),
	'date_du_jour_heure' => 'Nous sommes le '.date('d/m/Y').' et il est '.date('H:i'),

	# VENTES

		#Entetes
			'vente_entete_id' => 'ID',
			'vente_entete_date' => 'Date',
			'vente_entete_article' => 'Article',
			'vente_entete_quantites' => 'Quantit&eacute;',
			'vente_entete_date_envoi' => 'Date d\'envoi',

			'dons_titre_mise_a_jour' => 'Mise &agrave; jour des dons',
	# RESSOURCES

		#Messages
		'ressources_info' => 'Vous pouvez g&eacute;rer ici les diff&eacute;rentes ressources pr&ecirc;t&eacute;es aux membres (livres, mat&eacute;riels, ...)<br />La puce indique la disponibilit&eacute; des diff&eacute;rentes ressources',
		'ressources_danger_suppression' => 'Vous vous appr&ecirc;tez &agrave; effacer l\'article n&deg; @id_ressource@ !',

		# Titres
			'ressources_titre_gestion_ressources' => 'Gestion des ressources',
			'ressources_titre_edition_ressources' => 'Edition de ressource',
			'ressources_titre_suppression_ressources' => 'Suppression de ressource',
			'ressources_titre_liste_ressources' => 'Liste des ressources',
			'ressources_titre_mise_a_jour' => 'Mise &agrave; jour des ventes',

		# En-tete
			'ressources_entete_intitule' => 'Article',
			'ressources_entete_code' => 'Code',
			'ressources_entete_montant' => 'Montant',

		# Navigation
			'ressources_nav_gestion_' => 'Gestion des ressources',
			'ressources_nav_ajouter' => 'Ajouter une ressource',
			'ressources_nav_supprimer' => 'Supprimer la ressource',
			'ressources_nav_editer' => 'Editer la ressource',

		# Libelle
			'ressources_num' => 'RESSOURCE N&deg;',
			'ressources_libelle_code' => 'Code',
			'ressources_libelle_intitule' => 'Article',
			'ressources_libelle_date_acquisition' => 'Date d\'acquisition (AAAA-MM-JJ)',
			'ressources_libelle_prix_location' => 'Prix de la location (en euros)',
			'ressources_libelle_statut' => 'Statut',
			'ressources_libelle_statut_ok' => 'Libre',
			'ressources_libelle_statut_reserve' => 'R&eacute;serv&eacute;',
			'ressources_libelle_statut_suspendu' => 'En suspend',
			'ressources_libelle_statut_sorti' => 'D&eacute;saffect&eacute;',
			'ressources_libelle_commentaires' => 'Commentaires',

		# Prêts

			#Messages
				'prets_danger_suppression' => 'Vous vous appr&ecirc;tez &agrave; effacer la r&eacute;servation n&deg; @id_pret@ !',

			# Titres
				'prets_titre_gestion_prets' => 'Gestion des r&eacute;servations',
				'prets_titre_edition_prets' => 'Edition de r&eacute;servation',
				'prets_titre_suppression_prets' => 'Suppression de r&eacute;servation',
				'prets_titre_liste_reservations' => 'Liste des r&eacute;servations',

			# En-tete
				'prets_entete_date_sortie' => 'Date sortie',
				'prets_entete_nom' => 'Nom',
				'prets_entete_duree' => 'Dur&eacute;e',
				'prets_entete_date_retour' => 'Date retour',
				'prets_entete_reservation' => 'R&eacute;servation',
				'prets_entete_retour' => 'Restitution',

			# Navigation
				'prets_nav_gerer' => 'G&eacute;rer les r&eacute;servations',
				'prets_nav_ajouter' => 'Ajouter une r&eacute;servation',
				'prets_nav_annuler' => 'Annuler la r&eacute;servation',
				'prets_nav_editer' => 'Editer la r&eacute;servation',

			# Libelle
				'prets_libelle_date_sortie' => 'Date de sortie',
				'prets_libelle_duree' => 'Dur&eacute;e',
				'prets_libelle_num_emprunteur' => 'N&deg; de l\'emprunteur',
				'prets_libelle_commentaires' => 'Commentaires',
				'prets_libelle_date_retour' => 'Date de retour',
				'prets_libelle_montant' => 'Montant (en euros)',
				'prets_libelle_mode_paiement' => 'Mode de paiement',

		#Votre association
			'votre_asso' => 'Votre association',
			'president' => 'President',
			'votre_equipe' => 'Votre &eacute;quipe',
			'donnees_perso' => 'Donn&eacute;es Personnelles',
			'donnees_internes' => 'Donn&eacute;es Internes',

	# Plan comptable
		#Message
			'plan_info' => 'Vous pouvez g&eacute;rer ici les comptes de votre plan comptable<br />Vous devez au minimum d&eacute;finir les comptes de produits n&eacute;cessaires &agrave; la configuration du plugin et les comptes financiers relatifs aux diff&eacute;rentes modes de paiement.</p><p>Vous pouvez vous inspirer du plan comptable normalis&eacute; fran&ccedil;ais joint au package.',

		# Titres
			'plan_comptable' => 'Plan comptable',

		# En-tete
			'plan_entete_tous' => 'Tous',

		# Navigation
			'plan_nav_ajouter' => 'Ajouter une r&eacute;f&eacute;rence comptable',
			'operations_comptables' => 'Op&eacute;rations comptables',

		#Libelle
			'plan_libelle_comptes_actifs' => 'Comptes actifs',
			'plan_libelle_comptes_desactives' => 'Comptes d&eacute;sactiv&eacute;s',
			'plan_libelle_oui' => 'oui',
			'plan_libelle_non' => 'non',
			'direction_plan' => 'Type d\'op&eacute;rations',
	# Destination comptable
			'ajouter_destination' => 'ajouter une destination',
			'supprimer_destination' => 'supprimer',
		# Titres
			'destination_comptable' => 'Destination comptable',

		# Navigation
			'destination_nav_ajouter' => 'Ajouter une destination comptable',
		# Bilan
			'toutes_destination' => 'toutes destinations',
	# Comptes
			'compte_financier' => 'Compte financier',

	# Verifications
			'erreur_titre' => 'Une erreur est pr&eacute;sente dans votre saisie',
			'erreur_recette_depense' => 'Une op&eacute;ration ne peut contenir simultan&eacute;ment des d&eacute;penses et recettes. Par ailleurs les d&eacute;penses ou recettes ne peuvent pas etre n&eacute;gatives ou toutes les deux nulles',
			'erreur_montant_destination' => 'La somme des montants affect&eacute;s aux diff&eacute;rentes destinations ne correspond pas au montant global de l\'op&eacute;ration',
			'erreur_destination_dupliquee' => 'Une meme destination a &eacute;t&eacute; s&eacute;lectionn&eacute;e plusieurs fois',
			'erreur_configurer_association_titre' => 'Votre saisie contient des erreurs !',
			'erreur_configurer_association_reference_multiple' => 'Une meme r&eacute;f&eacute;rence comptable ne doit pas etre utilis&eacute;e pour plusieurs fonctions activ&eacute;es(ventes, dons, prets, activit&eacute;s) ou cotisations',
			'erreur_id_adherent' => 'Ce num&eacute;ro de membre ne correspond &agrave; aucun membre de l\'association',
			'erreur_pas_de_classe_financiere' => 'Aucune classe de comptes financiers d&eacute;finie au plan comptable !',
			'erreur_pas_de_plan_comptable' => 'Pas de plan comptable d&eacute;fini !',
			'erreur_pas_de_destination' => 'Pas de destination d&eacute;finie !',
			'erreur_gestion_comptable_inactive' => 'Afin de pouvoir g&eacute;rer les cotisations, dons et ventes, la gestion comptable doit etre activ&eacute;e',
			'erreur_plan_classe' => 'La classe d\'un compte doit etre un entier entre 0 et 9',
			'erreur_plan_code' => 'Le code d\'un compte est compos&eacute; de caract&egrave;res alphanum&eacute;riques uniquement et doit commencer par 2 chiffres. Le premier chiffre doit etre &eacute;gal &agrave; la classe du compte',
			'erreur_plan_code_duplique' => 'Ce code est d&eacute;j&agrave; utilis&eacute; pour une autre r&eacute;f&eacute;rence comptable(peut-etre d&eacute;sactiv&eacute;e)',
			'erreur_format_date' => 'La date doit etre au format AAAA-MM-JJ',
			'erreur_date' =>  'Cette date n\'existe pas',
			'erreur_operation_non_permise_sur_ce_compte' => 'Ce compte n\'accepte qu\'un seul type d\'op&eacute;rations (recette ou d&eacute;pense) et ne correspond pas a celle que vous avez rentr&eacute;',
			'erreur_montant' => 'Les valeurs n&eacute;gatives ne sont pas autoris&eacute;es',
			// chaines collectee automatiquement

'a_developper' => 'A d&eacute;velopper',
'acheteur' => 'Acheteur',
'action_sur_les_dons' => 'Action sur les dons',
'action' => 'Action',
'activite_nd' => 'Activit&eacute; n&deg;',
'adresse' => 'Adresse',
'ajouter_une_categorie_de_cotisation' => 'Ajouter une cat&eacute;gorie de cotisation',
'ajouter_une_operation' => 'Ajouter une op&eacute;ration',
'apres_confirmation_vous_ne_pourrez_plus_modifier_ces_operations' => 'Apr&egrave;s confirmation vous ne pourrez plus modifier ces op&eacute;rations.',
'argent' => 'Argent',
'article' => 'Article',
'avoir_actuel' => 'Avoir actuel',
'avoir_initial' => 'Avoir initial',
'categorie' => 'Cat&eacute;gorie',
'classe' => 'Classe',
'code' => 'Code :',
'code_de_l_article' => 'Code de l\'article',
'colis' => 'Colis',
'commentaires' => 'Commentaires',
'compte_active' => 'Compte activ&eacute; :',
'compte' => 'Compte',
'contre_valeur_en_e__' => 'Contre-valeur (en &euro;) :',
'geste_association' => 'Geste de l\'association :',
'contrepartie' => 'Contrepartie',
'crediteur' => 'compte cr&eacute;diteur',
'debiteur' => 'compte d&eacute;biteur',
'depense' => 'D&eacute;pense :',
'depenses' => 'D&eacute;penses',
'destination' => "Destinations",
'date_aaaa_mm_jj' => 'Date (AAAA-MM-JJ) :',
'date_report_aaa_mm_jj' => 'Date report (AAA-MM-JJ) :',
'date' => 'Date',
'don' => 'Don :',
'don_financier_en_e__' => 'Don financier (en &euro;) :',
'duree_en_mois' => 'Dur&eacute;e (en mois)',
'duree_mois' => 'Dur&eacute;e (mois)',
'email' => 'Email',
'en_bleu_recettes_en_rose_depenses' => 'En bleu : Recettes<br />En rose : D&eacute;penses',
'en_rose_vente_enregistree_en_bleu_vente_expediee' => 'En rose : Vente enregistr&eacute;e<br />En bleu : Vente exp&eacute;di&eacute;e',
'encaisse' => 'Encaisse',
'entrees' => 'Entr&eacute;es :',
'env' => 'Env',
'envoi' => 'Envoi',
'envoye_le_aaaa_mm_jj' => 'Envoy&eacute; le (AAAA-MM-JJ) :',
'financier' => 'Financier',
'fonction' => 'Fonction',
'frais_d_envoi_en_e__' => 'Frais d\'envoi (en &euro;) :',
'gestion_des_emprunts_et_des_prets' => 'Gestion des emprunts et des pr&ecirc;ts',
'gestion_pour_association' => 'Gestion pour Association',
'id' => 'ID',
'imputation' => 'Imputation :',
'intitule' => 'Intitul&eacute; :',
'justification' => 'Justification',
'libelle_complet' => 'Libell&eacute; complet',
'membre' => 'Membre',
'mettre_a_jour_la_vente' => 'Mettre &agrave; jour la vente',
'mettre_a_jour_le_don' => 'Mettre &agrave; jour le don',
'mettre_a_jour' => 'Mettre &agrave; jour',
'montant_en_euros' => 'Montant (en euros)',
'montant' => 'Montant',
'nd_d_adherent' => 'N&deg; d\'adh&eacute;rent :',
'nd_de_membre' => 'N&deg; de membre :',
'nom_de_l_acheteur' => 'Nom de l\'acheteur :',
'nom_du_bienfaiteur' => 'Nom du bienfaiteur :',
'nom' => 'Nom',
'portable' => 'Portable',
'pret_nd' => 'Pr&ecirc;t n&deg;',
'prix_de_vente_en_e__' => 'Prix de vente(en &euro;) :',
'qte' => 'Qt&eacute;',
'quantite_achetee' => 'Quantit&eacute; achet&eacute;e :',
'reference' => 'R&eacute;f&eacute;rence',
'reserve' => 'R&eacute;s&eacute;rv&eacute;',
'resultat_courant' => 'R&eacute;sultat courant',
'recette' => 'Recette :',
'remarques' => 'Remarques :',
'solde' => 'Solde :',
'solde_initial' => 'Solde initial',
'solde_reporte_en_euros' => 'Solde report&eacute; (en euros) :',
'sorties' => 'Sorties :',
'supprime' => 'Supprim&eacute;',
'supprimer_le_don' => 'Supprimer le don',
'supprimer' => 'Supprimer',
'telephone' => 'T&eacute;l&eacute;phone',
'tous_les_membres_a_relancer' => 'Tous les membres &agrave; relancer',
'toutes_les_etiquettes_a_generer' => 'Toutes les &eacute;tiquettes &agrave; g&eacute;n&eacute;rer',
'valeur' => 'Valeur',
'validite' => 'Validit&eacute;',
'vous_vous_appretez_a_effacer_la_ligne_de_compte' => 'Vous vous appr&ecirc;tez &agrave; effacer la ligne de compte',
'vous_vous_appretez_a_effacer_le_categorie' => 'Vous vous appr&ecirc;tez &agrave; effacer le cat&eacute;gorie',
'vous_vous_appretez_a_effacer_le_compte' => 'Vous vous appr&ecirc;tez &agrave; effacer le compte',
'vous_vous_appretez_a_effacer_la_destination' => 'Vous vous appr&ecirc;tez &agrave; effacer la destination',
'vous_vous_appretez_a_effacer_le_don' => 'Vous vous appr&ecirc;tez &agrave; effacer le don',
'vous_vous_appretez_a_envoyer' => 'Vous vous appr&ecirc;tez &agrave; envoyer',
'vous_vous_appretez_a_valider_les_operations' => 'Vous vous appr&ecirc;tez &agrave; valider les op&eacute;rations&nbsp;:',
'vous_vous_appretez_a_effacer' => 'Vous vous appr&ecirc;tez &agrave; effacer',
'relance' => 'relance',
'relances' => 'relances',
'ventes' => 'ventes',

'action_sur_les_ventes_associatives' => 'Action sur les ventes associatives',
'ajout_de_cotisation' => 'Ajout de cotisation',
'ajouter_un_don' => 'Ajouter un don',
'ajouter_une_vente' => 'Ajouter une vente',
'bilans_comptables' => 'Bilans comptables',
'date_du_paiement_AAAA-MM-JJ' => 'Date du paiement (AAAA-MM-JJ)',
'edition_plan_comptable' => 'Edition plan comptable',
'informations_comptables' => 'Informations comptables',
'modification_des_comptes' => 'Modification des comptes',
'relance_de_cotisations' => 'Relance de cotisations',
'suppression_de_compte' => 'Suppression de compte',
'suppression_de_destination' => 'Suppression de destination',
'tous_les_dons' => 'Tous les dons',
'toutes_les_ventes' => 'Toutes les ventes',
'montant_paye_en_euros' => 'Montant pay&eacute; (en euros)',
'nouvelle_cotisation' => 'Nouvelle cotisation',
);
?>
