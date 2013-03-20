<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/boussole?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_boussole' => 'Brújula',
	'bouton_supprimer' => 'Eliminar',
	'bulle_afficher' => 'Mostrar en los modelos',
	'bulle_aller_site' => 'Ir a la página del sitio referenciado',
	'bulle_cacher' => 'No mostrar en los modelos',
	'bulle_deplacer_bas' => 'Desplazar hacia abajo',
	'bulle_deplacer_haut' => 'Desplazar hacia arriba',

	// C
	'colonne_alias' => 'Alias',
	'colonne_nbr_sites' => 'Contiene',
	'colonne_titre' => 'Título',
	'colonne_version' => 'Versión',

	// D
	'description_noisette_boussole' => 'Visualización estándar de una brújula. Puede elegir el modelo de visualización (enlaces textuales, logos...) así como su configuración precisa. ',
	'description_noisette_boussole_actualite' => 'Affichage des articles syndiqués des sites d\'une boussole selon le modèle d\'affichage <code>boussole_liste_actualite</code>.', # NEW
	'description_noisette_boussole_contenu_z' => 'Affichage de toutes les informations d\'une boussole comme contenu principal d\'une page Z et selon le modèle d\'affichage <code>boussole_contenu_z</code>.', # NEW
	'description_noisette_boussole_fil_ariane' => 'Affichage du fil d\'ariane d\'une boussole.', # NEW
	'description_page_boussole' => 'Page des informations détaillées d\'une boussole', # NEW

	// I
	'info_ajouter_boussole' => 'En ajoutant des boussoles à votre base de données, vous aurez la possiblité d\'utiliser les modèles fournis pour les afficher dans vos pages publiques.<br />Si la boussole existe déjà ce formulaire permettra de la mettre à jour en conservant la configuration d\'affichage.', # NEW
	'info_boite_boussoles_gerer' => '<strong>Cette page est uniquement accessible aux responsables du site.</strong><p>Elle permet l’ajout, la mise à jour et la suppression des boussoles. Il est aussi possible de se rendre sur la page de configuration de l\'affichage de chaque boussole.</p>', # NEW
	'info_configurer_boussole' => 'Ce formulaire vous permez de configurer l\'affichage de la boussole en choisissant les sites à afficher ou pas et l\'ordre d\'affichage dans un groupe. Les sites non affichés sont repérés par un fond hachuré et une police grise.', # NEW
	'info_fichier_boussole' => 'Saisissez l\'url du fichier de description de votre boussole', # NEW
	'info_liste_aucune_boussole' => 'Aucune boussole n\'a encore été chargée dans votre base. Utilisez le formulaire ci-dessous pour en ajouter.', # NEW
	'info_site_boussole' => 'Este sitio es parte de la brújula:',
	'info_site_boussoles' => 'Ce site fait partie des boussoles :', # NEW

	// L
	'label_1_boussole' => '@nb@ boussole', # NEW
	'label_1_site' => '@nb@ site', # NEW
	'label_a_class' => 'Classe de l\'ancre englobant le logo', # NEW
	'label_actualise_le' => 'Actualisée le', # NEW
	'label_affiche' => 'Affiché ?', # NEW
	'label_afficher_descriptif' => 'Afficher le descriptif des sites ?', # NEW
	'label_afficher_lien_accueil' => 'Afficher le lien vers la page d\'accueil ?', # NEW
	'label_afficher_slogan' => 'Afficher le slogan des sites ?', # NEW
	'label_ariane_separateur' => 'Séparateur :', # NEW
	'label_boussole' => 'Boussole à afficher', # NEW
	'label_cartouche_boussole' => 'Afficher le cartouche de la boussole ?', # NEW
	'label_demo' => 'Retrouvez la page de démo de cette boussole à l\'adresse', # NEW
	'label_descriptif' => 'Descriptivo',
	'label_div_class' => 'Classe du div englobant', # NEW
	'label_div_id' => 'Id du div englobant', # NEW
	'label_fichier_xml' => 'Fichier XML', # NEW
	'label_li_class' => 'Classe de chaque balise li de la liste', # NEW
	'label_logo' => 'Logo',
	'label_max_articles' => 'Número máximo de artículos mostrados por sitio',
	'label_max_sites' => 'Número máximo de sitios',
	'label_mode' => 'Elija una brújula',
	'label_mode_standard' => '«@boussole@», brújula oficial de los sitios SPIP',
	'label_modele' => 'Modelo de visualización',
	'label_n_boussoles' => '@nb@ brújulas',
	'label_n_sites' => '@nb@ sitios',
	'label_nom' => 'Nombre',
	'label_p_class' => 'Clase de párrafo englobando el descriptivo',
	'label_sepia' => 'Código del color sepia (sin #)',
	'label_slogan' => 'Eslogan',
	'label_taille_logo' => 'Tamaño máximo del logo (en pixels)',
	'label_taille_logo_boussole' => 'Tamaño máximo del logo de la brújula (en pixels)',
	'label_taille_titre' => 'Tamaño máximo del título de una brújula',
	'label_titre_actualite' => '¿Mostrar el título del bloque de actualidad?',
	'label_titre_boussole' => '¿Mostrar el título de la brújula?',
	'label_titre_groupe' => '¿Mostrar el título del grupo?',
	'label_titre_site' => '¿Mostrar el título de los sitios?',
	'label_type_bulle' => 'Information affichée dans la bulle de chaque lien', # NEW
	'label_type_description' => 'Descripción mostrada al lado del logo',
	'label_ul_class' => 'Classe de la balise ul de la liste', # NEW
	'label_url' => 'URL',
	'label_version' => 'Versión',

	// M
	'message_nok_boussole_inconnue' => 'Aucune boussole ne correspond &agrave l\'alias « @alias@ »', # NEW
	'message_nok_champ_obligatoire' => 'Este campo es obligatorio',
	'message_nok_ecriture_bdd' => 'Error de escritura en la base de datos (tabla @table@)',
	'message_nok_xml_introuvable' => 'El archivo «@fichier@» no se encuentra',
	'message_nok_xml_invalide' => 'Le fichier XML « @fichier@ » de description de la boussole n\'est pas conforme à la DTD', # NEW
	'message_ok_boussole_actualisee' => 'La brújula «@fichier@» ha sido actualizada. ',
	'message_ok_boussole_ajoutee' => 'La brújula «@fichier@» ha sido añadida. ',
	'modele_boussole_liste_avec_logo' => 'Lista de enlaces con nombres, logos y descripción',
	'modele_boussole_liste_par_groupe' => 'Lista de enlaces textuales por grupo',
	'modele_boussole_liste_simple' => 'Lista simple de enlaces textuales',
	'modele_boussole_panorama' => 'Galería de los logos',
	'modele_boussole_panorama_sepia' => 'Galería de los logos con efecto sepia',

	// O
	'option_aucune_description' => 'Ninguna descripción',
	'option_descriptif_site' => 'Descriptivo del sitio',
	'option_nom_site' => 'Nombre del sitio',
	'option_nom_slogan_site' => 'Nombre y eslogan del sitio',
	'option_slogan_site' => 'Eslogan del sitio',

	// T
	'titre_boite_autres_boussoles' => 'Otras brújulas',
	'titre_boite_infos_boussole' => 'BRÚJULA DEL ALIAS',
	'titre_boite_logo_boussole' => 'LOGO DE LA BRÚJULA',
	'titre_form_ajouter_boussole' => 'Añadir o actualizar una brújula',
	'titre_formulaire_configurer' => 'Configuración de la visualización de la brújula',
	'titre_liste_boussoles' => 'Lista de las brújulas disponibles',
	'titre_page_boussole' => 'Gestión de las brújulas'
);

?>
