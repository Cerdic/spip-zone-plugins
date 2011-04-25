<?php
/**
 * Pack langue espagnol
 * 
 * @package spiplistes
 */
 // $LastChangedRevision: 47066 $
 // $LastChangedBy: paladin@quesaco.org $
 // $LastChangedDate: 2011-04-25 19:54:15 +0200 (Lun 25 avr 2011) $
 
// Este es un archivo para SPIP -- This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_es'] = array(

// CP-20081126: clasificado por scripts
// action/spiplistes_agenda.php
// action/spiplistes_changer_statut_abonne.php
// action/spiplistes_envoi_lot.php
// action/spiplistes_journal.php
// action/spiplistes_lire_console.php
// action/spiplistes_liste_des_abonnes.php
// action/spiplistes_listes_abonner_auteur.php
// action/spiplistes_moderateurs_gerer.php
'voir_historique' => 'Ver env&iacute;os anteriores'
, 'pas_de_liste_prog' => "Ning&uacute;n env&iacute;o programado"

// action/spiplistes_supprimer_abonne.php
// balise/formulaire_abonnement.php
, 'inscription_liste_f' => 'Recibir&aacute;s el bolet&iacute;n informativo y los emails en formato @f@: '
, 'inscription_listes_f' => 'Recibir&aacute;s los boletines informativos y los emails en formato @f@: '
, 'inscription_reponse_s' => "Est&aacute;s inscrit@ para recibir el bolet&iacute;n informativo de @s@"
, 'inscription_reponses_s' => 'Est&aacute;s inscrit@ para recibir los boletines informativos de @s@'
, 'vous_abonne_aucune_liste' => "No est&aacute;s inscrito a ning&uacute;n bolet&iacute;n informativo"
, 'liste_dispo_site_' => "Bolet&iacute;n informativo de este sitio : "
, 'listes_dispos_site_' => "Boletines informativos de este sitio : "
, 'desole_pas_de_liste' => "Por ahora no hay boletines informativos disponibles"
, 'pour_vous_abonner' => "Para inscribirte en los boletines informativos"

// obsoleto
, 'abonnement_mail_passcookie' => "
	<br />
	Para modificar tu inscripci&oacute;n en los boletines informativos de <strong>@nom_site_spip@</strong> (@adresse_site@), 	
	puedes ir a:<br /><br />
	<a href='@adresse_site@/spip.php?page=abonnement&d=@cookie@'>@adresse_site@/spip.php?page=abonnement&d=@cookie@</a><br /><br />
	All&iacute; puedes modificar tu inscripci&oacute;n
	<br/>"
, 'bienvenue_sur_la_liste_' => "Bienvenid@ a los boletines informativos de "
, 'vos_abos_sur_le_site_' => "Tu inscripci&oacute;n en "
, 'votre_format_de_reception_' => "Tu formato de recepci&oacute;n "
, '_cliquez_lien_formulaire' => "cliquea sobre este enlace para acceder al formulario"
, 'pour_modifier_votre_abo_' => "Para modificar tu inscripci&oacute;n "
, 'abonnement_presentation' => "
	Escribe tu email.
	All&iacute; se enviar&aacute; un mensaje de confirmaci&oacute;n.
	El enlace te permitir&aacute; seleccionar los boletines informativos disponibles.
	"
, 'confirmation_inscription' => "Confirma tu inscripci&oacute;n"
, 'souhait_modifier_abo'=>'Quieres modificar tu inscripci&oacute;n en el bolet&iacute;n informativo'
, 'suspendre_abonnement_' => "Suprimir mi inscripci&oacute;n "
, 'vous_etes_redact' => "Est&aacute;s inscrito como redactor(a)."
, 'vous_etes_membre' => "Est&aacute;s inscrit@ para recibir los boletines informativos de este sitio.
	A veces es necesario autentificarse para continuar recibi&eacute;ndolos."

// balise/formulaire_modif_abonnement.php
, 'abonnement_modifie' => 'Cambios realizados'
, 'abonnement_nouveau_format' => 'Formato para recibir los boletines y emails: '

// base/spiplistes_init.php
, 'autorisation_inscription' => 'SPIP-listes ha activado la autorizaci&oacute;n para inscribir visitantes'

// base/spiplistes_tables.php
// base/spiplistes_upgrade.php
// docs/spiplistes_aide_fr.html
// exec/spiplistes_abonne_edit.php
, 'adresse_mail_obligatoire' => "Falta el email. No se puede hacer la inscripci&oacute;n."
, 'abonne_sans_format' => "<b>Esta cuenta est&aacute; inactiva</b>. No hay formatos de email seleccionados. No puedes recibir emails hasta que no selecciones un formato para recibirlos."
, 'Desabonner_temporaire' => "Suprimir temporalmente esta cuenta."
, 'Desabonner_definitif' => "Suprimir esta cuenta de todos los boletines."
, 'export_etendu_' => "Exportaci&oacute;n ampliada"
, 'exporter_statut' => "Exportar el estado (invitad@, redactora, etc.)"
, 'editer_fiche_abonne' => "Modificar la inscripci&oacute;n;"
, 'edition_dun_abonne' => "Modificar una inscripci&oacute;n"
, 'format_de_reception' => "Formato" // + formulaire
, 'format_reception' => "Formato:"
, 'format_de_reception_desc' => "Puedes elegir un formato para recibir los correos y boletines informativos.<br /><br />
   Tambi&eacute;n puedes suprimir temporalmente tu cuenta. 
   Permanecer&aacute;s inscrit@ en las listas, pero los emails no ser&aacute;n enviados
   hasta que no selecciones un formato para recibirlos."
, 'mettre_a_jour' => '<h3>SPIP-listes va a actualizarse</h3>'
, 'regulariser' => 'poner al d&iacute;a las cuentas suprimidas...<br />'
, 'Supprimer_ce_contact' => "Suprimir esta cuenta"
, 'abonne_listes' => "Esta cuenta esta asociada a las siguientes listas"
, 'n_duplicata_mail' => "@n@ duplicado(s)"
, 'n_incorrect_mail' => "@n@ incorrecto(s)"

// exec/spiplistes_abonnes_tous.php
, 'repartition_abonnes' => "Distribuci&oacute;n de las inscripciones"
, 'abonnes_titre' => 'Inscripciones'
, 'chercher_un_auteur' => "Buscar una cuenta"
, 'une_inscription' => 'Una inscripci&oacute;n encontrada'
, 'suivi' => 'Seguimiento de inscripciones' // + presentation
, 'abonne_aucune_liste' => 'Sin inscripci&oacute;n en ninguna lista'
, 'format_aucun' => "Sin formato"
, 'repartition_formats' => "Distribuci&oacute;n de formatos"

// exec/spiplistes_aide.php
// exec/spiplistes_autocron.php
// exec/spiplistes_config.php
, 'personnaliser_le_courrier' => "Personalizar el email"
, 'personnaliser_le_courrier_desc' => 
	"Puedes personalizar el email para cada inscripci&oacute a&ntilde;adiendo
   en tu plantilla los tags necesarios. Por ejemplo, para a&ntilde;adir
   el nombre de la cuenta en el email, escribe en 
   tu plantilla _AUTEUR_NOM_ (recuerda poner un gui&oacute;n bajo al final)."
, 'utiliser_smtp' => "Utilizar SMTP"
, 'requiert_identification' => "Requiere identificarse"
, 'adresse_smtp' => "Direcci&oacute;n email del <em>sender</em> SMTP"
, '_aide_install' => "<p>Bienvenid@ a SPIP-Listes.</p>
	<p class='verdana2'>Por defecto, la instalaci&oacute;n de SPIP-Listes est&aacute; en modo <em>simulaci&oacute;n
	</em> para que puedas hacer pruebas y descubrir sus posibilidades.</p>
	<p class='verdana2'>Para modificar las opciones de SPIP-Listes, visita la <a href='@url_config@'>p&aacute;gina de configuraci&oacute;n</a>.</p>"
