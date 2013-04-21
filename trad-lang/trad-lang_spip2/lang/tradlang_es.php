<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/tradlang?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucunmodule' => 'Ningún módulo.',
	'auteur_revision' => '@nb@ modification de traduction.', # NEW
	'auteur_revision_specifique' => '@nb@ modification de traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'auteur_revisions' => '@nb@ modifications de traductions.', # NEW
	'auteur_revisions_langue' => 'La langue de ses participations :', # NEW
	'auteur_revisions_langues' => 'Les @nb@ langues de ses participations :', # NEW
	'auteur_revisions_specifique' => '@nb@ modifications de traductions en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW

	// B
	'bouton_activer_lang' => 'Activar el idioma "@lang@" para este módulo',
	'bouton_exporter_fichier' => 'Exportar el archivo',
	'bouton_exporter_fichier_langue' => 'Exportar el archivo de idioma en "@lang@"',
	'bouton_exporter_fichier_langue_original' => 'Exportar el archivo de idioma original ("@lang_mere@")',
	'bouton_exporter_fichier_po' => 'Exportar el archivo en .po',
	'bouton_exporter_fichier_zip' => 'Exportar los archivos en zip',
	'bouton_precedent' => 'Paso anterior',
	'bouton_suivant' => 'Paso siguiente',
	'bouton_supprimer_langue_module' => 'Eliminar este idioma del módulo',
	'bouton_supprimer_module' => 'Eliminar este módulo',
	'bouton_traduire' => 'Traducir',
	'bouton_upload_langue_module' => 'Enviar un archivo de idioma',
	'bouton_vos_favoris_non' => 'Sus módulos no favoritos',
	'bouton_vos_favoris_oui' => 'Sus módulos favoritos',
	'bouton_vos_favoris_tous' => 'Todos los módulos',

	// C
	'cfg_form_tradlang_autorisations' => 'Las autorizaciones',
	'cfg_inf_type_autorisation' => 'Si hace la selección por estatus o por autor, se le pedirá aquí abajo su selección de estatus o de autor.',
	'cfg_lbl_autorisation_auteurs' => 'Autorizar por lista de autores',
	'cfg_lbl_autorisation_statuts' => 'Autorizar por estatus de autores',
	'cfg_lbl_autorisation_webmestre' => 'Autorizar a los webmasters solamente',
	'cfg_lbl_liste_auteurs' => 'Autores del sitio',
	'cfg_lbl_statuts_auteurs' => 'Estatus posibles',
	'cfg_lbl_type_autorisation' => 'Método de autorización',
	'cfg_legend_autorisation_configurer' => 'Administrar plugins',
	'cfg_legende_autorisation_modifier' => 'Modificar las traducciones',
	'cfg_legende_autorisation_voir' => 'Ver la interfaz de traducción',
	'codelangue' => 'Código de idioma',
	'crayon_changer_statut' => '¡Atención! Ha modificado el contenido sin modificar el estatus',
	'crayon_changer_statuts' => '¡Atención! Ha modificado el contenido de uno o más espacios sin modiicar el estatus.',

	// E
	'entrerlangue' => 'Añadir un código idioma',
	'erreur_aucun_item_langue_mere' => 'El idoma de origen "@lang_mere@" no contiene ningún ítem de idioma.',
	'erreur_aucun_module' => 'No hay módulos disponibles en la base.',
	'erreur_aucun_tradlang_a_editer' => 'Ningún contenido de idioma está considerado como no traducido',
	'erreur_autorisation_modifier_modules' => 'Usted no está autorizado para traducir los módulos de idioma.',
	'erreur_autoriser_profil' => 'Usted no está autorizado para modificar este perfil.',
	'erreur_choisir_lang_cible' => 'Seleccione el idioma al que desea traducir.',
	'erreur_choisir_lang_orig' => 'Seleccione el idioma de origen que servirá de base para la traducción',
	'erreur_choisir_module' => 'Seleccione el módulo que desea traducir.',
	'erreur_code_langue_existant' => 'Esta opción de idioma ya existe para este módulo',
	'erreur_code_langue_invalide' => 'Este código de idioma está invalidado',
	'erreur_langue_activer_impossible' => 'El código de idioma "@lang@" ya no existe.',
	'erreur_langues_autorisees_insuffisantes' => 'Usted debe seleccionar al menos dos idiomas',
	'erreur_langues_differentes' => 'Seleccione un idioma para la traducción diferente al idioma original',
	'erreur_modif_tradlang_session' => 'Usted no puede modificar este ítem de idioma.',
	'erreur_modif_tradlang_session_identifier' => 'Identifíquese por favor.',
	'erreur_module_inconnu' => 'Este módulo no está disponible',
	'erreur_pas_langue_cible' => 'Seleccione el idioma de destino',
	'erreur_repertoire_local_inexistant' => 'Atención: el repertorio para la copia de seguridad local "squelettes/lang" no existe',
	'erreur_statut_js' => 'El idioma del contenido se ha modificado pero no su estatus',
	'erreur_upload_aucune_modif' => 'No hay ninguna modificación en su archivo',
	'erreur_upload_choisir_une' => 'Usted debe al menos validar una modicación',
	'erreur_upload_fichier_php' => 'Su archivo "@fichier@" no se corresponde con el archivo esperado "@fichier_attendu@".',
	'erreur_variable_manquante' => 'La siguiente parte del contenido no ha de ser modificada:',
	'erreur_variable_manquante_js' => 'Uno o más elementos obligatorios han sido modificados',
	'erreur_variable_manquantes' => 'Las @nb@ partes siguientes del contenido no han de ser modificadas',
	'explication_comm' => 'El comentario es una información añadida en el archivo de idioma con el fin de explicitar por ejemplo una elección de traducción particular',
	'explication_langue_cible' => 'El idioma en el que desea traducir',
	'explication_langue_origine' => 'El idioma desde el que usted traduce (solamente los que se encuentran al 100% están disponibles)',
	'explication_langues_autorisees' => 'Los usuarios no podrán crear nuevas traducciones salvo en los idiomas seleccionados.',
	'explication_limiter_langues_bilan' => 'Por defecto, @nb@ idiomas serán mostrados si los usuarios no han seleccionado idiomas preferentes en su perfil.',
	'explication_limiter_langues_bilan_nb' => 'Cuántos idiomas serán mostrados por defecto (serán seleccionados los idiomas mayormente traducidos)',
	'explication_sauvegarde_locale' => 'Guarda los archivos en el esqueleto del sitio',
	'explication_sauvegarde_post_edition' => 'Guarda los archivos temporales de cada modificación de cadena de idioma',

	// F
	'favoris_ses_modules' => 'Sus módulos favoritos',
	'favoris_vos_modules' => 'Sus módulos favoritos',

	// I
	'icone_modifier_tradlang' => 'Modificar esta cadena de idioma',
	'icone_modifier_tradlang_module' => 'Modificar este módulo de idioma',
	'importer_module' => 'Importación de nuevo módulo de idioma',
	'importermodule' => 'Importar un módulo',
	'info_1_tradlang' => '@nb@ cadena de idioma',
	'info_1_tradlang_module' => '1 módulo de idioma',
	'info_aucun_participant_lang' => 'Aucun auteur du site n\'a encore traduit en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_aucun_tradlang_module' => 'Ningún módulo de idioma',
	'info_auteur_sans_favori' => 'Cet auteur n\'a aucun module en favori.', # NEW
	'info_chaine_jamais_modifiee' => 'Esta cadena nunca ha sido modificada.',
	'info_chaine_originale' => 'Esta cadena es la cadena original. ',
	'info_choisir_langue' => 'Dans une langue spécifique', # NEW
	'info_contributeurs' => 'Colaboradores',
	'info_filtrer_status' => 'Filtrar por estatus:',
	'info_langue_mere' => '(idioma de origen)',
	'info_langues_non_preferees' => 'Otros idiomas:',
	'info_langues_preferees' => 'Idioma(s) preferente(s):',
	'info_module_nb_items_langue_mere' => 'La langue mère du module est <abbr title="@lang_mere@">@lang_mere_longue@</abbr> et comporte @nb@ items de langue.', # NEW
	'info_module_traduction' => '@total@ @statut@ (@percent@%)',
	'info_module_traduit_langues' => 'Este módulo está traducido o parcialmente traducido en @nb@ idiomas.',
	'info_module_traduit_pc' => 'Módulo traducido un @pc@%',
	'info_module_traduit_pc_lang' => 'Módulo "@module@" traducido un @pc@% en @lang@ (@langue_longue@)',
	'info_modules_priorite_traduits_pc' => 'Los módulos proritarios "@priorite@" son traducidos @pc@% en @lang@',
	'info_nb_items_module' => '@nb@ items en el módulo "@module@"',
	'info_nb_items_module_modif' => '@nb@ items del módulo "@module@" están modificadas a la espera de verificación en @lang@ (@langue_longue@)"',
	'info_nb_items_module_modif_aucun' => 'Ningún ítem del módulo "@module@" está modificado ni a la espera de verificación en @lang@ (@langue_longue@)',
	'info_nb_items_module_modif_un' => 'Un ítem del módulo "@module@" está modificado y a la espera de verficación en @lang@ (@langue_longue@)"',
	'info_nb_items_module_new' => '@nb@ items del módulo "@module@" están pendientes de traducción en @lang@ (@langue_longue@)"',
	'info_nb_items_module_new_aucun' => 'Ningún ítem del módulo "@module@" está pendiente de traducción en @lang@ (@langue_longue@)',
	'info_nb_items_module_new_un' => 'Un ítem del módulo "@module@" está pendiente de traducción en @lang@ (@langue_longue@)"',
	'info_nb_items_module_ok' => '@nb@ items del módulo "@module@" están traducidos en @lang@ (@langue_longue@)"',
	'info_nb_items_module_ok_aucun' => 'Ningún ítem del módulo "@module@" está traducido en @lang@ (@langue_longue@)',
	'info_nb_items_module_ok_un' => 'Un ítem del módulo "@module@" está traducido en @lang@ (@langue_longue@)"',
	'info_nb_items_priorite' => 'Los módulos prioritarios "@priorite@" tienen @nb@ items',
	'info_nb_items_priorite_modif' => '@pc@% de los items prioritarios "@priorite@" están modificados y a la espera de verificación en @lang@ (@langue_longue@)',
	'info_nb_items_priorite_new' => '@pc@% de los items prioritarios "@priorite@" son nuevos en @lang@ (@langue_longue@)',
	'info_nb_items_priorite_ok' => 'Los módulos prioritarios "@priorite@" están traducidos un @pc@% en @lang@ (@langue_longue@)',
	'info_nb_modules_favoris' => '@nb@ modules favoris.', # NEW
	'info_nb_participant' => '@nb@ auteur inscrit sur ce site a participé au moins une fois à la traduction.', # NEW
	'info_nb_participant_lang' => '@nb@ auteur inscrit sur ce site a participé au moins une fois à la traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_nb_participants' => '@nb@ auteurs inscrits sur ce site ont participé au moins une fois à la traduction.', # NEW
	'info_nb_participants_lang' => '@nb@ auteurs inscrits sur ce site ont participé au moins une fois à la traduction en <abbr title="@lang@">@langue_longue@</abbr>.', # NEW
	'info_nb_tradlang' => '@nb@ cadenas de idioma',
	'info_nb_tradlang_module' => '@nb@ módulos de idioma',
	'info_percent_chaines' => '@traduites@ / @total@ cadenas traducidas en "[@langue@] @langue_longue@"',
	'info_revisions_stats' => 'Revisiones',
	'info_status_ok' => 'OK',
	'info_statut' => 'Statut', # NEW
	'info_str' => 'Texto de la cadena de idioma',
	'info_textarea_readonly' => 'Este campo de texto es sólo de lectura',
	'info_tradlangs_sans_version' => '@nb@ cadenas de idioma no han creado una primera revisión (estas primeras revisiones son creadas por CRON).',
	'info_traducteur' => 'Traducteur(s)', # NEW
	'info_traduire_module_lang' => 'Traducir el módulo "@module@" en @langue_longue@ (@lang@)',
	'infos_trad_module' => 'Informaciones acerca de las traducciones',
	'item_creer_langue_cible' => 'Crear un nuevo idioma de destino',
	'item_langue_cible' => 'El idioma de destino:',
	'item_langue_origine' => 'El idioma de origen:',
	'item_manquant' => 'Falta un ítem en este idioma (en relación al idioma de origen)',
	'items_en_trop' => 'Hay @nb@ items de más en este idioma (en relación con el idioma de origen)',
	'items_manquants' => 'Hay @nb@ items de menos en este idioma (en relación al idioma de origen)',
	'items_modif' => 'Items modificados:',
	'items_new' => 'Nuevos items:',
	'items_total_nb' => 'Número total de items:',

	// J
	'job_creation_revisions_modules' => 'Creación de las revisiones de origen del módulo "@module@"',

	// L
	'label_fichier_langue' => 'Archivo de idioma para cargar',
	'label_id_tradlang' => 'Identificador de la cadena',
	'label_idmodule' => 'ID del módulo',
	'label_lang' => 'Idioma',
	'label_langue_mere' => 'Idioma original',
	'label_langues_autorisees' => 'No autorizar salvo ciertos idiomas',
	'label_langues_preferees_auteur' => 'Su(s) idioma(s) preferente(s)',
	'label_langues_preferees_autre' => 'Su(s) idiomas preferentes',
	'label_limiter_langues_bilan' => 'Limitar el número de idiomas visibles en el balance',
	'label_limiter_langues_bilan_nb' => 'Número de idiomas',
	'label_nommodule' => 'Nombre del módulo',
	'label_priorite' => 'Prioridad',
	'label_proposition_google_translate' => 'Proposición de Google Translate',
	'label_recherche_module' => 'En el módulo:',
	'label_recherche_status' => 'Con el estatus: ',
	'label_repertoire_module_langue' => 'Repertorio del módulo',
	'label_sauvegarde_locale' => 'Permite guardar localmente los archivos',
	'label_sauvegarde_post_edition' => 'Guardar los archivos de cada modificación',
	'label_synchro_base_fichier' => 'Sincronizar la base y los archivos',
	'label_texte' => 'Descripción del módulo',
	'label_tradlang_comm' => 'Comentario',
	'label_tradlang_status' => 'Estatus de la traducción ',
	'label_tradlang_str' => 'Cadena traducida (@lang@)',
	'label_update_langues_cible_mere' => 'Actualizar este idioma en la base de datos',
	'label_valeur_fichier' => 'En su archivo',
	'label_valeur_fichier_valider' => 'Validar la modificación de su archivo',
	'label_valeur_id' => 'Código de idioma:',
	'label_valeur_originale' => 'En la base de datos',
	'label_version_originale' => 'Cadena original (@lang@)',
	'label_version_originale_choisie' => 'En el idioma elegido (@lang@)',
	'label_version_originale_comm' => 'Comentario de la versión original (@lang@)',
	'label_version_selectionnee' => 'Cadena en el idioma seleccionado (@lang@)',
	'label_version_selectionnee_comm' => 'Comentario en el idioma seleccionado (@lang@)',
	'languesdispo' => 'Idiomas disponibles',
	'legend_conf_bilan' => 'Ver el balance',
	'lien_accueil_interface' => 'Inicio de la interfaz de traducción',
	'lien_aide_recherche' => 'Ayuda en la búsqueda',
	'lien_aucun_status' => 'Ninguno',
	'lien_bilan' => 'Balance de traducciones en curso.',
	'lien_check_all' => 'Marcar todo',
	'lien_check_none' => 'Desmarcar todo',
	'lien_code_langue' => 'Código de idioma no válido. El código de idioma ha de tener al menos dos letras (norma ISO-631).',
	'lien_confirm_export' => 'Confirmar la exportación del archivo en curso (es decir, reemplazar @fichier@)',
	'lien_editer_chaine' => 'Modificar',
	'lien_editer_tous' => 'Editar todas las cadenas no traducidas',
	'lien_export' => 'Exportar automáticamente el archivo actual.',
	'lien_page_depart' => '¿Volver a la página de inicio?',
	'lien_profil_auteur' => 'Su perfil',
	'lien_profil_autre' => 'Su perfil',
	'lien_proportion' => 'Proporción de cadenas mostradas',
	'lien_recharger_page' => 'Recargar la página.',
	'lien_recherche_avancee' => 'Búsqueda avanzada',
	'lien_retour' => 'Volver',
	'lien_retour_module' => 'Volver al módulo "@module@"',
	'lien_retour_page_auteur' => 'Volver a su página',
	'lien_retour_page_auteur_autre' => 'Volver a su página',
	'lien_revenir_traduction' => 'Volver a la página de traducción',
	'lien_sauvegarder' => 'Guardar/Restaurar el archivo actual.',
	'lien_telecharger' => '[Descargar]',
	'lien_traduction_module' => 'Módulo',
	'lien_traduction_vers' => ' a',
	'lien_traduire_suivant_str_module' => 'Traducir la siguiente cadena no traducida del módulo "@module@"',
	'lien_trier_langue_non' => 'Mostrar el balance global',
	'lien_utiliser_google_translate' => 'Utilizar esta versión',
	'lien_voir_bilan_lang' => 'Ver el balance del idioma @langue_longue@ (@lang@)',
	'lien_voir_bilan_module' => 'Ver el balance del módulo @nom_mod@ - @module@',
	'lien_voir_toute_chaines_module' => 'Ver todas las cadenas del módulo.',

	// M
	'menu_info_interface' => 'Mostrar un enlace a la interfaz de traducción',
	'menu_titre_interface' => 'Interfaz de traducción',
	'message_afficher_vos_modules' => 'Mostrar los módulos:',
	'message_aucun_resultat_chaine' => 'Ningún resultado se corresponde con sus criterios en las cadenas de idioma.',
	'message_aucun_resultat_statut' => 'Ninguna cadena se corresponde con el estatus solicitado.',
	'message_aucune_nouvelle_langue_dispo' => 'Este módulo está disponible en todos los idiomas posibles',
	'message_changement_lang_orig' => 'El idioma de origen de traducción elegido ("@lang_orig@") no se encuentra suficientemente traducido, éste es reemplazado por el idioma "@lang_nouvelle@".',
	'message_changement_lang_orig_inexistante' => 'El idioma de origen de traducción elegido ("@lang_orig@") no existe, éste es reemplazado por "@lang_nouvelle@".',
	'message_changement_statut' => 'Cambio del estatus de "@statut_old@" en "@statut_new@"',
	'message_confirm_redirection' => 'Usted va a ser redirigido a la modificación del módulo',
	'message_demande_update_langues_cible_mere' => 'Usted puede solicitar a un administrador resincronizar este idioma con el idioma principal.',
	'message_info_choisir_langues_profiles' => 'Usted puede seleccionar sus idiomas preferentes <a href="@url_profil@">en su perfil</a> para mostrarlos por defecto.',
	'message_lang_cible_selectionnee_auto_preferees' => 'El idioma al cual usted va a traducir ha sido seleccionada automáticamente ("@lang@") a partir de sus idiomas de preferencia. Puede cambiarlo utilizando el formulario de selección de modulos. ',
	'message_langues_choisies_affichees' => 'Solamente los idiomas que usted ha elegido son mostrados: @langues@.',
	'message_langues_preferees_affichees' => 'Solamente se muestran sus idiomas de preferencia: @langues@.',
	'message_langues_utilisees_affichees' => 'Solamente los @nb@ idiomas más utilizados son mostrados: @langues@.',
	'message_module_langue_ajoutee' => 'El idioma "@langue@" ha sido añadido al módulo "@module@".',
	'message_module_updated' => 'El módulo de idioma "@module@" ha sido actualizado.',
	'message_passage_trad' => 'Acceso a la traducción',
	'message_passage_trad_creation_lang' => 'Se ha creado el idioma @lang@ y se accede a la traducción',
	'message_suppression_module_ok' => 'El módulo @module@ ha sido suprimido.',
	'message_suppression_module_trads_ok' => 'El módulo @module@ ha sido eliminado. @nb@ items de traducción pertenecientes han sido igualmente eliminados. ',
	'message_synchro_base_fichier_ok' => 'El archivo y la base de datos están sincronizados.',
	'message_synchro_base_fichier_pas_ok' => 'El archivo y la base de datos no están sincronizados.',
	'message_upload_nb_modifies' => 'Usted ha modificado @nb@ cadenas de idioma.',
	'module_deja_importe' => 'El módulo "@module@" ya está importado',
	'moduletitre' => 'Módulos disponibles',

	// N
	'nb_item_langue_en_trop' => '1 ítem está de más en el idioma "@langue_longue@" (@langue@).',
	'nb_item_langue_inexistant' => '1 ítem no existe en el idioma "@langue_longue@" (@langue@).',
	'nb_item_langue_mere' => 'El idioma principal de este módulo comporta 1 ítem.',
	'nb_items_langue_cible' => 'El idioma de destino "@langue@" comporta @nb@ items determinados del idioma de origen.',
	'nb_items_langue_en_trop' => '@nb@ items están de más en el idioma "@langue_longue@" (@langue@).',
	'nb_items_langue_inexistants' => '@nb@ items no existen en el idioma "@langue_longue@" (@langue@).',
	'nb_items_langue_mere' => 'El idioma principal de este módulo comporta @nb@ items.',
	'notice_affichage_limite' => 'La visualización está limitada a @nb@ cadenas de lenguaje no traducidas.',
	'notice_aucun_module_favori_priorite' => 'Ningún módulo de la prioridad "@priorite@" se corresponde.',

	// R
	'readme' => 'Este plugin permite administrar los archivos de idioma',

	// S
	'str_status_modif' => 'Modificado (MODIF)',
	'str_status_new' => 'Nuevo (NEW)',
	'str_status_traduit' => 'Traducido',

	// T
	'texte_contacter_admin' => 'Contacte con un administrador si desea participar.',
	'texte_erreur' => 'ERROR',
	'texte_erreur_acces' => '<b>Atención: </b>imposible escribir en el archivo <tt>@fichier_lang@</tt>. Revise los derechos de acceso.',
	'texte_existe_deja' => ' ya existe.',
	'texte_explication_langue_cible' => 'Para el idioma de destino, debe indicar si trabaja en un idioma ya existente, o si crea un nuevo idioma.',
	'texte_export_impossible' => 'Imposible exportar el archivo. Verifique los derechos de escritura en el archivo @cible@',
	'texte_filtre' => 'Filtro (buscar)',
	'texte_inscription_ou_login' => 'Debe crear una cuenta en el sitio o identificarse para acceder a la traducción.',
	'texte_interface' => 'Interfaz de traducción:',
	'texte_interface2' => 'Interfaz de traducción',
	'texte_langue' => 'Idioma:',
	'texte_langue_cible' => 'el idioma de destino, que es el idioma en el que traduce;',
	'texte_langue_origine' => 'el idioma de origen que le servirá de modelo (priorice el idioma original si es posible);',
	'texte_langues_differentes' => 'El idioma de destino y el idioma de origen deben ser diferentes.',
	'texte_modifier' => 'Modificar',
	'texte_module' => 'módulo de idioma a traducir;',
	'texte_module_traduire' => 'Módulo a traducir:',
	'texte_non_traduit' => 'no traducido',
	'texte_operation_impossible' => 'Operación imposible. Cuando la casilla \'marcar todo\' está marcada,<br> las operaciones han de ser del tipo \'Consultar\'.',
	'texte_pas_autoriser_traduire' => 'Usted no dispone de los derechos necesarios para acceder a las traducciones.',
	'texte_pas_de_reponse' => '... ninguna respuesta',
	'texte_recapitulatif' => 'Traducciones globales',
	'texte_restauration_impossible' => 'imposible restaurar el archivo',
	'texte_sauvegarde' => 'Interfaz de traducción, Guardar/Restaurar el archivo',
	'texte_sauvegarde_courant' => 'Copia de seguridad del archivo en curso:',
	'texte_sauvegarde_impossible' => 'imposible guardar el archivo',
	'texte_sauvegarder' => 'Guardar',
	'texte_selection_langue' => 'Para mostrar un archivo de idioma traducido/en traducción, por favor
	  seleccione el idioma: ',
	'texte_selectionner' => 'Para comenzar la labor de traducción, ha de seleccionar:',
	'texte_selectionner_version' => 'Elija la versión del archivo, luego haga clic en el botón de abajo.',
	'texte_seul_admin' => 'Solamente una cuenta de administrador puede acceder a este paso.',
	'texte_total_chaine' => 'Número de cadenas:',
	'texte_total_chaine_conflit' => 'Número de cadenas más utilizadas:',
	'texte_total_chaine_modifie' => 'Número de cadenas para actualizar:',
	'texte_total_chaine_non_traduite' => 'Número de cadenas no traducidas:',
	'texte_total_chaine_traduite' => 'Número de cadenas traducidas:',
	'texte_tout_selectionner' => 'Seleccionar todo',
	'texte_type_operation' => 'Tipo de operación',
	'texte_voir_bilan' => 'Ver el <a href="@url@" class="spip_in">balance de traducciones</a>.',
	'tfoot_total' => 'Total',
	'th_avancement' => 'Adelante',
	'th_comm' => 'Comentario',
	'th_date' => 'Fecha',
	'th_items_modifs' => 'Items modificados',
	'th_items_new' => 'Nuevos items',
	'th_items_traduits' => 'Items traducidos',
	'th_langue' => 'Idioma',
	'th_langue_mere' => 'Idioma de origen',
	'th_langue_origine' => 'Texto del idioma de origen',
	'th_langue_voulue' => 'Traducción en "@lang@"',
	'th_module' => 'Módulo',
	'th_status' => 'Estatus',
	'th_total_items_module' => 'Número total de items',
	'th_traduction' => 'Traducción',
	'th_traduction_voulue' => 'Traducción en "@lang@"',
	'titre_bilan' => 'Balance de traducciones',
	'titre_bilan_langue' => 'Balance de traducciones del idioma "@lang@"',
	'titre_bilan_module' => 'Balance de traducciones del módulo "@module@"',
	'titre_changer_langue_selection' => 'Cambiar el idioma seleccionado',
	'titre_changer_langues_affichees' => 'Cambiar los idiomas mostrados',
	'titre_commentaires_chaines' => 'Comentarios acerca de esta cadena',
	'titre_form_import_step_1' => 'Paso 1: envíe su archivo',
	'titre_form_import_step_2' => 'Paso 2: verificación de sus modificaciones',
	'titre_inscription' => 'Registro',
	'titre_logo_tradlang_module' => 'Logo del módulo',
	'titre_modifications_chaines' => 'Últimas modificaciones en esta cadena',
	'titre_modifier' => 'Modificar',
	'titre_page_auteurs' => 'Liste des contributeurs', # NEW
	'titre_page_configurer_tradlang' => 'Configuración del plugin Trad-lang',
	'titre_page_tradlang_module' => 'Módulo #@id@ : @module@',
	'titre_profil_auteur' => 'Edite su perfil',
	'titre_profil_autre' => 'Edite su perfil',
	'titre_recherche_tradlang' => 'Cadenas de idioma',
	'titre_revisions_ses' => 'Sus colaboraciones',
	'titre_revisions_sommaire' => 'Últimas modificaciones',
	'titre_revisions_vos' => 'Sus colaboraciones',
	'titre_stats_ses' => 'Ses statistiques', # NEW
	'titre_stats_trads_journalieres' => 'Número de revisiones diarias',
	'titre_stats_trads_mensuelles' => 'Número de revisiones mensuales',
	'titre_stats_vos' => 'Vos statistiques', # NEW
	'titre_tradlang' => 'Trad-lang',
	'titre_tradlang_chaines' => 'Cadenas de idioma',
	'titre_tradlang_module' => 'Módulo de idioma',
	'titre_tradlang_modules' => 'Módulos de idioma',
	'titre_tradlang_non_traduit' => '1 cadena de idioma no traducida',
	'titre_tradlang_non_traduits' => '@nb@ cadenas de idioma no traducidas',
	'titre_traduction' => 'Traducciones',
	'titre_traduction_chaine_de_vers' => 'Traducción de la cadena «@chaine@» del módulo «@module@» de <abbr title="@lang_orig_long@">@lang_orig@</abbr> a <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduction_de' => 'Traducción de ',
	'titre_traduction_module_de_vers' => 'Traducción del módulo "@module@" de <abbr title="@lang_orig_long@">@lang_orig@</abbr> a <abbr title="@lang_cible_long@">@lang_cible@</abbr>',
	'titre_traduire' => 'Traducir',
	'tradlang' => 'Trad-Lang',
	'traduction' => 'Traducción @lang@',
	'traductions' => 'Traducciones'
);

?>
