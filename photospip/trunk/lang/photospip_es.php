<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/photospip?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_creer_vignette' => 'Crear una miniatura a partir de este documento',
	'bouton_editer_image' => 'Editar la imagen',
	'bouton_editer_vignette' => 'Editar la miniatura',
	'bouton_modifier_document' => 'Modificar la información del documento',
	'bouton_revenir_version' => 'Volver a esta versión',
	'bouton_supprimer_previsu' => 'Volver a la versión sin previsualización',
	'bouton_supprimer_version' => 'Eliminar esta versión',
	'bouton_supprimer_vignette' => 'Eliminar esta miniatura',
	'bouton_supprimer_vignette_document' => 'Eliminar la miniatura de este documento',
	'bouton_tester' => 'Previsualizar',
	'bouton_valider_continuer' => 'Validar y continuar',
	'bouton_valider_fermer' => 'Validar y volver a la página precedente',

	// D
	'date_doc' => 'Fecha de publicación:',
	'date_modif_doc' => 'Fecha de última modificación:',
	'donnees_exif' => 'Datos EXIF',

	// E
	'erreur_auth_modifier' => 'No está autorizado para modificar este documento.',
	'erreur_choisir_version' => 'Elija una versión',
	'erreur_doc_numero' => 'Debe indicar un identificador de documento existente.',
	'erreur_form_filtre' => 'Indique por favor un filtro a aplicar.',
	'erreur_form_filtre_sstest' => 'El filtro que ha intentado aplicar no permite ser probado. No puede aplicarlo. ',
	'erreur_form_filtre_valeur_obligatoire' => 'Debe elegir un valor.',
	'erreur_form_type_resultat' => 'Debe elegir el tipo de resultado',
	'erreur_image_process' => 'El sitio ya no utiliza GD2 para gestionar las imágenes, utilícelo por favor para su tratamiento.',
	'erreur_nb_versions_atteint' => 'Se ha alcanzado el tope de versiones diferentes de la imagen (@nb@). Sólo puede hacer pruebas sin aplicar las modificaciones.',
	'erreur_previsu' => 'Si el resultado le satisface, puede validarlo abajo del formulario; si no, puede probar otros filtros.',
	'erreur_selectionner_au_moins_une_valeur' => 'Debe seleccionar al menos un valor',
	'erreur_valeur_numerique' => 'Este filtro necesita un valor numérico como parámetro',
	'erreur_valeurs_numeriques' => 'Este filtro necesita valores numéricos',
	'explication_image_flip_horizontal' => 'Aplicar un efecto "espejo" según un eje horizontal (abajo<->arriba). No se necesita ningún reglaje. ',
	'explication_image_flip_vertical' => 'Aplicar un efecto "espejo" según un eje vertical (izquierda<->derecha). No se necesita ningún reglaje. ',
	'explication_image_flou' => 'Le filtre image_flou rend l\'image... floue. On peut lui passer en paramètre un nombre compris entre 1 et 11, définissant l’intensité du flou (de 1 à 11 pixels de flou).', # NEW
	'explication_image_gamma' => 'Le filtre Gamma change la luminosité d\'une image.<br />Il rend une image plus claire ou plus foncée.<br />Son paramètre est compris entre -254 et 254. Les valeurs supérieures à zéro rendent l\'image plus claire (254 rend toute l\'image entièrement blanche); les valeurs négatives rendent l\'image plus foncée (-254 rend l\'image complètement noire).', # NEW
	'explication_image_nb' => 'Transforma la imagen en blanco y negro',
	'explication_image_niveau_de_gris_auto' => 'Corrección automática de los niveles de la imagne.<br />(No se necesitan parámetros).',
	'explication_image_passe_partout' => 'Este filtro reducirá el tamaño de la imagen al mínimo haciendo que entre en el ajuste de anchura y altura proporcionadas.',
	'explication_image_recadre' => 'Ajusta la imagen en función de la selección del usuario.',
	'explication_image_reduire' => 'Este filtro reducirá el tamaño de la imagen proporcionalmente en función de la altura y de la anchura proporcionada.',
	'explication_image_rotation' => 'Fait tourner l’image d’un angle égal au paramètre passé. Les valeurs positives sont dans le sens des aiguilles d’une montre et inversement.<br />Attention : ce filtre modifie les dimensions de l’image.', # NEW
	'explication_image_saturation_desaturation' => 'Ce filtre va saturer ou désaturer les couleurs d’une image.<br />La luminosité de l’image et le contraste sont inchangés<br />Dans le premier cas, la couleur est « affadie » ; l’effet, utilisé subtilement, donnera des tonalités de photographie vieillie...<br />Dans l’autre cas, à l’inverse, le même filtre pourra «doper» les couleurs.', # NEW
	'explication_image_sincity' => 'Ce filtre donne un aspect "Sin City" (Ne nécessite aucun réglage).<br />Il exécute une désaturation contrastée et une accentuation du rouge.', # NEW
	'explication_resultats' => 'Al validar la modificación de imágenes son posibles tres tipos de resultados.',
	'explication_resultats_defaut' => 'Valor por defecto seleccionado al cargar el formulario.',
	'explication_tourner' => 'Aplicar una rotación de 90, 180 o 270 grados a la imagen<br />Este filtro no puede ser probado, sólo puede aplicarse.',

	// I
	'id_document' => 'ID del documento en el sitio:',
	'image_taille_actuelle' => 'Tamaño actual de la imagen:',
	'info_modifier_image' => 'Editar la imagen',
	'info_modifier_vignette' => 'Modificar la miniatura del documento #@id_document@',
	'info_nb_versions' => '@nb@ versiones',
	'info_nb_versions_une' => 'Una versión',

	// L
	'label_angle_rotation' => 'Ángulo de rotación:',
	'label_choisir_filtres' => 'Elija los filtros a activar',
	'label_compression_rendu' => 'Qualité de compression du rendu (en %, 85 par défaut) :', # NEW
	'label_couleur_sepia' => 'Color:',
	'label_hauteur_previsu' => 'Altura máxima de la previsualización en px (450 por defecto): ',
	'label_image_aplatir' => 'Aplastar la imagen',
	'label_image_flip_horizontal' => 'Voltear horizontalmente la imagen',
	'label_image_flip_vertical' => 'Voltear verticalmente la imagen',
	'label_image_flou' => 'Filtro Flou',
	'label_image_gamma' => 'Filtro Gamma',
	'label_image_nb' => 'Filtro blanco y negro',
	'label_image_niveau_de_gris_auto' => 'Niveles automáticos',
	'label_image_passe_partout' => 'Réduire l\'image (passe-partout)', # NEW
	'label_image_recadre' => 'Ajustar la imagen',
	'label_image_reduire' => 'Reducir la imagen',
	'label_image_rotation' => 'Rotación manual de la imagen',
	'label_image_saturation_desaturation' => 'Filtro [De]saturación',
	'label_image_sepia' => 'Filtro Sepia',
	'label_image_sincity' => 'Filtro Sin City',
	'label_largeur_previsu' => 'Anchura máxima de la previsualización en px (450 por defecto): ',
	'label_limiter_version' => 'Limitar el número de versiones posibles a:',
	'label_modif_creer_nouvelle_image' => 'Un nuevo documento será creado a partir de la imagen original',
	'label_modif_creer_version_image' => 'L\'image originale sera enregistrée en tant que version du nouveau document qui la remplacera dans le site', # NEW
	'label_modif_remplacer_image' => 'La imagen original será simplemente reemplazada',
	'label_modif_vignette_creer_version_image' => 'La miniatura original se registrará bajo la versión de la nueva miniatura que la reemplazará en el sitio',
	'label_modif_vignette_remplacer_image' => 'La miniatura original será simplemente reemplazada',
	'label_niveau_flou' => 'Nivel de flou:',
	'label_niveau_gamma' => 'Nivel Gamma:',
	'label_niveau_saturation_desaturation' => 'Nivel de saturación:',
	'label_ratio' => 'Ratio de la selección:',
	'label_ratio_libre' => 'Libre',
	'label_recadre_height' => 'Altura de la selección (en px):',
	'label_recadre_width' => 'Anchura de la selección (en px):',
	'label_recadre_x1_y1' => 'Posición (esquina superior izquierda)',
	'label_recadre_x2_y2' => 'Posición (esquina inferior derecha)',
	'label_reduire_height' => 'Altura (en px):',
	'label_reduire_width' => 'Anchura (en px):',
	'label_resultats' => 'Elección de los resultados posibles por el usuario',
	'label_resultats_defaut' => 'Valor por defecto preseleccionado',
	'label_tourner' => 'Rotación parametrizada',
	'label_tourner_180' => 'Girar 180º',
	'label_tourner_270' => 'Girar 90º a la izquierda',
	'label_tourner_90' => 'Girar 90º a la derecha',
	'label_type_modification' => '¿Cuál será el resultado?',
	'label_type_retour' => '¿Qué hacer tras la aplicación del filtro?',
	'label_type_retour_continuer' => 'Continuar con la modificación de la imagen',
	'label_type_retour_retour' => 'Cerrar la modificación',
	'legend_configuration' => 'Configuración del plugin',
	'legend_configuration_publique' => 'Configuración de la parte pública',
	'legend_configuration_resultats' => 'Configuración de los resultados',
	'legend_filtres_a_disposition' => 'Filtros disponibles',
	'legende_filtres_de_couleur' => 'Filtros de coloración',
	'legende_filtres_format' => 'Modificar el formato',
	'lien_editer_image' => 'Editar esta imagen',
	'lien_editer_vignette' => 'Editar la miniatura',

	// M
	'message_image_taille_actuelle' => 'Tamaño actual de la imagen: @largeur@x@hauteur@px.',
	'message_limite_versions' => 'El número de versiones precedentes están limitadas a @limite@.',
	'message_nouvelle_image_creee' => 'Su nueva imagen ha sido creada #@id_document@',
	'message_ok_version_retour' => 'Ha vuelto a la versión #@version@',
	'message_ok_version_supprimee' => 'La versión #@version@ se ha eliminado',
	'message_pas_de_versions' => 'Este documento no está versionado.',
	'message_vignette_installe_succes' => 'La miniatura se ha cargado con éxito',

	// P
	'photospip' => 'PhotoSPIP',

	// T
	'taille_fichier' => 'Tamaño del archivo: ',
	'title_version' => 'Versión #@version@',
	'titre_informations_images' => 'Información del documento',
	'titre_page_image_edit' => 'Edición de la imagen',
	'type_original' => 'Tipo de documento:'
);

?>