, 'adresse_envoi_defaut' => "Direcci&oacute;n de email enviada por defecto"
, 'adresse_on_error_defaut' => "Direcci&oacute;n de retorno para los errores"
, 'pas_sur' => '<p>Si no sabes que elegir, selecciona la funci&oacute;n de mail de PHP.</p>'
, 'Complement_des_courriers' => "Complemento de los emails"
, 'Complement_lien_en_tete' => "Enlace en el email"
, 'Complement_ajouter_lien_en_tete' => "A&ntilde;adir un enlace en el encabezado del email"
, 'Complement_lien_en_tete_desc' => "Enviar en el encabezado del email HTML el enlace
   al email original guardado en el sitio."
, 'Complement_tampon_editeur' => "Est&aacute; opci&oacute;n permite a&ntilde;adir el sello Editor o Editora"
, 'Complement_tampon_editeur_desc' => "Est&aacute; opci&oacute;n permite a&ntilde;adir el sello Editor o Editora al final del email. "
, 'Complement_tampon_editeur_label' => "A&ntilde;adir el sello Editor o Editora al final del email"
, 'Envoi_des_courriers' => "Env&iacute;o de emails"
, 'log_console' => "Consola"
, 'log_details_console' => "Opciones de la consola"
, 'log_voir_destinataire' => "Listar las direcciones de email enviados en la consola cuando termine el env&iacute;o."
, 'log_console_syslog_desc' => "Est&aacute;s en una red local (@IP_LAN@). Si quieres, puedes activar la consola como syslog en lugar de las informaciones de SPIP (aconsejado con unix)."
, 'log_console_syslog_texte' => "Activar las informaciones del sistema (reenv&iacute;o como syslog)"
, 'log_console_syslog' => "Consola syslog"
, 'log_voir_le_journal' => "Ver la informaci&oacute;n de SPIP-Listes en la consola"
, 'recharger_journal' => "Recargar la informaci&oacute;n"
, 'fermer_journal' => "Cerrar la informaci&oacute;n"
, 'methode_envoi' => 'Forma de env&iacute;o'
, 'mode_suspendre_trieuse' => "Cancelar los env&iacute;os de boletines"
, 'Suspendre_le_tri_des_listes' => "Con esta opci&oacute;n puedes - en caso de bloqueo - cancelar el env&iacute;o 
	de boletines y modificar su configuraci&oacute;n. Cambia est&aacute; opci&oacute;n para volver a activar 
	el env&iacute;o de boletines programados."
, 'mode_suspendre_meleuse' => "Cancelar el env&iacute;o de correos"
, 'suspendre_lenvoi_des_courriers' => "Con esta opci&oacute;n puedes - en caso de bloqueo - cancelar el env&iacute;o 
	de correos y modificar su configuraci&oacute;n. Cambia est&aacute; opci&oacute;n para volver a activar 
	los env&iacute;os pendientes. "
, 'nombre_lot' => 'N&uacute;mero de env&iacute;os por lote'
, 'php_mail' => 'Utilizar la funci&oacute;n mail() de PHP'
, 'patron_du_tampon_' => "Plantilla del sello : "
, 'Patron_de_pied_' => "Plantilla del pie "
, 'personnaliser_le_courrier_label' => "Activar la personnalizaci&oacute;n de los emails"
, 'parametrer_la_meleuse' => "Configurar el env&iacute;o"
, 'smtp_hote' => 'Servidor'
, 'smtp_port' => 'Puerto'
, 'simulation_desactive' => "Modo simulaci&oacute;n desactivado."
, 'simuler_les_envois' => "Simular el env&iacute;o de emails"
, 'abonnement_simple' => '<strong>Inscripci&oacute;n simple: </strong><br /><em>S&oacute; se env&iacute;a un 
	mensaje de confirmaci&oacute;n.</em>'
, 'abonnement_code_acces' => '<strong>Inscripci&oacute;n completa: </strong><br /><i>Se env&iacute;a un 
	mensaje de confirmaci&oacute;n con login y contrase&ntilde;a para entrar en la zona privada del sitio. </i>'
, 'mode_inscription' => 'Configurar el modo de inscripci&oacute;n'

// exec/spiplistes_courrier_edit.php
, 'Generer_le_contenu' => "Crear el contenido autom&aacute;ticamente"
, 'Langue_du_courrier_' => "Idioma del email:"
, 'generer_Apercu' => "crear y previsualizar"
, 'a_partir_de_patron' => "Desde una plantilla"
, 'avec_introduction' => "Con texto de introducci&oacute;n"
, 'calcul_patron_attention' => "Algunas plantillas insertan el texto al final (Texto del email). 
	Si actualizas los emails, recuerda desmarcar esta opci&oacute;n antes de generar el contenido."
, 'charger_patron' => 'Seleccionar una plantilla para el correo'
, 'Courrier_numero_' => "Correo n&uacute;mero:" // + _gerer
, 'Creer_un_courrier_' => "Escribir un correo:"
, 'choisir_un_patron_' => "Seleccionar una plantilla"
, 'Courrier_edit_desc' => 'Puedes elegir que se cree autom&aacute;ticamente el contenido del correo
	o escribirlo directamente en el <strong>texto del correo</strong>.'
, 'Contenu_a_partir_de_date_' => "Contenido desde "
, 'Cliquez_Generer_desc' => "Cliquea aqu&iacute; para <strong>@titre_bouton@</strong> el correo insertando el resultado 
	en el @titre_champ_texte@."
, 'Lister_articles_de_rubrique' => "Con los art&iacute;culos de la secci&oacute;n"
, 'Lister_articles_mot_cle' => "Con los art&iacute;culos de la palabra clave"
, 'edition_du_courrier' => "Edici&oacute;n del correo" // + gerer
, 'generer_un_sommaire' => "Crear un &iacute;ndice"
, 'generer_patron_' => "Crear la plantilla "
, 'generer_patron_avant' => "antes del &iacute;ndice"
, 'generer_patron_apres' => "despu&eacute;s del &iacute;ndice."
, 'introduction_du_courrier_' => "Presentaci&oacute;n del correo antes del contenido"
, 'Modifier_un_courrier__' => "Modificar un correo:"
, 'Modifier_ce_courrier' => "Modificar este correo"
, 'sujet_courrier' => '<strong>T&iacute;tulo del correo</strong> [obligatorio]'
, 'texte_courrier' => '<strong>Texto del correo</strong> (HTML permitido)'
, 'avec_patron_pied__' => "Con la plantilla para el pie: "

// exec/spiplistes_courrier_gerer.php
, 'Erreur_Adresse_email_invalide' => 'Error: la direcci&oacute;n del email no es v&aacute;lida'
, 'langue_' => '<strong>Idioma:</strong>&nbsp;'
, 'calcul_patron' => 'Calculado con la plantilla para texto'
, 'calcul_html' => 'Calculado desde la versi&oacuten HTML del mensaje'
, 'dupliquer_ce_courrier' => "Duplicar este correo"
, 'destinataire_sans_format_alert' => "Destinatari@ sin formato de recepci&oacute;n.
	Utiliza un formato (texto o html) para esta cuenta o selecciona otr@ destinatari@."
