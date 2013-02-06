<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Crear la lista',
	'action_recuperer_liste' => 'Recuperar la lista de los sitios',

	// C
	'cfg_description_piwik' => 'Aquí puede indicar su identificador piwik, así como la dirección del servidor que administra sus estadísticas.',
	'cfg_erreur_recuperation_data' => 'Hay un error de comunicación con el servidor, verifique por favor la dirección y el token',
	'cfg_erreur_token' => 'Su token de identificación no es válido',
	'cfg_erreur_user_token' => 'La correspondencia entre Nombre de usuario / Token no es correcta. ',

	// E
	'explication_adresse_serveur' => 'Introduzca la dirección sin "http://" ni "https://" ni barra final',
	'explication_creer_site' => 'El siguiente enlace le permite crear un sitio web en el servidor Piwik que estará disponible después en la lista. Verifique que ha configurado bien la dirección y el nombre de su sitio web SPIP antes de hacer click, ésta será la información utilizada.',
	'explication_exclure_ips' => 'Para excluir varias direcciones, sepárelas por punto y coma',
	'explication_identifiant_site' => 'La lista de los sitios webs disponibles en el servidor Piwik se ha recuperado automáticamente grácias a las informaciones presentadas. Seleccione en la siguiente lista el que le convenga',
	'explication_mode_insertion' => 'Il existe deux modes d\'insertion dans les pages du code nécessaire au bon fonctionnement du plugin. Par le pipeline "insert_head" (méthode automatique mais peu configurable), ou par l\'insertion d\'une balise (méthode manuelle en insérant dans le pied de vos pages la balise #PIWIK) qui, quant à elle est pleinement configurable.', # NEW
	'explication_recuperer_liste' => 'Le lien ci-dessous vous permet de récupérer la liste des sites que votre compte peut administrer sur le serveur Piwik.', # NEW
	'explication_restreindre_statut_prive' => 'Choisissez ici les statuts d\'utilisateurs qui ne seront pas comptabilisés dans les statistiques dans l\'espace privé', # NEW
	'explication_restreindre_statut_public' => 'Choisissez ici les statuts d\'utilisateurs qui ne seront pas comptabilisés dans les statistiques dans la partie publique', # NEW
	'explication_token' => 'Le token d\'identification est disponible dans vos préférences personnelles ou dans la partie API de votre serveur Piwik', # NEW

	// I
	'info_aucun_site_compte' => 'Ningún sitio web está asociado a su cuenta Piwik.',
	'info_aucun_site_compte_demander_admin' => 'Ha de solicitar a un administrador de su servidor Piwik el añadir un sitio correspondiente',

	// L
	'label_adresse_serveur' => 'Adresse URL du serveur (https:// ou http://)', # NEW
	'label_comptabiliser_prive' => 'Contabilizar las visitas al espacio privado',
	'label_creer_site' => 'Crear un sitio en el servidor Piwik',
	'label_exclure_ips' => 'Excluir ciertas direcciones IP',
	'label_identifiant_site' => 'L\'identifiant de votre site sur le serveur Piwik', # NEW
	'label_mode_insertion' => 'Modo de inserción en las páginas públicas',
	'label_piwik_user' => 'Compte utilisateur Piwik', # NEW
	'label_recuperer_liste' => 'Récupérer la liste des sites sur le serveur Piwik', # NEW
	'label_restreindre_auteurs_prive' => 'Restringir a ciertos usuarios conectados (privados)',
	'label_restreindre_auteurs_public' => 'Restringir a ciertos usuarios conectados (público)',
	'label_restreindre_statut_prive' => 'Restreindre certains statuts d\'utilisateurs dans l\'espace privé', # NEW
	'label_restreindre_statut_public' => 'Restreindre certains statuts d\'utilisateurs dans la partie publique', # NEW
	'label_token' => 'Token de identificación en el servidor',

	// M
	'mode_insertion_balise' => 'Insertion par la balise #PIWIK (modification nécessaire de vos squelettes)', # NEW
	'mode_insertion_pipeline' => 'Insertion automatique par le pipeline "insert_head"', # NEW

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Votre identifiant', # NEW
	'textes_url_piwik' => 'Votre serveur piwik' # NEW
);

?>
