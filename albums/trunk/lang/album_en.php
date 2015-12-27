<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/album?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_associer' => 'Add this album', # ou alors : link associate ?
	'bouton_dissocier' => 'Remove',
	'bouton_dissocier_explication' => 'Dissociate album from object',
	'bouton_editer_texte_album' => 'Edit text',
	'bouton_supprimer' => 'Delete',
	'bouton_supprimer_explication' => 'Delete permanently album',
	'bouton_transvaser' => 'Transfer',
	'bouton_transvaser_explication' => 'Extract documents from the album and associate them to the object.',
	'bouton_valider_deplacer_documents' => 'Save changes',
	'bouton_vider' => 'Empty',
	'bouton_vider_explication' => 'Empty album from its documents',

	// C
	'c_albumotheque_filtres' => 'Lateral filters let you active certain criteria
		in order to restrict the selection of albums. One click on an entry activates the filter, another click dis-activates it.
		By combining them you can easily find any album.
		When the lists are too long, search fields let you find a specific object.',
	'c_albumotheque_presentation' => 'Welcome to the «albumothèque»!<br>
You can create autonomous albums from this page and from the toolbar, or create albums associated with editorial objects on their own pages.
<br>Each album is editable «on site» (editing text and manipulating documents), or from its page.',
	'c_albumotheque_titre_filtres' => 'Filter selection',
	'c_albumotheque_titre_presentation' => 'Albums',
	'cfg_titre_albums' => 'Albums',

	// E
	'erreur_deplacement' => 'Treatment could not be performed',
	'explication_deplacer_documents' => '<strong>Experimental</strong> : it is possible to drag and drop documents between albums.
		If you are authorized, when hovering a document, the cursor will change to indicate an action is possible.
		Once the documents have been moved, a form will appear at the bottom of the list to save the changes.',
	'explication_onglet_ajouter_choisir' => 'Indicate their number separated by commas or click on "browse" to choose them manually.',
	'explication_onglet_ajouter_creer' => 'Title and description are optionals. You can add documents now or later when the album is created.',

	// F
	'filtre_extensions' => 'File extensions',
	'filtre_medias' => 'Documents types',
	'filtre_non_vus' => 'Not inserted in text',
	'filtre_orphelins' => 'Orphans',
	'filtre_types_utilisations' => 'Types of uses',
	'filtre_utilisations' => 'Use',
	'filtre_vus' => 'Inserted in text',

	// I
	'icone_ajouter_album' => 'Add an album',
	'icone_creer_album' => 'Create a new album',
	'icone_modifier_album' => 'Edit this album',
	'info_1_album' => '1 album',
	'info_1_utilisation' => '1 Use',
	'info_aucun_album' => 'No album',
	'info_nb_albums' => '@nb@ albums',
	'info_nb_utilisations' => '@nb@ uses',
	'info_nouvel_album' => 'New album',

	// L
	'label_activer_album_objets' => 'Enable albums for the contents :',
	'label_activer_deplacer_documents' => 'Drag and drop',
	'label_album_numero' => 'Number(s)',
	'label_case_deplacer_documents' => 'Drag and drop documents between albums.',
	'label_case_utiliser_titre_defaut' => 'By default, suggest the parent object’s title',
	'label_descriptif' => 'Description',
	'label_modele_alias_liste' => 'List',
	'label_modele_alias_vignettes' => 'Thumbnails',
	'label_modele_alignement' => 'Alignment',
	'label_modele_alignement_centre' => 'Center',
	'label_modele_alignement_droite' => 'Right',
	'label_modele_alignement_gauche' => 'Left',
	'label_modele_choisir' => 'Model choice',
	'label_modele_defaut' => 'Default',
	'label_modele_descriptif' => 'Display description',
	'label_modele_description_liste' => 'View documents as a list',
	'label_modele_description_vignettes' => 'View images as thumbnails',
	'label_modele_hauteur_images' => 'Max height of images',
	'label_modele_identifiant' => 'Album id',
	'label_modele_labels_images' => 'Display the label of each image',
	'label_modele_largeur_images' => 'Max width of images',
	'label_modele_meta_dimensions' => 'Dimensions',
	'label_modele_meta_extension' => 'Extension',
	'label_modele_meta_taille' => 'Size',
	'label_modele_metas' => 'Document information',
	'label_modele_nom_liste' => 'an album (list)',
	'label_modele_nom_vignettes' => 'an album (thumbnails)',
	'label_modele_pagination' => 'Pagination',
	'label_modele_parcourir_albums' => 'Browse albums',
	'label_modele_placeholder_dimension' => 'Size in pixels, without unity',
	'label_modele_ratio' => 'Ratio of re-shaped pictures',
	'label_modele_ratio_placeholder' => '16/9, 2.21:1, 4-3 etc.',
	'label_modele_recadrer_images' => 'Crop images',
	'label_modele_titre' => 'Display title',
	'label_modele_titre_perso' => 'Custom title',
	'label_modele_tri_date' => 'Date',
	'label_modele_tri_id' => 'Document number',
	'label_modele_tri_media' => 'Document type',
	'label_modele_tri_titre' => 'Title',
	'label_modele_trier' => 'Sort by :',
	'label_onglet_ajouter_choisir' => 'Associate one or several existing albums.',
	'label_onglet_ajouter_creer' => 'Create and associate a new album.',
	'label_titre' => 'Title',
	'label_utiliser_titre_defaut' => 'Title of a new album',

	// M
	'message_1_album_ajoute' => '1 album has been added.',
	'message_activer_cfg_documents' => 'In the document configuration form located in the «site content» menu, tick the « Albums » checkbox.',
	'message_album_non_editable' => 'This album cannot be edited : it’s in use by one or several objects that you can’t edit.',
	'message_avertissement_cfg_documents' => 'Warning! Adding documents to albums is not enabled. This is a necessary option for the albums to work properly.',
	'message_balise_inseree_succes' => 'The tag was inserted in the text',
	'message_id_album_ajoute' => 'The album <a href="@url@">N° @id_album@</a> has been added.',
	'message_nb_albums_ajoutes' => '@nb@ albums have been added.',
	'message_supprimer' => 'Delete permanently? This action cannot be reversed.',
	'message_vider' => 'Remove all the documents ?',

	// O
	'onglet_ajouter_choisir' => 'Choose album(s)',
	'onglet_ajouter_creer' => 'New album',
	'onglet_configurer_options' => 'Options',
	'onglet_configurer_outils' => 'Tools',

	// T
	'texte_activer_ajout_albums' => 'You can enable the interface to add albums to articles, sections and so on.
Similar to documents in portfolios, albums may be referenced in the text or displayed separately.',
	'texte_changer_statut' => 'Album status',
	'texte_creer_album' => 'Create a new album',
	'texte_double_clic_inserer_balise' => 'Double-click to insert the tag in the text.',
	'texte_modifier' => 'Edit',
	'texte_personnaliser_balise_album' => 'Customize tag',
	'texte_statut_poubelle' => 'to the dustbin',
	'texte_statut_prepa' => 'Unpublished',
	'texte_statut_publie' => 'published online',
	'titre_album' => 'Album',
	'titre_albums' => 'Albums',
	'titre_documents_deplaces' => 'Documents moved',
	'titre_logo_album' => 'Logo',
	'titre_page_configurer_albums' => 'Configure Albums'
);

?>
