<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/contacts?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_contact' => 'There is no contact !', # MODIF
	'aucune_organisation' => 'There is no organization', # MODIF
	'auteur_associe_est_a_la_poubelle' => 'The associated author is in the rubbish bin! This author will be deleted in a few days.',
	'auteur_associe_inexistant' => 'The associated author does not exist! The author must have been put in the rubbish bin and has been deleted.',
	'auteur_lie' => 'Id of the linked author',

	// B
	'bouton_contacts' => 'Contacts',
	'bouton_contacts_organisations' => 'Contacts & Organizations',
	'bouton_organisations' => 'Organisations',
	'bouton_rechercher' => 'Search',
	'bouton_repertoire' => 'Directory',

	// C
	'cfg_activer_squelettes_publics_zpip1' => 'Public skeleton of ZPIP v1',
	'cfg_activer_squelettes_publics_zpip1_explication' => 'Enable public skeletons for  ZPIP version 1, which allow to go to the contacts and organizations in the public space?', # MODIF
	'cfg_afficher_infos_sur_auteurs' => 'Detail display of the authors ?', # MODIF
	'cfg_afficher_infos_sur_auteurs_explication' => 'View contact or organisation information on the authors pages in the private area?', # RELIRE
	'cfg_associer_aux_auteurs' => 'Associate to the authors ?', # MODIF
	'cfg_associer_aux_auteurs_explication' => 'Allow to associate contacts or organizations to authors. This adds a form to associate an author on the contact pages or organization, and conversely it adds a form to link to a contact or an organization on the authors pages.',
	'cfg_lier_organisations_rubriques' => 'Link to sections',
	'cfg_lier_organisations_rubriques_explication' => 'Allow to link organizations to sections ?
		This display the organization selector on sections and the section selector on organizations.', # MODIF
	'cfg_relations_avec_auteurs' => 'Relationship with authors', # MODIF
	'cfg_supprimer_reciproquement_auteurs_et_contacts' => 'Mutually delete authors and contacts? ', # MODIF
	'cfg_supprimer_reciproquement_auteurs_et_contacts_explication' => 'By activating this option, when a contact (or an organisation) is deleted, the author associated with this contact, if one exists, sees their status move to the rubbish bin. Inversely, in the same way, if an author is put in the rubbish bin, the contact associated is deleted. This option can be useful on certain sites in order to avoid contacts left without an author (if the latter has been deleted) but be vigilent: deletion is definitive and authors are put in the rubbish ben even if they have written articles...',
	'changer' => 'Change',
	'chercher_contact' => 'Search',
	'chercher_organisation' => 'Search',
	'chercher_statut' => 'Status',
	'confirmer_delier_contact' => 'Are you sure you want to unlink this organisation from this contact?', # MODIF
	'confirmer_delier_organisation' => 'Are you sure you want to unlink this contact from this organisation?', # MODIF
	'confirmer_delier_organisation_rubrique' => 'Are you sure you want to unlink this organization from this section?', # MODIF
	'confirmer_supprimer_contact' => 'Ares you sure you want to delete all information about this contact?', # MODIF
	'confirmer_supprimer_organisation' => 'Are you sure you want to delete all the information about this organisation?', # MODIF
	'contact' => 'Contact',
	'contact_ajouter' => 'Add a contact',
	'contact_ajouter_lien' => 'Add this contact',
	'contact_associe_a_auteur_numero' => 'Link to author number',
	'contact_associer_a_auteur' => 'Link to an author',
	'contact_aucun' => 'No contact',
	'contact_creer' => 'Create a contact',
	'contact_creer_associer' => 'Create and associate a contact',
	'contact_editer' => 'Edit this contact',
	'contact_logo' => 'Contact logo',
	'contact_nouveau_titre' => 'New contact',
	'contact_numero' => 'Contact number',
	'contact_retirer_lien' => 'Delete the contact',
	'contact_retirer_tous_lien' => 'Delete all the contacts',
	'contact_un' => 'A contact',
	'contact_voir' => 'See',
	'contacts' => 'Contacts',
	'contacts_nb' => '@nb@ contacts',
	'creer_auteur_contact' => 'Create a new author and link it to this contact',
	'creer_auteur_organisation' => 'Create a new author and link it to this organization',

	// D
	'definir_auteur_comme_contact' => 'Set as contact',
	'definir_auteur_comme_organisation' => 'Set as organisation',
	'delier_cet_auteur' => 'Unlink',
	'delier_contact' => 'Disassociate',
	'delier_organisation' => 'Disassociate',

	// E
	'est_un_contact' => 'This author is set as a contact.',
	'est_une_organisation' => 'This author is set as an organization.',
	'explication_activite' => 'Activity of the organization : NGO, education, edition...',
	'explication_contacts_ou_organisations' => 'You can set this author as a contact or as an organization.Vous pouvez définir cet auteur
  Additional fields can be then filled from the author modification page.',
	'explication_identification' => 'Organisation identification : VAT, etc.',
	'explication_statut_juridique' => 'company, organisation, ...',
	'explication_supprimer_contact' => 'Deleting this contact will remove all the additional informations which have been filled on the author page.',
	'explication_supprimer_organisation' => 'Deleting this organization will remove all the additional informations which have been filled on the author page.',
	'explications_page_contacts' => 'Page under development. <br /><br /> Actions considered :
