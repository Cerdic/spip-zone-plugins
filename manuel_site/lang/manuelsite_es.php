<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/manuelsite?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'configurer_explication' => 'Ce plugin installe une icône d’aide permettant d’afficher depuis n’importe quelle page de l’espace privé le manuel de rédaction du site. Ce manuel est @texte@ Il a pour but d’expliquer aux rédacteurs l’architecture du site, dans quelle rubrique ranger quoi, comment encoder et installer une vidéo... Bref tout ce que vous voulez et qui est spécifique à votre site.', # NEW
	'configurer_explication_l_article' => '<a href="@url@" title="Manual de redacción">el artículo #@idart@</a> de su sitio.',
	'configurer_explication_un_article' => 'un artículo del sitio. ',
	'configurer_titre' => 'Configurar el manual de redacción del sitio',

	// E
	'erreur_article' => 'El artículo definido en la configuración del plugin no se encuentra: #@idart@',
	'erreur_article_publie' => 'El artículo del manual definido en la configuración del plugin no está publicado en línea: <a href="@url@">#@idart@</a>',
	'erreur_pas_darticle' => 'El artículo del manual no está definido en la configuración del plugin',
	'explication_afficher_bord_gauche' => 'Mostrar el icono del manual arriba a la izquierda (si no el manual se mostrará en columna)',
	'explication_background_color' => 'Introduzca el color de fondo de la zona de visualización del manual',
	'explication_cacher_public' => 'Cacher cet article dans l’espace public, flux rss compris', # NEW
	'explication_email' => 'Correo electrónico de contacto para los redactores',
	'explication_faq' => 'Encontrará aquí los códigos de los bloques genéricos que se utilizan para redactar su manual. El texto correspondiente a cada código se muestra (sin formato) a la vista. Basta con copiar/pegar el código deseado en el área de texto de su artículo.<br />Para no mostrar la cuestión, añadir <i>|q=non</i>.<br />Para agregar la configuración, añadir <i>|params=p1:v1;p2:v2</i>.',
	'explication_id_article' => 'Introduzca el número del artículo que contiene el manual',
	'explication_intro' => 'Texte d’introduction au manuel (sera placé avant le chapo)', # NEW
	'explication_largeur' => 'Introduzca la anchura de la zona de visualización del manual',

	// F
	'fermer_le_manuel' => 'Cerrar el manual',

	// H
	'help' => 'Ayuda:',

	// I
	'intro' => 'Ce document a pour but d’aider les rédacteurs à l’utilisation du site. Il vient en complément du document intitulé « [Cours SPIP pour rédacteurs->@url@] » qui est une aide globale à l’utilisation de SPIP. Vous y trouverez une description de l’architecture du site, de l’aide technique sur des points particuliers...', # NEW

	// L
	'label_afficher_bord_gauche' => 'Visualización',
	'label_background_color' => 'Color de fondo',
	'label_cacher_public' => 'Ocultar',
	'label_email' => 'Correo electrónico',
	'label_id_article' => 'Nº del artículo',
	'label_intro' => 'Introducción',
	'label_largeur' => 'Anchura',
	'legende_apparence' => 'Apariencia',
	'legende_contenu' => 'Contenido',

	// T
	'titre_faq' => 'FAQ del Manual de redacción',
	'titre_manuel' => 'Manual de redacción del sitio',
	'titre_menu' => 'Manual de redacción del sitio'
);

?>
