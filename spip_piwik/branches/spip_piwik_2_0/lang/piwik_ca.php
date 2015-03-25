<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=ca
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Crear el lloc',

	// C
	'cfg_description_piwik' => 'Podeu introduir aquí el vostre nom d’usuari piwik i l’adreça del servidor que gestiona les vostres estadístiques.',
	'cfg_erreur_recuperation_data' => 'Hi ha un error de comunicació amb el servidor, verifiqueu l’adreça i el token',
	'cfg_erreur_token' => 'El vostre token d’identificació és invàlid',

	// E
	'explication_adresse_serveur' => 'Entreu l’adreça sense "http://" ni "https://" ni barra final',
	'explication_creer_site' => 'El següent enllaç us permet crear un lloc al servidor Piwik que estarà disponible a continuació a la llista. Verifiqueu que heu configurat l’adreça correctament i el nom del vostre lloc SPIP abans de clicar. Són aquestes informacions les que s’utilitzaran.',
	'explication_exclure_ips' => 'Per excloure diverses adreces, separeu-les amb punts i comes',
	'explication_identifiant_site' => 'La llista dels llocs disponibles al servidor Piwik s’ha recuperat automàticament gràcies a les informacions presentades. Seleccioneu de la següent llista la que més us convingui',
	'explication_mode_insertion' => 'Hi ha dues maneres d’inserir a les pàgines el codi necessari per un bon funcionament del connector. Mitjançant el pipeline "insert_head" (mètode automàtic però poc configurable), o mitjançant la inserció d’una etiqueta (mètode manual inserint a la part inferior de les vostres pàgines l’etiqueta #PIWIK) que, a més a més, és totalment configurable.',
	'explication_restreindre_statut_prive' => 'Escolliu aquí els estats d’usuaris que no es comptabilitzaran a les estadístiques en l’espai privat',
	'explication_restreindre_statut_public' => 'Escolliu aquí els estats d’usuaris que no es comptabilitzaran a les estadístiques a la part pública',
	'explication_token' => 'El token d’identificació està disponible o bé a les vostres preferències personals o a la part API de vostre servidor Piwik',

	// L
	'label_adresse_serveur' => 'Adreça URL del servidor (https:// o http://)',
	'label_comptabiliser_prive' => 'Comptabilitzar les visites de l’espai privat',
	'label_creer_site' => 'Crear un lloc al servidor Piwik',
	'label_exclure_ips' => 'Excloure certes adreces IP',
	'label_identifiant_site' => 'L’identificador del vostre lloc al servidor Piwik',
	'label_mode_insertion' => 'Mode d’inserció a les pàgines públiques',
	'label_restreindre_auteurs_prive' => 'Restringir determinats usuaris connectats (privat)',
	'label_restreindre_auteurs_public' => 'Restringir determinats usuaris connectats (públic)',
	'label_restreindre_statut_prive' => 'Restringir determinats estats d’usuaris a l’espai privat',
	'label_restreindre_statut_public' => 'Restringir determinats estats d’usuaris a l’espai públic',
	'label_token' => 'Token d’identificació al vostre servidor',

	// M
	'mode_insertion_balise' => 'Inserció per mitjà de l’etiqueta #PIWIK (cal que modifiqueu els vostres esquelets)',
	'mode_insertion_pipeline' => 'Inserció automàtica per mitja del pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Nom d’usuari',
	'textes_url_piwik' => 'El vostre servidor piwik'
);

?>