, 'envoi_date' => 'Fecha de env&iacute;o: '
, 'envoi_debut' => 'Comienzo del env&iacute;o: '
, 'envoi_fin' => 'Final del env&iacute;o: '
, 'erreur_envoi' => 'N&uacute;mero de env&iacute;os err&oacute;neos: '
, 'Erreur_liste_vide' => "Error: esta lista no tiene inscripciones."
, 'Erreur_courrier_introuvable' => "Error: este correo no existe." // + previsu
, 'Envoyer_ce_courrier' => "Enviar este email"
, 'format_html__n' => "Formato html: @n@"
, 'format_texte__n' => "Format texte: @n@"
, 'message_arch' => 'Correo archivado'
, 'message_en_cours' => 'Correo envi&acute;ndose...'
, 'message_type' => 'Correo'
, 'sur_liste' => 'A la lista' // + casier
, 'Supprimer_ce_courrier' => "Suprimir este correo"
, 'email_adresse' => 'Direcci&oacute;n email para probarlo' // + liste
, 'email_test' => 'Enviar un correo de prueba'
, 'Erreur_courrier_titre_vide' => "Error: el correo no tiene t&iacute;tulo."
, 'message_en_cours' => 'Este correo est&aacute; en curso de redacci&oacute;n'
, 'modif_envoi' => 'Puedes modificarlo o realizar el env&iacute;o'
, 'message_presque_envoye' =>'Este correo va a ser enviado'
, 'Erreur_Adresse_email_inconnue' => 'Atenci&oacute;n, la direcci&oacute;n email de prueba no corresponde
	con ninguna inscripci&oacute;n, <br />el env&iacute;o no puede hacerse, puedes repetir el proceso<br /><br />'

// exec/spiplistes_courrier_previsu.php
, 'lettre_info' => 'El correo informativo del sitio'

// exec/spiplistes_courriers_casier.php
// exec/spiplistes_import_export.php
, 'Exporter_une_liste_d_abonnes' => "Exportar una lista de inscripciones"
, 'Exporter_une_liste_de_non_abonnes' => "Exportar una lista de no inscrit@s"
, '_aide_import' => "Puedes importar una lista de inscripciones desde un archivo de texto.<br />
	Esta lista debe estar en formato s&oacute;lo de texto, y con una l&iacute;a
   para cada inscripci&oacute;n. Cada l&iacute;nea debe estar as&iacute;:<br />
	<tt style='display:block;margin:0.75em 0;background-color:#ccc;border:1px solid #999;padding:1ex;'>adresse@mail<span style='color:#f66'>
   [separaci&oacute;n]</span>login<span style='color:#f66'>[separaci&oacute;n]</span>nom</tt>
	<tt style='color:#f66'>[separaci&oacute;n]</tt> debe ser una tabulaci&oacute;n o un punto y coma.<br /><br />
	La direcci&oacute;n email y el login deben ser &uacute;nicos. Si ya
   existen en la base de datos, la l&iacute;nea ser&aacute; rechazada.<br />
	El primer campo adresse@mail es obligatorio. Los otros dos pueden 
   dejarse vac&iacute;os (puedes importar listas de otras versiones de SPIP-Listes)."
, 'annuler_envoi' => "Cancelar el env&iacute;o" // + _gerer
, 'envoi_patron' => 'Enviar con la plantilla'
, 'import_export' => 'Importar / Exportar'
, 'incorrect_ou_dupli' => " (incorrecto o duplicado)"
, 'membres_liste' => 'Lista de inscripciones'
, 'Messages_automatiques' => 'Env&iacute;os programados'
, 'Pas_de_liste_pour_import' => "Tienes que seleccionar al menos una lista de detinatari@s para poder importar las inscripciones."
, 'Resultat_import' => "Resultado de la importaci&oacute;n"
, 'Selectionnez_une_liste_pour_import' => "Tienes que seleccionar al menos una lista de destinatari@s para poder importar las inscripciones."
, 'Selectionnez_une_liste_de_destination' => "Selecciona una o varias listas de destinatari@s para las inscripciones."
, 'Tous_les_s' => "Cada @s@"
, 'Toutes_les_semaines' => "Semanal"
, 'Tous_les_mois' => "Mensual, "
, 'Tous_les_ans' => 'Anual'
, 'version_html' => '<strong>Versi&oacute;n HTML</strong>'
, 'version_texte' => '<strong>Versi&oacute;n texte</strong>'
, 'erreur_import' => 'El archivo de importaci&oacute;n tiene un error en la l&iacute;nea '
, 'envoi_manuel' => 'Env&iacute;o manual'
, 'format_date' => 'Y/m/d'
, 'importer' => 'Importar inscripciones'
, 'importer_fichier' => 'Importar un archivo'
, 'importer_fichier_txt' => '<p><strong>Las inscripciones deben estar en un archivo de texto simple (texto) 
	que no tenga m&aacute;s que una direcci&oacute;n email por l&iacute;nea</strong></p>'
, 'importer_preciser' => '<p>Se&ntilde;ala las listas y el formato para la importaci&oacute;n de inscripciones</p>'
, 'prochain_envoi_prevu' => 'Pr&oacute;ximo env&iacute;o' // + gerer
, 'option_import_' => "Opciones de importaci&oacute;n "
, 'forcer_abos_' => "Modificar las inscripciones (si la direcci&oacute;n email existe en la base, modifica la inscripci&oacute;n
	para seleccionarla)."
, 'erreur_import_base' => "Error de importaci&oacute;n. Datos incorrectos o error en la base SQL."
, 'erreur_n_fois' => '(error encontrado @n@ veces)'
, 'Liste_de_destination_s' => "Lista de destinatari@s: @s@"
, 'Listes_de_destination_s' => "Listas de destinatari@s: @s@"
, 'pas_dimport' => "Sin importaci&oacute;n. El archivo est&aacute; vac&iacute;o o todas las direcciones ya existen en la base."

// exec/spiplistes_liste_edit.php
, 'texte_dinsctription_' => "Texto de inscripci&oacute;n: "
, 'Creer_une_liste_' => "Crear una lista "
, 'en_debut_de_semaine' => "al comienzo de la semana"
, 'en_debut_de_mois' => "al comienzo del mes"
, 'envoi_non_programme' => "Env&iacute;o no programado"
, 'edition_dune_liste' => "Edici&oacute; de una lista"
, 'texte_contenu_pied' => '<br />(Mensaje a&ntilde;adido al final de cada email en el momento del env&iacute;o)<br />'
, 'texte_pied' => '<p><strong>Texto de pie de p&aacute;gina</strong>'
, 'modifier_liste' => 'Modificar esta lista '
, 'txt_abonnement' => '(Escribe el texto para la inscripci&oacute;n a esta lista, ser&aacute; mostrado en la parte p&uacute;blica si est&aacute; activa)'

// exec/spiplistes_liste_gerer.php
, 'forcer_les_abonnement_liste' => "Modificar las inscripciones de esta lista"
, 'periodicite_tous_les_n_s' => "Periodicidad: cada @n@ @s@"
, 'liste_sans_titre' => 'Lista sin t&iacute;tulo'
, 'statut_interne' => 'Privada'
, 'statut_publique' => 'P&uacute;blica'
, 'adresse' => 'Escribe la direcci&oacute;n email para las respuestas de los correos
	(por defecto, la direcci&oacute;n email de admin del sitio ser&aacute; utilizada como direcci&oacute;n de respuesta):'
, 'Ce_courrier_ne_sera_envoye_qu_une_fois' => 'Este correo s&oacute;lo se enviar&aacute; una vez'
, 'adresse_de_reponse' => 'Direcci&oacute;n de respuesta'
, 'adresse_mail_retour' => 'Direcci&oacute;n email de gesti&oacute;n de la lista (reply-to)'
, 'Attention_action_retire_invites' => 'Cuidado: esta acci&oacute;n suprime a l@s invitad@s de la lista de inscripciones'
, 'A_partir_de' => 'Desde'
, 'Apercu_plein_ecran' => 'Abrirse en una nueva ventana'
, 'Attention_suppression_liste' => 'Cuidado: quieres suprimir un bolet&iacute;n informativo.
	Las inscripciones ser&aacute;n borradas de este bolet&iacute;n informativo automaticamente.' 
