<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/noizetier?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'apercu' => 'Ver', # "Aperçu" lo he traducido como "ver", podría también traducirse como "visión de conjunto"

	// B
	'bloc_sans_noisette' => 'Este bloc no contiene noisette ', # "Bloc" en castellano sería "bloque", y "noisette" "nuez", pero prefiero dejar las palabras  en francés, pues son términos que considero es preferible dejarlos en la lengua de origen.

	// C
	'choisir_noisette' => 'Elija la noisette que usted quiere añadir :',
	'compositions_non_installe' => '<b>Plugin compositions :</b> este  plugin no está instalado en vuestra Web. No es necesario para el funcionamiento del Noizetier. No obstante, si está activado, usted podrá declarar composiciones directamente en el noizetier.',

	// D
	'description_bloc_contenu' => 'Contenido principal de cada página.',
	'description_bloc_extra' => 'Información extra contextual  para cada página.',
	'description_bloc_navigation' => 'Informaciones de navegación propias a cada página.',
	'description_bloctexte' => 'El título es opcional. Para el texto, puede utilizar los atajos tipográficos de SPIP. ',

	// E
	'editer_composition' => 'Modificar esta composición',
	'editer_composition_heritages' => 'Definir las herencias.',
	'editer_configurer_page' => 'Configurar  las noisettes de esta página', # No se en que estaba pensando, en alguno de mis comentarios había dicho que noisette es nuez y la verdad es que es avellana, ustedes perdonen por mi falta de concentración, en cuanto pueda lo corregiré.
	'editer_exporter_configuration' => 'Exportar la configuración',
	'editer_importer_configuration' => 'Importar una config.',
	'editer_noizetier_explication' => 'Selecione la página de la cual quiere configurar las noisettes ', # Ya lo dije noisette =avellana, pero lo dejo sin traducir. Si alguien piensa que es mejor traducirlo...
	'editer_noizetier_titre' => 'Gestionar las noisettes',
	'editer_nouvelle_page' => 'Crear una nueva página / composición',
	'erreur_aucune_noisette_selectionnee' => 'Usted debe seleccionar una noisette !', # Prefiero no traducir noisette que sería avellana , pues es un término técnico importante del plugin, y le da el nombre al plugin.
	'erreur_doit_choisir_noisette' => 'Usted debe elegir una noisette.',
	'erreur_mise_a_jour' => 'Un error se ha producido durante la actualización  de la base de datos. ',
	'explication_glisser_deposer' => 'Usted puede añadir una noisette o bien reordenarlas por simple deslizar-depositar',
	'explication_heritages_composition' => 'Usted puede definir aquí las composiciones que serán heredadas por los objetos de la rama.',
	'explication_noizetier_css' => 'Puede añadir  a la noisette otras  clases CSS suplementarias.',
	'explication_raccourcis_typo' => 'Puede utilizar los  atajos tipográficos de SPIP.',

	// F
	'formulaire_ajouter_noisette' => 'Añadir una  noisette', # No se si poner avellana o noisette....
	'formulaire_composition' => 'identificador  de composition',
	'formulaire_composition_explication' => 'Indique una  palabra-clave única (minúsculas, sin espacio, sin guión y sin acento)  de manera que permita   identificar esta composición.<br />Por ejemplo : <i>micompo</i>.',
	'formulaire_composition_mise_a_jour' => 'Composición actualizada ',
	'formulaire_configurer_bloc' => 'Configurar el bloc :', # También se puede traducir bloc por bloque, pero prefiero dejarlo así de momento....
	'formulaire_configurer_page' => 'Configurar la página :',
	'formulaire_deplacer_bas' => 'Desplazar hacia abajo',
	'formulaire_deplacer_haut' => 'Desplazar hacia arriba',
	'formulaire_description' => 'Descripción',
	'formulaire_description_explication' => 'Puede utilizar los atajos SPIP usuales, como la baliza <multi>.', # No se si debo poner en html la baliza multi :  &lt;multi&gt;  o bien utilizar los signos  mayor que y menor que....
	'formulaire_erreur_format_identifiant' => 'L’identifiant ne peut contenir que des minuscules sans accent, des chiffres et le caractère _ (underscore).', # El nombre de usuario solo puede contener letras minúsculas sin acento, números y el carácter _(underscore)
	'formulaire_icon' => 'Icône', # Icono
	'formulaire_icon_explication' => 'Puede poner el camino relativo hacia un icono (por ejemplo : <i>images/objet-liste-contenus.png</i>).',
	'formulaire_identifiant_deja_pris' => 'Este nombre de usuario ya está utilizado !',
	'formulaire_import_compos' => 'Importar las composiciones del noizetier',
	'formulaire_import_fusion' => 'Fusionar con la configuración actual.',
	'formulaire_import_remplacer' => 'Remplazar la configuración actual',
	'formulaire_liste_compos_config' => 'Este fichero de configuración define las composiciones del noizetier siguientes : ',
	'formulaire_liste_pages_config' => 'Este fichero de configuración define las noisettes sobre las siguientes páginas :',
	'formulaire_modifier_composition' => 'Modificar esta composición :',
	'formulaire_modifier_composition_heritages' => 'Modificar las herencias',
	'formulaire_modifier_noisette' => 'Modificar esta  noisette',
	'formulaire_modifier_page' => 'Modificar esta página',
	'formulaire_noisette_sans_parametre' => 'Esta  noisette no propone parámetro',
	'formulaire_nom' => 'Título',
	'formulaire_nom_explication' => 'Puede utilizar la baliza  <multi>.', # veo que en la versión original hay un error al transformar un el signo < en su entidad html
	'formulaire_nouvelle_composition' => 'Nueva composición',
	'formulaire_obligatoire' => 'Campos obligatorios',
	'formulaire_supprimer_noisette' => 'Suprimir esta  noisette', # Suprimir esta noisette
	'formulaire_supprimer_noisettes_page' => 'Suprimir las noisettes de esta página',
	'formulaire_supprimer_page' => 'Suprimir esta página',
	'formulaire_type' => 'Tipo de página',
	'formulaire_type_explication' => 'Indique  sobre  que objeto se aplica esta composición o bien si usted desea crear una página autónoma. ',
	'formulaire_type_import' => 'Tipo de importación',
	'formulaire_type_import_explication' => 'Puede usted fusionar el fichero de configuración con su  configuración actual (las noisettes de cada página serán añadidas a sus noisettes ya definidas) o bien puede remplazar su  configuración por esta otra.',

	// I
	'icone_introuvable' => 'Icono no encontrado !',
	'ieconfig_ne_pas_importer' => 'No importar',
	'ieconfig_noizetier_export_explication' => 'Exportará la configuración de las  noisettes y de las  composiciones du noiZetier.',
	'ieconfig_noizetier_export_option' => '¿ Incluir en la exportación ?',
	'ieconfig_non_installe' => '<b>Plugin Importador/Exportador de configuraciones :</b> este  plugin no está  installado  en vuestro sitio. No es necesario al funcionamiento del noizetier. No obstante, si está activado, usted podrá exportar e importar configuraciones de noisettes en el noizetier.',
	'ieconfig_probleme_import_config' => 'Se ha encontrado un problema  al importar la configuración del noizetier.',
	'info_composition' => 'COMPOSICION :',
	'info_page' => 'Esta página le permite gestionar el contenido a las zonas de acceso restringido de vuestro sitio.',
	'installation_tables' => 'Tablas del  plugin noiZetier instaladas.<br />',
	'item_titre_perso' => 'Título personalizado',

	// L
	'label_afficher_titre_noisette' => '¿ Mostrar un título   de noisette ?',
	'label_niveau_titre' => 'Nivel del título :',
	'label_noizetier_css' => 'Clases CSS : ',
	'label_texte' => 'Texto :',
	'label_texte_introductif' => 'Texto introductivo (opcional) :',
	'label_titre' => 'Título : ',
	'label_titre_noisette' => '¿Si se muestra un título, cual ?',
	'label_titre_noisette_perso' => 'Si título personalizado :',
	'liste_icones' => 'Lista de iconos',
	'liste_pages' => 'Liste de páginas',

	// M
	'masquer' => 'Ocultar',
	'mode_noisettes' => 'Editar las noisettes',
	'modif_en_cours' => 'Modificaciones en curso',
	'modifier_dans_prive' => 'Modificar en el espacio privado',

	// N
	'ne_pas_definir_d_heritage' => 'No definir la herencia',
	'noisette_numero' => 'noisette numero :',
	'noisettes_composition' => 'Noisettes específicas a la composición <i>@composition@</i> :', # lo que es código, no lo traduzco, lo dejo tal cual
	'noisettes_disponibles' => 'Noisettes disponibles',
	'noisettes_page' => 'Noisettes específicas a la página  <i>@type@</i> :',
	'noisettes_toutes_pages' => 'Noisettes comunes a todas las páginas :',
	'noizetier' => 'noiZetier', # Dejamos {noizetier} en vez de {avellano}, para mas claridad  en  la comprensión de la programación del plugin.
	'nom_bloc_contenu' => 'Contenido',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navegación', # Navegación
	'nom_bloctexte' => 'Bloque  de texto libre',
	'non' => 'No',
	'notice_enregistrer_rang' => 'Cliquear sobre guardar para hacer una copia de seguridad del ordén de noisettes.',

	// O
	'operation_annulee' => 'Operación anulada',
	'oui' => 'Si',

	// P
	'page' => 'Página',
	'page_autonome' => 'Página autónoma',
	'probleme_droits' => 'Usted no tiene los derechos necesarios para efectuar esta modificación.',

	// Q
	'quitter_mode_noisettes' => 'Dejar la edición de las noisettes ',

	// R
	'retour' => 'Volver',

	// S
	'suggestions' => 'Sugerencias',

	// W
	'warning_noisette_plus_disponible' => 'ATENCION : esta noisette ya no está disponible.',
	'warning_noisette_plus_disponible_details' => 'El squelette de esta noisette  (<i>@squelette@</i>) ya no está accesible. Puede que se trate de una noisette que necesite un plugin que usted haya desactivado o desinstalado.' # squelette lo dejo tal cual no lo traduzco
);

?>
