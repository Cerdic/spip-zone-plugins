<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/boussole?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_actualiser_boussoles' => 'Actualizar las brújulas',
	'bouton_actualiser_caches' => 'Actualizar las cachés',
	'bouton_boussole' => 'Brújula',
	'bouton_retirer_boussole' => 'Retirer la boussole', # NEW
	'bouton_retirer_serveur' => 'Retirar el servidor',
	'bouton_supprimer' => 'Eliminar',
	'bulle_afficher' => 'Mostrar en los modelos',
	'bulle_aller_site' => 'Ir a la página del sitio referenciado',
	'bulle_cacher' => 'No mostrar en los modelos',
	'bulle_deplacer_bas' => 'Desplazar hacia abajo',
	'bulle_deplacer_haut' => 'Desplazar hacia arriba',

	// C
	'colonne_alias' => 'Alias',
	'colonne_description_cache' => 'Descripción',
	'colonne_fichier_cache' => 'Caché',
	'colonne_nbr_sites' => 'Contiene',
	'colonne_prefixe_plugin' => '¿Plugin?',
	'colonne_serveur' => 'Servidor',
	'colonne_titre' => 'Título',
	'colonne_url' => 'URL',
	'colonne_version' => 'Versión',

	// D
	'description_noisette_boussole' => 'Visualización estándar de una brújula. Puede elegir el modelo de visualización (enlaces textuales, logos...) así como su configuración precisa. ',
	'description_noisette_boussole_actualite' => 'Visualización de los artículos sindicados de los sitios de una brújula según el modelo de visualización <code>boussole_liste_actualite</code>.',
	'description_noisette_boussole_contenu_z' => 'Visualización de toda la información de una brújula como contenido principal de una página Z y según el modelo de visualización <code>boussole_contenu_z</code>.',
	'description_noisette_boussole_fil_ariane' => 'Visualización del hilo de Ariadna de una brújula.',
	'description_page_boussole' => 'Página de información detallada de una brújula',

	// I
	'info_activite_serveur' => 'Par défaut, la fonction serveur du plugin n\'est pas active. Vous pouvez l\'activer en choisissant l\'option adéquate ci-dessous et en lui affectant un nom.', # NEW
	'info_ajouter_boussole' => 'Añadiendo las brújulas a su base de datos, tendrá la posibilidad de utilizar los modelos proporcionados para mostrarlos en sus páginas públicas.<br />Si la brújula ya existe este formulario permitirá actualizarla conservando la configuración de la visualización. ',
	'info_ajouter_serveur' => 'Este formulario le permite añadir un servidor de brújulas. Por defecto, el servidor «spip» está siempre disponible en los sitios clientes. ',
	'info_alias_boussole_manuelle' => 'Saisissez l\'alias de la boussole manuelle telle que définie dans son fichier XML.', # NEW
	'info_boite_boussoles_gerer_client' => '<strong>Esta página sólo es accesible para los responsables del sitio.</strong><p>Permite agregar, actualizar y eliminar brújulas en base de datos para su visualizacion en el sitio. Se puede también ir a la página de configuración de la visualización de cada brújula haciendo clic sobre su nombre en la lista.</p><p>Un formulario permite asimismo configurar los servidores de brújulas accesibles desde la web.</p>',
	'info_boite_boussoles_gerer_serveur' => '<strong>Esta página sólo es accesible para los responsables del sitio.</strong><p>Permite actualzar manualmente la caché de las brújulas albergadas por este servidor. Las cachés pueden descargarse haciendo clic sobre su nombre en la lista.</p>', # MODIF
	'info_boussole_manuelle' => 'Brújula manual',
	'info_cache_boussole' => 'Caché de la brújula «@boussole@»', # MODIF
	'info_cache_boussoles' => 'Caché de las brújulas alojadas',
	'info_configurer_boussole' => 'Este formulario le permite configurar la visualización de la brújula eligiendo los sitios a mostrar o no y el orden de visualización en un grupo. Los sitios no mostrados se indican por un fondo tramado y una fuente color gris. ',
	'info_declarer_boussole_manuelle' => 'Ce formulaire vous permet de déclarer une boussole manuelle hébergée par ce site. Une fois déclarée, la boussole deviendra accessible par les sites client utilisant ce serveur.', # NEW
	'info_fichier_boussole' => 'Introduzca la url del archivo de descripción de su brújula',
	'info_liste_aucun_cache' => 'Ninguna caché ha sido todavía creada para las brújulas albergadas. Utilice el botón «actualizar las cachés» para crearlas.', # MODIF
	'info_liste_aucun_hebergement' => 'Aucune boussole n\'est encore hébergée sur ce serveur. Utilisez le formulaire ci-dessous pour déclarer une boussole manuelle ou activez un plugin de boussole sur ce site.', # NEW
	'info_liste_aucun_serveur' => 'Ningún servidor está todavía configurado para el sitio cliente.',
	'info_liste_aucune_boussole' => 'Ninguna brújula se ha cargado aún en su base de datos. Utilice el siguiente formulario para añadir una.',
	'info_nom_serveur' => 'Saisissez le nom que vous souhaitez donner à votre serveur de boussoles. Le nom «spip» est réservé et ne peut donc pas être utilisé.', # NEW
	'info_site_boussole' => 'Este sitio forma parte de la brújula:',
	'info_site_boussoles' => 'Este sitio forma parte de las brújulas:',
	'info_url_serveur' => 'Introduzca la URL del servidor para añadirla a la lista.', # MODIF

	// L
	'label_1_boussole' => '@nb@ brújula',
	'label_1_site' => '@nb@ sitio',
	'label_a_class' => 'Clase de anclaje englobando el logo',
	'label_activite_serveur' => 'Activer la fonction serveur ?', # NEW
	'label_actualise_le' => 'Actualizada el',
	'label_affiche' => '¿Mostrado?',
	'label_afficher_descriptif' => '¿Mostrar la descripción de los sitios?',
	'label_afficher_lien_accueil' => '¿Mostrar el enlace a la página de inicio?',
	'label_afficher_slogan' => '¿Mostrar el eslogan de los sitios?',
	'label_alias_boussole' => 'Alias de la boussole', # NEW
	'label_ariane_separateur' => 'Separador:',
	'label_boussole' => 'Brújula a mostrar',
	'label_cartouche_boussole' => '¿Mostrar el cartucho de la brújula?',
	'label_demo' => 'Encuentre la página de demostración de esta brújula en la dirección',
	'label_descriptif' => 'Descriptivo',
	'label_div_class' => 'Clase del div englobante',
	'label_div_id' => 'Id del div englobante',
	'label_fichier_xml' => 'Archivo XML',
	'label_li_class' => 'Clase de cada etiqueta li de la lista',
	'label_logo' => 'Logo',
	'label_max_articles' => 'Número máximo de artículos mostrados por sitio',
	'label_max_sites' => 'Número máximo de sitios',
	'label_mode' => 'Elija una brújula',
	'label_mode_standard' => '«@boussole@», brújula oficial de los sitios SPIP', # MODIF
	'label_modele' => 'Modelo de visualización',
	'label_n_boussoles' => '@nb@ brújulas',
	'label_n_sites' => '@nb@ sitios',
	'label_nom' => 'Nombre',
	'label_nom_serveur' => 'Nom du serveur', # NEW
	'label_p_class' => 'Clase de párrafo englobando la descripción',
	'label_sepia' => 'Código del color sepia (sin #)',
	'label_slogan' => 'Eslogan',
	'label_taille_logo' => 'Tamaño máximo del logo (en pixels)',
	'label_taille_logo_boussole' => 'Tamaño máximo del logo de la brújula (en pixels)',
	'label_taille_titre' => 'Tamaño máximo del título de una brújula',
	'label_titre_actualite' => '¿Mostrar el título del bloque de actualidad?',
	'label_titre_boussole' => '¿Mostrar el título de la brújula?',
	'label_titre_groupe' => '¿Mostrar el título del grupo?',
	'label_titre_site' => '¿Mostrar el título de los sitios?',
	'label_type_bulle' => 'Información mostrada en la burbuja de cada enlace',
	'label_type_description' => 'Descripción mostrada al lado del logo',
	'label_ul_class' => 'Clase de la etiqueta ul de la lista',
	'label_url' => 'URL',
	'label_url_serveur' => 'URL del servidor',
	'label_version' => 'Versión',

	// M
	'message_nok_alias_boussole_manquant' => 'L\'alias de la boussole n\'a pas été fournie au serveur « @serveur@ ».', # NEW
	'message_nok_aucune_boussole_hebergee' => 'Ninguna brújula está todavía alojada en el servidor «@serveur@».', # MODIF
	'message_nok_boussole_inconnue' => 'Ninguna brújula se corresponde al alias «@alias@»', # MODIF
	'message_nok_boussole_non_hebergee' => 'La brújula «@alias@» no está alojada en el servidor «@serveur@».', # MODIF
	'message_nok_cache_boussole_indisponible' => 'El archivo caché de la brújula «@alias@» no está disponible en el servidor «@serveur@».', # MODIF
	'message_nok_cache_liste_indisponible' => 'El archivo caché de la lista de brújulas no se está disponible en el servidor «@serveur@».', # MODIF
	'message_nok_declaration_boussole_xml' => 'La boussole manuelle « @boussole@ » ne peut pas être déclarée car son fichier XML est introuvable.', # NEW
	'message_nok_ecriture_bdd' => 'Error de escritura en la base de datos (tabla @table@)',
	'message_nok_nom_serveur_spip' => 'Le nom de serveur « spip » est réservé. Choisissez en un autre.', # NEW
	'message_nok_reponse_invalide' => 'La respuesta del servidor «@serveur@» está mal formada.', # MODIF
	'message_ok_boussole_actualisee' => 'La brújula «@fichier@» ha sido actualizada. ', # MODIF
	'message_ok_boussole_ajoutee' => 'La brújula «@fichier@» ha sido añadida. ', # MODIF
	'message_ok_boussole_manuelle_ajoutee' => 'La boussole manuelle « @boussole@ » a été déclarée au serveur et les caches ont été mis à jour.', # NEW
	'message_ok_serveur_ajoute' => 'El servidor «@serveur@» se ha añadido (@url@).', # MODIF
	'modele_boussole_liste_avec_logo' => 'Lista de enlaces con nombres, logos y descripción',
	'modele_boussole_liste_par_groupe' => 'Lista de enlaces textuales por grupo',
	'modele_boussole_liste_simple' => 'Lista simple de enlaces textuales',
	'modele_boussole_panorama' => 'Galería de los logos',
	'modele_boussole_panorama_sepia' => 'Galería de los logos con efecto sepia',

	// O
	'onglet_client' => 'Función Cliente',
	'onglet_configuration' => 'Configuration du plugin', # NEW
	'onglet_serveur' => 'Función Servidor',
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
	'titre_form_ajouter_serveur' => 'Añadir un servidor de brújulas', # MODIF
	'titre_form_boussole_manuelle' => 'Déclarer une boussole manuelle', # NEW
	'titre_form_configurer_serveur' => 'Configurer la fonction serveur', # NEW
	'titre_formulaire_configurer' => 'Configuración de la visualización de la brújula',
	'titre_liste_boussoles' => 'Lista de las brújulas disponibles a la visualización',
	'titre_liste_caches' => 'Lista de brújulas alojadas',
	'titre_liste_serveurs' => 'Lista de servidores disponibles', # MODIF
	'titre_page_boussole' => 'Gestión de las brújulas',
	'titre_page_configurer' => 'Configuration du plugin Boussole' # NEW
);

?>