, 'Abonner_tous_les_invites_public' => "Inscribir todas las cuentas de invitadas a esta lista p&uacute;blica."
, 'Abonner_tous_les_inscrits_prives' => "Inscribir todas las cuentas a esta lista privada, menos las de visitantes."
, 'boite_confirmez_envoi_liste' => "Quieres enviar autom&aacute;ticamente este bolet&iacute;n informativo.<br />
	Necesitas confirmarlo."
, 'cette_liste_est_' => "Esta lista es: @s@"
, 'Confirmer_la_suppression_de_la_liste' => "Confirmar la suppresi&oacute;n de la lista "
, 'Confirmez_requete' => "Confirmar la petici&oacute;n."
, 'date_expedition_' => "Fecha de salida "
, 'Dernier_envoi_le_' => "&Uacute;ltimo env&iacute;o:"
, 'forcer_abonnement_desc' => "Puedes modificar las inscripciones a esta lista, sea para todas 
	las cuentas (visitantes, redactores y redactoras o administradoras), sea s&oacute;lo para todas
	las de visitantes."
, 'forcer_abonnement_aide' => "<strong>Cuidado</strong>: s&oacute;lo por existir una inscripci&oacute;n en esta lista no recibir&aacute; los emails. Es 	necesario que est&eacute; confirmado el formato de recepci&oacute;n: html o solamente texto.<br />
	Se puede cambiar ese formato en la <a href='@lien_retour@'>en la p&aacute;gina de seguimiento de inscripciones</a>"
, 'forcer_abonnements_nouveaux' => "Si seleccionas la opci&oacute;n <strong>Cambiar las inscripciones al formato...</strong>, 
	cambiar&aacute;s el formato de recepci&oacute;n de las nuevas inscripciones.
	Las inscripciones anteriores conservar&aacute;n sus preferencias."
, 'Forcer_desabonner_tous_les_inscrits' => "Borrar todas las inscripciones de esta lista."
, 'gestion_dune_liste' => "Gesti&oacute;n de una lista"
, 'message_sujet' => 'T&iacute;tulo '
, 'mods_cette_liste' => "Moderan esta lista"
, 'nbre_abonnes' => "N&uacute;mero de inscripciones: "
, 'nbre_mods' => "N&uacute;mero de moderadores(as): "
, 'patron_manquant_message' => "Tienes que seleccionar un plantilla principal antes de modificar las opciones para 
	el env&iacute;o de esta lista."
, 'liste_sans_patron' => "Lista sin plantilla." // courriers_listes
, 'Patron_grand_' => "Plantilla principal "
, 'sommaire_date_debut' => "Para hoy"
, 'abos_cette_liste' => "Inscripciones en esta lista"
, 'confirme_envoi' => 'Confirmar el env&iacute;o'
, 'env_esquel' => 'Env&iacute;o programado con la plantilla'
, 'env_maint' => 'Enviar ahora'
, 'date_act' => 'Datos actualizados'
, 'forcer_les_abonnements_au_format_' => "Cambiar las inscripciones al formato: "
, 'pas_denvoi_auto_programme' => "No hay env&iacute;os programados para este bolet&iacute;n informativo."
, 'Pas_de_periodicite' => "Sin periodicidad."
, 'prog_env' => 'Programar el env&iacute;o'
, 'prog_env_non' => 'No programar el env&iacute;o'
, 'conseil_regenerer_pied' => "<br />Esta plantilla pertenece a una versi&oacute;n anterior de SPIP-Listes.<br />
	Recomendaci&oacute;n: selecciona una nueva plantilla de pie de p&aacute;gina para tomar en cuenta el multiling&#252;ismo
	o la versi&oacute;n &#39;s&oacute;lo texto&#39 de la plantilla."
, 'boite_alerte_manque_vrais_abos' => "No hay inscripciones para este bolet&iacute;n informativo,
	o no han seleccionado un formato para recibir los emails.
	<br />
	Selecciona el formato para recibir los emails, al menos de una inscripci&oacute;n, antes de validar el env&iacute;o."	

// exec/spiplistes_listes_toutes.php
// exec/spiplistes_maintenance.php
, 'abonnes' => 'inscripciones'
, '1_abonne' => '1 inscripci&oacute;n'
, 'annulation_chrono_' => "Quitar la programaci&oacute;n (chrono) para "
, 'conseil_sauvegarder_avant' => "<strong>Recomendaci&oacute;n</strong>: haz una copia de seguridad de la base de datos antes de confirmar la supresi&oacute;n
   @objet@. No se podr&aacute; recuperar."
, 'des_formats' => "de los formatos"
, 'des_listes' => "de las listas"
, 'des_abonnements' => "de las inscripciones"
, 'confirmer_supprimer_formats' => "Suprimir el formato de recepci&oacute;n de emails de las inscripciones."
, 'maintenance_objet' => "Mantenimiento @objet@"
, 'nb_abos' => "qt."
, 'pas_de_liste' => "No hay listas del tipo &laquo;env&iacute;o no programado&raquo;."
, 'pas_de_format' => "No se ha definido ning&uacute;n formato de recepci&oacute;n."
, 'pas_de_liste_en_auto' => "No hay listas del tipo &laquo;env&iacute;o programado (chrono)&raquo;."
, 'forcer_formats_' => "Cambiar el formato para recibir emails "
, 'forcer_formats_desc' => "Cambiar el formato para recibir emails de todas las inscripciones..."
, 'modification_objet' => "Modificar @objet@"
, 'Suppression_de__s' => "Suprimir: @s@"
, 'suppression_' => "Suprimir @objet@"
, 'suppression_chronos_' => "Suprimir los env&iacute;os programados (chrono) "
, 'suppression_chronos_desc' => "Si suprimes la temporalizaci&oacute;n (chrono), la lista no ser&aacute; suprimida. Su periodicidad
	se conservar&aacute;, pero el env&iacute;o ser&aacute; suspendido. Para reactivar la temporalizaci&oacute;n (chrono) 
	necesitas se&ntilde;alar una nueva fecha para el primer env&iacute;o. "
, 'Supprimer_les_listes' => "Suprimir las listas"
, 'Supprimer_la_liste' => "Suprimir la lista..."
, 'Suspendre_abonnements' => "Cancelar las inscripciones para esta cuenta"
, 'separateur_de_champ_' => "Separador del campo "
, 'separateur_tabulation' => "tabulaci&oacute;n (<code>\\t</code>)"
, 'separateur_semicolon' => "punto y coma (<code>;</code>)"
, 'nettoyage_' => "Vaciar "
, 'confirmer_nettoyer_abos' => "Confirmar el vaciado de  la tabla de las inscripciones."
, 'pas_de_pb_abonnements' => "No se han encontrado errores en la tabla de inscripciones."
, '_n_abos_' => " @n@ inscripciones "
, '_1_abo_' => " 1 inscripci&oacute; "
, '_n_auteurs_' => " @n@ cuentas "
, '_1_auteur_' => " 1 cuenta "

// exec/spiplistes_menu_navigation.php
// exec/spiplistes_voir_journal.php
// genie/spiplistes_cron.php
// inc/spiplistes_agenda.php
, 'boite_agenda_titre_' => "Planificaci&oacute;n de los boletines "
, 'boite_agenda_legende' => "Sobre @nb_jours@ d&iacute;s"
, 'boite_agenda_voir_jours' => "Ver @nb_jours@ d&iacute;as"

// inc/spiplistes_api.php
// inc/spiplistes_api_abstract_sql.php
// inc/spiplistes_api_courrier.php
// inc/spiplistes_api_globales.php
// inc/spiplistes_api_journal.php
, 'titre_page_voir_journal' => "Informaci&oacute;n de SPIP-Listes"
, 'mode_debug_actif' => "Modo debug activo"

