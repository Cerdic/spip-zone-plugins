<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Crear el lloc',
	'action_recuperer_liste' => 'R&eacute;cup&eacute;rer la liste des sites', # NEW

	// C
	'cfg_description_piwik' => 'Podeu introduir aqu&iacute; el vostre nom d\'usuari piwik i l\'adre&ccedil;a del servidor que gestiona les vostres estad&iacute;stiques.',
	'cfg_erreur_recuperation_data' => 'Hi ha un error de comunicaci&oacute; amb el servidor, verifiqueu l\'adre&ccedil;a i el token',
	'cfg_erreur_token' => 'El vostre token d\'identificaci&oacute; &eacute;s inv&agrave;lid',
	'cfg_erreur_user_token' => 'La correspondance Nom d\'utilisateur / Token n\'est pas correcte.', # NEW

	// E
	'explication_adresse_serveur' => 'Entreu l\'adre&ccedil;a sense "http://" ni "https://" ni barra final',
	'explication_creer_site' => 'El seg&uuml;ent enlla&ccedil; us permet crear un lloc al servidor Piwik que estar&agrave; disponible a continuaci&oacute; a la llista. Verifiqueu que heu configurat l\'adre&ccedil;a correctament i el nom del vostre lloc SPIP abans de clicar. S&oacute;n aquestes informacions les que s\'utilitzaran.',
	'explication_exclure_ips' => 'Per excloure diverses adreces, separeu-les amb punts i comes',
	'explication_identifiant_site' => 'La llista dels llocs disponibles al servidor Piwik s\'ha recuperat autom&agrave;ticament gr&agrave;cies a les informacions presentades. Seleccioneu de la seg&uuml;ent llista la que m&eacute;s us convingui',
	'explication_mode_insertion' => 'Hi ha dues maneres d\'inserir a les p&agrave;gines el codi necessari per un bon funcionament del connector. Mitjan&ccedil;ant el pipeline "insert_head" (m&egrave;tode autom&agrave;tic per&ograve; poc configurable), o mitjan&ccedil;ant la inserci&oacute; d\'una etiqueta (m&egrave;tode manual inserint a la part inferior de les vostres p&agrave;gines l\'etiqueta #PIWIK) que, a m&eacute;s a m&eacute;s, &eacute;s totalment configurable.',
	'explication_recuperer_liste' => 'Le lien ci-dessous vous permet de r&eacute;cup&eacute;rer la liste des sites que votre compte peut administrer sur le serveur Piwik.', # NEW
	'explication_restreindre_statut_prive' => 'Escolliu aqu&iacute; els estats d\'usuaris que no es comptabilitzaran a les estad&iacute;stiques en l\'espai privat',
	'explication_restreindre_statut_public' => 'Escolliu aqu&iacute; els estats d\'usuaris que no es comptabilitzaran a les estad&iacute;stiques a la part p&uacute;blica',
	'explication_token' => 'El token d\'identificaci&oacute; est&agrave; disponible o b&eacute; a les vostres prefer&egrave;ncies personals o a la part API de vostre servidor Piwik',

	// I
	'info_aucun_site_compte' => 'Aucun site n\'est associ&eacute; &agrave; votre compte Piwik.', # NEW
	'info_aucun_site_compte_demander_admin' => 'Vous devez demander &agrave; un administrateur de votre serveur Piwik d\'ajouter un site correspondant.', # NEW

	// L
	'label_adresse_serveur' => 'Adre&ccedil;a URL del servidor (https:// o http://)',
	'label_comptabiliser_prive' => 'Comptabilitzar les visites de l\'espai privat',
	'label_creer_site' => 'Crear un lloc al servidor Piwik',
	'label_exclure_ips' => 'Excloure certes adreces IP',
	'label_identifiant_site' => 'L\'identificador del vostre lloc al servidor Piwik',
	'label_mode_insertion' => 'Mode d\'inserci&oacute; a les p&agrave;gines p&uacute;bliques',
	'label_piwik_user' => 'Compte utilisateur Piwik', # NEW
	'label_recuperer_liste' => 'R&eacute;cup&eacute;rer la liste des sites sur le serveur Piwik', # NEW
	'label_restreindre_auteurs_prive' => 'Restringir determinats usuaris connectats (privat)',
	'label_restreindre_auteurs_public' => 'Restringir determinats usuaris connectats (p&uacute;blic)',
	'label_restreindre_statut_prive' => 'Restringir determinats estats d\'usuaris a l\'espai privat',
	'label_restreindre_statut_public' => 'Restringir determinats estats d\'usuaris a l\'espai p&uacute;blic',
	'label_token' => 'Token d\'identificaci&oacute; al vostre servidor',

	// M
	'mode_insertion_balise' => 'Inserci&oacute; per mitj&agrave; de l\'etiqueta #PIWIK (cal que modifiqueu els vostres esquelets)',
	'mode_insertion_pipeline' => 'Inserci&oacute; autom&agrave;tica per mitja del pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Nom d\'usuari',
	'textes_url_piwik' => 'El vostre servidor piwik'
);

?>
