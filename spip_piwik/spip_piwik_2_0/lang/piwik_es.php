<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Crear la lista',
	'action_recuperer_liste' => 'Recuperar la lista de sitios webs',

	// C
	'cfg_description_piwik' => 'Aquí puede indicar su identificador piwik, así como la dirección del servidor que administra sus estadísticas.',
	'cfg_erreur_recuperation_data' => 'Hay un error de comunicación con el servidor, verifique por favor la dirección y el token',
	'cfg_erreur_token' => 'Su token de identificación no es válido',
	'cfg_erreur_user_token' => 'La correspondencia entre Nombre de usuario / Token no es correcta. ',

	// E
	'explication_adresse_serveur' => 'Introduzca la dirección sin "http://" ni "https://" ni barra final',
	'explication_creer_site' => 'El siguiente enlace le permite crear un sitio web en el servidor Piwik que estará disponible después en la lista. Verifique que ha configurado bien la dirección y el nombre de su sitio web SPIP antes de hacer click, ésta será la información utilizada.',
	'explication_exclure_ips' => 'Para excluir varias direcciones, sepárelas por punto y coma',
	'explication_identifiant_site' => 'La lista de los sitios webs disponibles en el servidor Piwik se ha recuperado automáticamente gracias a las informaciones presentadas. Seleccione en la siguiente lista el que le convenga',
	'explication_mode_insertion' => 'Hay dos modos de inserción en las páginas del código necesario para el buen funcionamiento del plugin. Mediante el pipeline "insert_head" (método automático pero poco configurable), o mediante la inserción de una etiqueta (método manual cuando se inserta al pie de sus páginas la etiqueta #PIWIK), la cual es plenamente configurable. ',
	'explication_recuperer_liste' => 'El siguiente enlace le permite recuperar la lista de sitios webs que su cuenta puede administrar en el servidor Piwik.',
	'explication_restreindre_statut_prive' => 'Elija aquí los estatus de usuarios que no serán contabilizados en las estadísticas en el espacio privado',
	'explication_restreindre_statut_public' => 'Elija aquí los estatus de usuarios que no serán contabilizados en las estadísticas en la parte público',
	'explication_token' => 'El token de identificación está disponible en sus preferencias personales o en la parte API de su servidor Piwik',

	// I
	'info_aucun_site_compte' => 'Ningún sitio web está asociado a su cuenta Piwik.',
	'info_aucun_site_compte_demander_admin' => 'Ha de solicitar a un administrador de su servidor Piwik el añadir un sitio correspondiente',

	// L
	'label_adresse_serveur' => 'Dirección URL del servidor (https:// o http://)',
	'label_comptabiliser_prive' => 'Contabilizar las visitas al espacio privado',
	'label_creer_site' => 'Crear un sitio en el servidor Piwik',
	'label_exclure_ips' => 'Excluir ciertas direcciones IP',
	'label_identifiant_site' => 'El identificador de su sitio web en el servidor Piwik',
	'label_mode_insertion' => 'Modo de inserción en las páginas públicas',
	'label_piwik_user' => 'Cuenta de usuario Piwik',
	'label_recuperer_liste' => 'Recuperar la lista de sitios en el servidor Piwik',
	'label_restreindre_auteurs_prive' => 'Restringir a ciertos usuarios conectados (privados)',
	'label_restreindre_auteurs_public' => 'Restringir a ciertos usuarios conectados (público)',
	'label_restreindre_statut_prive' => 'Restringir a ciertos estatus de usuarios en el espacio privado',
	'label_restreindre_statut_public' => 'Restringir a ciertos estatus de usuarios en la parte pública',
	'label_token' => 'Token de identificación en el servidor',

	// M
	'mode_insertion_balise' => 'Inserción por la etiqueta #PIWIK (modificación necesaria de sus essqueletos)',
	'mode_insertion_pipeline' => 'Inserción automática por el pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Su identificador',
	'textes_url_piwik' => 'Su servidor Piwik'
);

?>