// inc/spiplistes_api_presentation.php
, '_aide' => 'SPIP-Listes te permite enviar boletines informativos autom&aacute;ticos y mensajes colectivos a las personas inscritas. Puedes utilizar una plantilla, escribir un mensaje o prepararlo en HTML. 
<br><br> Las personas inscritas pueden modificar sus opciones en l&iacute;nea: darse de baja, boletines informativos que les interesan y formato en el que quieren recibir sus mensajes (HTML/texto).
<br><br>Los mensajes ser&aacute;n traducidos autom&aacute;ticamente al formato texto para las personas que lo soliciten o que tengan configurada su recepci&oacute;n de correos en modo texto.<br><br><b>Nota:</b><br>El env&iacute;o de mensajes puede tardar unos minutos: los env&iacute;os salen cuando se visita la parte p&uacute;blica de la web. Puedes modificar ese env&iacute;o cliqueando en el enlace "Seguimiento de env&iacute;os".'
, 'envoi_en_cours' => 'Enviando...'
, 'nb_destinataire_sing' => " destinatari@"
, 'nb_destinataire_plur' => " destinatari@s"
, 'aucun_destinataire' => "sin destinatari@"
, '1_liste' => '@n@ lista'
, 'n_listes' => '@n@ listas'
, 'utilisez_formulaire_ci_contre' => "Utiliza el formulario para activar/desactivar esta opci&oacute;n."
, 'texte_boite_en_cours' => 'SPIP-Listes esta enviando emails.<p>Esta nota desaparecer&aacute; cuando el env&iacute;o termine.</p>'
, 'meleuse_suspendue_info' => "Los env&iacute;os pendientes se han detenido."
, 'casier_a_courriers' => "Todos los env&iacute;os" // + courriers_casier
, 'Pas_de_donnees' => "Lo siento, el registro solicitado no existe en la base de datos."
, '_dont_n_sans_format_reception' => ", hay @n@ sin formato de recepci&oacute;n de emails"
, 'mode_simulation' => "Modo simulaci&oacute;n"
, 'mode_simulation_info' => "El modo simulaci&oacute;n est&aacute; activado. Se simula el env&iacute;o de emails. 
	No se env&iacute;a ning&uacute;n email en este modo."
, 'meleuse_suspendue' => "Env&iacute;o suspendido"
, 'Meleuse_reactivee' => "Env&iacute;o reactivado"
, 'nb_abonnes_sing' => " inscripci&oacute;n"
, 'nb_abonnes_plur' => " inscripciones"
, 'nb_moderateur_sing' => " moderador(a)"
, 'nb_moderateur_plur' => " moderadores/as"
, 'aide_en_ligne' => "Ayuda en l&iacute;nea"

// inc/spiplistes_dater_envoi.php
, 'attente_validation' => "en espera de validaci&oacute;n"
, 'courrier_en_cours_' => "Correo edit&aacute;ndose "
, 'date_non_precisee' => "Fecha desconocida"

// inc/spiplistes_destiner_envoi.php
, 'email_tester' => 'Email de prueba'
, 'Choix_non_defini' => 'No hay nada seleccionado.'
, 'Destination' => "Destino"
, 'aucune_liste_dispo' => "No hay listas disponibles."

// inc/spiplistes_import.php
// inc/spiplistes_lister_courriers_listes.php
, 'Prochain_envoi_' => "Pr&oacute;ximo env&iacute;o "

// inc/spiplistes_listes_forcer_abonnement.php
// inc/spiplistes_listes_selectionner_auteur.php
, 'lien_trier_nombre' => "Ordenar por n&uacute;mero de inscripciones"
, 'Abonner_format_html' => "Formato HTML"
, 'Abonner_format_texte' => "Formato texto"
, 'ajouter_un_moderateur' => "A&ntilde;adir moderador o moderadora "
, 'Desabonner' => "Darse de baja"
, 'Pas_adresse_email' => "Sin direcci&oacute;n email"
, 'sup_mod' => "Suprimir este moderador/a"
, 'supprimer_un_abo' => "Suprimir una inscripci&oacute;n de esta lista"
, 'supprimer_cet_abo' => "Suprimir esta inscripci&oacute;n de la lista" // + pipeline
, 'abon_ajouter' => "A&ntilde;adir una inscripci&oacute;n "

// inc/spiplistes_mail.inc.php
// inc/spiplistes_meleuse.php
, 'erreur_sans_destinataire' => 'Error: No se han encontrado destinatari@s para este correo'
, 'envoi_annule' => 'Env&iacute;o anulado'
, 'sans_adresse' => ' Mail no enviado -> Hay que indicar una direcci&oacute;n de respuesta'
, 'erreur_mail' => 'Error: env&iacute;o del mail imposible (comprueba si mail() de php est&aacute; disponible)'
, 'modif_abonnement_text' => 'Si quieres modificar tus datos de inscripci&oacute;n, entra en la siguiente direcci&oacute;n: '
, 'msg_abonne_sans_format' => "no hay formato para la recepci&oacute;n de emails"
, 'modif_abonnement_html' => "<br />Cliquea aqu&iacute; si quieres modificar tus datos de inscripci&oacute;n"

// inc/spiplistes_naviguer_paniers.php
// inc/spiplistes_pipeline_I2_cfg_form.php
// inc/spiplistes_pipeline_affiche_milieu.php
, 'Adresse_email_obligatoire' => "Se necesita una direcci&oacute;n de email para inscribirse en los boletines informativos. "
, 'Alert_abonnement_sans_format' => "Tu inscripci&oacute;n se ha suspendido. No recibir&aacute;s los emails de estos boletines
	informativos. Para recibirlos de nuevo tienes que seleccionar un formato de recepci&oacute;n y validar el formulario. "
, 'abonnements_aux_courriers' => "Inscripciones en los correos"
, 'Forcer_abonnement_erreur' => "Se ha encontrado un error t&eacute;cnico tras la modificaci&oacute;n de una lista de inscripciones. 
	Comprueba esta lista antes de continuar."
, 'Format_obligatoire_pour_diffusion' => "Para confirmar la inscripci&oacute;n, necesitas seleccionar un formato para los emails."
, 'Valider_abonnement' => "Validar est&aacute; inscripci&oacute;n"
, 'vous_etes_abonne_aux_listes_selectionnees_' => "Vous &ecirc;tes abonn&eacute; aux listes s&eacute;lectionn&eacute;es "

// inc/spiplistes_pipeline_ajouter_boutons.php
// inc/spiplistes_pipeline_ajouter_onglets.php
// inc/spiplistes_pipeline_header_prive.php
// inc/spiplistes_pipeline_insert_head.php

// formulaires, patrons, etc.
, 'abo_1_lettre' => 'Bolet&iacute;n informativo '
, 'abonnement_seule_liste_dispo' => "Inscripci&oacute;n en el &uacute;nico bolet&iacute;n disponible "
, 'abo_listes' => 'Abonnement', 'abonnement_0' => 'Inscripci&oacute;n'
, 'abonnement_titre_mail'=> 'Modificar tus datos de inscripci&oacute;n'
, 'votre_abo_listes' => "Tu inscripci&oacute;n en los boletines informativos"
, 'lire' => 'Leer'
, 'listes_de_diffusion_' => "Boletines informativos "
, 'jour' => 'd&iacute;a'
, 'jours' => 'd&iacute;as'
, 'abonnement_bouton'=>'Modificar'
, 'abonnement_cdt' => "<a href='http://bloog.net/?page=spip-listes'>SPIP-Listes</a>"
, 'abonnement_change_format' => "Puedes cambiar el formato para recibir los emails o darte de baja: "
, 'abonnement_texte_mail' => 'Escribe la direcci&oacute;n email con la cual est&aacute;s inscrit@.
	Recibir&aacute;s un email permitiendo modificar tus datos.'
