<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/contacts_et_organisations/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'annuaire' => 'Annuaire',
	'annuaire_ajouter' => 'Ajouter un annuaire',
	'annuaire_aucun' => 'Aucun annuaire',
	'annuaire_champ_descriptif_label' => 'Descriptif',
	'annuaire_champ_identifiant_explication' => 'Un identifiant informatique unique, pour aider à filtrer vos contenus, par exemple avec {annuaire=associations} pour l’identifiant "associations".',
	'annuaire_champ_identifiant_label' => 'Identifiant',
	'annuaire_champ_titre_label' => 'Titre',
	'annuaire_creer' => 'Créer un nouvel annuaire',
	'annuaire_creer_associer' => 'Créer et associer un nouvel annuaire',
	'annuaire_editer' => 'Modifier cet annuaire',
	'annuaire_logo' => 'Logo de cet annuaire',
	'annuaire_supprimer' => 'Supprimer cet annuaire',
	'annuaire_un' => 'Un annuaire',
	'annuaires' => 'Annuaires',
	'annuaires_nb' => '@nb@ annuaires',
	'annuaires_tout' => 'Tous les annuaires',
	'associe_a' => 'associé à :',
	'associer_a' => 'Associer à :',
	'aucun_contact' => 'Il n’y a aucun contact !',
	'aucune_organisation' => 'Il n’y a aucune organisation !',
	'auteur_associe_est_a_la_poubelle' => 'L’auteur associé est à la poubelle ! Cet auteur sera effacé dans quelques jours.',
	'auteur_associe_inexistant' => 'L’auteur associé est inexistant ! L’auteur avait du être mis à la poubelle et a été supprimé.',
	'auteur_lie' => 'Id de l’auteur lié',

	// B
	'bouton_contacts' => 'Contacts',
	'bouton_contacts_organisations' => 'Contacts & Organisations',
	'bouton_organisations' => 'Organisations',
	'bouton_rechercher' => 'Rechercher',
	'bouton_repertoire' => 'Répertoire',

	// C
	'cfg_activer_squelettes_publics_zpip1' => 'Squelettes publics ZPIP v1',
	'cfg_activer_squelettes_publics_zpip1_explication' => 'Activer les squelettes publics pour ZPIP en version 1,
		permettant le parcourir les contacts et organisations dans l’espace public ?',
	'cfg_afficher_infos_sur_auteurs' => 'Affichage détaillé des auteurs ?',
	'cfg_afficher_infos_sur_auteurs_explication' => 'Afficher les infos de contact ou d’organisation
		également sur les pages auteurs dans l’espace privé ?',
	'cfg_associer_aux_auteurs' => 'Associer aux auteurs ?',
	'cfg_associer_aux_auteurs_explication' => 'Permettre d’associer des contacts ou organisations
		aux auteurs. Cela ajoute un formulaire pour associer un auteur sur les pages contact ou organisation,
		et inversement cela ajoute un formulaire pour lier un contact ou une organisation sur les pages des auteurs.',
	'cfg_lier_contacts_objets_explication' => 'Permettre d’associer les contacts aux contenus éditoriaux suivants. Cela affiche le sélecteur de contacts dans les pages d’administration de ces contenus.',
	'cfg_lier_contacts_objets_label' => 'Associer les contacts',
	'cfg_lier_organisations_objets_explication' => 'Permettre d’associer les organisations aux contenus éditoriaux suivants. Cela affiche le sélecteur d’organisations dans les pages d’administration de ces contenus.',
	'cfg_lier_organisations_objets_label' => 'Associer les organisations',
	'cfg_relations_avec_auteurs' => 'Relation avec les auteurs',
	'cfg_relations_avec_objets' => 'Relation avec les contenus éditoriaux (autres que les auteurs)',
	'cfg_supprimer_reciproquement_auteurs_et_contacts' => 'Supprimer réciproquement les auteurs et contacts ?',
	'cfg_supprimer_reciproquement_auteurs_et_contacts_explication' => 'Avec cette option active, lorsqu’un contact
		(ou une organisation) est supprimé, l’auteur associé à ce contact, s’il en existe un, voit son statut
		passer à la poubelle. De la même manière, inversement, si un auteur est mis à la poubelle,
		le contact éventuel associé est supprimé. Cette option peut être pratique sur certains sites
		afin d’éviter des contacts orphelins de leur auteur (si celui-ci a été supprimé) mais soyez vigilents :
		une suppression est définitive et les auteurs passent à la poubelle même s’ils ont écrit des articles…',
	'cfg_utiliser_annuaires_explication' => 'Activer la possibilité de classer vos contacts et organisations dans plusieurs annuaires différents.',
	'cfg_utiliser_annuaires_label' => 'Utiliser plusieurs annuaires',
	'cfg_utiliser_organisations_arborescentes_explication' => 'Il est possible de définir qu’une organisation est enfant d’une autre. Lorsque cette option est activée, cela ajoute un champ dans l’édition d’une organisation pour préciser ce lien de parenté.',
	'cfg_utiliser_organisations_arborescentes_label' => 'Utiliser une arborescence d’organisations',
	'changer' => 'Changer',
	'chercher_contact' => 'Chercher',
	'chercher_organisation' => 'Chercher',
	'chercher_statut' => 'Statut',
	'confirmer_delier_contact' => 'Êtes-vous sûr de vouloir délier cette organisation de ce contact ?',
	'confirmer_delier_organisation' => 'Êtes-vous sûr de vouloir délier ce contact de cette organisation ?',
	'confirmer_delier_organisation_rubrique' => 'Êtes-vous sûr de vouloir délier cette organisation de cette rubrique ?',
	'confirmer_supprimer_contact' => 'Êtes-vous sûr de vouloir supprimer
		les informations relatives à ce contact ?',
	'confirmer_supprimer_organisation' => 'Êtes-vous sûr de vouloir supprimer
		les informations relatives à cette organisation ?',
	'contact' => 'Contact',
	'contact_ajouter' => 'Ajouter un contact',
	'contact_ajouter_associe_a' => 'Ajouter un contact associé à :',
	'contact_ajouter_lien' => 'Ajouter ce contact',
	'contact_associe_a_auteur_numero' => 'Associée à auteur numéro',
	'contact_associer_a_auteur' => 'Associer à un auteur',
	'contact_aucun' => 'Aucun contact',
	'contact_creer' => 'Créer un contact',
	'contact_creer_associer' => 'Créer et associer un contact',
	'contact_creer_dans_cet_annuaire' => 'Créer un contact dans cet annuaire',
	'contact_editer' => 'Éditer ce contact',
	'contact_logo' => 'Logo de ce contact',
	'contact_nouveau_titre' => 'Nouveau contact',
	'contact_numero' => 'Contact numéro',
	'contact_retirer_lien' => 'Retirer le contact',
	'contact_retirer_tous_lien' => 'Retirer tous les contacts',
	'contact_un' => 'Un contact',
	'contact_voir' => 'Voir',
	'contacts' => 'Contacts',
	'contacts_nb' => '@nb@ contacts',
	'contacts_tout' => 'Tous les contacts',
	'creer_auteur_contact' => 'Créer un nouvel auteur et le lier à ce contact',
	'creer_auteur_organisation' => 'Créer un nouvel auteur et le lier à cette organisation',

	// D
	'definir_auteur_comme_contact' => 'Définir comme contact',
	'definir_auteur_comme_organisation' => 'Définir comme organisation',
	'delier_cet_auteur' => 'Désassocier',
	'delier_contact' => 'Désassocier',
	'delier_organisation' => 'Désassocier',

	// E
	'erreur_annuaire_identifiant_existant' => 'Cet identifiant est déjà utilisé par l’un de vos annuaires.',
	'est_un_contact' => 'Cet auteur est défini comme étant un contact.',
	'est_une_organisation' => 'Cet auteur est défini comme étant une organisation.',
	'explication_activite' => 'Activité de l’organisation : humanitaire, formation, édition...',
	'explication_contacts_ou_organisations' => 'Vous pouvez définir cet auteur
		comme étant un contact ou comme étant une organisation.
		Ces attributions donnent accès à des champs de saisies supplémentaires
		dans la fiche de renseignement de l’auteur.',
	'explication_identification' => 'Identifiant de l’organisation, comme par exemple N° de TVA, SIRET, SIRENE...',
	'explication_ouvertures' => 'Jours, horaires, saisons…',
	'explication_statut_juridique' => 'SA, SARL, association...',
	'explication_supprimer_contact' => 'La suppression du contact supprimera
		toutes les informations supplémentaires renseignées sur l’auteur.',
	'explication_supprimer_organisation' => 'La suppression de l’organisation supprimera
		toutes les informations supplémentaires renseignées sur l’auteur.',
	'explications_page_contacts' => 'Page en cours de développement. <br /><br />Actions envisagées :<ul>
	<li>voir tous les contacts</li><li>transformer les auteurs en contacts</li><li>importer des contacts</li><li>...</li></ul><br />Merci pour vos suggestions sur <a href="http://contrib.spip.net/Plugin-Contacts-Organisations#pagination_comments-list">le forum</a> ;-)',
	'explications_page_organisations' => 'Page en cours de développement. <br /><br />Actions envisagées :<ul>
	<li>voir toutes les organisations</li><li>transformer des auteurs en organisations</li><li>importer des organsations</li><li>...</li></ul><br />Merci pour vos suggestions sur <a href="http://contrib.spip.net/Plugin-Contacts-Organisations#pagination_comments-list">le forum</a> ;-)',
	'exporter_contacts' => 'Télécharger ces contacts en tableur',
	'exporter_organisations' => 'Télécharger ces organisations en tableur',

	// I
	'info_contacts_organisation' => 'Contacts de l’organisation',
	'info_nb_contacts' => 'Contacts liés',
	'info_organisation_appartenance' => 'Organisation d’appartenance',
	'info_organisations_appartenance' => 'Organisations d’appartenance',
	'info_organisations_filles' => 'Organisations filles',
	'info_organisations_meres' => 'Organisations mères',
	'info_tous_contacts' => 'Tous les contacts',
	'info_toutes_organisations' => 'Toutes les organisations',
	'infos_contacts_ou_organisations' => 'Contacts & Organisations',

	// L
	'label_activite' => 'Activité',
	'label_auteur_associe' => 'Auteur associé',
	'label_civilite' => 'Civilité',
	'label_date_creation' => 'Date de création',
	'label_date_naissance' => 'Date de naissance',
	'label_descriptif' => 'Description',
	'label_email' => 'Email',
	'label_fonction' => 'Fonction',
	'label_identification' => 'Identification',
	'label_nom' => 'Nom',
	'label_nom_organisation' => 'Organisation',
	'label_organisation' => 'Organisation liée',
	'label_organisation_parente' => 'Organisation parente',
	'label_ouvertures' => 'Périodes d’ouverture',
	'label_prenom' => 'Prénom',
	'label_prenom_nom' => 'Prénom + Nom',
	'label_pseudo' => 'Pseudo',
	'label_recherche_auteurs' => 'Chercher dans les auteurs',
	'label_recherche_contacts' => 'Chercher dans les contacts',
	'label_recherche_organisations' => 'Chercher dans les organisations',
	'label_statut_juridique' => 'Statut juridique',
	'label_tarifs' => 'Tarifs',
	'label_telephone' => 'Tél.',
	'label_type_liaison' => 'Liaison',
	'label_url_site' => 'Site internet',
	'lier_ce_contact' => 'Lier ce contact',
	'lier_cet_auteur' => 'Lier',
	'lier_cette_organisation' => 'Lier cette organisation',
	'lier_contact' => 'Lier un contact',
	'lier_organisation' => 'Lier une organisation',
	'liste_contacts' => 'Liste les contacts',
	'liste_organisations' => 'Liste les organisations',

	// N
	'nb_contact' => '1 contact',
	'nb_contacts' => '@nb@ contacts',
	'nom_contact' => 'Nom',
	'nom_organisation' => 'Nom',
	'non_renseigne' => 'Non renseigné',

	// O
	'organisation' => 'Organisation',
	'organisation_ajouter' => 'Ajouter une organisation',
	'organisation_ajouter_lien' => 'Ajouter cette organisation',
	'organisation_associe_a_auteur_numero' => 'Associée à auteur numéro',
	'organisation_associer_a_auteur' => 'Associer à un auteur',
	'organisation_aucun' => 'Aucune organisation',
	'organisation_creer' => 'Créer une organisation',
	'organisation_creer_associer' => 'Créer et associer une organisation',
	'organisation_creer_dans_cet_annuaire' => 'Créer une organisation dans cet annuaire',
	'organisation_creer_fille' => 'Créer une organisation fille',
	'organisation_editer' => 'Éditer cette organisation',
	'organisation_logo' => 'Logo de l’organisation',
	'organisation_nouveau_titre' => 'Nouvelle organisation',
	'organisation_numero' => 'Organisation numéro',
	'organisation_retirer_lien' => 'Retirer l’organisation',
	'organisation_retirer_tous_lien' => 'Retirer toutes les organisations',
	'organisation_un' => 'Une organisation',
	'organisation_voir' => 'Voir',
	'organisations' => 'Organisations',
	'organisations_nb' => '@nb@ organisations',
	'organisations_tout' => 'Toutes les organisations',

	// P
	'prenom' => 'Prénom',

	// R
	'recherche_de' => 'Recherche de « @recherche@ »',
	'rechercher' => 'Rechercher',
	'resume_annuaire' => 'Résumé de votre annuaire',
	'resume_annuaires' => 'Résumé de vos annuaires',

	// S
	'statut_juridique' => 'Statut juridique',
	'suppression_automatique_de_organisation_prochainement' => 'Sans intervention de votre part,
		la configuration actuelle du plugin Contacts & Organisations entraînera
		la suppression automatique de cette organisation dans les jours à venir.',
	'suppression_automatique_du_contact_prochainement' => 'Sans intervention de votre part,
		la configuration actuelle du plugin Contacts & Organisations entraînera
		la suppression automatique de ce contact dans les jours à venir.',
	'supprimer_contact' => 'Supprimer ce contact',
	'supprimer_organisation' => 'Supprimer cette organisation',

	// T
	'titre_contact' => 'Détails du contact',
	'titre_contacts' => 'Les contacts',
	'titre_organisation' => 'Détails de l’organisation',
	'titre_organisations' => 'Les organisations',
	'titre_page_configurer_contacts_et_organisations' => 'Configurer Contacts & Organisations',
	'titre_page_contacts' => 'Gestion des contacts',
	'titre_page_organisations' => 'Gestion des organisations',
	'titre_page_repertoire' => 'Répertoire',
	'titre_parametrages' => 'Paramétrages',
	'tous' => 'Tous'
);

?>
