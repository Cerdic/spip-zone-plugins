<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_sauvegarder' => 'Guardar la base',

	// C
	'colonne_auteur' => 'Creada por',
	'colonne_nom' => 'Nombre',

	// E
	'erreur_impossible_creer_verifier' => 'No se puede crear el archivo @fichier@, verifique los derechos de escritura del directorio @rep_bases@.',
	'erreur_impossible_liste_tables' => 'No se pueden listar las tablas de la base.',
	'erreur_probleme_donnees_corruption' => 'Problema con los datos de @table@, ¡posible corrupción!',
	'erreur_repertoire_inaccessible' => 'El directorio @rep@ es inaccesible en escritura.',
	'erreur_repertoire_perso_inaccessible' => 'El directorio @rep@ configurado no es accesible: utilización del directorio de copias de seguridad de SPIP en su lugar.',

	// H
	'help_cfg_generale' => 'Estos parámetros de configuración se aplican a todas las copias de seguridad manuales o automáticas.',
	'help_contenu' => 'Elija los parámetros de contenido de su archivo de copia de seguridad.',
	'help_contenu_auto' => 'Elegir el contenido de las copias de seguridad automáticas.',
	'help_frequence' => 'Elegir la frecuencia en días de las copias de seguridad automáticas.',
	'help_liste_tables' => 'Por defecto, todas las tablas SPIP se exportan a excepción de las tablas @noexport@. Si desea elegir precisamente las tablas de las que hacer una copia de seguridad (así como las tablas no SPIP) abra la lista desmarcando la casilla.',
	'help_mail_max_size' => 'Introducir el tamaño máximo en Mo del archivo de copia de seguridad más allá del cual el correo electrónico no será enviado (valor a verificar de su proveedor de correo).',
	'help_max_zip' => 'El archivo de copia de seguridad se comprime en zip automáticamente si su tamaño es inferior a un umbral. Introducir este umbral en Mo. (Dicho umbral es necesario para no dejar caer al servidor por la confección de un zip demasiado grande)',
	'help_notif_active' => 'Si desea estar prevenido de los tratamientos automáticos, active las notificaciones. Para la copia de seguridad automática recibirá el archivo generado por correo electrónico si éste no es demasiado voluminoso y el plugin Facteur está activado.',
	'help_notif_mail' => 'Introducir las direcciones separándolas por comas",". Estas direcciones se añaden a la del webmaster del sitio.',
	'help_obsolete' => 'Introducir la duración en días de conservación de las copias de seguridad',
	'help_prefixe' => 'Introducir el prefijo junto al nombre de cada archivo de copia de seguridad',
	'help_repertoire' => 'Para utilizar un directorio de almacenaje diferente al de las copias de seguridad SPIP, indique la ruta desde la raiz del sitio (con / al final)',
	'help_restauration' => '<strong>¡¡¡Atención!!!</strong> estas copias de seguridad no están <strong>en formato de las de SPIP</strong> y no pueden utilizarse con la herramienta de restauración de la base de SPIP.<br /><br />
							Para toda restauración es necesario utilizar la interfaz <strong>phpmyadmin</strong> de su servidor de base de datos.<br /><br />
							Estas copias de seguridad contienen comandos que permiten <strong>borrar</strong> las tablas de su base SPIP y <strong>sustituirlas</strong> por los datos archivados. ¡Los datos <strong>más recientes</strong> que los de la copia de seguridad <strong>SE PERDERÁN</strong>!',
	'help_sauvegarde_1' => 'Esta opción le permite obtener una copia de seguridad de la estructura y del contenido de la base en un fichero en formato MySQL que se almacenará en el directorio tmp/dump/. El archivo se denomina <em>@prefixe@_aaaammjj_hhmmss.</em>. El prefijo de las tablas se conserva.',
	'help_sauvegarde_2' => 'La copia de seguridad automática está activada (frecuencia en días: @frequence@).',

	// I
	'info_sql_auteur' => 'Autor:',
	'info_sql_base' => 'Base: ',
	'info_sql_compatible_phpmyadmin' => 'Archivo SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Fecha: ',
	'info_sql_debut_fichier' => 'Inicio del archivo',
	'info_sql_donnees_table' => 'Datos de la tabla @table@',
	'info_sql_fichier_genere' => 'Este archivo ha sido generado por el
plugin Saveauto',
	'info_sql_fin_fichier' => 'Final del archivo',
	'info_sql_ipclient' => 'IP Cliente: ',
	'info_sql_mysqlversion' => 'Versión MySQL: ',
	'info_sql_os' => 'OS Servidor:',
	'info_sql_phpversion' => 'Versión PHP: ',
	'info_sql_plugins_utilises' => '@nb@ plugins utilizados:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versión de SPIP: ',
	'info_sql_structure_table' => 'Estructura de la tabla @table@',

	// L
	'label_donnees' => 'Datos de las tablas',
	'label_frequence' => 'Frecuencia de las copias de seguridad',
	'label_mail_max_size' => 'Límite de envío del correo electrónico',
	'label_max_zip' => 'Límite de los zips',
	'label_nettoyage_journalier' => 'Activar la limpieza diaria de los archivos',
	'label_notif_active' => 'Activar las notificaciones',
	'label_notif_mail' => 'Direcciones de correo electrónico para notificar',
	'label_obsolete_jours' => 'Conservación de las copias de seguridad',
	'label_prefixe_sauvegardes' => 'Prefijo',
	'label_repertoire_sauvegardes' => 'Directorio',
	'label_sauvegarde_reguliere' => 'Activer la copia de seguridad regular',
	'label_structure' => 'Estructura de las tablas',
	'label_tables_non_spip' => 'Tablas no SPIP',
	'label_toutes_tables' => 'Crear una copia de todas las tablas de SPIP',
	'legend_cfg_generale' => 'Parámetros generales de las copias de seguridad',
	'legend_cfg_notification' => 'Notificaciones',
	'legend_cfg_sauvegarde_reguliere' => 'Tratamientos automáticos',

	// M
	'message_aucune_sauvegarde' => 'Ninguna copia de seguridad está disponible para descarga',
	'message_cleaner_sujet' => 'Limpieza de las copias de seguridad',
	'message_notif_cleaner_intro' => 'La supresión automática de las copias de seguridad obsoletas (cuya fecha es anterior a @duree@ días) se ha efectuado correctamente. Los siguientes archivos han sido eliminados:',
	'message_notif_sauver_intro' => 'La copia de seguridad de la base @base@ ha sido efectuada correctamente por el autor @auteur@.',
	'message_sauvegarde_nok' => 'Error durante la copia de seguridad de la base.',
	'message_sauvegarde_ok' => 'La copia de seguridad de la base se ha realizado correctamente.',
	'message_sauver_sujet' => 'Copia de seguridad de la base @base@',
	'message_telechargement_nok' => 'Error durante la descarga.',

	// T
	'titre_boite_historique' => 'Copias de seguridad MySQL disponibles para descargar en @dossier@',
	'titre_boite_sauver' => 'Crear una copia de seguridad MySQL',
	'titre_page_configurer' => 'Configuración del plugin Sauvegarde automática',
	'titre_page_saveauto' => 'Crear una copia de seguridad de la base en formato MySQL',
	'titre_saveauto' => 'Copia de seguridad automática'
);

?>