, 'article_entier' => 'Leer el art&iacute;culo completo'
, 'form_forum_identifiants' => 'Confirmar'
, 'form_forum_identifiant_confirm'=> 'Se ha realizado tu inscripci&oacute;n. Recibir&aacute;s un email de confirmaci&oacute;n.'
, 'demande_enregistree_retour_mail' => "
	Tu petici&oacute;n se ha registrado. Recibir&aacute;s un email de confirmaci&oacute;n.
	"
, 'effectuez_modif_validez' => "
	<span>Hola @s@,</span>
	<br />
	Realiza los cambios que desees y despu&eacute;s valida este formulario.
	"
, 'vous_etes_desabonne' => "
	Has sido dado de baja en los boletines informativos,
	pero tu cuenta en este sitio sigue siendo v&aacute;lida. Para volver a este formulario de modificaci&oacute;n
	utiliza el enlace que has recibido o vuelve a poner tu direcci&oacute;n de email en el formulario de inscripci&oacute;n.
	"
, 'inscription_mail_forum' => 'Con estos identificadores puedes conectarte a @nom_site_spip@ (@adresse_site@)'
, 'inscription_mail_redac' => 'Con estos identificadores puedes conectarte a @nom_site_spip@ (@adresse_site@) 
	y entrar en la zona privada (@adresse_site@/ecrire)'
, 'inscription_visiteurs' => 'La inscripci&oacute;n te permite
	intervenir en los foros reservados y recibir boletines informativos.'
, 'inscription_redacteurs' => "La zona privada de este sitio est&aacute; abierta a visitantes inscritos.
	Con tu cuenta podr&aacute;s consultar los art&iacute;culos en curso de redacci&oacute;n, proponer art&iacute;culos
	y participar en todos los foros. La inscripci&oacute;n tambi&ecute;n permite acceder a las zonas restringidas del sitio 
	y recibir boletines y emails informativos."
, 'mail_non' => 'No est&aacute;s inscrita para recibir los correos informativos de @nom_site_spip@'
, 'messages_auto' => 'Forma de env&iacute;o'
, 'nouveaute_intro' => 'Hola, <br />Novedades del sitio'
, 'nom' => 'Nombre'
, 'texte_lettre_information' => 'Correo informativo de '
, 'vous_pouvez_egalement' => 'Tambi&eacute;n puedes'
, 'vous_inscrire_auteur' => 'puedes inscribirte como redactor o redactora'
, 'voir_discussion' => 'Ver la discusi&oacute;n'
, 'inconnu' => 'no hay m&aacute;s inscripciones en la lista'
, 'infos_liste' => 'Informaci&oacute;n de esta lista'
, 'editeur' => 'Redactor/a: '
, 'html_description' => " Texto enriquecido (uso de negrita o cursiva e im&aacute;genes)"
, 'texte_brut' => "Texto simple"
, 'vous_etes_abonne_aux_listes_' => "Est&aacute;s inscrit@ en los boletines informativos:"
, 'vous_etes_abonne_a_la_liste_' => "Est&aacute;s inscrit@ en el bolet&iacute;n informativo:"

// tableau items *_options
, 'Liste_de_destination' => "Listas de destinatari@s"
, 'Listes_1_du_mois' => "P&uacute;blicas, 1<sup><small>o</small></sup> del mes."
, 'Liste_diffusee_le_premier_de_chaque_mois' => "Primer bolet&iacute;n informativo de cada mes. "
, 'Listes_autre' => "Otra periodicidad"
, 'Listes_autre_periode' => "Listas p&uacute;blicas con otra periodicidad"
, 'Listes_diffusion_prive' => "Listas privadas"
, 'Liste_hebdo' => "Lista semanal"
, 'Publiques_hebdos' => "P&uacute;blicas, semanales"
, 'Listes_diffusion_hebdo' => "Listas p&uacute;blicas semanales"
, 'Liste_mensuelle' => "Lista mensual"
, 'Publiques_mensuelles' => "P&uacute;blicas, mensuales"
, 'Listes_diffusion_mensuelle' => "Listas p&uacute;blicas mensuales"
, 'Listes_diffusion_publiques_desc' => "La inscripci&oacute;n a esta lista aparecer&aacute; en la parte p&uacute;blica del sitio."
, 'Liste_annuelle' => "Lista anual"
, 'Publiques_annuelles' => "P&uacute;blicas, anuales"
, 'Listes_diffusion_annuelle' => "Listas p&uacute;blicas anuales"
, 'Listes_diffusion_publique' => 'Boletines informativos p&uacute;blicos'
, 'Listes_diffusion_privees' => 'Boletines informativos privados'
, 'Listes_diffusion_privees_desc' => "La inscripci&oacute;n a estas listas esta reservada a administradoras y redactoras."
, 'Listes_diffusion_suspendue' => 'Boletines informativos suspendidos'
, 'Listes_diffusion_suspendue_desc' => " "
, 'Courriers_en_cours_de_redaction' => 'Correos en curso de redacci&oacute;n'
, 'Courriers_en_cours_denvoi' => 'Env&iacute;os activos'
, 'Courriers_prets_a_etre_envoye' => "Env&iacute;os preparados"
, 'Courriers_publies' => "Env&iacute;os programados y realizados"
, 'Courriers_auto_publies' => "Env&iacute;os directos realizados"
, 'Courriers_stope' => "Env&iacute;os activos, pero detenidos"
, 'Courriers_vides' => "Env&iacute;os suspendidos (vac&iacute;os)"
, 'Courriers_sans_destinataire' => "Env&iacute;os sin destinatari@ (lista vac&iacute;a)"
, 'Courriers_sans_liste' => "Env&iacute;os sin inscripciones (no hay lista)"
, 'devenir_redac'=>'Ser redactor o redactora del sitio'
, 'devenir_membre'=>'Bolet&iacute;n de inscripci&oacute;n'
, 'devenir_abonne' => "Inscribirse"
, 'desabonnement_valid'=>'Esta direcci&oacute;n de email no est&aacute; inscrita en este correo informativo' 
, 'pass_recevoir_mail'=>'Recibir&aacute;s un email vous indic&aacute;ndote como modificar tu inscripci&oacute;n. '
, 'discussion_intro' => 'Hola, <br />Estas son las discusiones iniciadas en el sitio'
, 'En_redaction' => "Correos en curso de redacci&oacute;n"
, 'En_cours' => "En curso"
, 'editeur_nom' => "Nombre "
, 'editeur_adresse' => "Direcci&oacute;n "
, 'editeur_rcs' => "N&deg; RCS "
, 'editeur_siret' => "N&deg; SIRET "
, 'editeur_url' => "URL del sitio "
, 'editeur_logo' => "URL del logotipo "
, 'Envoi_abandonne' => "Env&iacute;o suspendido"
, 'Liste_prive' => "Lista privada"
, 'Liste_publique' => "Lista p&uacute;blica"
, 'message_redac' => 'En curso de redacci&oacute;n y preparado para el env&iacute;o'
, 'Prets_a_envoi' => "Preparados para el env&iacute;o"
, 'Publies' => "Env&iacute;os programados"
, 'publies_auto' => "Env&iacute;os directos"
, 'Stoppes' => "Detenidos"
, 'Sans_destinataire' => "Sin destinatari@"
, 'Sans_abonnement' => "Sin inscripci&oacute;n"
, 'sans_abonne' => "sin inscritas"
, 'sans_moderateur' => "sin moderaci&oacute;n"

