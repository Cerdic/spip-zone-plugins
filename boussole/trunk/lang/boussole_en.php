<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/boussole?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_actualiser_boussoles' => 'Update compasses',
	'bouton_actualiser_caches' => 'Update file caches',
	'bouton_boussole' => 'Compass',
	'bouton_retirer_boussole' => 'Retirer la boussole', # NEW
	'bouton_retirer_serveur' => 'Remove the server',
	'bouton_supprimer' => 'Delete',
	'bulle_afficher' => 'Display in the models',
	'bulle_aller_site' => 'Go to the page of the referenced website',
	'bulle_cacher' => 'Do not display in the models',
	'bulle_deplacer_bas' => 'Move down',
	'bulle_deplacer_haut' => 'Move up',

	// C
	'colonne_alias' => 'Alias',
	'colonne_description_cache' => 'Description',
	'colonne_fichier_cache' => 'File cache',
	'colonne_nbr_sites' => 'Contains',
	'colonne_prefixe_plugin' => 'Plugin?',
	'colonne_serveur' => 'Server',
	'colonne_titre' => 'Title',
	'colonne_url' => 'URL',
	'colonne_version' => 'Version',

	// D
	'description_noisette_boussole' => 'Standard compass display. You can choose the displayed model (text links, logos...) and its configuration',
	'description_noisette_boussole_actualite' => 'Display syndicated website articles of a compass depending on the display model <code>boussole_liste_actualite</code>.',
	'description_noisette_boussole_contenu_z' => 'Display all informations of a compass as a main Z page content and depending on the compass display <code>boussole_contenu_z</code>.',
	'description_noisette_boussole_fil_ariane' => 'Display the breadcrumb trail of a compass',
	'description_page_boussole' => 'Page of a compass detailled information.',

	// I
	'info_ajouter_boussole' => 'By adding compasses to your database, you can use available models to display them in your public pages.<br />If the compass already exists, this form allows to update it while keeping the same display configuration.',
	'info_ajouter_serveur' => 'This form allows you to add a compasses server. By default, the "spip" server is always available on customer websites. The list of available servers is displayed above and allows you also to remove a configured server.', # MODIF
	'info_alias_boussole_manuelle' => 'Saisissez l\'alias de la boussole manuelle telle que définie dans son fichier XML.', # NEW
	'info_boite_boussoles_gerer_client' => '<strong>Only webmasters have access to this page.</strong><p>It provides database fonctions to add, update or delete compasses to display. IBy clicking on compass name, you can go to compass configuraton page to define display parameters</p>', # MODIF
	'info_boite_boussoles_gerer_serveur' => '<strong>Only webmasters have access to this page.</strong><p>It provides manual update of hosted compasses file caches. additionally, compass file cache can be downloaded by clicking on its name.</p>', # MODIF
	'info_boussole_manuelle' => 'Manual compass',
	'info_cache_boussole' => 'File cache of compass "@boussole@"', # MODIF
	'info_cache_boussoles' => 'File cache of hosted compasses list',
	'info_configurer_boussole' => 'This form allows you to configure the compass display by choosing the websites you want to display or not and their order in a group. The non-displayed websites have hatched background and grey fonts.',
	'info_declarer_boussole_manuelle' => 'Ce formulaire vous permet de déclarer une boussole manuelle hébergée par ce site. Une fois déclarée, la boussole deviendra accessible par les sites client utilisant ce serveur.', # NEW
	'info_fichier_boussole' => 'Enter the url of your compass description file.',
	'info_liste_aucun_cache' => 'No file cache has been generated yet for hosted compasses. Click "update file caches" button to run the generation.', # MODIF
	'info_liste_aucun_hebergement' => 'Aucune boussole n\'est encore hébergée sur ce serveur. Utilisez le formulaire ci-dessous pour déclarer une boussole manuelle ou activez un plugin de boussole sur ce site.', # NEW
	'info_liste_aucun_serveur' => 'No server has been configured to the customer site.',
	'info_liste_aucune_boussole' => 'No compass have been loaded from your database. Please, use the following form to add one.',
	'info_site_boussole' => 'This website belongs to the compass:',
	'info_site_boussoles' => 'This website belongs to the compasses:',
	'info_url_serveur' => 'Type in the URL of the server to add it to the list.', # MODIF

	// L
	'label_1_boussole' => '@nb@ compass',
	'label_1_site' => '@nb@ websites',
	'label_a_class' => 'Class of the anchor including the logo',
	'label_actualise_le' => 'Updated on',
	'label_affiche' => 'Displayed?',
	'label_afficher_descriptif' => 'Display the websites description?',
	'label_afficher_lien_accueil' => 'Dipslay the home page link?',
	'label_afficher_slogan' => 'Display the websites slogan?',
	'label_alias_boussole' => 'Alias de la boussole', # NEW
	'label_ariane_separateur' => 'Separator:',
	'label_boussole' => 'Compass to display',
	'label_cartouche_boussole' => 'Display the text block of the compass?',
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
	'label_mode_standard' => '"@boussole@", official compass of SPIP websites', # MODIF
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
	'label_titre_actualite' => 'Display the title of the news block?',
	'label_titre_boussole' => 'Display the title of the compass?',
	'label_titre_groupe' => 'Display the title of each group?',
	'label_titre_site' => 'Display the title of each website?',
	'label_type_bulle' => 'Displayed information in the bubble on each link',
	'label_type_description' => 'Displayed description on the side of the logo',
	'label_ul_class' => 'ul marker class of the list',
	'label_url' => 'URL',
	'label_url_serveur' => 'Server URL',
	'label_version' => 'Version',

	// M
	'message_nok_aucune_boussole_hebergee' => 'No compass is still hosted on the server "@serveur@".', # MODIF
	'message_nok_boussole_inconnue' => 'Compass named "@alias@ is unknown"', # MODIF
	'message_nok_boussole_non_hebergee' => 'Compass named "@alias@" is not hosted on server "@serveur@".', # MODIF
	'message_nok_cache_boussole_indisponible' => 'File cache of compass named "@alias@" is not available on server "@serveur@".', # MODIF
	'message_nok_cache_liste_indisponible' => 'File cache of hosted compasses list is not available on server "@serveur@".', # MODIF
	'message_nok_declaration_boussole_xml' => 'La boussole manuelle « @boussole@ » ne peut pas être déclarée car son fichier XML est introuvable.', # NEW
	'message_nok_ecriture_bdd' => 'Error when writing in database (table @table@)',
	'message_nok_reponse_invalide' => 'Answer of server "@serveur@" is malformed.', # MODIF
	'message_ok_boussole_actualisee' => 'The compass "@fichier@" has been updated.', # MODIF
	'message_ok_boussole_ajoutee' => 'The compass "@fichier@" has been added.', # MODIF
	'message_ok_boussole_manuelle_ajoutee' => 'La boussole manuelle « @boussole@ » a été déclarée au serveur et les caches ont été mis à jour.', # NEW
	'message_ok_serveur_ajoute' => 'The "@serveur@" server has been added (@url@).', # MODIF
	'modele_boussole_liste_avec_logo' => 'List of links with names, logos and descritpion',
	'modele_boussole_liste_par_groupe' => 'List of text links per group',
	'modele_boussole_liste_simple' => 'List with text links',
	'modele_boussole_panorama' => 'Logos galery',
	'modele_boussole_panorama_sepia' => 'Logos galery with sepia effect ',

	// O
	'onglet_client' => 'Client',
	'onglet_serveur' => 'Server',
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
	'titre_form_ajouter_serveur' => 'Add a compasses server', # MODIF
	'titre_form_boussole_manuelle' => 'Déclarer une boussole manuelle', # NEW
	'titre_formulaire_configurer' => 'Compass display configuration',
	'titre_liste_boussoles' => 'List of compasses available for display',
	'titre_liste_caches' => 'List of file caches for hosted compasses',
	'titre_liste_serveurs' => 'List of available servers', # MODIF
	'titre_page_boussole' => 'Compass management'
);

?>
