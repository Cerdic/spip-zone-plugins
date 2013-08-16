<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/boussole?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_actualiser_boussoles' => 'Actualiser les boussoles', # NEW
	'bouton_actualiser_caches' => 'Actualiser les caches', # NEW
	'bouton_boussole' => 'Compass',
	'bouton_supprimer' => 'Delete',
	'bulle_afficher' => 'Display in the models',
	'bulle_aller_site' => 'Go to the page of the referenced website',
	'bulle_cacher' => 'Do not display in the models',
	'bulle_deplacer_bas' => 'Move down',
	'bulle_deplacer_haut' => 'Move up',

	// C
	'colonne_alias' => 'Alias',
	'colonne_description_cache' => 'Description', # NEW
	'colonne_fichier_cache' => 'Cache', # NEW
	'colonne_nbr_sites' => 'Contains',
	'colonne_prefixe_plugin' => 'Plugin ?', # NEW
	'colonne_titre' => 'Title',
	'colonne_version' => 'Version',

	// D
	'description_noisette_boussole' => 'Standard compass display. You can choose the displayed model (text links, logos...) and its configuration',
	'description_noisette_boussole_actualite' => 'Display syndicated website articles of a compass depending on the display model <code>boussole_liste_actualite</code>.',
	'description_noisette_boussole_contenu_z' => 'Display all informations of a compass as a main Z page content and depending on the compass display <code>boussole_contenu_z</code>.',
	'description_noisette_boussole_fil_ariane' => 'Display the breadcrumb trail of a compass',
	'description_page_boussole' => 'Page of a compass detailled information.',

	// I
	'info_ajouter_boussole' => 'By adding compasses to your database, you can use available models to display them in your public pages.<br />If the compass already exists, this form allows to update it while keeping the same display configuration.',
	'info_boite_boussoles_gerer_client' => '<strong>Cette page est uniquement accessible aux responsables du site.</strong><p>Elle permet l’ajout, la mise à jour et la suppression des boussoles en base de données en vue de leur affichage sur ce site. Il est aussi possible de se rendre sur la page de configuration de l\'affichage de chaque boussole en cliquant sur son nom dans la liste.</p>', # NEW
	'info_boite_boussoles_gerer_serveur' => '<strong>Cette page est uniquement accessible aux responsables du site.</strong><p>Elle permet de mettre à jour manuellement le cache des boussoles hébergées par ce site serveur. Il est possible de télécharger les caches en cliquant sur leur nom dans la liste.</p>', # NEW
	'info_boussole_manuelle' => 'Manuelle', # NEW
	'info_cache_boussole' => 'Cache de la boussole «@boussole@»', # NEW
	'info_cache_boussoles' => 'Cache des boussoles hébergées', # NEW
	'info_configurer_boussole' => 'This form allows you to configure the compass display by choosing the websites you want to display or not and their order in a group. The non-displayed websites have hatched background and grey fonts.',
	'info_fichier_boussole' => 'Enter the url of your compass description file.',
	'info_liste_aucun_cache' => 'Aucun cache n\'a encore été créé pour les boussoles hébérgées. Utilisez le bouton «actualiser les caches» pour les générer.', # NEW
	'info_liste_aucune_boussole' => 'No compass have been loaded from your database. Please, use the following form to add one.',
	'info_site_boussole' => 'This website belongs to the compass :',
	'info_site_boussoles' => 'This website belongs to the compasses :',

	// L
	'label_1_boussole' => '@nb@ compass',
	'label_1_site' => '@nb@ websites',
	'label_a_class' => 'Class of the anchor including the logo',
	'label_actualise_le' => 'Updated on',
	'label_affiche' => 'Displayed ?',
	'label_afficher_descriptif' => 'Display the websites description ?',
	'label_afficher_lien_accueil' => 'Dipslay the home page link ?',
	'label_afficher_slogan' => 'Display the websites slogan ?',
	'label_ariane_separateur' => 'Separator :',
	'label_boussole' => 'Compass to display',
	'label_cartouche_boussole' => 'Display the text block of the compass ?',
	'label_demo' => 'Get the demo page of this compass at the web adress',
	'label_descriptif' => 'Description',
	'label_div_class' => 'Class of the including div',
	'label_div_id' => 'Id of the including div',
	'label_fichier_xml' => 'XML File',
	'label_li_class' => 'Class of each li marker of the list',
	'label_logo' => 'Logo',
	'label_max_articles' => 'Maximum number of articles displayed by website',
	'label_max_sites' => 'Maximum number of websites',
	'label_mode' => 'Choose a compass',
	'label_mode_standard' => '"@boussole@", official compass of SPIP websites',
	'label_modele' => 'Display model',
	'label_n_boussoles' => '@nb@ compasses',
	'label_n_sites' => '@nb@ websites',
	'label_nom' => 'Name',
	'label_p_class' => 'Paragraph class including the description',
	'label_sepia' => 'Sepia color code (without #)',
	'label_slogan' => 'Slogan',
	'label_taille_logo' => 'Maximum size of the logo (in pixels)',
	'label_taille_logo_boussole' => 'Maximum size of the compass logo (in pixels)',
	'label_taille_titre' => 'Maximum size of the compass title',
	'label_titre_actualite' => 'Display the title of the news block ?',
	'label_titre_boussole' => 'Display the title of the compass ?',
	'label_titre_groupe' => 'Display the title of each group ?',
	'label_titre_site' => 'Display the title of each website ?',
	'label_type_bulle' => 'Displayed information in the bubble on each link',
	'label_type_description' => 'Displayed description on the side of the logo',
	'label_ul_class' => 'ul marker class of the list',
	'label_url' => 'URL',
	'label_version' => 'Version',

	// M
	'message_nok_boussole_inconnue' => 'No compass correspond to the alias "@alias@"',
	'message_nok_champ_obligatoire' => 'Mandatory field',
	'message_nok_ecriture_bdd' => 'Database writing error (table @table@)',
	'message_nok_xml_introuvable' => 'The file « @fichier@ » cannot be found',
	'message_nok_xml_invalide' => 'The XML description file « @fichier@ » of the compass is not conform to the DTD',
	'message_ok_boussole_actualisee' => 'The compass « @fichier@ » has been updated.',
	'message_ok_boussole_ajoutee' => 'The compass « @fichier@ » has been added.',
	'modele_boussole_liste_avec_logo' => 'List of links with names, logos and descritpion',
	'modele_boussole_liste_par_groupe' => 'List of text links per group',
	'modele_boussole_liste_simple' => 'List with text links',
	'modele_boussole_panorama' => 'Logos galery',
	'modele_boussole_panorama_sepia' => 'Logos galery with sepia effect ',

	// O
	'onglet_client' => 'Fonction Client', # NEW
	'onglet_serveur' => 'Fonction Serveur', # NEW
	'option_aucune_description' => 'No description',
	'option_descriptif_site' => 'Website description',
	'option_nom_site' => 'Website name',
	'option_nom_slogan_site' => 'Name and slogan of the website',
	'option_slogan_site' => 'Website slogan',

	// T
	'titre_boite_autres_boussoles' => 'Other compasses',
	'titre_boite_infos_boussole' => 'ALIAS COMPASS',
	'titre_boite_logo_boussole' => 'COMPASS LOGO',
	'titre_form_ajouter_boussole' => 'Add or update a compass',
	'titre_formulaire_configurer' => 'Compass display configuration',
	'titre_liste_boussoles' => 'List of available compasses', # MODIF
	'titre_liste_caches' => 'Liste des caches des boussoles hébergées', # NEW
	'titre_page_boussole' => 'Compass management'
);

?>