// raccourcis des paniers
, 'aller_au_panier_' => "Ir a"
, 'aller_aux_listes_' => "ir a las listas "
, 'Nouveau_courrier' => 'Nuevo correo'
, 'Nouvelle_liste_de_diffusion' => 'Nuevo bolet&iacute;n informativo'
, 'trieuse_suspendue' => "Ordenaci&oacute;n suspendida"
, 'trieuse_suspendue_info' => "El tratamiento de los boletines informativos programados se ha suspendido."
, 'Trieuse_reactivee' => "Ordenaci&oacute;n reactivada"

// mots
, 'ajout' => "A&ntilde;adido"
, 'aucun' => "ninguno"
, 'Configuration' => 'Configuraci&oacute;n'
, 'courriers' => 'Correos'
, 'creation' => "Creaci&oacute;n"
, '_de_' => " de "
, 'email' => 'Email'
, 'format' => 'Formato'
, 'modifier' => 'Modificar'
, 'max_' => "Max "
, 'Patrons' => 'Plantillas'
, 'patron_' => "Plantilla: "
, 'spiplistes' => "SPIP-Listes"
, 'recherche' => 'Buscar'
, 'retablir' => "Restablecer"
, 'site' => 'Sitio web'
, 'sujets' => 'T&iacute;tulos'
, 'sup_' => "Sup."
, 'total' => "Total "
, 'voir' => 'ver'
, 'Vides' => "Vac&iacute;os"
, 'choisir' => 'Seleccionar'
, 'desabo' => 'dado de baja'
, 'desabonnement' => 'Darse de baja'
, 'desabonnes' => 'Dad@s de baja'
, 'destinataire' => 'destinatari@'
, 'destinataires' => 'Destinatari@s'
, 'erreur' => 'Error'
, 'html' => 'HTML'
, 'retour_link' => 'Volver'
, 'texte' => 'Texto'
, 'version' => 'versi&oacuten'
, 'fichier_' => "Archivo "

, 'jquery_inactif' => "jQuery no encontrado. Gracias por activarlo."

///////
// a priori, pas|plus utilise'
, 'supprime_contact_base' => 'Eliminar definitivamente de la base'
, 'forcer_lot' => 'Realizar el env&iacute;o del siguiente lote'
, 'erreur_destinataire' => 'Error de destinatari@: no se ha enviado'
, 'contacts_lot' => 'Cuentas de este lote'
, 'envoi_fini' => 'Env&iacute;os realizados'
, 'non_courrier' => 'No quedan correos por enviar'
, 'non_html' => 'Tu programa de correo electr&oacute;nico aparentemente no puede mostrar correctamente la versi&oacute;n gr&aacute;fica (HTML) de este email'
, 'envoi_erreur' => 'Error: SPIP-Listes no encuentra destinatari@ para este correo'
, 'email_reponse' => 'Email de respuesta: '
, 'envoi_listes' => 'Enviar a las inscripciones de esta lista: '
, 'confirmer' => 'Confirmar'
, 'listes_emails' => 'Correos informativos'
, 'info_liste_1' => 'lista'
, 'bonjour' => 'Hola,' // deja dans SPIP
, 'envoi_tous' => 'Enviar a todas las inscripciones'
, 'patron_detecte' => '<p><strong>Plantilla encontrada para la versi&oacute;n de texto</strong><p>'
, 'val_texte' => 'Texto'
, 'membres_sans_messages_connecte' => 'No tienes nuevos mensajes'
, 'messages_derniers' => '&Uacute;ltimos mensajes'
, 'pas_abonne_en_ce_moment' => "no est&aacute;s inscrita"
, 'reinitialiser' => 'reinicializar'
, 'mail_a_envoyer' => 'N&uacute;mero de env&iacute;os por realizar: '
, 'lettre_d_information' => 'Correo informativo'
, 'desole' => 'Lo siento'
, 'Historique_des_envois' => 'Hist&oacute;rico de env&iacute;s'
, 'patron_disponibles' => 'Plantillas disponibles'
, 'liste_diff_publiques' => 'Boletines informativos p&uacute;blicos<br /><i>La parte p&uacute;blica del sitio 
	propondr&aacute; la inscripci&oacute;n en estas listas.</i>'
, 'messages_non_lus_grand' => 'No hay nuevos mensajes'
, 'messages_repondre' => 'Responder'
, 'Liste_abandonnee' => "Lista abandonada"
, 'par_date' => 'Por fecha de inscripci&oacute;n'
, 'info_auto' => 'SPIP-Listes para SPIP puede enviar regularmente las novedades del sitio recientemente publicadas (art&iacute;culos, breves, foros,...).'
, 'format2' => 'Formato:'
, 'liste_des_abonnes' => "Lista de inscripciones"
, 'lieu' => 'Localidad'
, 'efface_base' => 'se ha suprimido de las listas y de la base de datos'
, 'lot_suivant' => 'Realizar el env&iacute;o del siguiente lote'
, 'listes_internes' => 'Boletines informativos internos<br /><i>Cuando se va a realizar un env&iacute;o estas listas son mostradas como posibles destinatari@s</i>'
, 'adresses_importees' => "Direcciones importadas"
, 'aff_envoye' => 'Correos enviados'
, 'abonner' => 'inscribirse'
, 'abonnes_liste_int' => 'Inscripciones en listas internas: '
, 'abonnes_liste_pub' => 'Inscripciones en listas p&uacute;blicas: '
, 'actualiser' => 'Actualizar'
, 'a_destination_de_' => 'destinado a '
, 'aff_lettre_auto' => 'Correos inforamtivos enviados'
, 'alerte_edit' => 'Este formulario permite modificar el texto de un email.
	Puedes elegir comenzar importando una plantilla para generar el contenido del mensaje.'
, 'alerte_modif' => '<strong>Despu&ecaute;s de guardar el email, podr&aacute;s modificar su contenido</strong>'
, 'lock' => 'Bloqueo activo: '
, 'Apercu' => "Revisado"
, 'bouton_listes' => 'Correos informativos'
, 'bouton_modifier' => 'Modificar'
, 'dans_jours' => 'en'
, 'charger_le_patron' => 'Crear el email'
, 'choix_defini' => 'No hay nada seleccionado'
, 'definir_squel_choix' => 'Al redactar un nuevo mensaje, SPIP-Listes te permite seleccionar una plantilla. Cliqueando en el bot&oacute;n, ver&aacute;s el contenido del mensaje en \'una de las plantillas del directorio <b>/patrons</b> (situado en la ra&iacute;z de tu sitio Spip). <p><b>Puedes personalizar estas plantillas a tu gusto.</b></p> <UL><li>Las plantillas pueden hacerse con HTML cl&aacute;sico</li>
<li>La plantilla tambi&eacute;n puede contener bucles SPIP</li>
<li>Despu&eacute;s de preparar el mensaje, puedes volver a editarlo antes de enviarlo (para modificar su contenido)</li>
</ul><p>La funci&oacute;n "seleccionar una plantilla" permite utilizar plantillas HTML personalizadas pata tus mensajes o crear boletines de informaci&oacute;n tem&aacute;ticos ya que el contenido lo pueden gestionar los bucles de SPIP.</p><p>Importante: el esqueleto no debe contener balizas como body, head ou html.</p>'
, 'definir_squel' => 'Seleccionar la plantilla de correo a previsualizar'
, 'courrier_realise_avec_spiplistes' => "Email generado con SPIP-Listes"
, 'definir_squel_texte' => 'Si puedes acceder por FTP, puedes a&ntilde;adir esqueletos SPIP en el directorio /patrons (situada en la ra&iacute;z de tu sitio Spip)
.'
, 'dernier_envoi'=>'&Uacute;ltimo env&iacute;o'
, 'desabonnement_confirm'=>'Est&aacute;s a punto de darte de baja de los correos informativos'
, 'date_depuis'=>'desde @delai@'
, 'envoi_charset' => 'Juego de caracteres para le env&iacute;o'
, 'envoi_nouv' => 'Env&iacute;o de novedades'
, 'envoi_program' => 'Env&iacute;o programado'
, 'envoi_smtp' => 'Para realizar env&iacute;os por SMTP se necesita en este campo la direcci&oacute;n email de quien realiza el env&iacute;o.'
, 'envoi_texte' => 'Si ce courrier vous convient, vous pouvez l\'envoyer'
, 'email_envoi' => 'Env&iacute;o de emails'
, 'envoi' => 'Env&iacute;o:'
, 'erreur_install' => '<h3>error: spip-listes est&aacute; mal instalado!</h3>'
, 'erreur_install2' => '<p>Comprueba los pasos de la instalaci&oacute;n, sobretodo si has renombrado <i>mes_options.txt</i> como <i>mes_options.php</i>.</p>'
, 'exporter' => 'Exportar la lista de inscripciones'
, 'Erreur_appel_courrier' => "Error enviando el correo"
, 'faq' => 'FAQ'
, 'forum' => 'Foro'
, 'ferme' => 'Discusi&oacute;n cerrada'
, 'gestion_du_courrier' => "Gesti&oacute;n del correo"

