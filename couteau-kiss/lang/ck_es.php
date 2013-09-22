<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/174?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'La caché no puede tener un tamaño inferior a 10Mo',
	'erreur_dossier_squelette_invalide' => 'El dosier esqueleto no puede ser una ruta absoluta ni contener referencias <tt>../</tt>',
	'explication_dossier_squelettes' => 'Puede indicar varios repertorios separados por ’:’, que se tomarán en orden. El repertorio titulado "<tt>squelettes</tt>" es siempre el último, en caso de que exista.',
	'explication_image_seuil_document' => 'Las imágenes descargadas pueden pasarse automáticamente a modo documento, más allá de una anchura determinada',
	'explication_introduction_suite' => 'Los siguientes puntos son añadidos por la etiqueta <tt>#INTRODUCTION</tt> cuando se corta un texto. Por defecto <tt> (...)</tt>',

	// L
	'label_cache_duree' => 'Duración del caché(s)',
	'label_cache_duree_recherche' => 'Duración del caché de la búsqueda(s)',
	'label_cache_strategie' => 'Estrategia del caché',
	'label_cache_strategie_jamais' => 'Ningún caché (esta opción se cancelará al cabo de 24 horas)',
	'label_cache_strategie_normale' => 'Caché de duración limitada',
	'label_cache_strategie_permanent' => 'Caché de duración ilimitada',
	'label_cache_taille' => 'Tamaño del caché (Mo)',
	'label_compacte_head_ecrire' => 'Comprimir siempre CSS y javascript',
	'label_derniere_modif_invalide' => 'Actualizar el caché con cada nueva publicación',
	'label_docs_seuils' => 'Limitar el tamaño de los documentos durante la descarga',
	'label_dossier_squelettes' => 'Dosier <tt>esqueletos</tt>',
	'label_forcer_lang' => 'Forzar el idioma de la url o del visitante (<tt>$forcer_lang</tt>)',
	'label_image_seuil_document' => 'Amplitud de las imágenes modo documento',
	'label_imgs_seuils' => 'Limitar el tamaño de las imágenes durante la descarga',
	'label_inhiber_javascript_ecrire' => 'Desactivar javascript en los artículos',
	'label_introduction_suite' => 'Puntos suspensivos',
	'label_logo_seuils' => 'Limitar el tamaños de los logos durante la descarga',
	'label_longueur_login_mini' => 'Longitud mínima de los inicio de sesión',
	'label_max_height' => 'Altura máxima (pixel)',
	'label_max_size' => 'Carga máxima (ko)',
	'label_max_width' => 'Anchura máxima (pixel)',
	'label_nb_objets_tranches' => 'Número de objetos en las listas',
	'label_no_autobr' => 'Desactivar la inclusión de los alineados (retorno de línea simples) en el texto',
	'label_no_set_html_base' => 'Ningún añadido automático de <tt>&lt;base href="..."&gt;</tt>',
	'label_options_ecrire_perfo' => 'Rendimiento',
	'label_options_ecrire_secu' => 'Seguridad',
	'label_options_skel' => 'Cálculo de las páginas',
	'label_options_typo' => 'Tratamiento de textos',
	'label_supprimer_numero' => 'Eliminar automáticamente los número de los títulos',
	'label_toujours_paragrapher' => 'Encapsular todos los párrafos en un <tt><p></tt> (también los textos constituidos por un solo párrafo)',
	'legend_cache_controle' => 'Control del caché',
	'legend_espace_prive' => 'Espacio privado',
	'legend_image_documents' => 'Imágenes y documentos',
	'legend_site_public' => 'Sitio público',

	// M
	'message_ok' => 'Su configuración ha sido tenido en cuenta y registrada en el fichero <tt>@file@</tt>. Ahora es aplicada.',

	// T
	'texte_boite_info' => 'Esta página le permite configurar fácilmente la configuración oculta de SPIP.

Si fuerza alguna configuración en su archivo <tt>config/mes_options.php</tt>, este formulario no tendrá efecto sobre ellos.

Cuando haya terminado la configuración de su sitio, podrá, si lo desea, copiar-pegar el contenido del archivo <tt>tmp/ck_options.php</tt> en <tt>config/mes_options.php</tt> antes de desinstalar este plugin que no será más útil.',
	'titre_page_couteau' => 'Cuchillo KISS'
);

?>