<ul>
  <li>see all contacts</ li>   <li>transform the authors in contacts</ li><li>import contacts </ li><li>...</ li> </ ul><br / > Thanks for your suggestions on <a href="http://contrib.spip.net/Plugin-Contacts-Organisations#pagination_comments-list">the forum</a> ;-)',
	'explications_page_organisations' => 'Page under development. <br /><br /> Actions considered :
<ul>
<li>see all organizations</ li> <li>transform the authors in organizations</ li><li>import organizations </ li><li>...</ li> </ ul><br / > Thanks for your suggestions on <a href="http://contrib.spip.net/Plugin-Contacts-Organisations#pagination_comments-list">the forum</a> ;-)',

	// I
	'info_contacts_organisation' => 'Organisation’s contacts',
	'info_nb_contacts' => 'Linked contacts',
	'info_organisation_appartenance' => 'Belonging Organisation',
	'info_organisations_appartenance' => 'Membership organizations',
	'info_organisations_filles' => 'Children organizations',
	'info_organisations_meres' => 'Parents organizations',
	'info_tous_contacts' => 'All the contacts',
	'info_toutes_organisations' => 'All the organisations',
	'infos_contacts_ou_organisations' => 'Contacts & Organisations',

	// L
	'label_activite' => 'Activity',
	'label_civilite' => 'Gender',
	'label_date_creation' => 'Date of creation',
	'label_date_naissance' => 'Date of birth',
	'label_descriptif' => 'Description',
	'label_email' => 'Email',
	'label_fonction' => 'Function',
	'label_identification' => 'Identification',
	'label_nom' => 'Name',
	'label_nom_organisation' => 'Organisation',
	'label_organisation' => 'Linked organisation',
	'label_organisation_parente' => 'Parent organization',
	'label_prenom' => 'First name',
	'label_prenom_nom' => 'First + last name',
	'label_pseudo' => 'Pseudo',
	'label_recherche_auteurs' => 'Search in authors',
	'label_recherche_contacts' => 'Search in contacts',
	'label_recherche_organisations' => 'Search in organisations',
	'label_statut_juridique' => 'Status',
	'label_telephone' => 'Phone',
	'label_type_liaison' => 'Link',
	'lier_ce_contact' => 'Attach this contact',
	'lier_cet_auteur' => 'Link',
	'lier_cette_organisation' => 'Attach to this organisation',
	'lier_contact' => 'Attach a contact',
	'lier_organisation' => 'Attach to an organisation',
	'liste_contacts' => 'List of all contacts in the database',
	'liste_organisations' => 'List of all organisations in the database',

	// N
	'nb_contact' => '1 contact',
	'nb_contacts' => '@nb@ contacts',
	'nom_contact' => 'Name',
	'nom_organisation' => 'Name',
	'non_renseigne' => 'Unknown',

	// O
	'organisation' => 'Organization',
	'organisation_ajouter' => 'Add an organization',
	'organisation_ajouter_lien' => 'Add this organization',
	'organisation_associe_a_auteur_numero' => 'Linked to author number',
	'organisation_associer_a_auteur' => 'Link to an author',
	'organisation_aucun' => 'No organization',
	'organisation_creer' => 'Create an organization',
	'organisation_creer_associer' => 'Create and associate an organization',
	'organisation_creer_fille' => 'Create a child organization',
	'organisation_editer' => 'Edit this organizatino',
	'organisation_logo' => 'Organization logo',
	'organisation_nouveau_titre' => 'New organization',
	'organisation_numero' => 'Organization number',
	'organisation_retirer_lien' => 'Delete the organization',
	'organisation_retirer_tous_lien' => 'Delete all the organizations',
	'organisation_un' => 'One organization',
	'organisation_voir' => 'See',
	'organisations' => 'Organisations',
	'organisations_nb' => '@nb@ organizations',

	// P
	'prenom' => 'First name',

	// R
	'recherche_de' => 'Search for "@recherche@"',
	'rechercher' => 'Search',

	// S
	'statut_juridique' => 'Legal status',
	'suppression_automatique_de_organisation_prochainement' => 'Without your intervention, the current configuration of the Contacts & Organisations plugin will lead to the automatic deletion of this organisation in the next few days.',
	'suppression_automatique_du_contact_prochainement' => 'Without your intervention, the current configuration of the Contacts & Organisations plugin will lead to the automatic deletion of this contact in the next few days.',
	'supprimer_contact' => 'Delete this contact',
	'supprimer_organisation' => 'Delete this organisation',

	// T
	'titre_contact' => 'Contact details',
	'titre_organisation' => 'Organization details',
	'titre_page_configurer_contacts_et_organisations' => 'Configure Contacts & Organizations',
	'titre_page_contacts' => 'Contacts management',
	'titre_page_organisations' => 'Organisations management',
	'titre_page_repertoire' => 'Directory',
	'titre_parametrages' => 'Parameter setting',
	'tous' => 'All'
);

?>