, 'info_heberg' => 'Algunos servidores desactivan el env&iacute;o programado de emails desde sus servidores.
	En ese caso las siguientes funcionalidades de SPIP-Listes pour SPIP no funcionar&aacute;n'
, 'info_nouv' => 'Has activado el env&iacute;o de novedades'
, 'info_nouv_texte' => 'Pr&oacute;ximo env&iacute;o en @proch@ d&iacute;as'
, 'log' => 'Logs'
, 'login' => 'Conectarse'
, 'logout' => 'Desconectarse'
, 'mail_format' => 'Est&aacute;s inscrit@ para recibir los novedades de @nom_site_spip@ en formato'
, 'messages_auto_texte' => '<p>Por defecto la plantilla \'nouveautes.html\' permite \'enviar autom&aacute;ticamente la lista de los art&iacute;culos y breves publicados en el sitio desde el &uacute;ltimo env&iacute;o. </p><p>Puedes personalizar el mensaje incluyendo una \'direcci&oacute;n, \'un logo o \'una imagen de fondo para los t&iacute;tulos de las secciones editando el archivo <b>"nouveautes.html"</b> (situado en el directorio \'/patrons\' en la ra&iacute;z de tu sitio Spip).</p>'
, 'membres_groupes' => 'Grupos de usuari@s'
, 'membres_profil' => 'Perfil'
, 'membres_messages_deconnecte' => 'Con&eacute;ctate para verificar los mensajes privados'
, 'membres_avec_messages_connecte' => 'Tienes @nombres@ nuevo(x) mensaje(s)'
, 'message' => 'Mensaje: '
, 'message_date' => 'Enviado '
, 'messages' => 'Mensajes'
, 'messages_forum_clos' => 'Foro desactivado'
, 'messages_nouveaux' => 'Nuevos mensajes'
, 'messages_pas_nouveaux' => 'No hay nuevos mensajes'
, 'messages_voir_dernier' => 'Ver el &uacute;ltimo mensaje'
, 'moderateurs' => "Moderan"
, 'mis_a_jour' => 'Actualizado'
, 'nouveaux_messages' => 'Nuevos mensajes'
, 'numero' => 'N&nbsp;'
, 'photos' => 'Fotos'
, 'poster' => 'Enviar un mensaje'
, 'publie' => 'Publicado'
, 'aucune_liste_publique' => "No hay boletines informativos disponibles."
, 'revenir_haut' => 'Subir'
, 'reponse' => 'Repondiendo al mensaje'
, 'reponse_plur' => 'respuestas'
, 'reponse_sing' => 'respuesta'
, 'retour' => 'Direcci&oacute;n de correo electr&oacute;nico de administraci&oacute;n de la lista (reply-to)'
, 'Suivi_des_abonnements' => 'Seguimiento de inscripciones'
, 'sujet_nouveau' => 'Nuevo t&iacute;tulo'
, 'sujet_auteur' => 'Autor o autora'
, 'sujet_visites' => 'Visitas'
, 'sujet_courrier_auto' => 'T&iacute;tulo del email: '
, 'sujets_aucun' => 'Por ahora no hay mensajes para este foro'
, 'sujet_clos_titre' => 'Tema cerrado'
, 'sujet_clos_texte' => 'Este tema est&aacute; cerrado, no se pueden enviar mensajes.'
, 'masquer_le_journal_SPIPLISTES' => "No mostrar la informaci&oacute;n de SPIP-Listes"
, 'abon' => 'L@S INSCRIT@S'
, 'abonees' => 'tod@s l@s inscrit@s'
, 'abonnement_newsletter' => '<strong>Inscripci&oacute;n en el correo informativo</strong>'
, 'acces_a_la_page' => 'No puedes acceder a esta p&aacute;gina.'
, 'adresse_deja_inclus' => 'Direcci&oacute;n desconocida'
, 'Choisir_cette_liste' => 'Seleccionar esta lista'
, 'Charger_un_patron' => "Seleccionar una plantilla"
, 'date_ref' => 'Fecha de referencia'
, 'efface' => 'has sido suprimid@ de las listas y de la base de datos'
, 'email_collec' => 'Reenviar un correo'
, 'email_test_liste' => 'Enviar a un bolet&iacute;n informativo'
, 'envoyer' => 'enviar el email'
, 'envoyer_a' => 'Enviar a '
, 'listes_poubelle' => 'Boletines informativos en la papelera'
, 'Liste_numero_:' => 'Lista n&uacute;mero:'
, 'mail_tache_courante' => 'Emails enviados ahora: '
, 'messages_auto_envoye' => 'Correos directos enviados'
, 'nb_abonnes' => 'En las listas: '
, 'nb_inscrits' => 'En el sitio:  '
, 'nb_listes' => 'Incripciones en todas las listas: '
, 'nouvelle_abonne' => 'La siguiente cuenta se ha a&ntilde;adido a la lista'
, 'pas_acces' => 'No puedes acceder a esta p&aacute;gina.'
, 'plus_abonne' => ' no hay m&aacute;s incripciones en la lista '
, 'prochain_envoi_aujd' => 'Pr&oacute;ximo env&iacute;o hoy'
, 'prochain_envoi_prevu_dans' => 'Pr&oacute;ximo env&iacute;o en '
, 'program' => 'Programaci&oacute;n de correos'
, 'plein_ecran' => "(Pantalla completa)"
, 'remplir_tout' => 'No puede haber campos vac&iacute;os'
, 'repartition' => 'Distribuci&oacute;n'
, 'squel' => 'Plantilla: &nbsp;'
, 'suivi_envois' => 'Seguimiento de env&iacute;os'
, 'supprime_contact' => 'Suprimir este contacto definitivamente'
, 'tableau_bord' => 'Cuadro resumen'
, 'toutes' => 'Todas las inscripciones'
, 'acces_refuse' => 'No puedes acceder a este sitio'
, 'confirmation_format' => ' en formato '
, 'confirmation_liste_unique_1' => 'Est&aacute;s inscrit@ para recibir los boletines informativos de este sitio'
, 'confirmation_liste_unique_2' =>'Has elegido recibir los correos dirigidos a la siguiente lista:'
, 'confirmation_listes_multiples_1' => 'Est&aacute;s inscrit@ para recibir los boletines informativos de este sitio'
, 'confirmation_listes_multiples_2' => 'Has elegido recibir los correos dirigidos a las siguientes listas:'
, 'contacts' => 'N&uacute;mero de cuentas'
, 'patron_erreur' => 'La plantilla seleccionada no funciona con las opciones actualmente seleccionadas'
, 'abonees_titre' => 'Inscripciones'
, 'options' => 'radio|plano|Formato:|Html,Texto,Darse de baja|html,texto,no'

);


?>
