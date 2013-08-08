<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/langonet?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_corriger' => 'Obtener las correcciones',
	'bouton_generer' => 'Generar',
	'bouton_langonet' => 'LangOnet',
	'bouton_lister' => 'Listar',
	'bouton_rechercher' => 'Buscar',
	'bouton_verifier' => 'Verificar',
	'bulle_afficher_fichier_lang' => 'Mostrar el archivo de idioma generado el @date@',
	'bulle_afficher_fichier_log' => 'Mostrar el log del @date@',
	'bulle_corriger' => 'Descargar el archivo de idioma corregido',
	'bulle_telecharger_fichier_lang' => 'Descargar el archivo de idioma generado el @date@',
	'bulle_telecharger_fichier_log' => 'Descargar el log del @date@',

	// E
	'entete_log_avertissement_nonmais' => 'ADVERTENCIA: estos ítems no pertenecen al módulo',
	'entete_log_avertissement_peutetre_definition' => 'ADVERTENCIA: verificar que estos ítems están definidos',
	'entete_log_avertissement_peutetre_utilisation' => 'ADVERTENCIA: verificar que estos ítems están utilizados',
	'entete_log_date_creation' => 'Archivo generado el @log_date_jour@ a @log_date_heure@.',
	'entete_log_erreur_definition' => 'ERROR: ítems del módulo no definidos',
	'entete_log_erreur_definition_nonmais' => 'ERROR: ítems de otros módulos no definidos',
	'entete_log_erreur_fonction_l' => 'ERROR: Caso de uso de la función _L()',
	'entete_log_erreur_utilisation' => 'ERROR: ítems no utilizados',

	// I
	'info_arborescence_scannee' => 'Elija la carpeta de base cuya arborescencia sera escaneada',
	'info_bloc_langues_generees' => 'Hacer clic sobre uno de estos vínculos para descargar uno de los archivos de idioma generados.',
	'info_bloc_logs_definition' => 'Hacer clic sobre este vínculo para descargar el último archivo de logs de verificación de las definiciones que faltan en un archivo de idioma.',
	'info_bloc_logs_fonction_l' => 'Hacer clic sobre este vínculo para descargar el último archivo de logs de verificación de los usos de _L() en una arborescencia.',
	'info_bloc_logs_utilisation' => 'Haga clic sobre uno de estos vínculos para descargar el último archivo de verificación de las definiciones obsoletas de un archivo de idioma.',
	'info_chemin_langue' => 'Carpeta en la cual esta instalado el archvivo de idioma (ejemplo: <em>plugins/rainette/lang/</em>, o <em>ecrire/lang/</em>)',
	'info_fichier_liste' => 'Elija para cual archivo de idioma quiere listar los ítems, entre los presentes en el sitio.',
	'info_fichier_verifie' => 'Elija el archivo de idioma a verificar entre los presentes en el sitio.',
	'info_generer' => 'Esta opción le permite generar, a partir de un idioma fuente, el archivo de idioma de un módulo dado en el idioma elegido. Si el archivo del idioma elegido ya existe, su contenido esta reutilizado para construir el nuevo archivo.',
	'info_langue' => 'Abreviación del idioma (ejemplo: <em>fr</em>, <em>en</em>, <em>es</em>...)',
	'info_lister' => 'Esta opción le permite visualizar los ítems de una archivo de idioma ordenados por orden alfabético.',
	'info_mode' => 'Corresponde a la cadena que sera insertada a la creación de un nuevo ítem para el idioma elegido.',
	'info_module' => 'Corresponde al prefijo del archivo de idioma sin la abreviación del idioma (ejemplo: <em>rainette</em> para el plugin del mismo nombre, o <em>ecrire</em> para SPIP)',
	'info_pattern_item_cherche' => 'Ingrese una cadena que corresponda a todo o parte de una clave de ítem de idioma. La búsqueda es insensible a las mayúsculas y minúsculas.',
	'info_pattern_texte_cherche' => 'Ingrese una cadena que corresponda a toda o parte de una traducción francesa de ítem de idioma. La búsqueda es insensible a las mayúsculas y minúsculas.',
	'info_rechercher_item' => 'Esta opción le permite buscar ítems de idioma en todos los archivos de idioma presentes en el sitio. Por tema de velocidad, solo los archivos de idioma francesa son escaneados.',
	'info_rechercher_texte' => 'Esta opción le permite buscar ítems de idioma mediante su traducción francesa en los archivos de idioma de SPIP <em>ecrire_fr</em>, <em>public_fr</em> y <em>spip_fr</em>. El objetivo de esta búsqueda es de verificar si un texto ya existe en SPIP antes de crearlo.',
	'info_table' => 'Puede consultar aquí la lista alfabética de los ítems de idioma del archivo «<em>@langue@</em>» (@total@). Cada bloque muestra los ítems que comparten la misma letra inicial, la clave en negrita, y el texto al frente. Sobrevuela una inicial para hacer aparecer la lista correspondiente.',
	'info_verifier' => 'Esta opción permite, por una parte, de verificar los archivos de idioma de un módulo bajo dos ángulos complementarios. Así es posible de verificar si unos ítems de idioma utilizados en un grupo de archivos (un plugin, por ejemplo) no son definidos en el archivo de idioma idóneo, o que ciertos ítems de idioma definidos ya no son utilizados.<br />Por otra parte, es posible listar y corregir todas las utilizaciones de la función _L() en los archivos PHP de una arborescencia elegida.',

	// L
	'label_arborescence_scannee' => 'Arborescencia a escanear',
	'label_avertissement' => 'Advertencias',
	'label_chemin_langue' => 'Ubicación del archivo de idioma',
	'label_correspondance' => 'Tipo de correspondencia',
	'label_correspondance_commence' => 'Empieza por',
	'label_correspondance_contient' => 'Contiene',
	'label_correspondance_egal' => 'Igual a',
	'label_erreur' => 'Errores',
	'label_fichier_liste' => 'Archivo de idioma',
	'label_fichier_verifie' => 'Idioma a verificar',
	'label_langue_cible' => 'Idioma seleccionado',
	'label_langue_source' => 'Idioma fuente',
	'label_mode' => 'Modo de creación de nuevos ítems',
	'label_module' => 'Módulo',
	'label_pattern' => 'Cadena a buscar',
	'label_verification' => 'Tipo de verificación',
	'label_verification_definition' => 'Detección de definiciones vacías',
	'label_verification_fonction_l' => 'Detección de casos de utilización de la función _L()',
	'label_verification_utilisation' => 'Detección de definiciones obsoletas',
	'legende_resultats' => 'Resultados de la verificación',
	'legende_table' => 'Lista de ítems del archivo de idioma elegido',
	'legende_trouves' => 'Lista de ítems encontrados (@total@)',

	// M
	'message_nok_aucun_fichier_log' => 'Ningún archivo de log disponible para descargar',
	'message_nok_aucune_langue_generee' => 'Ningún archivo de idioma generado disponible para descargar',
	'message_nok_champ_obligatoire' => 'Este campo es obligatorio',
	'message_nok_ecriture_fichier' => '¡El archivo de idioma «<em>@langue@</em>» del módulo «<em>@module@</em>» no fue creado porque un error se produjo al momento de la escritura!',
	'message_nok_fichier_langue' => '¡La generación falló porque el archivo de idioma «<em>@langue@</em>» del módulo «<em>@module@</em>» no se encuentra en la carpeta «<em>@dossier@</em>»!',
	'message_nok_fichier_log' => '¡El archivo de log con los resultados de la verificación no pudo ser creado!',
	'message_nok_fichier_script' => '¡El archivo de script con los comandos de replazo de las funciones _L por _T no pudo ser creado!',
	'message_nok_item_trouve' => '¡Ningún ítem de idioma corresponde a la búsqueda!',
	'message_ok_definis_incertains_0' => 'Ningún ítem de idioma utilizado en un contexto complejo, como por ejemplo, _T(\'@module@:item_\'.$variable).',
	'message_ok_definis_incertains_1' => 'El ítem de idioma siguiente esta utilizado en un contexto complejo y podría ser no definido en el archivo de idioma «<em>@langue@</em>». Le invitamos a verificarlo:',
	'message_ok_definis_incertains_n' => 'Los @nberr@ ítems de idioma siguientes son utilizados en un contexto complejo y podrían ser definidos en el archivo de idioma «<em>@langue@</em>». Le invitamos a verificarlos uno por uno:',
	'message_ok_fichier_genere' => 'El archivo de idioma «<em>@langue@</em>» del módulo «<em>@module@</em>» a sido generado con éxito .<br />Puede recuperar el archivo «<em>@fichier@</em>».',
	'message_ok_fichier_log' => 'La verificación se hizo exitosamente. Puede consultar los resultados más abajo en el formulario..<br />El archivo «<em>@log_fichier@</em>» ha sido creado para guardar estos resultados.',
	'message_ok_fichier_log_script' => 'La verificación terminó exitosamente. Puede consultar los resultados más abajo en el formulario.<br />El archivo «<em>@log_fichier@</em>» ha sido creado para guardar estos resultados, de la misma manera que el archivo de comandos de remplazo _L por _T, «<em>@script@</em>».',
	'message_ok_fonction_l_0' => 'Ningún caso de utilización de la función _L() detectado en los archivos PHP de la carpeta «<em>@ou_fichier@</em>».',
	'message_ok_fonction_l_1' => 'Un solo caso de utilización de la función _L() detectado en los archivos PHP de la carpeta «<em>@ou_fichier@</em>»:',
	'message_ok_fonction_l_n' => '@nberr@ casos de utilización de la función _L() detectados en los archivos PHP de la carpeta «<em>@ou_fichier@</em>»:',
	'message_ok_item_trouve' => 'La búsqueda de la cadena @pattern@ se hizo exitosamente.',
	'message_ok_item_trouve_commence_1' => 'El ítem de idioma siguiente empieza por la cadena buscada:',
	'message_ok_item_trouve_commence_n' => 'Los @sous_total@ ítems siguientes empiezan por la cadena buscada:',
	'message_ok_item_trouve_contient_1' => 'El ítem de idioma siguiente contiene la cadena buscada:',
	'message_ok_item_trouve_contient_n' => 'Los @sous_total@ ítems siguientes contienen la cadena buscada:',
	'message_ok_item_trouve_egal_1' => 'El ítem de idioma siguiente corresponde exactamente a la cadena buscada:',
	'message_ok_item_trouve_egal_n' => 'Los @sous_total@ ítems siguientes corresponden exactamente a la cadena buscada:',
	'message_ok_non_definis_0' => 'Todos los ítems de idioma del módulo «<em>@module@</em>» utilizados en los archivos de la carpeta «<em>@ou_fichier@</em>» están bien definidos en el archivo de idioma  «<em>@langue@</em>».',
	'message_ok_non_definis_1' => 'El ítem de idioma siguiente del módulo «<em>@module@</em>» esta utilizado en unos archivos de la carpeta «<em>@ou_fichier@</em>» pero no esta definido en el archivo de idioma «<em>@langue@</em>»:',
	'message_ok_non_definis_n' => 'Los @nberr@ ítems de idioma siguientes del módulo «<em>@module@</em>» son utilizados en unos archivos de la carpeta «<em>@ou_fichier@</em>» pero no están definidos en el archivo de idioma «<em>@langue@</em>»:',
	'message_ok_non_utilises_0' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_0s' => 'Tous les items de langue définis  dans le fichier de langue «<em>@langue@</em>» sont bien utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>».', # NEW
	'message_ok_non_utilises_1' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_1s' => 'L\'item de langue ci-dessous est bien défini dans le fichier de langue «<em>@langue@</em>», mais n\'est pas utilisé dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_n' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers du répertoire «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_non_utilises_ns' => 'Les @nberr@ items de langue ci-dessous sont bien définis dans le fichier de langue «<em>@langue@</em>», mais ne sont pas utilisés dans les fichiers des répertoires «<em>@ou_fichier@</em>» :', # NEW
	'message_ok_nonmais_definis_0' => 'Los archivos de la carpeta «<em>@ou_fichier@</em>» no utilizan ningún ítem de idioma que sea correctamente definido en un otro módulo que «<em>@module@</em>».',
	'message_ok_nonmais_definis_1' => 'El ítem de idioma siguiente esta utilizado correctamente en los archivos de la carpeta «<em>@ou_fichier@</em>» pero esta definido en un otro módulo que «<em>@module@</em>». Le invitamos a verificarlo:',
	'message_ok_nonmais_definis_n' => 'Los @nberr@ ítems de idioma siguientes están utilizados correctamente en los archivos de la carpeta «<em>@ou_fichier@</em>» pero están definidos en un otro módulo que «<em>@module@</em>». Le invitamos a verificarlos uno por uno:',
	'message_ok_nonmaisnok_definis_0' => 'Los archivos de la carpeta «<em>@ou_fichier@</em>» no utilizan ningún ítem de idioma que sea incorrectamente definido en un otro módulo que «<em>@module@</em>».',
	'message_ok_nonmaisnok_definis_1' => 'El ítem de idioma siguiente esta utilizado en los archivos de la carpeta «<em>@ou_fichier@</em>» pero no como un ítem del módulo «<em>@module@</em>». No esta definido en su propio módulo, así que le invitamos a verificarlo:',
	'message_ok_nonmaisnok_definis_n' => 'Los @nberr@ ítems de idioma siguientes están utilizados en archivos de la carpeta «<em>@ou_fichier@</em>» pero no como ítems del módulo «<em>@module@</em>». No están definidos en su propio módulo, así que le invitamos a verificarlos uno por uno:',
	'message_ok_table_creee' => 'La table des items du fichier de langue @langue@ a été correctement créée.', # NEW
	'message_ok_utilises_incertains_0' => 'Aucun item de langue n\'est utilisé dans un contexte complexe (par exemple :  _T(\'@module@:item_\'.$variable)).', # NEW
	'message_ok_utilises_incertains_1' => 'L\'item de langue ci-dessous est peut-être utilisé dans un contexte complexe. Nous vous invitons à le vérifier :', # NEW
	'message_ok_utilises_incertains_n' => 'Les @nberr@ items de langue ci-dessous sont peut-être utilisés dans un contexte complexe. Nous vous invitons à les vérifier un par un :', # NEW

	// O
	'onglet_generer' => 'Générer une langue', # NEW
	'onglet_lister' => 'Afficher une langue', # NEW
	'onglet_rechercher' => 'Rechercher un item', # NEW
	'onglet_verifier' => 'Vérifier une langue', # NEW
	'option_aucun_dossier' => 'aucune arborescence sélectionnée', # NEW
	'option_aucun_fichier' => 'aucune langue sélectionnée', # NEW
	'option_mode_index' => 'Item de la langue source', # NEW
	'option_mode_new' => 'Balise &lt;NEW&gt; uniquement', # NEW
	'option_mode_new_index' => 'Item de la langue source précédé de &lt;NEW&gt;', # NEW
	'option_mode_new_valeur' => 'Chaîne dans la langue source précédée de &lt;NEW&gt;', # NEW
	'option_mode_pas_item' => 'Ne pas créer d\'item', # NEW
	'option_mode_valeur' => 'Chaîne dans la langue source', # NEW
	'option_mode_vide' => 'Une chaîne vide', # NEW

	// T
	'test' => 'TEST : Cet item de langue sert pour la recherche de raccourci et est égal à test.', # NEW
	'test_item_1_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_2_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.', # NEW
	'test_item_non_utilise_1' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().', # NEW
	'test_item_non_utilise_2' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().', # NEW
	'texte_item_defini_ou' => '<em>défini dans :</em>', # NEW
	'texte_item_mal_defini' => '<em>mais pas défini dans le bon module :</em>', # NEW
	'texte_item_non_defini' => '<em>mais défini nulle part !</em>', # NEW
	'texte_item_utilise_ou' => '<em>utilisé dans :</em>', # NEW
	'titre_bloc_langues_generees' => 'Fichiers de langue', # NEW
	'titre_bloc_logs_definition' => 'Définitions manquantes', # NEW
	'titre_bloc_logs_fonction_l' => 'Utilisations de _L()', # NEW
	'titre_bloc_logs_utilisation' => 'Définitions obsolètes', # NEW
	'titre_form_generer' => 'Génération des fichiers de langue', # NEW
	'titre_form_lister' => 'Affichage des fichiers de langue', # NEW
	'titre_form_rechercher_item' => 'Recherche de raccourcis dans les fichiers de langue', # NEW
	'titre_form_rechercher_texte' => 'Recherche de textes dans les fichiers de langue SPIP', # NEW
	'titre_form_verifier' => 'Vérification des fichiers de langue', # NEW
	'titre_page' => 'LangOnet', # NEW
	'titre_page_navigateur' => 'LangOnet', # NEW

	// Z
	'z_test' => 'TEST : Cet item de langue sert pour la recherche de raccourci et contient test.' # NEW
);

?>
