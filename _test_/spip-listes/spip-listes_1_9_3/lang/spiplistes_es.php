<?php

// Este es un archivo para SPIP -- This is a SPIP module file  --  Ceci est un fichier module de SPIP

$GLOBALS['i18n_spiplistes_es'] = array(

//_
'_aide' => 'SPIP-Listes te permite enviar boletines informativos autom&aacute;ticos y mensajes colectivos a las personas inscritas. Puedes utilizar una plantilla, escribir un mensaje o prepararlo en HTML. 
<br><br> Las personas inscritas pueden modificar sus opciones en l&iacute;nea: darse de baja, boletines informativos que les interesan y formato en el que quieren recibir sus mensajes (HTML/texto).
<br><br>Los mensajes ser&aacute;n traducidos autom&aacute;ticamente al formato texto para las personas que lo soliciten o que tengan configurada su recepci&oacute;n de correos en modo texto.<br><br><b>Nota:</b><br>El env&iacute;o de mensajes puede tardar unos minutos: los env&iacute;os salen cuando se visita la parte p&uacute;blica de la web. Puedes forzar ese env&iacute;o cliqueando en el enlace "Seguimiento de env&iacute;os".',

// A
'abo_1_lettre' => '<b>Recibir boletines y correos con las novedades del sitio </b>',
'abonnement_0' => 'Inscripci&oacute;n',
'abonnement' => 'Inscripci&oacute;n',
'abonnement_mod'=>'Modificar tu inscripci&oacute;n',
'abonnement_bouton'=>'Modificar',
'abonnement_cdt'=>'<a href=\'http://bloog.net\'>SPIP-Listes</a>' ,
'abonnement_change_format'=>'Puedes cambiar las boletines informativos y el formato en el que recibes las novedades y los mensajes, o darte de baja. ',
'abonnement_mail'=>'Para modificar tu inscripci&oacute;n, cliquea en este enlace',
'abonnement_modifie'=>'Los cambios se han guardado',
'abonnement_nouveau_format'=>'Tu nuevo formato para recibir nuestras novedades e informaciones: ',
'abonnement_titre_mail'=>'Modificar tu inscripci&oacute;n',
'abonnement_texte_mail'=>'Escribe la direcci&oacute;n de correo electr&oacute;nico con la que est&aacute;s inscrito(a). 
Te enviaremos un correo electr&oacute;nico con el enlace a la p&aacute;gina en la que podr&aacute;s hacer los cambios.',
'abonnement_mail_passcookie'=>'(este es un mensaje autom&aacute;tico)

Hola. En el siguiente enlace:

@adresse_site@/spip.php?page=abonnement&d=@cookie@

podr&aacute;s cambiar el formato de recepci&oacute;n de los mensajes y novedades de
"@nom_site_spip@" (@adresse_site@) o darte de baja.

Un saludo
  
    ',

'abonner' => 'inscribirse',
'abonnement_modifie'=>'Las modificaciones se han realizado',
'abonnement_nouveau_format'=>'Tu formato para recibir emails a partir de ahora: ',
'abonnement_titre_mail'=>'Modificar tus datos de inscripci&oacute;n',
'abonnement_texte_mail'=>'Indica l\'adresse la direcci&oacute;n de email con la que estabas inscrit@. 
Recibir&aacute;s un email para acceder a la p&aacute;gina de modificaci&oacute;n de tus datos de inscripci&oacute;n.',
'abonnes_liste_int' => '<br />Inscripciones en boletines internos: ',
'abonnes_liste_pub' => '<p>Inscripciones en boletines p&uacute;blicos: ',
'actualiser' => 'Actualizar esta p&aacute;gina',
'a_destination' => 'enviar a ',
'adresse' => 'Direcci&oacute;n de correo electr&oacute;nico para responder al mensaje (por defecto, la direcci&oacute;n del/la webmestre/webmistress ser&aacute; utilizada como direcci&oacute;n de respuesta) :',
'adresse_envoi' => 'Remitente por defecto',
'adresses_importees' => 'Direcciones importantes',
'adresse_smtp' => 'direcci&oacute;n email del <i>sender</i> SMTP',
'aff_redac' => 'Mensajes en curso de redacci&oacute;n',
'aff_encours' => 'Mensajes a enviar',
'aff_envoye' => 'Mensajes enviados "manualmente"',
'aff_lettre_auto' => 'Boletines informativos enviados de forma programada',
'aff_redac' => 'Mensajes en curso de redacci&oacute;n',
'alerte_edit' => 'Atenci&oacute;n: este mensaje puede ser modificado por l@s administradore(a)s y lo recibir&aacute;n todas las personas inscritas para recibir el bolet&iacute;n informativo (puedes seleccionarla en el siguiente paso).<br />No utilices los mensajes colectivos nada m&aacute;s que para acontecimientos importantes de la vida del sitio.',
'alerte_modif' => '<b>Despu&eacute;s de \'visualizar el mensaje\',<br />podr&aacute;s modificar su contenido</b>',
'annuler_envoi' => 'Cancelar el env&iacute;o',
'article_entier' => 'Leer el art&iacute;culo entero',

// B

'bouton_listes' => 'Boletines de informaci&oacute;n',
'bouton_modifier' => 'Modificar este mensaje',

//C
'calcul_patron' => 'Calcular la versi&oacute;n texto con la plantilla',
'calcul_html' => 'Calcular desde la versi&oacute;n HTML del mensaje',
'Cette_liste_est' => 'Bolet&iacute;n informativo ',
'charger_le_patron' => 'Visualizar el mensaje',
'charger_patron' => 'Elegir una plantilla',
'choix_defini' => 'Nada seleccionado.\n',
'Configuration' => 'Configuraci&oacute;n',
'courriers' => 'Mensajes',

//D
'dans_jours' => 'en',
'definir_squel' => 'Elegir la plantilla y el formato a previsualizar',
'definir_squel_choix' => 'Al redactar un nuevo mensaje, SPIP-Listes te permite seleccionar una plantilla. Cliqueadno en el bot&oacute;n, ver&aacute;s el contenido del mensaje en \'una de las plantillas del directorio <b>/patrons</b> (situado en la ra&iacute;z de tu sitio Spip). <p><b>Puedes personalizar estas plantillas a tu gusto.</b></p> <UL><li>Las plantillas pueden hacerse con HTML cl&aacute;sico</li>
<li>La plantilla tambi&eacute;n puede contener bucles SPIP</li>
<li>Despu&eacute;s de preparar el mensaje, puedes volver a editarlo antes de enviarlo (para modificar su contenido)</li>
</ul><p>La funci&oacute;n "elegir una plantilla" permite utilizar plantillas HTML personalizadas pata tus mensajes o crear boletines de informaci&oacute;n tem&aacute;ticos ya que el contenido lo pueden gestionar los bucles de SPIP.</p><p>Atenci&oacuten: ce squelette ne doit pas contenir de balises body, head ou html mais juste du code HTML ou des boucles Spip.</p>',
'definir_squel_texte' => 'Si dispones de acceso por FTP, puedes a&ntilde;adir esqueletos SPIP (u otras plantillas) en el directorio /patrons (en la ra&iacute;z de vuestro sitio Spip).',
'dernier_envoi'=>'&Uacute;ltimo env&iacute;o hace',
'devenir_redac'=>'Ser redactor(a) de este sitio',
'devenir_abonne'=>'Bolet&iacute;n de inscripci&oacute;n',
'desabonnement'=>'No recibir novedades del sitio por email' ,
'desabonnement_valid'=>'La siguiente direcci&oacuten no est&aacute; inscrita para recibir nuestras novedades e informaciones' ,
'pass_recevoir_mail'=>'Hemos enviado un correo electr&oacute;nico para que puedas cambiar tu inscripci&oacute;n.',
'desabonnement_confirm'=>'Vas a darte de baja para recibir las novedades de este sitio',
'date_depuis'=>'desde @delai@', //FIX ME
'discussion_intro' => 'Hola, <br />Estos son los comentarios del sitio',

//E
'email' => 'Mensaje',
'email_envoi' => 'Env&iacute;o de emails',
'envoi' => 'Env&iacute;o:',
'envoi_charset' => 'Charset del env&iacute;o',
'envoi_date' => 'Fecha del env&iacute;o: ',
'envoi_debut' => 'Comienzo del env&iacute;o: ',
'envoi_fin' => 'Fin del env&iacute;o: ',
'envoi_nouv' => 'Env&iacute;o de novedades',
'envoi_patron' => 'Env&iacute;o de la plantilla',
'envoi_program' => 'Env&iacute;o programado',
'envoi_smtp' => 'Lors d\'un envoi via la m&eacute;thode SMTP ce champ d&eacute;finit l\'adresse de l\'envoyeur.',
'envoi_texte' => 'Si has terminado tu mensaje, puedes enviarlo ya con una de estas dos opciones:',
'erreur_envoi' => 'N&uacute;mero de env&iacute;os fallados: ',
'erreur_install' => '<h3>error: spip-listes est&aacute; mal instalado!</h3>',
'erreur_install2' => '<p>Comprobar los pasos de la instalaci&oacute;n, sobre todo si has renombrado bien <i>mes_options.txt</i> como <i>mes_options.php</i>.</p>',
'exporter' => 'Exportar las inscripciones del bolet&iacute;n informativo',

//F
'faq' => 'FAQ',
'forum' => 'Foro',
'ferme' => 'Este foro est&aacute; cerrado',
'form_forum_identifiants' => 'Confirmaci&oacute;n',
'form_forum_identifiant_confirm'=>'Tu inscripci&oacute;n est&aacute; guardada, recibir&aacute;s un mensaje de confirmaci&oacute;n.',
'forum' => 'Foro',
'ferme' => 'Este foro est&aacute; cerrado',
'form_forum_identifiants' => 'Confirmaci&0acute;n',
'format' => 'Formato',
'format2' => 'Formato:',
'format_html' => 'Formato html: ',
'format_texte' => 'Formato texto: ',

//H
'Historique_des_envois' => 'Env&iacute;os',

//I
'info_auto' => 'SPIP-Listes para SPIP puede enviar peri&oacute;dicamente a l@s inscrit@s, la \'informaci&oacute;n de las &uacute;ltimas novedades del sitio (art&iacute;culos, breves, mensajes en los foros, sitios sindicados,... recientemente publicados).',
'info_heberg' => 'En algunos servidores est&aacute; desactivado el \'env&iacute;o autom&aacute;tico de mensajes. En ese caso, las siguientes opciones de SPIP-Listes para SPIP no funcionar&aacute;n',
'info_nouv' => 'Tienes activado el env&iacute;o de novedades',
'info_nouv_texte' => 'Pr&oacute;simo env&iacute;o de novedades en @proch@ d&iacute;as',
'inscription_mail_forum' => 'Estos son tus identificadores para conectarte a @nom_site_spip@ (@adresse_site@)',
'inscription_mail_redac' => 'Estos son tus identificadores para conectarte a @nom_site_spip@ (@adresse_site@) et l\'interface de rédaction (@adresse_site@/ecrire)',
'inscription_visiteurs' => 'Inscripci&oacute;n s&oacute;lo como visitante',
'inscription_redacteurs' =>'Inscripci&oacute;n como redactor(a)',
'import_export' => 'Importar / Exportar',

//J
'jours' => 'd&iacute;a(s)',

//L
'langue' => '<strong>Idioma: </strong>&nbsp;',
'lettre_d_information' => 'Mensaje informativo',
'lieu' => 'Localizaci&oacute;n',
'lire' => 'Leer',
'Listes_de_diffusion' => 'Boletines de informaci&oacute;n',
'login' => 'Conexi&oacute;n',
'logout' => 'Desconexi&oacute;n',
'log' => 'Logs',
'lot_suivant' => 'Enviar siguiente lote',
'liste_numero' => 'BOLET&Iacute;N N&Uacute;MERO:',

//M
'mail_format' => 'Est&aacute;s inscrit@ para recibir los novedades de @nom_site_spip@ en formato',
'mail_non' => 'No est&aacute;s inscrit@ para recibir las novedades de @nom_site_spip@',
'membres_avec_messages_connecte' => 'Tienes @nombres@ nuevo(s) mensaje(s)',
'membres_groupes' => 'Grupos de usuari@s',
'membres_liste' => 'Inscripciones',
'membres_messages_deconnecte' => 'Con&eacute;ctate para verificar los mensajes privados',
'membres_profil' => 'Perfil',
'membres_sans_messages_connecte' => 'No hay nuevos mensajes',
'message' => 'Mensaje: ',
'message_arch' => 'Mensaje guardado','message_date' => 'Enviarlo ',
'messages_auto' => 'Mensajes autom&aacute;ticos',
'messages_auto_texte' => '<p>Por defecto la plantilla \'nouveautes.html\' permite \'enviar autom&aacute;ticamente la lista de los art&iacute;culos y breves publicados en el sitio desde el &uacute;ltimo env&iacute;o. </p><p>puedes personalizar el mensaje incluyendo una \'direcci&oacute;n, \'un logo o \'una imagen de fondo para los t&iacute;tulos de las secciones editando el archivo <b>"nouveautes.html"</b> (situado en el directorio \'/patrons\' en la ra&iacute;z de tu sitio Spip).</p>',
'message_redac' => 'En curso de redacci&oacute;n y pendiente de ser enviado',
'message_sujet' => 'T&iacute;tulo',
'message_type' => 'Correo electr&oacute;nico',
'messages_auto_envoye' => 'Novedades enviadas',
'message_en_cours' => 'Mensaje para enviar',
'messages' => 'Mensajes',
'Messages_automatiques' => 'Programaci&oacute;n de env&iacute;os de boletines informativos',
'messages_derniers' => '&uacute;ltimos mensajes',
'messages_forum_clos' => 'Foro desactivado',
'messages_non_lus_grand' => 'No hay mensajes nuevos',
'messages_nouveaux' => 'Nuevos mensajes',
'messages_pas_nouveaux' => 'No hay mensajes nuevos',
'messages_repondre' => 'Responder',
'messages_voir_dernier' => 'Ver el &uacute;ltimo mensaje',
'methode_envoi' => 'Sistema de env&iacute;o',
'mettre_a_jour' => '<h3>Acutalizar SPIP-listes</h3>',
'moderateurs' => 'Moderadora(e)(s)',
'modifier' => 'Cambiar',
'mis_a_jour' => 'Actualizado',

//N
'nb_abonnes_plur' => ' inscripciones',
'nb_abonnes_sing' => ' inscripxi&oacute;n',
'nbre_abonnes' => 'N&uacute;mero de inscripciones: ',
'nom' => 'Nombre de usuari@',
'nombre_lot' => 'N&uacute;mero de env&iacute;os por lote',
'Nouveau_courrier' => 'Nuevo mensaje',
'nouveaute_intro' => 'Hola, <br />Estas son las novedades publicadas en',
'nouveaux_messages' => 'Nuevos mensajes',
'Nouveau_courrier' => 'Nuevo mensaje',
'Nouvelle_liste_de_diffusion' => 'Nuevo bolet&iacute;n',
'numero' => 'N&nbsp;',

//P
'par_date' => 'Por fecha de inscripci&oacute;n',
'Patrons' => 'Plantillas',
'patron_disponibles' => 'Plantillas disponibles',
'pas_sur' => '<p>Si tienes dudas, elige la funci&oacute;n mail de PHP.</p>',
'photos' => 'Im&aacute;genes',
'php_mail' => 'Utilizar la funci&oacute;n mail() de PHP',
'poster' => 'Env&iacute;ar un mensaje',
'prochain_envoi_prevu' => 'Pr&oacute;ximo env&iacute;o hoy',
'plein_ecran' => '(Pantalla completa)',
'publie' => 'Publicado el',

//R
'recherche' => 'Buscar',
'regulariser' => 'regularizar las inscripciones en los boletines...<br />',
'revenir_haut' => 'Ir al principio de la p&aacute;gina',
'reponse' => 'Respuesta al mensaje',
'reponse_plur' => 'respuestas',
'reponse_sing' => 'respuesta',
'retour' => 'Direcci&oacute;n de correo electr&oacute;nico del bolet&iacute;n (para las respuestas)',

//S
'smtp' => 'Utilizar SMTP',
'spip_ident' => 'Necesitas una identificaci&oacute;n',
'smtp_hote' => 'Servidor',
'smtp_port' => 'Puerto',
'spip_listes' => 'Spip-listes',
'suivi' => 'Seguimiento de inscripciones',
'Suivi_des_abonnements' => 'Inscripciones',
'sujet_nouveau' => 'Nuevo mensaje',
'sujet_auteur' => 'Autor(a)',
'sujet_courrier' => '<b>T&iacute;tulo del mensaje</b> [obligatorio]<br />',
'sujet_courrier_auto' => 'T&iacute;tulo del mensaje autom&aacute;tico: ',
'sujet_visites' => 'Visitas',
'sujets' => 'Temas',
'sujets_aucun' => 'Por ahora no hay mensajes en este foro',
'site' => 'Sitio web',
'sujet_clos_titre' => 'Tema cerrado',
'sujet_clos_texte' => 'Este tema est&aacute; cerrado, no puedes env&iacute;ar mensajes.',
'sur_liste' => 'Con el bolet&iacute;n',
 
 //T
'texte_boite_en_cours' => 'SPIP-Listes tiene mensajes para enviar. <p> Puedes forzar su env&iacute;o entrando en "Revisar tareas pendientes".</p> <p>Este recuadro desaparecer&aacute; cuando acabe el env&iacute;o.</p>',
'texte_courrier' => '<b>Texto del mensaje</b> (HTML autorizado)',
'texte_contenu_pied' => '<br />(Texto a&ntilde;adido al pie de los emails al enviarlos)<br />',
'texte_lettre_information' => 'Informaci&oacute;n de ',
'texte_pied' => '<p><b>Texto de pie de p&aacute;gina</b>',
'Tous_les' => 'cada',

//V
'version_html' => '<b>Versi&oacute;n HTML</b>',
'version_texte' => '<b>Versi&oacute;n texto</b>',
'voir' => 'vista previa',
'vous_pouvez_egalement' => 'Cliquea',
'vous_inscrire_auteur' => 'aqu&iacute; si quieres inscribirte como redactor(a)',
'voir_discussion' => 'Ver el los comentarios',

// ====================== spip_listes.php3 ======================

'abon' => 'INSCRIPCIONES',
'abon_ajouter' => 'A&Ntilde;ADIR INSCRIPCIONES &nbsp; ',
'abonees' => 'Todas las inscripciones',
'abonne_listes' => 'Este contacto est&aacute; inscrito/a en los siguientes boletines',
'abonne_aucune_liste' => 'Inscripciones del sitio sin bolet&iacute;n',
'abonnement_simple' => '<b>Inscripci&oacute;n simple: </b><br><i>Las personas inscritas s&oacute;lo recibir&aacute;n un mensaje de confirmaci&oacute;n</i>',
'abonnement_code_acces' => '<b>Inscripci&oacute;n con identificadores (datos de acceso a la parte privada): </b><br><i>Las personas inscritas recibir&aacute;n un login y una contrase&ntilde;a que les permitir&aacute; acceder a la parte privada del sitio. </i>',
'abonnement_newsletter' => '<b>Inscripci&oacute;n en los boletines</b>',
'acces_a_la_page' => 'No tienes acceso a est&aacute; p&aacute;gina.',
'adresse_deja_inclus' => 'Est&aacute; direcci&oacute;n de correo ya est&aacute; inscrita',
'autorisation_inscription' => 'La bloOgletter vient d\'activer l\'autorisation de s\'inscrire aux visiteurs du site',


'choisir' => 'Seleccionar',
'Choisir_cette_liste' => 'Enviarlo a este grupo',
'confirme_envoi' => 'Para confirmar el env&iacute;o ve hasta el final.',

'date_act' => 'Datos actualizados',
'date_ref' => 'Fecha de referencia',
'desabo' => 'baja',
'desabonnes' => 'Sin inscripci&oacute;n',
'desole' => 'Desolado/a',
'destinataire' => 'destinatari@',
'destinataires' => 'Destinatari@s',

'efface' => 'ha sido borrado/a de la base de datos\"',
'efface_base' => 'Ha sido borrado/a de los boletines informativos y de la base de datos',
'email_adresse' => 'Direcci&oacute;n de prueba',
'email_collec' => 'Mensaje colectivo',
'email_test' => 'Env&iacute;ar un correo de prueba',
'email_test_liste' => 'Enviarlo a un bolet&iacute;n',
'email_tester' => 'Enviarlo a esta direcci&oacute;n',
'env_maint' => 'Enviar ahora',
'env_esquel' => 'Env&iacute;o programado con la plantilla ',
'envoyer' => 'enviar el correo',
'envoyer_a' => 'Enviar a',
'erreur' => 'Error',
'erreur_import' => 'El archivo de importaci&oacute;n tiene un error en la l&iacute;nea ',

'format_date' => 'd-m-Y',

'html' => 'HTML',

'importer' => 'Importar inscripciones',
'importer_fichier' => 'Importar un archivo',
'importer_fichier_txt' => '<p><b>Las inscripciones a importar deben estar en un fichero de texto que s&oacute;lo tenga una direcci&oacute;n por l&iacute;nea</b></p>',
'importer_preciser' => '<p>Selecciona los boletines informativos y el formato para las nuevas inscripciones</p>',
'inconnu' => 'no est&aacute; inscrit@ en el bolet&iacute;n',

'liste_diff_publiques' => 'Boletines p&uacute;blicos<br><i>En la parte p&uacute;blica del sitio se propone la inscripci&oacute;n a estos boletines.</i>',
'liste_sans_titre' => 'Bolet&iacute;n sin t&iacute;tulo',
'listes_internes' => 'Boletines informativos internos<br /><i>Al enviar un mensaje, estos boletines son propuestos entre los posibles destinatarios</i>',
'listes_poubelle' => 'Boletines en la papelera',
'lock' => 'Bloqueo activo: ',

'mail_a_envoyer' => 'N&uacute;mero de correos a enviar: ',
'mail_tache_courante' => 'Correos enviados en este lote: ',
'messages_auto_envoye' => 'Env&iacute;os autom&aacute;ticos de los boletines ya realizado',
'message_en_cours' => 'Mensaje NO enviado',
'message_presque_envoye' =>'Mensaje preparado para ser enviado.',
'mode_inscription' => 'Tipo de inscripci&oacute;n',
'modif_envoi' => 'Puedes modificarlo o enviarlo con las opciones del final.',
'modifier_liste' => 'Modificar las inscripciones de este bolet&iacute;n',

'nb_abonnes' => 'En los boletines: ',
'nb_inscrits' => 'En el sitio:  ',
'nb_listes' => 'Entre todas los boletines: ',
'Pas_de_courrier_auto_programme' => 'No hay env&iacute;os programados para este bolet&iacute;n.',
'nouvelle_abonne' => 'La siguiente inscripci&oacute;n ha sido a&ntilde;adida al bolet&iacute;n',

'pas_acces' => 'No tienes acceso permitido a esta p&aacute;gina.',
'plus_abonne' => ' No est&aacute;s inscrit@ en este bolet&iacute;n ',
'prochain_envoi_aujd' => 'Pr&oacute;ximo env&iacute;o hoy',
'prochain_envoi_prevu' => 'Pr&oacute;ximo env&iacute;o',
'prochain_envoi_prevu_dans' => 'P&oacute;ximo env&iacute;o en ',
'prog_env' => 'Programar un env&iacute;o',
'prog_env_non' => 'No programar el env&iacute;o',
'program' => 'Programaci&oacute;n de env&iacute;os',

'reinitialiser' => 'Actualizar',
'remplir_tout' => 'Tous les champs doivent être remplis',
'repartition' => 'Distribuci&oacute;n',
'retour_link' => 'Volver',

'Erreur_Adresse_email_inconnue' => 'La direcci&oacute;n de correo que has escrito no corresponde con ninguna inscripci&oacute;n, <br />No puede hacerse el env&iacute;o, int&eacute;ntalo otra vez<br /><br />',
'squel' => 'Plantilla: &nbsp;',
'statut_interne' => 'Interna',
'statut_publique' => 'P&uacute;blica',
'suivi_envois' => 'Revisar tareas pendientes',
'supprime_contact' => 'Suprimir esta inscripci&oacute;n definitivamente',
'supprime_contact_base' => 'Borrarlo definitivamente de la base de datos',

'tableau_bord' => 'Cuadro resumen',
'texte' => 'texto',
'toutes' => 'Todas las inscripciones de este sitio',
'txt_inscription' => 'Texto descriptivo del bolet&iacute;n informativo: ',
'txt_abonnement' => '(Indica aqu&iacute; el texto para la inscripci&oacute;n en este bolet&iacute;n, se ver&aacute; en la zona p&uacute;blica si el bolet&iacute;n es p&uacute;blico)',

'une_inscription' => 'Se ha encontrado una inscripci&oacute;n',

'val_texte' => 'Texto',
'version' => 'versi&oacute;n',
'voir_historique' => 'Ver todos los env&iacute;os',


// ====================== inscription-listes.php3 / abonnement.php3 ======================

'abo_listes' => '<b>Boletines informativos para recibir las novedades de este sitio </b>',
'acces_refuse' => 'No tienes acceso a este sitio',
'confirmation_format' => ' en formato ',
'confirmation_liste_unique_1' => 'Est&aacute;s inscrit@ para recibir los boletines informativos de este sitio',
'confirmation_liste_unique_2' =>'Has seleccionado recibir el bolet&iacute;n informativo:',
'confirmation_listes_multiples_1' => 'Est&aacute;s inscri@ en los boletines informativos de este sitio ',
'confirmation_listes_multiples_2' => 'Has seleccionado recibir los mensajes de los siguientes boletines informativos:',

'Erreur_Adresse_email_invalide' => 'Error, la direcci&oacute;n que has escrito no es v&aacute;lida',

'infos_liste' => 'Informaci&oacute;n sobre este bolet&iacute;n informativo',


// ====================== spip-meleuse.php3 ======================

'contacts' => 'N&uacute;mero de inscripciones',
'contacts_lot' => 'Env&iacute;os en este lote',
'editeur' => 'Enviado por: ',
'envoi_en_cours' => 'Realiz&aacute;ndose el env&iacute;o',
'envoi_tous' => 'Env&iacute a todas las inscripciones del sitio',
'envoi_listes' => 'Env&iacute;o a todas las inscripciones del bolet&iacute;n: ',
'envoi_erreur' => 'Error : SPIP-listes no encuentra destinatari@ para este mensaje',
'email_reponse' => 'Direcci&oacute;n de respuesta: ',
'envoi_annule' => 'Env&iacute;o cancelado',
'envoi_fini' => 'Env&iacute;os acabados',
'erreur_destinataire' => 'Error de destinatari@: no enviado',
'erreur_sans_destinataire' => 'Error: no hay destinatari@ para este mensaje',
'erreur_mail' => 'Error: no se puede enviar el mensaje (comprueba si mail() de php est&aacute; disponible)',

'forcer_lot' => 'Enviar el siguiente lote',

'non_courrier' => 'No quedan correos por enviar',
'non_html' => 'Puede que tu aplicaci&oacute;n para correo electr&oacute;nico no pueda mostrar la versi&oacute;n gr&aacute;fica (HTML) de este mensaje',

'sans_adresse' => 'Correo no enviado -> No hay direcci&oacute;n de respuesta',



// ====================== inc_import_patron.php3 ======================

'confirmer' => 'Confirmar',

'lettre_info' => 'Informaci&oacute;n de ',

'patron_erreur' => 'Puede que tu mensaje tenga menos de diez car&aacute;cteres o por las opciones que tienes seleccionadas haya alg&uacute;n error en el contenido mensaje.',


// ====================== listes.html ======================

'abonees_titre' => 'Inscripciones',


// ====================== inc-presentation.php3 ======================

'listes_emails' => 'Boletines informativos<br />y mensajes colectivos',


// ====================== mes-options.php3 ======================

'bonjour' => 'Hola,',

'inscription_response' => 'Est&aacute;s inscrit@ en el bolet&iacute;n informativo ',
'inscription_responses' => 'Est&aacute;s inscrit@ en los boletines ',
'inscription_liste' => 'Has seleccionado recibir los boletines informativos: ',
'inscription_listes' => 'Has seleccionado recibir los boletines informativos: ',
'inscription_format' => ' en formato ',

'info_1_liste' => '1 bolet&iacute;n informativo',
'info_liste_2' => 'boletines informativos',

'options' => 'radio|brut|Formato:|Html,Texto,No recibir novedades|html,texte,non'

);

?>