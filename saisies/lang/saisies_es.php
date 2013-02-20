<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saisies?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_parcourir_docs_article' => 'Buscar artículo',
	'bouton_parcourir_docs_breve' => 'Buscar breve',
	'bouton_parcourir_docs_rubrique' => 'Buscar la sección',
	'bouton_parcourir_mediatheque' => 'Buscar mediateca',

	// C
	'construire_action_annuler' => 'Anular',
	'construire_action_configurer' => 'Configurar',
	'construire_action_deplacer' => 'Mover',
	'construire_action_dupliquer' => 'Duplicar',
	'construire_action_dupliquer_copie' => '(copia)',
	'construire_action_supprimer' => 'Eliminar',
	'construire_ajouter_champ' => 'Añadir un campo',
	'construire_attention_enregistrer' => '¡No olvide guardar sus cambios!',
	'construire_attention_modifie' => 'Este formulario es diferente al original. Tiene la posibilidad de restablecerlo conforme a su estado inical. ',
	'construire_attention_supprime' => 'Sus cambios implican suprimir campos. Confirme por favor esta nueva versión del formulario. ',
	'construire_aucun_champs' => 'Actualmente no existen campos en este formulario. ',
	'construire_confirmer_supprimer_champ' => '¿Desea eliminar realmente este campo?',
	'construire_info_nb_champs_masques' => '@nb@ campo(s) oculto(s) el tiempo de configurar el grupo.',
	'construire_position_explication' => 'Indique delante de qué otro campo se cololocará.',
	'construire_position_fin_formulaire' => 'Al final del formulario',
	'construire_position_fin_groupe' => 'Al final del grupo @groupe@',
	'construire_position_label' => 'Posición del campo',
	'construire_reinitialiser' => 'Restablecer el formulario',
	'construire_reinitialiser_confirmer' => 'Va a perder todos los cambios. ¿Está seguro de volver al formulario original?',
	'construire_verifications_aucune' => 'Ninguna',
	'construire_verifications_label' => 'Tipo de verificación a efectuar',

	// E
	'erreur_generique' => 'Hay errores en los siguientes campos, revise por favor sus entradas',
	'erreur_option_nom_unique' => 'Este nombre ya ha sido utilizado en otro campo, y ha de ser único en el formulario.',

	// I
	'info_configurer_saisies' => 'Página de prueba de las entradas',

	// L
	'label_annee' => 'Año',
	'label_jour' => 'Día',
	'label_mois' => 'Mes',

	// O
	'option_aff_art_interface_explication' => 'Mostrar sólo los artículos en el idioma del usuario',
	'option_aff_art_interface_label' => 'Aparencia multilingüe',
	'option_aff_langue_explication' => 'Muestra el idioma del artículo o de la sección enfrente de su título',
	'option_aff_langue_label' => 'Mostrar el idioma',
	'option_aff_rub_interface_explication' => 'Mostrar sólo las secciones en el idioma del usuario',
	'option_aff_rub_interface_label' => 'Aparencia multilingüe',
	'option_attention_explication' => 'Un mensaje más importante que la explicación.',
	'option_attention_label' => 'Aviso',
	'option_autocomplete_defaut' => 'Dejar por defecto',
	'option_autocomplete_explication' => 'Al cargar la página, su navegador puede rellenar el campo en función de sus historial',
	'option_autocomplete_label' => 'Pre-relleno del campo',
	'option_autocomplete_off' => 'Desactivar',
	'option_autocomplete_on' => 'Activar',
	'option_cacher_option_intro_label' => 'Esconder la primera opción vacía',
	'option_choix_destinataires_explication' => 'Una lista de autoras o autores de SPIP. Se podrán elegir los destinatorios dentro de esta lista. Si no se selecciona nadie, la elección será la persona que instaló el sitio.',
	'option_choix_destinataires_label' => 'Destinaciones posibles',
	'option_class_label' => 'Clases CSS adicionales',
	'option_cols_explication' => 'Ancho del bloque (en número de carácteres). Esta opción no se aplica siempre, porque puede ser cancelada por los estilos CSS de tu sitio.',
	'option_cols_label' => 'Ancho',
	'option_datas_explication' => 'Tiene que dar una opción por línea bajo la forma "clave|Etiqueta de la opción"',
	'option_datas_label' => 'Lista de opciones posibles',
	'option_defaut_label' => 'Valor por defecto',
	'option_disable_avec_post_explication' => 'Como la opción anterior, pero igual envía el valor en un campo escondido.',
	'option_disable_avec_post_label' => 'Deactivar pero enviar',
	'option_disable_explication' => 'El campo ya no puede obtener el foco.',
	'option_disable_label' => 'Deactivar el campo',
	'option_erreur_obligatoire_explication' => 'Puede personalizar el mensaje de error mostrado para indicar una obligación (sino dejar en blanco).',
	'option_erreur_obligatoire_label' => 'Mensaje de obligación',
	'option_explication_explication' => 'Si hace falta, una frase corta que describe el campo',
	'option_explication_label' => 'Explicación',
	'option_groupe_affichage' => 'Aparencia',
	'option_groupe_description' => 'Descripción',
	'option_groupe_utilisation' => 'Uso',
	'option_groupe_validation' => 'Validación',
	'option_heure_pas_explication' => 'Lorsque vous utilisez l’horaire, un menu s’affiche pour aider à saisir heures et minutes. Vous pouvez ici choisir l’intervalle de temps entre chaque choix (par défaut 30min).', # NEW
	'option_heure_pas_label' => 'Intervalle des minutes dans le menu d’aide à la saisie', # NEW
	'option_horaire_label' => 'Horaire', # NEW
	'option_horaire_label_case' => 'Permettre de saisie aussi l’horaire', # NEW
	'option_info_obligatoire_explication' => 'Puede modificar la indicación de campo obligatoria: <i>[Obligatorio</i>.',
	'option_info_obligatoire_label' => 'Indicación de campo obligatorio',
	'option_inserer_barre_choix_edition' => 'Barra de edición completa',
	'option_inserer_barre_choix_forum' => 'barra de los foros',
	'option_inserer_barre_explication' => 'Integra una barra tipográfica si ésta es activa.',
	'option_inserer_barre_label' => 'Integrar una barra de herramientas',
	'option_label_case_label' => 'Etiqueta posicionada al lado de la checkbox',
	'option_label_explication' => 'El título que se enseñará',
	'option_label_label' => 'Etiqueta',
	'option_maxlength_explication' => 'El campo no podrá contener más carácteres que este número.',
	'option_maxlength_label' => 'Número máximo de carácteres',
	'option_multiple_explication' => 'Se podrán seleccionar varias opciones',
	'option_multiple_label' => 'Selección múltiple',
	'option_nom_explication' => 'Un nombre informático que identificará el campo. Sólo puede contener carácteres alfanuméricos minúsculos o el carácter "_".',
	'option_nom_label' => 'Nombre del campo',
	'option_obligatoire_label' => 'Campo obligatorio',
	'option_option_intro_label' => 'Label de la primera opción vacía',
	'option_option_statut_label' => 'Mostrar el estatus',
	'option_pliable_label' => 'Desplegable',
	'option_pliable_label_case' => 'El grupo de campos se podrá contraer y desplegar.',
	'option_plie_label' => 'Ya está contraido',
	'option_plie_label_case' => 'Si el grupo de campos se puede contraer, ya estará contraido cuando se enseñe el formulario.',
	'option_previsualisation_explication' => 'Si la barra tipográfica es activa, añade una pestaña de previsualización del texto.',
	'option_previsualisation_label' => 'Activar la previsualización',
	'option_readonly_explication' => 'El campo se puede leer, seleccionar, pero no se puede modificar.',
	'option_readonly_label' => 'Sólo lectura',
	'option_rows_explication' => 'Altura del bloque en número de líneas. Esta opción no se aplica siempre, porque puede ser cancelada por los estilos CSS de su sitio.',
	'option_rows_label' => 'Número de líneas',
	'option_size_explication' => 'Ancho del campo (número de carácteres). Esta opción no se aplica siempre, porque puede ser cancelada por los estilos CSS de su sitio.',
	'option_size_label' => 'Tamaño del campo',
	'option_type_choix_plusieurs' => 'Permitirle al usuario elegir <strong>varias</strong> personas destinatorias.',
	'option_type_choix_tous' => 'Poner a <strong>todos/as</strong> estas autoras como destinatorias. El usuario no tendrá ninguna opción.',
	'option_type_choix_un' => 'Permitirle al usuario elegir <strong>sólo una</strong> persona destinatoria.',
	'option_type_explication' => 'En modo "escondido", el contenido del campo no será visible.',
	'option_type_label' => 'Tipo del campo',
	'option_type_password' => 'Escondido',
	'option_type_text' => 'Normal',

	// S
	'saisie_auteurs_explication' => 'Permite seleccionar uno o más autores',
	'saisie_auteurs_titre' => 'Autores',
	'saisie_case_explication' => 'Permite activar o deactivar algo.',
	'saisie_case_titre' => 'Checkbox único',
	'saisie_checkbox_explication' => 'Permite elegir varias opciones con checkboxes',
	'saisie_checkbox_titre' => 'Checkboxes',
	'saisie_date_explication' => '¿Permitir introducir una fecha? Ayuda de calendario',
	'saisie_date_titre' => 'Fecha',
	'saisie_destinataires_explication' => 'Permite elegir una o varias personas destinatorias dentro de las autoras seleccionadas.',
	'saisie_destinataires_titre' => 'Personas destinatorias',
	'saisie_explication_explication' => 'Una explicación general.',
	'saisie_explication_titre' => 'Explicación',
	'saisie_fieldset_explication' => 'Un marco que podrá englobar varios campos.',
	'saisie_fieldset_titre' => 'Grupo de campos',
	'saisie_file_explication' => 'Mandar un archivo',
	'saisie_file_titre' => 'Archivo',
	'saisie_hidden_explication' => 'Un campo invisible, que ya contiene un valor',
	'saisie_hidden_titre' => 'Campo escondido',
	'saisie_input_explication' => 'Una sola línea de texto, que puede ser visible u ocultada (contraseña).',
	'saisie_input_titre' => 'Línea de texto',
	'saisie_oui_non_explication' => 'Sí o no. ¿Está claro? :)',
	'saisie_oui_non_titre' => 'Sí o no',
	'saisie_radio_defaut_choix1' => 'Uno',
	'saisie_radio_defaut_choix2' => 'Dos',
	'saisie_radio_defaut_choix3' => 'Tres',
	'saisie_radio_explication' => 'Permite elegir una opción dentro de varias opciones disponibles.',
	'saisie_radio_titre' => 'Botones de opción',
	'saisie_selecteur_article' => 'Muestra un navegador de selección de artículo',
	'saisie_selecteur_article_titre' => 'Selector de artículo',
	'saisie_selecteur_rubrique' => 'Muestra un navegador de selección de sección',
	'saisie_selecteur_rubrique_article' => 'Muestra un navegador de selección de artículo o de sección',
	'saisie_selecteur_rubrique_article_titre' => 'Selector de artículo o de sección',
	'saisie_selecteur_rubrique_titre' => 'Selector de sección',
	'saisie_selection_explication' => 'Elegir una opción dentro de una lista desplegable.',
	'saisie_selection_multiple_explication' => 'Permite elegir varias opciones con una lista.',
	'saisie_selection_multiple_titre' => 'Selección multiple',
	'saisie_selection_titre' => 'Lista desplegable',
	'saisie_textarea_explication' => 'Un campo de texto sobre varias líneas.',
	'saisie_textarea_titre' => 'Bloque de texto',

	// T
	'tous_visiteurs' => 'Todos los visitantes (incluso no registrados)',
	'tout_selectionner' => 'Seleccionar todo',

	// V
	'vue_sans_reponse' => '<i>Sin respuesta</i>',

	// Z
	'z' => 'zzz'
);

?>
